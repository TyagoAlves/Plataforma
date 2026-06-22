<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProcessResponse extends Model
{
    protected $fillable = ['process_id', 'ai_suggestion', 'final_response', 'status'];

    public function process()
    {
        return $this->belongsTo(Process::class);
    }
}
