<?php namespace Codelint\Ringo\Laravel\Sender;

use Illuminate\Support\Facades\Log;

/**
 * LoggerSender:
 * @date 2022/8/17
 * @time 22:30
 * @author Ray.Zhang <codelint@foxmail.com>
 **/
class LoggerSender extends AbstractSender
{

    public function send($message, $option = [], $recipients = [])
    {
        // Log::info($message);

        if (count($option))
        {
            $message .= ' - ' . $this->stringify($option);
        }

        if (count($recipients))
        {
            $message = $message . '(' . 'recipients:' . implode(',', $recipients) . ')';
        }

        Log::info($message);
    }
}