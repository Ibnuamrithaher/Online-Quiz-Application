<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Quiz extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'description',
        'is_active',
        'time_limit',
    ];

    public function questions()
    {
        return $this->hasMany(Question::class);
    }

    public function getMaxScoreAttribute()
    {
        // Hindari masalah N+1 query dengan eager loading relasi jika belum ada
        $this->loadMissing('questions.options');

        $maxScore = 0;
        foreach ($this->questions as $question) {
            if ($question->type === 'multiple_choice') {
                $maxPoints = $question->options->max('points');
                $maxScore += $maxPoints ?? 0;
            } elseif ($question->type === 'essay') {
                $maxScore += $question->points ?? 100;
            }
        }
        return $maxScore;
    }
}
