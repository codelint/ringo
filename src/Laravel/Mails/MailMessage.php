<?php namespace Codelint\Ringo\Laravel\Mails;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Arr;

/**
 * MessageMail:
 * @date 2021/11/26
 * @time 15:15
 * @author Ray.Zhang <codelint@foxmail.com>
 **/
class MessageMail extends Mailable {
    use Queueable, SerializesModels;

    protected $msg;
    protected $detail;
    protected $create_time;
    protected $env;

    /**
     * Create a new message instance.
     *
     * @param string $message
     * @param string|mixed $detail
     * @param array $env
     */
    public function __construct($message = '', $detail = '', $env = null)
    {
        $this->onConnection('logger');
        $this->msg = strval($message);
        $this->detail = $detail;
        $this->create_time = time();
        $this->env = $env ?: [
            'host' => gethostname(),
            'os' => php_uname(),
            'server_ip' => Arr::get($_SERVER, 'SERVER_ADDR', '-'),
            'http_referer' => Arr::get($_SERVER, 'HTTP_REFERER', '-'),
            'user_agent' => Arr::get($_SERVER, 'HTTP_USER_AGENT', '-'),
            'remote' => Arr::get($_SERVER, 'REMOTE_ADDR', '-'),
            'request_uri' => Arr::get($_SERVER, 'REQUEST_URI', '-'),
            'http_host' => Arr::get($_SERVER, 'HTTP_HOST', '-'),
            'http_accept_language' => Arr::get($_SERVER, 'HTTP_ACCEPT_LANGUAGE', '-'),
            'real_ip' => Arr::get($_SERVER, 'HTTP_X_FORWARDED_FOR', '-'),
            'real_proto' => Arr::get($_SERVER, 'HTTP_X_FORWARDED_PROTO', '-'),
//            'SVR_KEYS' => implode(',', array_keys($_SERVER))
        ];
        $this->subject($this->msg);
    }

    /**
     * Build the message.
     *
     * @return MessageMail
     */
    public function build()
    {
//        $detail_text = json_encode($this->detail, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
//        $detail_text = str_replace(' ', '&nbsp;', $detail_text);
//        $detail_text = str_replace("\n", '<br>', $detail_text);

        if (is_array($this->detail) && isset($this->detail[0]) && is_array($this->detail[0]))
        {
            $file = storage_path('/' . uniqid() . '.csv');
            $fields = array_keys($this->detail[0]);
            $content = collect($fields)->map(function ($f) {
                    return "\"$f\"";
                })->implode(',') . "\n";
            foreach ($this->detail as $item)
            {
                $content .= collect($fields)->map(function ($f) use ($item) {
                        return '"' . Arr::get($item, $f, '') . '"';
                    })->implode(',') . "\n";
            }
            file_put_contents($file, $content);
            $this->attach($file);
        }

        if (is_array($this->detail))
        {
            foreach ($this->detail as $k => $v)
            {
                $this->detail[$k] = is_int($k) || is_string($v) ? $v : json_encode($v, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            }
        }

        $meta = $this->env && count($this->env) ? $this->env : [];
        $meta['queue'] = $this->connection;
        $meta['request_at'] = function () {
            return date('Y-m-d H:i:s', $this->create_time) . '(' . date_default_timezone_get() . ')';
        };
        $meta['execute_at'] = function () {
            return date('Y-m-d H:i:s') . '(' . date_default_timezone_get() . ')';
        };
        $meta['elapsed_time'] = function () {
            return (time() - $this->create_time) . '(s)';
        };

        return $this->view('ringo::mail.message')
            ->with('mail_message', strval($this->msg))
            ->with('detail', $this->detail)
            ->with('meta', $meta);
    }
}

