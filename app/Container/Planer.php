<?php

/*まとめ

list
プランのぺ時ネーションをする。整数（ページ）を一つわたせ。渡さなかったら1ページ目として扱われる
まだ先があるかどうかは特に教えてくれない

show
idから探して持ち主がユーザーと一致すればあったと、しなければなかったと答える

update
id , paramを受け取る
プランの捜索に関してはshowと同じ
bodyの時間の最小感覚をチェックする

create
既存のプランの数を数える。はみ出してたら失敗する
validationはupdateと同じ

destroy
プランの削除
プランの搜索に関してはshowと同じ


id_to_body
idを受け取り対応するプランのbodyを返す
各種チェックは一切しない
cron用

*/









namespace App\Container;

use Illuminate\Support\Facades\DB;
use Validator;
use App\Plan;
use App\Container\Container;

class Planer extends Container
{

    public function list($page = 1){
        if($this->is_status_bad()){
            return;
        }
        $this->status_bad();
        if($page < 1){
            $this->set_error(201);
            return;
        }
        $this->result = DB::table('plans')->select(config('plan.list_column'))->where('user_id', $this->user_id)->offset(config('plan.max_number')*($page-1))->limit(config('plan.per_page'))->get();
        if(count($this->result) == 0){
            $this->set_error(202);
            return;
        }
        $this->status_ok();
    }


    public function show($id){
        if($this->is_status_bad()){
            return;
        }
        $this->status_bad();
        $this->result = Plan::find($id);
        if(is_null($this->result) || $this->result->user_id != $this->user_id){
            $this->set_error(202);
            return;
        }
        unset($this->result['user_id']);
        $this->status_ok();
    }


    public function update($n, $param){
        if($this->is_status_bad()){
            return;
        }
        $this->status_bad();
        $plan = Plan::find($n);
        if(is_null($plan) || $plan->user_id != $this->user_id){
            $this->set_error(202);
            return;
        }
        if($this->param_valid_fails($param)){
            $this->set_error(204);
            return;
        }
        else{
            $param['user_id'] = $this->user_id;
            $plan->fill($param);
            $plan->save();
        }
        $this->status_ok();
    }


    public function create($param){
        if($this->is_status_bad()){
            return;
        }
        $this->status_bad();
        if(DB::table('plans')->count() >= config('plan.max_number')){
            $this->set_error(203);
            return;
        }
        $this->result = new Plan;
        if($this->param_valid_fails($param)){
            $this->set_error(204);
            return;
        }
        $param['user_id'] = $this->user_id;
        $this->result->fill($param);
        $this->result->save();
        unset($this->result['user_id']);
        $this->status_ok();
    }


    public function destroy($id){
        if($this->is_status_bad()){
            return;
        }
        $this->status_bad();
        $plan = Plan::find($id);
        if(is_null($plan)|| $plan->user_id != $this->user_id){
            $this->set_error(205);
            return;
        }
        $plan->delete();
        $this->status_ok();
    }



    static public function id_to_body($id){
        return Plan::find($id)->body;
    }






////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////
    private function param_valid_fails($param){
        $validate_rule = [
            'name' => 'required|string',
            'body' => 'required',
        ];
        $validator = Validator::make($param, $validate_rule);
        if($validator->fails()){
            return true;
        }
        if(mb_strlen($param['name'], 'UTF-8') > config('plan.name_max_len')){
            return true;
        }
        foreach($param['body'] as $event){
            if(count($event) != 2){
                return true;
            }
            $min = intval(substr($event[0],0,2))*60 + intval(substr($event[0],-2));
            if($min % config('plan.event_min_interval')){
                return true;
            }
        }
        return false;
    }
}
