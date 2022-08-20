<?php namespace Codelint\Ringo\Laravel\Sender;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Facades\Log;

/**
 * MultiSender:
 * @date 2022/8/17
 * @time 23:04
 * @author Ray.Zhang <codelint@foxmail.com>
 **/
class MultiSender implements ISender
{
    /**
     * @var Arrayable
     */
    protected $senders;

    public function __construct($senders)
    {
        $this->senders = $senders;
    }

    public function send($message, $option = [], $recipients = [])
    {
        foreach ($this->senders as $sender)
        {
            /**
             * @var $sender ISender
             */
            try
            {
                $sender->send($message, $option, $recipients);
            } catch (\Exception $e)
            {
                Log::error($e->getMessage());
                Log::error($e->getTraceAsString());
            }
        }
    }
}