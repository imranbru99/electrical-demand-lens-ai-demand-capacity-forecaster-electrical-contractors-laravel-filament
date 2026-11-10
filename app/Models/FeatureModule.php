<?php

namespace App\Models;

use App\Support\ModuleGate;
use Illuminate\Database\Eloquent\Model;

class FeatureModule extends Model
{
    protected $fillable = [
        'key', 'name', 'category', 'description', 'enabled', 'is_core', 'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'enabled' => 'boolean',
            'is_core' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::saved(fn () => ModuleGate::flush());
        static::deleted(fn () => ModuleGate::flush());
    }
}
