<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;

class DataVersion
{
    public static function get(string $key = 'products')
    {
        return Cache::get("version:{$key}", 'v1');
    }

    public static function bump(string $key = 'products')
    {
        $new = uniqid("v", true);
        Cache::forever("version:{$key}", $new);
        return $new;
    }
}
