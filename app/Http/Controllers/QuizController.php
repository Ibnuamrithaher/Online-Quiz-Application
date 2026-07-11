<?php

namespace App\Http\Controllers;

use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\QuestionOption;
use Illuminate\Http\Request;
use App\Http\Requests\SubmitQuizAttemptRequest;

class QuizController extends Controller
{
    public function index()
    {
        $quizzes = Quiz::select('id', 'title', 'description', 'time_limit')
            ->where('is_active', true)
            ->latest()
            ->paginate(10);
        return view('quizzes.index', compact('quizzes'));
    }

    public function history()
    {
        $attempts = QuizAttempt::select('id', 'quiz_id', 'user_id', 'score', 'created_at')
            ->where('user_id', auth()->id())
            ->with(['quiz:id,title']) // max_score will lazily evaluate or run its own queries, we only need title and id from quiz
            ->latest()
            ->paginate(10);
            
        return view('quizzes.history', compact('attempts'));
    }

    public function show(Quiz $quiz)
    {
        if (!$quiz->is_active) {
            abort(404);
        }

        // Catat waktu mulai kuis untuk validasi durasi pengerjaan
        $sessionKey = 'quiz_start_' . $quiz->id;
        if (!session()->has($sessionKey)) {
            session()->put($sessionKey, now());
            \App\Models\ActivityLog::record('Start Quiz', "Participant started quiz: {$quiz->title} (ID: {$quiz->id})");
        }

        $quiz->load([
            'questions:id,quiz_id,content,type,category,image_path,points', 
            'questions.options:id,question_id,content,points'
        ]);
        return view('quizzes.show', compact('quiz'));
    }

    public function attempt(SubmitQuizAttemptRequest $request, Quiz $quiz)
    {
        if (!$quiz->is_active) {
            abort(404);
        }

        $userId = auth()->id();

        // 1. Mencegah Double Submit (Race Condition)
        $lock = \Illuminate\Support\Facades\Cache::lock('submit_quiz_' . $quiz->id . '_' . $userId, 10);
        if (!$lock->get()) {
            return back()->with('error', 'Sedang memproses jawaban Anda. Mohon tunggu.');
        }

        // 2. Validasi Durasi Waktu (Time Limit Enforcement)
        $sessionKey = 'quiz_start_' . $quiz->id;
        $startTime = session($sessionKey);
        
        if ($startTime && $quiz->time_limit) {
            // Beri toleransi waktu 1 menit untuk masalah jaringan
            if (\Carbon\Carbon::parse($startTime)->addMinutes($quiz->time_limit + 1)->isPast()) {
                $lock->release();
                return back()->with('error', 'Waktu pengerjaan kuis telah habis.');
            }
        }

        $validated = $request->validated();

        try {
            $attempt = \Illuminate\Support\Facades\DB::transaction(function () use ($quiz, $validated, $userId) {
                $attempt = QuizAttempt::create([
                    'user_id' => $userId,
                    'quiz_id' => $quiz->id,
                    'score' => 0, // Inisialisasi awal 0
                ]);

                // 3. Pindahkan logika grading yang panjang ke method private
                $score = $this->gradeAndSaveAnswers($quiz, $validated['answers'] ?? [], $attempt->id);

                $attempt->update(['score' => $score]);

                return $attempt;
            });

            // Hapus sesi waktu mulai dan lepaskan lock
            session()->forget($sessionKey);
            $lock->release();
            
            \App\Models\ActivityLog::record('Submit Quiz', "Participant submitted quiz: {$quiz->title} (ID: {$quiz->id}) with score: {$attempt->score}");

            return redirect()->route('quizzes.result', ['quiz' => $quiz->id, 'attempt' => $attempt->id]);
            
        } catch (\Exception $e) {
            $lock->release();
            \Illuminate\Support\Facades\Log::error('Quiz Attempt Error: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat menyimpan jawaban Anda. Silakan coba lagi.');
        }
    }

    /**
     * Memproses grading jawaban dan menyimpan data ke database.
     */
    private function gradeAndSaveAnswers(Quiz $quiz, array $answers, int $attemptId): float|int
    {
        $score = 0;
        $quiz->load([
            'questions:id,quiz_id,type', 
            'questions.options:id,question_id,points'
        ]);

        $answersToInsert = [];
        $now = now();

        foreach ($quiz->questions as $question) {
            $answerData = $answers[$question->id] ?? null;
            if (!$answerData) continue;

            $isCorrect = null;
            $optionId = null;
            $answerText = null;
            $answerScore = null;

            if ($question->type === 'multiple_choice') {
                $optionId = $answerData;
                $option = $question->options->where('id', $optionId)->first();
                if ($option) {
                    $isCorrect = $option->points > 0;
                    $score += $option->points;
                    $answerScore = $option->points;
                } else {
                    continue; // Skip jika opsi tidak valid
                }
            } else {
                // Essay
                $answerText = $answerData;
            }

            $answersToInsert[] = [
                'quiz_attempt_id' => $attemptId,
                'question_id' => $question->id,
                'question_option_id' => $optionId,
                'answer_text' => $answerText,
                'is_correct' => $isCorrect,
                'score' => $answerScore,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        if (!empty($answersToInsert)) {
            \App\Models\UserAnswer::insert($answersToInsert);
        }

        return $score;
    }

    public function result(Quiz $quiz, QuizAttempt $attempt)
    {
        if ($attempt->user_id !== auth()->id() || $attempt->quiz_id !== $quiz->id) {
            abort(403);
        }

        $attempt->load('answers:id,quiz_attempt_id,question_id,question_option_id,answer_text,score,is_correct');
        $quiz->load([
            'questions:id,quiz_id,content,type,category,image_path,explanation,points', 
            'questions.options:id,question_id,content,points'
        ]);
        return view('quizzes.result', compact('quiz', 'attempt'));
    }
}
