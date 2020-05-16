<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Container\LineEntrance;

class LoginController extends Controller
{
    public function register(Request $request, LineEntrance $line){
        $line->auth();
        $line->register();
        if($line->result == false){
             return view('page.error', ['message' => $line->error]);
        }
        return redirect('/')->cookie('id', $line->user->id);
    }


    public function login(Request $request, LineEntrance $line){
        $line->auth();
        $line->login();
        if($line->result == false){
            return view('page.error', ['message' => $line->error]);
        }
        return redirect('/')->cookie('id', $line->user->id);
    }


    public function logout(Request $request, LineEntrance $line){
        $line->login();
        $line->logout();
        return redirect('/');
    }


    public function withdraw(Request $request, LineEntrance $line){
        $line->login();
        $line->withdraw();
        return redirect('/');
    }
}



function aeaefa(){
        $now = Carbon::now();
        $today = $now->format('Y-m-d');
        $now = $now->format('H:i');

        $schedules = CalendarSchedule::where('date', $today)->get();

        $params = [];
        foreach($schedules as $schedule){
            $message = '';
            foreach($schedule->plan->body as $event){
                if($event[0] == $now){
                    $message = $event[1];
                    break;
                }
            }
            if($message != ''){
                $params[] = [
                    'user_id' => $schedule->user_id,
                    'message' => $message,
                ];
            }
        }

        $channel = new \stdClass;
        $channel->id = env('LINE_MESSAGING_CHANNEL_ID');
        $channel->secret = env('LINE_MESSAGING_CHANNEL_SECRET');
        $channel->access_token = env('LINE_MESSAGING_CHANNEL_ACCESS_TOKEN');

        $httpClient = new \LINE\LINEBot\HTTPClient\CurlHTTPClient($channel->access_token);
        $bot = new \LINE\LINEBot($httpClient, ['channelSecret' => $channel->secret]);

        foreach($params as $param){
            $textMessageBuilder = new \LINE\LINEBot\MessageBuilder\TextMessageBuilder($param['message']);
            $response = $bot->pushMessage($param['user_id'], $textMessageBuilder);
        }
    }
