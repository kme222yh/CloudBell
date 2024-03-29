<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Container\LineEntrance;
use App\Container\Planer;

class PlanController extends Controller
{
    public function list(Request $request, LineEntrance $line, Planer $planer){
        if(!$line->user){
            return response($planer->get_error(), 400);
        }
        $planer->setting($line->user->id);
        $planer->list();
        return $planer->get_result();
    }


    public function show(Request $request, LineEntrance $line, Planer $planer, $id){
        if(!$line->user){
            return response($planer->get_error(), 400);
        }
        $planer->setting($line->user->id);
        $planer->show($id);
        return $planer->get_result();
    }


    public function update(Request $request, LineEntrance $line, Planer $planer, $id){
        if(!$line->user){
            return response($planer->get_error(), 400);
        }
        $planer->setting($line->user->id);
        $planer->update($id, $request->input());
        if($planer->is_status_bad()){
            return response($planer->get_error(), 400);
        }
    }


    public function create(Request $request, LineEntrance $line, Planer $planer){
        if(!$line->user){
            return response($planer->get_error(), 400);
        }
        $planer->setting($line->user->id);
        $planer->create($request->input());
        return $planer->get_result();
    }


    public function destroy(Request $request, LineEntrance $line, Planer $planer, $id){
        if(!$line->user){
            return response($planer->get_error(), 400);
        }
        $planer->setting($line->user->id);
        $planer->destroy($id);
    }
}
