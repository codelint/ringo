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

class SendWeCorpMessageJob extends WeCorpJob {

    protected $message;
    protected $detail;
    protected $meta;

    private $chat_id;
    private $chat_name;


    /**
     * Create a new job instance.
     *
     * @param $message
     * @param array $info
     */
    public function __construct($message, array $info = [])
    {
        parent::__construct();

        $this->message = $message;
        $this->detail = $info;
        $this->chat_name = env('RINGO_WECORP_CHAT_ID', 'ringo');
        $this->chat_id = $this->channelName2Id($this->chat_name);
        $this->meta = $this->getMetaData();
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $chat_ids = cache()->get('ringo.wx.corp.chat_ids', []);
        if (!Arr::has($chat_ids, $this->chat_id))
        {
            $user_ids = env('RINGO_WECORP_CHAT_UIDS');
            $user_ids = $user_ids ? explode(',', $user_ids) : [];
            if (count($user_ids) > 0 && $this->genChannel($this->chat_name, $user_ids, $this->chat_id))
            {
                $chat_ids[] = $this->chat_id;
                cache()->put('ringo.wx.corp.chat_ids', $chat_ids);
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
        $this->chat_id = $this->channelName2Id($c_id);
        $this->chat_name = $c_id;
        return $this;
    }

    private function channelName2Id($channel_name): string
    {
        return $channel_name . ($this->agent_id ? "{$this->agent_id}" : '');
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
            if ($res && $res['errmsg'] == 'ok')
            {
                return true;
            }
            else
            {
                Log::info(json_encode($res));
                return false;
            }
        }

        return false;
    }

}