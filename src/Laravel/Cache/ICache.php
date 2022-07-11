<?php namespace Codelint\Ringo\Laravel\Cache;

/**
 * ICache:
 * @date 2022/7/11
 * @time 10:46
 * @author Ray.Zhang <codelint@foxmail.com>
 **/
interface ICache {

    public function get($key, $default = null);

    public function put($key, $value, $ttl = 86400);

    public function pull($key, $default = null);

}