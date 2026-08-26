<?php

namespace App\Services;

use App\Models\CustomerBehaviorEvent;
use App\Models\User;

class CustomerBehaviorService
{
    public function record(
        string $eventType,
        ?User $user = null,
        array $data = []
    ): CustomerBehaviorEvent {
        return CustomerBehaviorEvent::create([
            'user_id' => $user?->id,
            'session_key' => $data['session_key'] ?? session()->getId(),
            'event_type' => $eventType,
            'product_id' => $data['product_id'] ?? null,
            'product_variant_id' => $data['product_variant_id'] ?? null,
            'category_id' => $data['category_id'] ?? null,
            'value' => $data['value'] ?? null,
            'source' => $data['source'] ?? request()->route()?->getName(),
            'device_type' => $data['device_type'] ?? null,
            'metadata' => $data['metadata'] ?? null,
            'occurred_at' => now(),
        ]);
    }
}
