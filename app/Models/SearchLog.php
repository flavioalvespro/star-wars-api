<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SearchLog extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'entity_type',
        'search_term',
        'results_count',
        'response_time_ms',
        'searched_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'searched_at' => 'datetime',
        'response_time_ms' => 'float',
        'results_count' => 'integer',
    ];
}
