<?php

namespace GeoSot\AppCache\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static array get(string $key, array $default = [])
 * @method static bool has(string $key)
 * @method static bool add(string $key, array $data, bool $force = false)
 * @method static array remember(string $key, \Closure $callback)
 * @method static bool forget(string $key)
 * @method static bool clear()
 *
 * @see \GeoSot\AppCache\AppCache
 */
class AppCache extends Facade
{
    /**
     * Get the registered name of the component.
     */
    protected static function getFacadeAccessor(): string
    {
        return \GeoSot\AppCache\AppCache::class;
    }
}
