<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PosShortcut extends Model
{
    protected $fillable = [
        'action_key',
        'shortcut',
        'label_ar',
        'label_en',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];
}
