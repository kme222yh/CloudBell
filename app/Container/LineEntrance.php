<?php
/*
まとめ

auth
Lineサーバーからユーザー情報を取ってきます
Lineサーバーからredirectされてないと失敗します

register
ユーザー情報を元にワシのデータベースに登録します
必ずauthを先に呼び出してください

login
データベースからユーザー情報を取り出します
cookieからユーザーidを読み出します
直前でauthが呼ばれていれば用意されたユーザー情報でデーてベースを更新します
cookieがなくauthも呼ばれてなければ失敗します

logout
ログアウト

withdraw
退会処理
logoutを内包しており、最後にユーザーデータを完全に削除します

push_messages
lineにプッシュメッセージを送信する
[
    ['to'=>'ユーザーid', 'text'=>'本文'],
    ...
]   これをわたせ
cron用

user
ユーザー情報のモデル
login,registerのいずれかが成功すると使用可能

*/




namespace App\Container;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cookie;
use Validator;
use App\User;
use App\Container\Container;

class LineEntrance extends Container
{
    private $oath;
    private $message;
    private $query = [];
    private $user_param = [];

    public $user = null;

    public function __construct(Request $request){
        $this->oath = new \stdClass;
        $this->oath->id = config('line.login.id');
        $this->oath->secret = config('line.login.secret');
        $this->oath->request_url = $request->url();
        $this->oath->host = config('line.host.api');
        $this->oath->code = $request->code;

        $this->message = new \stdClass;
        $this->message->id = config('line.messagin.id');
        $this->message->secret = config('line.messaging.secret');
        $this->message->access_token = config('line.messaging.token');

        $this->query = $request->query();
        $this->user_param['id'] = $request->cookie('id');

        $this->status_ok();
    }







    public function auth(){
        $this->status_bad();
        $validate_rule = [
            'code' => 'required',
            'state' => 'in:'.csrf_token(),
        ];
        $validator = Validator::make($this->query, $validate_rule);
        if($validator->fails()){
            $this->set_error(101);
            return;
        }
        $response = $this->issue_access_token();
        if($response === false){
            $this->set_error(102);
            return;
        }
        $this->user_param['access_token'] = $response['access_token'];
        $this->user_param['refresh_token'] = $response['refresh_token'];
        $this->user_param['last_logged_in'] = now();
        $this->user_param['expires_at'] = now()->addSecond($response['expires_in']);
        $response = $this->get_profile($this->user_param['access_token']);
        if($response === false){
            $this->set_error(103);
            return;
        }
        $this->user_param['id'] = $response['userId'];
        $this->user_param['name'] = $response['displayName'];
        $this->status_ok();
    }


    public function register(){
        if($this->is_status_bad()){
            return;
        }
        $this->status_bad();
        $this->user = new User;
        $this->user->fill($this->user_param);
        if(User::find($this->user->id)){
            $this->set_error(104);
            return;
        }
        $this->user->save();
        Cookie::queue('id', $this->user->id, time()+60*60*24*7);
        $this->status_ok();
    }


    public function login(){
        if($this->is_status_bad()){
            return;
        }
        $this->status_bad();
        if(!$this->user_param['id']){
            $this->set_error(105);
            return;
        }
        $this->user = User::find($this->user_param['id']);
        if(!$this->user){
            $this->set_error(106);
            return;
        }
        $expires_at = new Carbon($this->user->expires_at);
        if($expires_at->isPast() || $expires_at->isToday()){
            $response = $this->refresh_access_token();
            if($response === false){
                $this->user = null;
                $this->set_error(107);
                return;
            }
            $this->user_param['access_token'] = $response['access_token'];
            $this->user_param['refresh_token'] = $response['refresh_token'];
            $this->user_param['expires_at'] = now()->addSecond($response['expires_in']);
        }
        $this->user_param['last_logged_in'] = now();
        $this->user->fill($this->user_param);
        $this->user->save();
        Cookie::queue('id', $this->user->id, time()+60*60*24*7);
        $this->status_ok();
    }


    public function logout(){
        if($this->is_status_bad()){
            return;
        }
        Cookie::queue('id', null, time() - 3600);
        if(!is_null($this->user)){
            $this->revoke_access_token();
        }
    }


    public function withdraw(){
        if($this->is_status_bad() || is_null($this->user)){
            return;
        }
        $this->logout();
        $this->user->delete();
    }



    static public function push_messages($messages){
        $url = 'https://api.line.me/v2/bot/message/push';
        $headers = [
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . config('line.messagin.token'),
        ];
        $params = [
            'to' => '',
            'messages' => [
                [
                    'type' => 'text',
                    'text' => '',
                ],
            ],
        ];
        foreach($messages as $message){
            $params['to'] = $message['to'];
            $params['messages'][0]['text'] = $message['text'];
            $response = Http::withHeaders($headers)->post($url, $params);
        }
    }






////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////
    private function issue_access_token(){
        $url = $this->oath->host . '/oauth2/v2.1/token';
        $params = [
            'grant_type' => 'authorization_code',
            'code' => $this->oath->code,
            'redirect_uri' => $this->oath->request_url,
            'client_id' => $this->oath->id,
            'client_secret' => $this->oath->secret
        ];
        $response = Http::asForm()->post($url, $params);
        $validate_rule = [
            'access_token' => 'required',
            'refresh_token' => 'required',
            'expires_in' => 'required|integer',
            'scope' => 'required|in:profile',
            'token_type' => 'required|in:Bearer',
        ];
        $validator = Validator::make($response->json(), $validate_rule);
        if($validator->fails()){
            return false;
        }
        else{
            return $response;
        }
    }

    private function revoke_access_token(){
        $url = $this->oath->host . '/oauth2/v2.1/revoke';
        $params = [
            'access_token' => $this->user->access_token,
            'client_id' => $this->oath->id,
            'client_secret' => $this->oath->secret
        ];
        return Http::asForm()->post($url, $params);
    }

    private function refresh_access_token(){
        $url = $this->oath->host . '/oauth2/v2.1/token';
        $params = [
            'grant_type' => 'refresh_token',
            'refresh_token' => $this->user->refresh_token,
            'client_id' => $this->oath->id,
            'client_secret' => $this->oath->secret
        ];
        $response = Http::asForm()->post($url, $params);
        $validate_rule = [
            'access_token' => 'required',
            'token_type' => 'required|in:Bearer',
            'refresh_token' => 'required',
            'expires_in' => 'required',
            'scope' => 'required|in:profile',
        ];
        $validator = Validator::make($response->json(), $validate_rule);
        if($validator->fails()){
            return false;
        }
        else{
            return $response;
        }
    }

    private function get_profile($access_token){
        $url = $this->oath->host . '/v2/profile';
        $headers = [
            'Authorization' => 'Bearer ' . $access_token,
        ];
        $response = Http::withHeaders($headers)->get($url);
        $validate_rule = [
            'displayName' => 'required',
            'userId' => 'required',
        ];
        $validator = Validator::make($response->json(), $validate_rule);
        if($validator->fails()){
            return false;
        }
        else{
            return $response;
        }
    }
}
