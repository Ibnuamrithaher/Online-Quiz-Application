<?php

namespace Database\Factories;

use App\Models\QuestionOption;
use App\Models\Question;
use Illuminate\Database\Eloquent\Factories\Factory;

class QuestionOptionFactory extends Factory
{
    protected $model = QuestionOption::class;

    public function definition(): array
    {
        return [
            'question_id' => Question::factory(),
            'content' => $this->faker->sentence(),
            'points' => 0,
        ];
    }

    public function correct(): static
    {
        return $this->state(fn (array $attributes) => [
            'points' => 10,
        ]);
    }
}
