<?php

/*まとめ

list
受け取った年と月に該当するイベントたちの配列を返す
yearとmonthが数字なのかは事前にチェックしてね
eventが見つからなくても何も言わずに空の配列を返す

add
eventを追加する。日付とidを受け取る

remove
eventを取り消す

*/





namespace App\Container;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use Validator;
use App\Container\Container;

class Calendarer extends Container
{

    public function list($year, $month){
        if($this->is_status_bad()){
            return;
        }
        $this->status_bad();
        if(12<$month || !$this->is_date_between_lange($year.'-'.$month.'-1')){
            $this->set_error(302);
            return;
        }
        $date = new Carbon($year.'-'.$month.'-1');
        $start = $date->firstOfMonth()->format('Y-m-d');
        $end = $date->lastOfMonth()->format('Y-m-d');
        $this->result = DB::table('calendar')->select(config('calendar.list_column'))->where('user_id', $this->user_id)->where('date', '>=', $start)->where('date', '<=', $end)->get();
        $this->status_ok();
    }


    public function add($date, $plan_id){
        if($this->is_status_bad()){
            return;
        }
        $this->status_bad();
        if(!$this->is_date_between_lange($date)){
            $this->set_error(302);
            return;
        }
        if(DB::table('calendar')->where('user_id', $this->user_id)->where('date', $date)->exists()){
            $this->set_error(303);
            return;
        }
        DB::table('calendar')->insert(['plan_id'=>$plan_id, 'date'=>$date, 'user_id'=>$this->user_id]);
        $this->status_ok();
    }


    public function remove($date){
        if($this->is_status_bad()){
            return;
        }
        $this->status_bad();
        if(DB::table('calendar')->where('user_id', $this->user_id)->where('date', $date)->doesntExist()){
            $this->set_error(304);
            return;
        }
        DB::table('calendar')->where('user_id', $this->user_id)->where('date', $date)->delete();
        $this->status_ok();
    }




    static public function todays(){
        return DB::table('calendar')->select(['user_id', 'plan_id'])->where('date', today())->get();
    }

    static public function clean(){
        if(now()->format('dHi')!='10000')
            return;
        $pre = new Carbon;
        $pre->addMonth(-env('CALENDAR_PREVIOUS'));
        DB::table('calendar')->where('date', '<', $pre)->delete();
    }






////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////
    private function is_date_between_lange($date){
        $pre = new Carbon;
        $pre->addMonth(-config('calendar.lange.previous'))->firstOfMonth();
        $suf = new Carbon;
        $suf->addMonth(config('calendar.lange.feature'))->lastOfMonth();
        return Carbon::parse($date)->between($pre, $suf);
    }
}
