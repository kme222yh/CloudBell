<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Container\LineEntrance;
use App\Container\Planer;

class PlanController extends Controller
{
    public function list(Request $request, LineEntrance $line, Planer $planer){
        $line->login();
        $planer->setting($line->user->id);
        $planer->list();
        if($planer->result == false){
            return response(['error'=>'400', 'error_description'=>$planer->error], 400);
        }
        return $planer->plans;
    }


    public function show(Request $request, LineEntrance $line, Planer $planer, $id){
        $line->login();
        $planer->setting($line->user->id);
        $planer->show($id);
        if($planer->result == false){
            return response(['error'=>'400', 'error_description'=>$planer->error], 400);
        }
        unset($planer->plan['user_id']);
        return $planer->plan;
    }


    public function update(Request $request, LineEntrance $line, Planer $planer, $id){
        $line->login();
        $planer->setting($line->user->id);
        $planer->update($id, $request->input());
        if($planer->result == false){
            return response(['error'=>'400', 'error_description'=>$planer->error], 400);
        }
    }


    public function create(Request $request, LineEntrance $line, Planer $planer){
        $line->login();
        $planer->setting($line->user->id);
        $planer->create($request->input());
        if($planer->result == false){
            return response(['error'=>'400', 'error_description'=>$planer->error], 400);
        }
    }


    public function destroy(Request $request, LineEntrance $line, Planer $planer, $id){
        $line->login();
        $planer->setting($line->user->id);
        $planer->destroy($id);
        if($planer->result == false){
            return response(['error'=>'400', 'error_description'=>$planer->error], 400);
        }
    }
}
