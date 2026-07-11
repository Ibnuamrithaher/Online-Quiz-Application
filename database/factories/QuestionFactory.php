<?php

namespace Database\Factories;

use App\Models\Question;
use App\Models\Quiz;
use Illuminate\Database\Eloquent\Factories\Factory;

class QuestionFactory extends Factory
{
    protected $model = Question::class;

    public function definition(): array
    {
        return [
            'quiz_id' => Quiz::factory(),
            'type' => 'multiple_choice',
            'category' => $this->faker->word(),
            'content' => $this->faker->sentence(),
            'points' => 10,
        ];
    }

    public function essay(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'essay',
            'points' => 25,
        ]);
    }
}
