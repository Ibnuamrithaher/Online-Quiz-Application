<?php

namespace Tests\Feature;

use App\Models\Question;
use App\Models\QuestionOption;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class ParticipantQuizTest extends TestCase
{
    use RefreshDatabase;

    public function test_participant_can_view_active_quiz_list()
    {
        $user = User::factory()->create();
        Quiz::factory()->create(['title' => 'Active Quiz', 'is_active' => true]);
        Quiz::factory()->create(['title' => 'Inactive Quiz', 'is_active' => false]);

        $response = $this->actingAs($user)->get('/quizzes');

        $response->assertStatus(200);
        $response->assertSee('Active Quiz');
        $response->assertDontSee('Inactive Quiz');
    }

    public function test_participant_can_view_quiz_detail_and_session_started()
    {
        $user = User::factory()->create();
        $quiz = Quiz::factory()->create(['is_active' => true]);

        $response = $this->actingAs($user)->get('/quizzes/' . $quiz->id);

        $response->assertStatus(200);
        $response->assertSessionHas('quiz_start_' . $quiz->id);
    }

    public function test_participant_can_submit_quiz_successfully()
    {
        $user = User::factory()->create();
        $quiz = Quiz::factory()->create(['is_active' => true, 'time_limit' => 60]);
        
        $mcQuestion = Question::factory()->create([
            'quiz_id' => $quiz->id,
            'type' => 'multiple_choice',
        ]);
        $correctOption = QuestionOption::factory()->correct()->create([
            'question_id' => $mcQuestion->id,
            'points' => 10,
        ]);

        $essayQuestion = Question::factory()->essay()->create([
            'quiz_id' => $quiz->id,
            'points' => 20,
        ]);

        $response = $this->actingAs($user)
            ->withSession(['quiz_start_' . $quiz->id => now()])
            ->post('/quizzes/' . $quiz->id . '/attempt', [
                'answers' => [
                    $mcQuestion->id => $correctOption->id,
                    $essayQuestion->id => 'This is my essay answer',
                ]
            ]);

        // Should redirect to result
        $attempt = QuizAttempt::where('user_id', $user->id)->first();
        $this->assertNotNull($attempt);
        $response->assertRedirect('/quizzes/' . $quiz->id . '/result/' . $attempt->id);

        // Score should be 10 because essay is not graded automatically
        $this->assertEquals(10, $attempt->score);
        $this->assertDatabaseHas('user_answers', [
            'quiz_attempt_id' => $attempt->id,
            'question_id' => $mcQuestion->id,
            'is_correct' => 1,
            'score' => 10,
        ]);
        $this->assertDatabaseHas('user_answers', [
            'quiz_attempt_id' => $attempt->id,
            'question_id' => $essayQuestion->id,
            'answer_text' => 'This is my essay answer',
            'is_correct' => null,
            'score' => null,
        ]);
    }

    public function test_participant_cannot_submit_if_time_limit_exceeded()
    {
        $user = User::factory()->create();
        $quiz = Quiz::factory()->create(['is_active' => true, 'time_limit' => 60]);

        // Mock session start 62 minutes ago (limit 60 + 1 tolerance = 61)
        $response = $this->actingAs($user)
            ->withSession(['quiz_start_' . $quiz->id => now()->subMinutes(62)])
            ->post('/quizzes/' . $quiz->id . '/attempt', [
                'answers' => [1 => 'dummy']
            ]);

        $response->assertSessionHas('error', 'Waktu pengerjaan kuis telah habis.');
        $this->assertDatabaseCount('quiz_attempts', 0);
    }

    public function test_double_submit_is_prevented_by_cache_lock()
    {
        $user = User::factory()->create();
        $quiz = Quiz::factory()->create(['is_active' => true, 'time_limit' => 60]);

        // Acquire lock to simulate concurrent request
        $lock = Cache::lock('submit_quiz_' . $quiz->id . '_' . $user->id, 10);
        $lock->get();

        $response = $this->actingAs($user)
            ->withSession(['quiz_start_' . $quiz->id => now()])
            ->post('/quizzes/' . $quiz->id . '/attempt', [
                'answers' => [1 => 'dummy']
            ]);

        $response->assertSessionHas('error', 'Sedang memproses jawaban Anda. Mohon tunggu.');
        $this->assertDatabaseCount('quiz_attempts', 0);
    }
}
