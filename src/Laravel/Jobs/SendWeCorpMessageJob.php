<?php namespace Codelint\Ringo\Laravel\Jobs;

/**
 * SendWeCorpMessage:
 * @date 2021/11/26
 * @time 14:54
 * @author Ray.Zhang <codelint@foxmail.com>
 **/

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;

class SendWeCorpMessageJob implements ShouldQueue {
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $message;
    protected $detail;
    protected $meta;

    private $chat_id;
    private $corp_id;
    private $secret;

    /**
     * Create a new job instance.
     *
     * @param $message
     * @param array $info
     */
    public function __construct($message, $info = [])
    {
        $this->message = $message;
        $this->detail = $info;
        $this->chat_id = env('RINGO_WECORP_CHAT_ID', 'ringo');
        $this->corp_id = env('RINGO_WECORP_ID', '');
        $this->secret = env('RINGO_WECORP_SECRET', '');

        $meta = array(
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

        $this->meta = $meta;

        $this->onConnection(env('RINGO_JOB_CONNECTION'));
    }

    //{
    //    "name" : "NAME",
    //    "owner" : "userid1",
    //    "userlist" : ["userid1", "userid2", "userid3"],
    //    "chatid" : "CHATID"
    //}


    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $chat_ids = cache()->get('base.Service.Logger.wx.chat_ids', []);
        if (!Arr::has($chat_ids, $this->chat_id))
        {
            $user_ids = env('RINGO_WECORP_CHAT_UIDS');
            $user_ids = $user_ids ? explode(',', $user_ids) : [];
            if (count($user_ids) > 0 && $this->genChannel($this->chat_id, $user_ids))
            {
                $chat_ids[] = $this->chat_id;
                cache()->put('base.Service.Logger.wx.chat_ids', $chat_ids);
            }
            else
            {
                Log::info('Create channel[' . $this->chat_id . '] for wecorp users(' . env('RINGO_WECORP_CHAT_UIDS') . ') failed!!!');
                return;
            }
        }
        $this->wx($this->message, $this->detail);
    }

    public function setChatId($c_id)
    {
        $this->chat_id = $c_id;
        return $this;
    }

    public function genChannel($channel_name, $user_ids, $channel_id = null)
    {
        $channel_id = $channel_id ?: $channel_name;
        $owner_id = $user_ids[0];
        $token = $this->getToken();

        $res = $this->callOnce('https://qyapi.weixin.qq.com/cgi-bin/appchat/get?access_token=' . $token, array(
            'access_token' => $token,
            'chatid' => $channel_id
        ), 'get');

        if ($res && $res['errmsg'] == 'ok')
        {
            return true;
        }

        $res = $this->callOnce('https://qyapi.weixin.qq.com/cgi-bin/appchat/create?access_token=' . $token, array(
            'name' => $channel_name,
            'owner' => $owner_id,
            'userlist' => $user_ids,
            'chatid' => $channel_id,

        ), 'post');

//        $res = $this->callOnce('https://qyapi.weixin.qq.com/cgi-bin/appchat/get?access_token=' . $token, array(
//            'access_token' => $token,
//            'chat_id' => $channel_id
//        ), 'get');

        if (!($res && $res['errmsg'] == 'ok'))
        {
            Log::error(json_encode($res));
        }
        
        return $res && $res['errmsg'] == 'ok';
    }

    private function getToken()
    {
        $token_res = cache()->remember('base.Service.Logger.wx.token', 7200 - 500, function () {
            $corpId = $this->corp_id;
            $secret = $this->secret;
            return $this->callOnce('https://qyapi.weixin.qq.com/cgi-bin/gettoken', array(
                'corpid' => $corpId,
                'corpsecret' => $secret
            ), 'get');
        });

        return $token_res && isset($token_res['access_token']) ? $token_res['access_token'] : null;
    }

    public function wx($message, $info = [])
    {
        $token = $this->getToken();

        if ($token)
        {
            $url = 'https://qyapi.weixin.qq.com/cgi-bin/appchat/send?access_token=' . $token;
            $params = $this->getMsgData($message, $info);
            // print_r($params);
            $params['chatid'] = $this->chat_id;
            $params['safe'] = 0;

            $res = $this->callOnce($url, $params, 'post');
            // {"errcode":0,"errmsg":"ok"}
            return $res && $res['errmsg'] == 'ok';
        }

        return false;
    }

    private function getMsgData($message, $info = [])
    {
        $url = Arr::get($info, 'url', Arr::get($info, 'link', null));

        $image = Arr::get($info, 'image', null);

        $summary = Arr::get($info, '_summary', 'from ' . gethostname());

        if(!$url && count($info) > ($summary ? 2 : 1) && 'on' == strtolower(env('RINGO_MSG_DETAIL', 'on')))
        {
            $url_data = array(
                'message' => $message,
                'detail' => $info,
                'meta' => $this->meta
            );
            $key = 'wx-msg-' . md5(json_encode($url_data));
            cache()->put($key , json_encode($url_data), 86400*21);
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

    private function callOnce($url, $args = null, $method = "post", $headers = array(), $withCookie = false, $timeout = 10)
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