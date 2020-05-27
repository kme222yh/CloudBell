<?php

namespace App\Http\Composers;

use Illuminate\View\View;

class HomeComposer
{
    public function compose(View $view){
        $base_url = config('line.host.login') . "/oauth2/v2.1/authorize?";
        $param = [
            'response_type' => 'code',
            'client_id' => config('line.login.id'),
            'redirect_uri' => '',
            'state' => csrf_token(),
            'scope' => 'profile',
        ];

        $param['redirect_uri'] = config('app.url') . '/register';
        $url = $base_url . http_build_query($param);
        $view->with('register_url', $url);

        $param['redirect_uri'] = config('app.url') . '/login';
        $url = $base_url . http_build_query($param);
        $view->with('login_url', $url);
    }
}
