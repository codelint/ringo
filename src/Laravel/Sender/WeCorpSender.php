<?php namespace Codelint\Ringo\Laravel\Sender;

use Codelint\Ringo\Laravel\Jobs\SendWeCorpMessageJob;

/**
 * WeCorpSender:
 * @date 2022/8/17
 * @time 22:30
 * @author Ray.Zhang <codelint@foxmail.com>
 **/
class WeCorpSender extends AbstractSender
{
    public function send($message, $option = [], $recipients = [])
    {
        if (!count($recipients))
        {
            $recipients[] = $this->config['default_cid'];
        }

        foreach ($recipients as $recipient)
        {
            $this->wx($message, $option, $recipient);
        }
    }

    private function wx($message, $option, $recipient)
    {
        $job = new SendWeCorpMessageJob($message, $option);
        $job->onConnection($this->config['job_connection']);
        $job->setChatId($recipient);
        dispatch($job);
    }
}