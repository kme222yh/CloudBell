<?php
namespace App\Container;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;

use Illuminate\Support\Carbon;
use App\CalendarSchedule;
use Database\Seeders\DemoUserSeeder;

class LineMessage
{
    public function __invoke(){
        $now = Carbon::now();
        $today = $now->format('Y-m-d');
        $now = $now->format('H:i');

        $schedules = CalendarSchedule::where('date', $today)->get();

        $params = [];
        foreach($schedules as $schedule){
            if($schedule->user_id == DemoUserSeeder::$GUEST)  continue;
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
}
