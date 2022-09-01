<?php namespace Codelint\Ringo\Laravel\Sender;

use Illuminate\Support\Facades\Log;
use ReflectionClass;

/**
 * SenderFactory:
 * @date 2022/8/17
 * @time 23:11
 * @author Ray.Zhang <codelint@foxmail.com>
 * @method ISender common()
 * @method ISender mail()
 * @method ISender app()
 **/
class SenderFactory
{
    public function __call(string $name, array $arguments)
    {
        $configs = config('ringo.' . $name);
        $job_connection = config('ringo.job_connection');
        $senders = [];
        foreach ($configs as $config_name)
        {
            if ($config = config('ringo.senders.' . $config_name))
            {
                $config['job_connection'] = $config['job_connection'] ?? $job_connection;
                if ($driver = $config['driver'])
                {
                    try
                    {
                        $reflection = new ReflectionClass($driver);
                        $sender = $reflection->newInstance($config);
                        $senders[] = $sender;
                    } catch (\ReflectionException $e)
                    {
                        Log::error($e->getMessage());
                        Log::error($e->getTraceAsString());
                    }
                }
            }
        }

        return new MultiSender($senders);
    }


}