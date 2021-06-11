<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Container\LineEntrance;

class MainController extends Controller
{
    public function index(Request $request, LineEntrance $line){
        if($line->user){
            return view('page.user', ['username' => $line->user->name]);
        }
        else{
            return view('page.home');
        }
    }

    public function config(){
        return response(['plan' => config('plan'), 'calendar' => config('calendar')]);
    }
}
