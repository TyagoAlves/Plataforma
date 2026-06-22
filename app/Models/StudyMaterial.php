<?php

namespace App\Models;

use App\Jobs\ProcessStudyMaterialJob;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StudyMaterial extends Model
{
    protected $fillable = ['user_id', 'subject_id', 'title', 'content', 'file_path', 'file_type', 'status'];

    protected function casts(): array
    {
        return [
            'status' => 'string',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    public function quizzes(): HasMany
    {
        return $this->hasMany(Quiz::class);
    }

    public function slides(): HasMany
    {
        return $this->hasMany(Slide::class);
    }

    public function podcasts(): HasMany
    {
        return $this->hasMany(Podcast::class);
    }

    protected static function booted(): void
    {
        static::created(function (StudyMaterial $material) {
            ProcessStudyMaterialJob::dispatch($material);
        });
    }
}
