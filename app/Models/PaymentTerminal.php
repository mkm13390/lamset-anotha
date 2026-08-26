<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PaymentTerminal extends Model
{
    protected $fillable = [
        'name',
        'provider',
        'bank_name',
        'terminal_id',
        'cash_register_id',
        'integration_type',
        'connection_host',
        'connection_port',
        'device_identifier',
        'is_active',
        'is_default',
        'settings',
    ];

    protected $casts = [
        'connection_port' => 'integer',
        'is_active' => 'boolean',
        'is_default' => 'boolean',
        'settings' => 'array',
    ];

    public function cashRegister(): BelongsTo
    {
        return $this->belongsTo(CashRegister::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(PosTerminalTransaction::class);
    }
}
