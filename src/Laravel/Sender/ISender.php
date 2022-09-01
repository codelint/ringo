<?php namespace Codelint\Ringo\Laravel\Sender;

/**
 * ISender:
 * @date 2022/8/17
 * @time 22:28
 * @author Ray.Zhang <codelint@foxmail.com>
 **/
interface ISender
{
    public function send($message, $option = [], $recipients = []);
}