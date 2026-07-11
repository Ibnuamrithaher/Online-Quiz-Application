<?php

namespace Tests\Unit;

use App\Models\Question;
use App\Models\QuestionOption;
use App\Models\Quiz;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class QuizModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_get_max_score_attribute_calculates_correctly()
    {
        $quiz = Quiz::factory()->create();

        // Add 1 multiple choice question with max 10 points
        $mcQuestion = Question::factory()->create([
            'quiz_id' => $quiz->id,
            'type' => 'multiple_choice',
        ]);
        QuestionOption::factory()->create([
            'question_id' => $mcQuestion->id,
            'points' => 5,
        ]);
        QuestionOption::factory()->create([
            'question_id' => $mcQuestion->id,
            'points' => 10,
        ]);

        // Add 1 essay question with 20 points
        Question::factory()->essay()->create([
            'quiz_id' => $quiz->id,
            'points' => 20,
        ]);

        // Max score should be 10 + 20 = 30
        $this->assertEquals(30, $quiz->max_score);
    }
}
