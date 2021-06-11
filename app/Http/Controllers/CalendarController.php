<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Container\LineEntrance;
use App\Container\Planer;
use App\Container\Calendarer;

class CalendarController extends Controller
{
    public function list(Request $request, LineEntrance $line, Calendarer $calendar, $year, $month){
        if(!$line->user){
            return response($planer->get_error(), 400);
        }
        $calendar->setting($line->user->id);
        $calendar->list($year, $month);
        if($calendar->is_status_bad()){
            return response($calendar->get_error(), 400);
        }
        return $calendar->get_result();
    }


    public function add(Request $request, LineEntrance $line, Planer $planer, Calendarer $calendar, $date, $plan_id){
        if(!$line->user){
            return response($planer->get_error(), 400);
        }
        $planer->setting($line->user->id);
        $planer->show($plan_id);
        if($planer->is_status_bad()){
            return response($planer->get_error());
        }
        $calendar->setting($line->user->id);
        $calendar->add($date, $plan_id);
        if($calendar->is_status_bad()){
            return response($calendar->get_error(), 400);
        }
        return $calendar->get_result();
    }


    public function remove(Request $request, LineEntrance $line, Calendarer $calendar, $date){
        if(!$line->user){
            return response($planer->get_error(), 400);
        }
        $calendar->setting($line->user->id);
        $calendar->remove($date);
        if($calendar->is_status_bad()){
            return response($calendar->get_error(), 400);
        }
        return $calendar->get_result();
    }
}
