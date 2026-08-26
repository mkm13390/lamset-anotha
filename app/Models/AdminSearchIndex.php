<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdminSearchIndex extends Model
{
    protected $table = 'admin_search_index';

    protected $fillable = [
        'entity_type',
        'entity_id',
        'title',
        'subtitle',
        'searchable_text',
        'route_name',
        'route_parameters',
        'is_active',
    ];

    protected $casts = [
        'entity_id' => 'integer',
        'route_parameters' => 'array',
        'is_active' => 'boolean',
    ];
}
