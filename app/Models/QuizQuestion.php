<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QuizQuestion extends Model
{
    protected $fillable = ['quiz_id', 'question', 'options', 'correct_answer', 'user_answer', 'answered_correctly', 'order'];

    protected function casts(): array
    {
        return [
            'options' => 'array',
            'answered_correctly' => 'boolean',
        ];
    }

    public function quiz(): BelongsTo
    {
        return $this->belongsTo(Quiz::class);
    }
}
