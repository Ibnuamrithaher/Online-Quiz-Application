<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuestionOption extends Model
{
    use \Illuminate\Database\Eloquent\Factories\HasFactory;

    protected $fillable = ['question_id', 'content', 'points'];

    protected $casts = [
        'points' => 'integer',
    ];

    public function question()
    {
        return $this->belongsTo(Question::class);
    }
}
