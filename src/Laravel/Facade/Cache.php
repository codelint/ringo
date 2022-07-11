<?php namespace Codelint\Ringo\Laravel\Facade;

use Illuminate\Support\Facades\Facade;

/**
 * Cache:
 * @date 2022/7/11
 * @time 10:42
 * @author Ray.Zhang <codelint@foxmail.com>
 * @method static mixed get($key, $default = null)
 * @method static mixed pull($key, $default = null)
 * @method static mixed put($key, $value, $ttl = 86400)
 **/
class Cache extends Facade {

    protected static function getFacadeAccessor()
    {
        return 'codelint.ringo.cache';
    }

}