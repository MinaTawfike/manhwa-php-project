<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Comic extends Model
{
    protected $fillable = ['title', 'description', 'poster', 'status', 'latest_update', 'options'];
    
    protected $casts = [
        'options' => 'array',
        'latest_update' => 'datetime',
    ];

    public function chapters(): HasMany
    {
        return $this->hasMany(Chapter::class);
    }
}
