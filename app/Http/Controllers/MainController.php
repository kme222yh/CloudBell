<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Container\LineEntrance;

class MainController extends Controller
{
    public function index(Request $request, LineEntrance $line){
        $line->login();
        if($line->result == true){
            return view('page.user', ['username' => $line->user->name]);
        }
        else{
            return view('page.home');
        }
    }
}
