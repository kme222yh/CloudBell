<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Cookie;
use Illuminate\Http\Request;
use App\Container\LineEntrance;
use Validator;

use App\User;

class LoginController extends Controller
{
    public function register(Request $request, LineEntrance $line){
        $user_param = $this->line_auth($request, $line);
        if(gettype($user_param) != 'array'){
            return $user_param;
        }

        $user = User::find($user_param['id']);
        if($user){
            return view('page.error', ['message' => '登録済みです。ログインしろ下さい。']);
        }
        else{
            $user = new User;
        }
        $user->fill($user_param)->save();

        return redirect('/')->cookie('id', $user_param['id']);
    }




    public function login(Request $request, LineEntrance $line){
        $user_param = $this->line_auth($request, $line);
        if(gettype($user_param) != 'array'){
            return $user_param;
        }


        $user = User::find($user_param['id']);
        if(!$user){
            return view('page.error', ['message' => '登録していません。登録しろ下さい']);
        }
        $user->fill($user_param)->save();

        return redirect('/')->cookie('id', $user_param['id']);
    }



    public function logout(Request $request, LineEntrance $line){
        if($line->user){
            Cookie::queue('id', null, time() - 3600);
            $line->revoke_access_token();
        }
        return redirect('/');
    }


    public function withdraw(Request $request, LineEntrance $line){
        if($line->user){
            Cookie::queue('id', null, time() - 3600);
            $line->revoke_access_token();
            $line->user->delete();
        }
        return redirect('/');
    }







    private function line_auth(Request $request, LineEntrance $line){
        $validate_rule = [
            'code' => 'required',
            'state' => 'required|in:'.csrf_token(),
        ];
        $validator = Validator::make($request->query(), $validate_rule);
        if($validator->fails()){
            return response('csrf error', 400);
        }

        $response = $line->issue_access_token();
        if($response === false){
            return response('issue error', 400);
        }

        $user_param = $response->json();
        unset($user_param['id_token']);
        unset($user_param['scope']);
        unset($user_param['token_type']);
        $user_param['expires_at'] = now()->addSecond($user_param['expires_in']);
        unset($user_param['expires_in']);

        $response = $line->get_profile($user_param['access_token']);
        if($response === false){
            return response('profile error', 400);
        }

        $user_param['id'] = $response['userId'];
        $user_param['name'] = $response['displayName'];

        return $user_param;
    }
}
