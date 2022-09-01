<?php

return [

    'job_connection' => env('RINGO_JOB_CONNECTION', 'sync'),

    'common' => [
        'logger'
    ],

    'mail' => [
        'mail'
    ],

    'app' => [
        'wx-corp'
    ],

    'senders' => [
        'wx-corp' => [
            'driver' => \Codelint\Ringo\Laravel\Sender\WeCorpSender::class,
            'corp_id' => env('RINGO_WECORP_ID'),
            'agent_id' => env('RINGO_AGENT_ID'),
            'secret' => env('RINGO_WECORP_SECRET'),
            'default_cid' => env('RINGO_WECORP_CHAT_ID'),
            'default_uids' => explode(',', env('RINGO_WECORP_CHAT_UIDS'))
        ],
        'mail' => [
            'driver' => \Codelint\Ringo\Laravel\Sender\MailSender::class,
            'default_mails' => []
        ],
        'logger' => [
            'driver' => \Codelint\Ringo\Laravel\Sender\LoggerSender::class
        ]
    ]


];
