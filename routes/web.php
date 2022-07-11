<?php

Route::get('/ringo/message/view/{view_id}', function($view_id){
    // $data = json_decode(cache($view_id, '{}'), true);
    $data = \Codelint\Ringo\Laravel\Facade\Cache::get($view_id, []);
    $detail =\Illuminate\Support\Arr::get($data, 'detail', $data);
    $extra = array();
    foreach($detail as $k => $v){
        if(is_array($v))
        {
            $extra[$k] = $v;
        }
    }

    return view($data['view'] ?? 'ringo::sample.info')
        ->with('message', \Illuminate\Support\Arr::get($data, 'message', $view_id))
        ->with('detail', $detail)
        ->with('extra', $extra)
        ->with('meta', \Illuminate\Support\Arr::get($data, 'meta', []));
});