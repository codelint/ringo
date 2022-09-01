<?php namespace Codelint\Ringo\Laravel\Sender;

/**
 * AbstractSender:
 * @date 2022/8/17
 * @time 22:36
 * @author Ray.Zhang <codelint@foxmail.com>
 **/
abstract class AbstractSender implements ISender
{

    protected array $config;

    public function __construct($config = [])
    {
        $this->config = $config;
    }

    protected function stringify($mixed): bool|string
    {
        return json_encode($mixed, JSON_UNESCAPED_UNICODE);
    }

}