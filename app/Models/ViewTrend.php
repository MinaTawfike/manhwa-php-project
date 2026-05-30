<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ViewTrend extends Model
{
    protected $fillable = [
        'date',
        'total_views',
        'unique_visitors',
        'bookmarks_added',
    ];

    protected $casts = [
        'date' => 'date',
        'total_views' => 'integer',
        'unique_visitors' => 'integer',
        'bookmarks_added' => 'integer',
    ];
}
