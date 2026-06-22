<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Podcast extends Model
{
    protected $fillable = ['user_id', 'study_material_id', 'title', 'script', 'audio_path', 'duration_seconds'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function studyMaterial(): BelongsTo
    {
        return $this->belongsTo(StudyMaterial::class);
    }
}
