<?php

namespace App\Support;

use App\Models\FeatureModule;
use Illuminate\Support\Facades\Cache;

class ModuleGate
{
    public static function enabled(string $key): bool
    {
        $modules = Cache::remember('feature_modules_map', 60, function () {
            return FeatureModule::query()->pluck('enabled', 'key')->all();
        });

        if (array_key_exists($key, $modules)) {
            return (bool) $modules[$key];
        }

        $definitions = collect(config('modules.definitions', []))->keyBy('key');

        return (bool) data_get($definitions->get($key), 'enabled', false);
    }

    public static function flush(): void
    {
        Cache::forget('feature_modules_map');
    }
}
