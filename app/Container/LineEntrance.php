<?php
/*
まとめ

auth
Lineサーバーからユーザー情報を取ってきます
Lineサーバーからredirectされてないと失敗します

register
ユーザー情報を元にワシのデータベースに登録します
authを先に呼び出してないと失敗します

login
データベースからユーザー情報を取り出します
cookieからユーザーidを読み出します
直前でauthが呼ばれていれば用意されたユーザー情報でデーてベースを更新します
cookieがなくauthも呼ばれてなければ失敗します

logout
ログアウト
実は直前でloginが呼ばれてないと例外が発生するかもね

withdraw
退会処理
logoutを内包しており、最後にユーザーデータを完全に削除します

user
ユーザー情報のモデル
login,registerのいずれかが成功すると使用可能

error
エラーが起きた時にここにメッセージが入る

result
エラーが起きちゃってた時はfalseが入ってる

*/








namespace App\Container;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cookie;
use Validator;
use App\User;

class LineEntrance
{
    private $oath;
    private $message;
    private $query = [];
    private $user_param = [];

    public $user = null;
    public $error = null;
    public $result = true;

    public function __construct(Request $request){
        $this->oath = new \stdClass;
        $this->oath->id = env('LINE_LOGIN_CHANNEL_ID');
        $this->oath->secret = env('LINE_LOGIN_CHANNEL_SECRET');
        $this->oath->request_url = $request->url();
        $this->oath->host = env('LINE_LOGIN_API_HOST');
        $this->oath->code = $request->code;

        $this->message = new \stdClass;
        $this->message->id = env('LINE_MESSAGING_CHANNEL_ID');
        $this->message->secret = env('LINE_MESSAGIN_CHANNEL_SECRET');
        $this->message->access_token = env('LINE_MESSAGING_CHANNEL_ACCESS_TOKEN');

        $this->query = $request->query();
        $this->user_param['id'] = $request->cookie('id');
    }

    public function auth(){
        $this->result = false;
        $validate_rule = [
            'code' => 'required',
            'state' => 'in:'.csrf_token(),
        ];
        $validator = Validator::make($this->query, $validate_rule);
        if($validator->fails()){
            $this->error = 'failed to validation in auth';
            return;
        }
        $response = $this->issue_access_token();
        if($response === false){
            $this->error = 'failed to issue_token in auth';
            return;
        }
        $this->user_param['access_token'] = $response['access_token'];
        $this->user_param['refresh_token'] = $response['refresh_token'];
        $this->user_param['last_logged_in'] = now();
        $this->user_param['expires_at'] = now()->addSecond($response['expires_in']);
        $response = $this->get_profile($this->user_param['access_token']);
        if($response === false){
            $this->error = 'failed to get_profile in auth';
            return;
        }
        $this->user_param['id'] = $response['userId'];
        $this->user_param['name'] = $response['displayName'];
        $this->result = true;
    }


    public function register(){
        if($this->result == false){
            return;
        }
        $this->result = false;
        $this->user = new User;
        $this->user->fill($this->user_param);
        if(User::find($this->user->id)){
            $this->error = 'it was duplicated in register';
            return;
        }
        $this->user->save();
        $this->result = true;
    }


    public function login(){
        if($this->result == false){
            return;
        }
        $this->result = false;
        if(!$this->user_param['id']){
            $this->error = 'did not find user-information';
            return;
        }
        $this->user = User::find($this->user_param['id']);
        if(!$this->user){
            $this->error = 'you maybe did not rester';
            return;
        }
        $expires_at = new Carbon($this->user->expires_at);
        if($expires_at->isPast() || $expires_at->isToday()){
            $response = $this->refresh_access_token();
            if($response === false){
                $this->user = null;
                $this->error = 'failed to update token';
                return;
            }
            $this->user_param['access_token'] = $response['access_token'];
            $this->user_param['refresh_token'] = $response['refresh_token'];
            $this->user_param['expires_at'] = now()->addSecond($response['expires_in']);
        }
        $this->user_param['last_logged_in'] = now();
        $this->user->fill($this->user_param);
        $this->user->save();
        $this->result = true;
    }


    public function logout(){
        if($this->result == false){
            return;
        }
        Cookie::queue('id', null, time() - 3600);
        $this->revoke_access_token();
    }


    public function withdraw(){
        if($this->result == false || !$this->user){
            return;
        }
        $this->logout();
        $this->user->delete();
    }



    public function push_message($message, $user_id){
        $url = 'https://api.line.me/v2/bot/message/push';
        $headers = [
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $this->message->access_token,
        ];
        $params = [
            'to' => $user_id,
            'messages' => [
                [
                    'type' => 'text',
                    'text' => $message,
                ],
            ],
        ];
        $response = Http::withHeaders($headers)->post($url, $params);
        return $response;
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
