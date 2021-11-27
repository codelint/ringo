<?php namespace Codelint\Ringo\Laravel;

use Codelint\Ringo\RingoLogger;
use Illuminate\Support\ServiceProvider;

/**
 * RingoProvider:
 * @date 2021/11/26
 * @time 15:05
 * @author Ray.Zhang <codelint@foxmail.com>
 **/
class RingoProvider  extends ServiceProvider {

    public function ns()
    {
        return 'ringo';
    }

    protected function base_dir($path)
    {
        return __DIR__ . '/../../' . $path;
    }

    public function register()
    {
        $this->app->singleton('codelint.ringo.logger', function () {
            return new RingoLogger();
        });
    }

    public function boot()
    {
        $this->loadViewsFrom($this->base_dir('resources/views'), $this->ns());
    }
}