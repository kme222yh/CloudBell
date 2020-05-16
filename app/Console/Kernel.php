<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

use Illuminate\Support\Carbon;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $line = resolve('App\Container\LineEntrance');
        $planer = resolve('App\Container\Planer');
        $calendar = resolve('App\Container\Calendarer');
        $now = Carbon::now()->format('H:i');
        $todays = $calendar->todays();
        $params = [];
        foreach($todays as $today){
            $message = '';
            foreach($planer->id_to_body($today->plan_id) as $event){
                if($event[0] == $now){
                    $message = $event[1];
                    break;
                }
            }
            if($message != ''){
                $params[] = [
                    'user_id' => $today->user_id,
                    'message' => $message,
                ];
            }
        }
        foreach($params as $param){
            $line->push_message($param['message'], $param['user_id']);
        }

        if(today()->day == 1 || $now=='00:00'){
            $calendar->clear();
        }
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
