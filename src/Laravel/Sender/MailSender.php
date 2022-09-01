<?php namespace Codelint\Ringo\Laravel\Sender;

use Codelint\Ringo\Laravel\Mails\MessageMail;
use Illuminate\Support\Facades\Mail;

/**
 * MailSender:
 * @date 2022/8/17
 * @time 22:30
 * @author Ray.Zhang <codelint@foxmail.com>
 **/
class MailSender extends AbstractSender
{

    public function send($message, $option = [], $recipients = [])
    {
        $message = is_string($message) ? $message : $this->stringify($message);

        $mails = is_array($recipients) && count($recipients) ? $recipients : ($this->config['default_mails'] ?? []);
        $targets = [];
        foreach ($mails as $mail)
        {
            $mail = trim($mail);
            if (preg_match('/^\w[-\w.+]*@([A-Za-z0-9][-A-Za-z0-9]+\.)+[A-Za-z]{2,14}$/', $mails, $matches))
            {
                $targets[] = $mail;
            }
        }
        if (count($targets))
        {
            Mail::to($targets)->queue((new MessageMail($message, $option))
                ->subject($message)
                ->onConnection($this->config['job_connection']));
        }
    }
}