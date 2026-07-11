<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use Illuminate\Http\Request;

class AttemptController extends Controller
{
    public function index(Quiz $quiz)
    {
        $attempts = QuizAttempt::where('quiz_id', $quiz->id)
            ->with('user')
            ->latest()
            ->paginate(20);

        return view('admin.attempts.index', compact('quiz', 'attempts'));
    }

    public function show(Quiz $quiz, QuizAttempt $attempt)
    {
        if ($attempt->quiz_id !== $quiz->id) {
            abort(404);
        }

        $attempt->load(['user', 'answers.question.options', 'answers.option']);

        return view('admin.attempts.show', compact('quiz', 'attempt'));
    }

    public function grade(Request $request, Quiz $quiz, QuizAttempt $attempt)
    {
        if ($attempt->quiz_id !== $quiz->id) {
            abort(404);
        }

        $validated = $request->validate([
            'grades' => 'required|array',
            'grades.*' => 'required|numeric|min:0',
        ]);

        $scoreAdded = 0;
        $errors = [];

        foreach ($validated['grades'] as $answerId => $inputScore) {
            $answer = $attempt->answers()->where('id', $answerId)->first();
            
            if ($answer && $answer->question->type === 'essay') {
                $maxPoints = $answer->question->points ?? 100;
                $newScore = (int)$inputScore;

                if ($newScore > $maxPoints) {
                    $errors["grades.$answerId"] = "Nilai tidak boleh lebih dari bobot maksimal soal ($maxPoints).";
                    continue;
                }

                $oldScore = $answer->score ?? 0;
                $scoreDifference = $newScore - $oldScore;
                $scoreAdded += $scoreDifference;

                $answer->update([
                    'is_correct' => $newScore > 0, // Just a flag
                    'score' => $newScore
                ]);
            }
        }

        if (!empty($errors)) {
            return back()->withErrors($errors)->withInput();
        }

        if ($scoreAdded !== 0) {
            $attempt->increment('score', $scoreAdded);
        }

        return redirect()->route('admin.quizzes.attempts.index', $quiz)->with('success', 'Penilaian essay berhasil disimpan.');
    }
}
