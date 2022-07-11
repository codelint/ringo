<?php namespace Codelint\Ringo\Laravel\Cache;

/**
 * DefaultCache:
 * @date 2022/7/11
 * @time 10:47
 * @author Ray.Zhang <codelint@foxmail.com>
 **/
class DefaultCache implements ICache {

    public function get($key, $default = null)
    {
        if ($v = cache()->get($key))
        {
            return json_decode($v, true);
        }
        else
        {
            return $default;
        }
    }

    public function put($key, $value, $ttl = 86400)
    {
        cache()->put($key, json_encode($value), $ttl);
    }

    public function pull($key, $default = null)
    {
        if ($v = cache()->pull($key))
        {
            return json_decode($v, true);
        }
        else
        {
            return $default;
        }
    }
}