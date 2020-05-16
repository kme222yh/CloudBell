<?php

namespace App\Container;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use Validator;

class Calendarer
{
    private $config = [];
    private $user_id = null;


    public $calendar = null;
    public $result = false;
    public $error = null;




    public function __construct(){
        $this->config['previous'] = env('CALENDAR_PREVIOUS');
        $this->config['feature'] = env('CALENDAR_FEATURE');
        $this->config['list_column'] = explode(',', env('CALENDAR_LIST_COLUMN'));
    }



    public function setting($user_id){
        if(!$user_id){
            $this->error = 'user_id error';
            return;
        }
        $this->user_id = $user_id;
        $this->result = true;
    }



    public function list($date){
        if($this->result == false){
            return;
        }
        $this->result = false;
        $date = new Carbon($date);
        $start = $date->firstOfMonth()->format('Y-m-d');
        $end = $date->lastOfMonth()->format('Y-m-d');
        $this->calendar = DB::table('calendar')->select($this->config['list_column'])->where('user_id', $this->user_id)->where('date', '>=', $start)->where('date', '<=', $end)->get();
        if(!$this->calendar){
            $this->error = 'did not find calendar';
            return;
        }
        $this->result = true;
    }


    public function add($date, $plan_id){
        if($this->result == false){
            return;
        }
        $this->result = false;
        $pre = new Carbon;
        $pre->addMonth(-$this->config['previous']);
        $suf = new Carbon;
        $suf->addMonth($this->config['feature']);
        if(!Carbon::parse($date)->between($pre, $suf)){
            $this->error = 'this date is out of calendar';
            return;
        }
        if(DB::table('calendar')->select(['date'])->where('user_id', $this->user_id)->where('date', $date)->first()){
            $this->error = 'event was duplicated in calendar';
            return;
        }
        DB::table('calendar')->insert(['plan_id'=>$plan_id, 'date'=>$date, 'user_id'=>$this->user_id]);
        $this->result = true;
    }


    public function remove($date){
        if($this->result == false){
            return;
        }
        $this->result = false;
        if(!DB::table('calendar')->select(['date'])->where('user_id', $this->user_id)->where('date', $date)->first()){
            $this->error = 'failed to remove event from plan';
            return;
        }
        DB::table('calendar')->where('user_id', $this->user_id)->where('date', $date)->delete();
        $this->result = true;
    }


    public function todays(){
        return $this->calendar = DB::table('calendar')->select(['user_id', 'plan_id'])->where('date', today())->get();
    }


    public function crean(){
        $pre = new Carbon;
        $pre->addMonth(-$this->config['previous']);
        DB::table('calendar')->where('date', '<', $pre)->delete();
    }
}
