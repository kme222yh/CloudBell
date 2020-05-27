<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

use Illuminate\Support\Carbon;
use App\Container\LineEntrance;
use App\Container\Planer;
use App\Container\Calendarer;

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
        $now = Carbon::now()->format('H:i');
        $todays = Calendarer::todays();
        $params = [];
        foreach($todays as $today){
            $message = '';
            foreach(Planer::id_to_body($today->plan_id) as $event)
                if($event[0] == $now){
                    $message = $event[1];
                    break;
                }
            if($message != '')
                $params[] = ['to' => $today->user_id, 'text' => $message];
        }
        LineEntrance::push_messages($params);
        Calendarer::clean();
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
