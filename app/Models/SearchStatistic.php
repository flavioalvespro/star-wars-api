<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SearchStatistic extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'top_queries',
        'avg_response_time_ms',
        'popular_hours',
        'total_searches',
        'computed_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'top_queries' => 'array',
        'popular_hours' => 'array',
        'computed_at' => 'datetime',
        'avg_response_time_ms' => 'float',
        'total_searches' => 'integer',
    ];
}
