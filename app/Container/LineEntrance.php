<?php
namespace App\Container;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Validator;
use App\User;

class LineEntrance
{

    private $oath;
    public $user;

    public function __construct(Request $request){
        $this->oath = new \stdClass;
        $this->oath->id = env('LINE_LOGIN_CHANNEL_ID');
        $this->oath->secret = env('LINE_LOGIN_CHANNEL_SECRET');
        $this->oath->request_url = $request->url();
        $this->oath->host = env('LINE_LOGIN_API_HOST');
        $this->oath->code = $request->code;

        $id = $request->cookie('id');
        if($id){
            $this->user = User::find($id);
            if($this->user){
                $expires_at = new Carbon($this->user->expires_at);
                if($expires_at->isPast()){
                    $response = $this->refresh_access_token();
                    if($response === false){
                        $this->user = null;
                    }
                    else{
                        $this->user->access_token = $response['access_token'];
                        $this->user->refresh_token = $response['refresh_token'];
                        $this->user->expires_at = now()->addSecond($response['expires_in']);
                        $this->user->save();
                    }
                }
            }
        }
    }



    public function issue_access_token(){
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


    public function revoke_access_token(){
        $url = $this->oath->host . '/oauth2/v2.1/revoke';
        $params = [
            'access_token' => $this->user->access_token,
            'client_id' => $this->oath->id,
            'client_secret' => $this->oath->secret
        ];
        return Http::asForm()->post($url, $params);
    }


    public function refresh_access_token(){
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


    public function get_profile($access_token){
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
