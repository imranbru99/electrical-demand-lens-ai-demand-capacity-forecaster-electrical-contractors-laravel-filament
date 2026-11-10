<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CalendarIntegration extends Model
{
    protected $fillable = [
        'provider', 'label', 'enabled', 'config', 'last_synced_at',
    ];

    protected function casts(): array
    {
        return [
            'enabled' => 'boolean',
            'config' => 'array',
            'last_synced_at' => 'datetime',
        ];
    }
}
