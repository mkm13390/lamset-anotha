<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AiCustomerMarketingProfile extends Model
{
    protected $table = 'ai_customer_marketing_profiles';

    protected $fillable = [
        'user_id',
        'purchase_probability',
        'churn_probability',
        'discount_sensitivity',
        'predicted_lifetime_value',
        'preferred_channel',
        'preferred_send_window',
        'top_categories',
        'top_products',
        'next_best_actions',
        'model_version',
        'scored_at',
    ];

    protected $casts = [
        'purchase_probability' => 'decimal:6',
        'churn_probability' => 'decimal:6',
        'discount_sensitivity' => 'decimal:6',
        'predicted_lifetime_value' => 'decimal:3',
        'top_categories' => 'array',
        'top_products' => 'array',
        'next_best_actions' => 'array',
        'scored_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
