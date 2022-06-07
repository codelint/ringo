<?php namespace Codelint\Ringo\Laravel\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Arr;

/**
 * WeCorpJob:
 * @date 2022/6/7
 * @time 01:10
 * @author Ray.Zhang <codelint@foxmail.com>
 **/
abstract class WeCorpJob implements ShouldQueue {
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $corp_id;
    protected $agent_id;
    protected $secret;

    public function __construct()
    {
        $this->corp_id = env('RINGO_WECORP_ID', '');
        $this->secret = env('RINGO_WECORP_SECRET', '');
        $this->agent_id = env('RINGO_WECORP_AGENT_ID', '');

        $this->onConnection(env('RINGO_JOB_CONNECTION'));
    }

    protected function getMetaData()
    {
        return array(
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
            'real_proto' => Arr::get($_SERVER, 'HTTP_X_FORWARDED_PROTO', '-')
        );
    }

    protected function getToken()
    {
        $ttl = 7200 - 500;
        $md5 = md5($this->corp_id . $this->secret . $ttl);
        $token_res = cache()->remember('base.Service.Logger.wx.token.' . $md5, $ttl, function () {
            $corpId = $this->corp_id;
            $secret = $this->secret;
            return $this->callOnce('https://qyapi.weixin.qq.com/cgi-bin/gettoken', array(
                'corpid' => $corpId,
                'corpsecret' => $secret
            ), 'get');
        });

        return $token_res && isset($token_res['access_token']) ? $token_res['access_token'] : null;
    }

    protected function getMsgData($message, $info = [])
    {
        $url = Arr::get($info, 'url', Arr::get($info, 'link', null));

        $image = Arr::get($info, 'image', null);

        $summary = Arr::get($info, '_summary', 'from ' . gethostname());

        if (!$url && count($info) > ($summary ? 2 : 1) && 'on' == strtolower(env('RINGO_MSG_DETAIL', 'on')))
        {
            $url_data = array(
                'message' => $message,
                'detail' => $info,
                'meta' => $this->meta
            );
            $key = 'wx-msg-' . md5(json_encode($url_data));
            cache()->put($key, json_encode($url_data), 86400 * 21);
            $url = url('/ringo/message/view/' . $key);
        }

        if ($url && $image)
        {
            $data = array(
                'msgtype' => 'news',
                'news' => array(
                    'articles' => [
                        array(
                            'title' => $message,
                            'description' => 'from ' . gethostname(),
                            'url' => $url,
                            'picurl' => $image,
                        )
                    ]
                ),
            );
        }
        elseif ($url)
        {
            $data = array(
                'msgtype' => 'textcard',
                'textcard' => array(
                    'title' => $message,
                    'description' => $summary,
                    'url' => $url,
                    'btntxt' => '更多',
                ),
            );
        }
        else
        {
            $data = array(
                'msgtype' => 'text',
                'text' => array('content' => $message),
            );
        }

        return $data;
    }


    protected function callOnce($url, $args = null, $method = "post", $headers = array(), $withCookie = false, $timeout = 10)
    {
        $ch = curl_init();
        if ($method == "post")
        {
            curl_setopt($ch, CURLOPT_POSTFIELDS, is_string($args) ? $args : json_encode($args));
            curl_setopt($ch, CURLOPT_POST, 1);
        }
        else
        {
            $data = $args ? http_build_query($args) : null;
            if ($data)
            {
                if (stripos($url, "?") > 0)
                {
                    $url .= "&$data";
                }
                else
                {
                    $url .= "?$data";
                }
            }
        }
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        if (!empty($headers))
        {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        }
        if ($withCookie)
        {
            curl_setopt($ch, CURLOPT_COOKIEJAR, $_COOKIE);
        }
        $r = curl_exec($ch);
        curl_close($ch);
        return @json_decode($r, true);
    }
}