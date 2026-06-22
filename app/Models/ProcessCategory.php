<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProcessCategory extends Model
{
    protected $fillable = ['name', 'slug', 'description', 'icon'];

    public function processes()
    {
        return $this->hasMany(Process::class);
    }
}
