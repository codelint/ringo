<?php namespace Codelint\Ringo\Laravel\Jobs;

use Illuminate\Support\Arr;

/**
 * SendCorpChatMessageJob:
 * @date 2022/6/7
 * @time 01:16
 * @author Ray.Zhang <codelint@foxmail.com>
 * @reference https://qyapi.weixin.qq.com/cgi-bin/message/send?access_token
 **/
class SendCorpChatMessageJob extends WeCorpJob {

    protected string $message;
    protected array $detail;
    protected array $meta;

    protected string $uid;

    public function __construct($uid, $message, $info = [])
    {
        parent::__construct();
        $this->uid = $uid;
        $this->message = $message;
        $this->detail = $info;
        $this->meta = $this->getMetaData();

    }

    public function handle()
    {

        $token = $this->getToken();

        if ($token && $this->agent_id)
        {
            $url = 'https://qyapi.weixin.qq.com/cgi-bin/message/send?access_token=' . $token;

            $params = $this->getMsgData($this->message, $this->detail);
            // print_r($params);
            $params['touser'] = $this->uid;
            $params['safe'] = 0;
            $params['enable_duplicate_check'] = 1;
            $params['duplicate_check_interval'] = 60;
            $params['agentid'] = $this->agent_id;

            $this->callOnce($url, $params, 'post');
        }

    }


}