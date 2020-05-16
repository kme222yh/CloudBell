<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Container\LineEntrance;
use App\Container\Planer;
use App\Container\Calendarer;

class CalendarController extends Controller
{
    public function list(Request $request, LineEntrance $line, Calendarer $calendar, $date){
        $line->login();
        $calendar->setting($line->user->id);
        $calendar->list($date);
        if($calendar->result == false){
            return response(['error'=>'400', 'error_description'=>$calendar->error], 400);
        }
        return $calendar->calendar;
    }


    public function add(Request $request, LineEntrance $line, Planer $planer, Calendarer $calendar, $date, $plan_id){
        $line->login();
        $planer->setting($line->user->id);
        $planer->show($plan_id);
        if($planer->result == false){
            return response(['error'=>'400', 'error_description'=>$planer->error], 400);
        }
        $calendar->setting($line->user->id);
        $calendar->add($date, $plan_id);
        if($calendar->result == false){
            return response(['error'=>'400', 'error_description'=>$calendar->error], 400);
        }
        return $calendar->calendar;
    }


    public function remove(Request $request, LineEntrance $line, Calendarer $calendar, $date){
        $line->login();
        $calendar->setting($line->user->id);
        $calendar->remove($date);
        if($calendar->result == false){
            return response(['error'=>'400', 'error_description'=>$calendar->error], 400);
        }
        return $calendar->calendar;
    }
}
