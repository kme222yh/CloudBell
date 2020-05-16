<?php

namespace App\Http\Composers;

use Illuminate\View\View;

class HomeComposer
{
    public function compose(View $view){
        $base_url = env('LINE_LOGIN_ACCESS_HOST') . "/oauth2/v2.1/authorize?";
        $param = [
            'response_type' => 'code',
            'client_id' => env('LINE_LOGIN_CHANNEL_ID'),
            'redirect_uri' => '',
            'state' => csrf_token(),
            'scope' => 'profile',
        ];

        $param['redirect_uri'] = env('APP_URL') . '/register';
        $url = $base_url . http_build_query($param);
        $view->with('register_url', $url);

        $param['redirect_uri'] = env('APP_URL') . '/login';
        $url = $base_url . http_build_query($param);
        $view->with('login_url', $url);
    }
}
