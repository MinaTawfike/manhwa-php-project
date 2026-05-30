<?php

namespace App\Helpers;

use Closure;
use Illuminate\Support\Facades\Cache;

class PaginationCache
{
    public static function remember(string $prefix, string $filter, int $page, int $ttl, Closure $callback)
    {
        return Cache::remember(self::key($prefix, $filter, $page), $ttl, $callback);
    }

    public static function rememberTotals(string $prefix, string $filter, int $ttl, Closure $callback): array
    {
        return Cache::remember(self::key($prefix, $filter, null), $ttl, $callback);
    }

    private static function key(string $prefix, string $filter, ?int $page): string
    {
        return implode(':', array_filter([
            $prefix,
            md5($filter),
            $page === null ? null : 'page_'.$page,
        ]));
    }
}
