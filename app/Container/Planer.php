<?php

namespace App\Container;

use Illuminate\Support\Facades\DB;
use Validator;
use App\Plan;

class Planer
{
    private $config = [];
    private $user_id = null;


    public $plan = null;
    public $plans = null;
    public $result = false;
    public $error = null;



    public function __construct(){
        $this->config['max_plans'] = env('PLAN_MAX_NUMBER');
        $this->config['max_once'] = env('PLAN_ONCE_LISTS');
        $this->config['list_column'] = explode(',', env('PLAN_LIST_COLUMN'));
        $this->config['plan_interval'] = env('PLAN_MINIMUM_EVENT_INTERVAL');
    }


    public function setting($user_id){
        if(!$user_id){
            $this->error = 'user_id error';
            return;
        }
        $this->user_id = $user_id;
        $this->result = true;
    }


    public function list($page = 1){
        if($this->result == false){
            return;
        }
        $this->result = false;
        if($page < 1){
            $this->error = 'plans pagenation error';
            return;
        }
        $this->plans = DB::table('plans')->select($this->config['list_column'])->where('user_id', $this->user_id)->offset($this->config['max_once']*($page-1))->limit($this->config['max_once'])->get();
        if(!$this->plans){
            $this->error = 'did not find plans';
            return;
        }
        $this->result = true;
    }


    public function show($id){
        if($this->result == false){
            return;
        }
        $this->result = false;
        $this->plan = Plan::find($id);
        if(!$this->plan || $this->plan->user_id != $this->user_id){
            $this->plan = null;
            $this->error = 'did not find plan';
            return;
        }
        $this->result = true;
    }


    public function update($n, $param){
        if($this->result == false){
            return;
        }
        $this->result = false;
        $this->plan = Plan::find($n);
        if(!$this->plan || $this->plan->user_id != $this->user_id || $this->param_valid_fails($param)){
            $this->plan = null;
            $this->error = 'something is wrong';
            return;
        }
        else{
            $param['user_id'] = $this->user_id;
            $this->plan->fill($param);
            $this->plan->save();
        }
        $this->result = true;
    }


    public function create($param){
        if($this->result == false){
            return;
        }
        $this->result = false;
        if(DB::table('plans')->count() >= $this->config['max_plans']){
            $this->error = 'plans are too much';
            return;
        }
        $this->plan = new Plan;
        if($this->param_valid_fails($param)){
            $this->error = 'failed to create plan';
            return;
        }
        $param['user_id'] = $this->user_id;
        $this->plan->fill($param);
        $this->plan->save();
        $this->result = true;
    }


    public function destroy($id){
        if($this->result == false){
            return;
        }
        $this->result = false;
        $this->plan = Plan::find($id);
        if(!$this->plan || $this->plan->user_id != $this->user_id){
            $this->plan = null;
            $this->error = 'failed to delete plan';
            return;
        }
        $this->plan->delete();
        $this->result = true;
    }



    public function id_to_body($id){
        return Plan::find($id)->body;
    }







    private function param_valid_fails($param){
        $validate_rule = [
            'name' => 'required',
            'body' => 'required',
        ];
        $validator = Validator::make($param, $validate_rule);
        if($validator->fails()){
            return true;
        }
        foreach($param['body'] as $event){
            if(count($event) != 2){
                return true;
            }
            $min = intval(substr($event[0],0,2))*60 + intval(substr($event[0],-2));
            if($min % $this->config['plan_interval']){
                return true;
            }
        }
        return false;
    }
}
