<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Process extends Model
{
    protected $fillable = ['process_category_id', 'number', 'title', 'content', 'status', 'date', 'type'];

    protected function casts(): array
    {
        return [
            'date' => 'date',
        ];
    }

    public function category()
    {
        return $this->belongsTo(ProcessCategory::class, 'process_category_id');
    }

    public function response()
    {
        return $this->hasOne(ProcessResponse::class);
    }
}
