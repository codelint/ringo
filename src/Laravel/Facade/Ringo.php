<?php namespace Codelint\Ringo\Laravel\Facade;

use Illuminate\Support\Facades\Facade;

/**
 * Ringo:
 * @date 2021/11/26
 * @time 15:04
 * @author Ray.Zhang <codelint@foxmail.com>
 * @method static void info(string $message, array $info = [])
 * @method static void mail(string $message, array $info = [], array $mails = [])
 * @method static void ex_mail(\Exception|\Throwable $exception, array $mails = [])
 * @method static void notify(string $message, array $info = [], array $mails = [])
 * @method static void alert(string $message, array $info = [])
 * @method static void error(string $message, array $info = [])
 * @method static void weCorp(string $message, array $info = [])
 * @method static void weChat(string $uid, string $message, array $info = [])
 * @method static void weError(string $message, array $info = [])
 * @method static void exception(\Exception $exception)
 **/
class Ringo extends Facade {

    protected static function getFacadeAccessor()
    {
        return 'codelint.ringo.logger';
    }

}