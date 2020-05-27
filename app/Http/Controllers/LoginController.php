<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Container\LineEntrance;

class LoginController extends Controller
{
    public function register(Request $request, LineEntrance $line){
        $line->auth();
        $line->register();
        if($line->is_status_bad()){
             return view('page.error', ['message' => $line->get_error()['discription']]);
        }
        return redirect('/');
    }


    public function login(Request $request, LineEntrance $line){
        $line->auth();
        $line->login();
        if($line->is_status_bad()){
            return view('page.error', ['message' => $line->get_error()['discription']]);
        }
        return redirect('/');
    }


    public function logout(Request $request, LineEntrance $line){
        $line->login();
        $line->logout();
        return redirect('/');
    }


    public function withdraw(Request $request, LineEntrance $line){
        $line->login();
        $line->withdraw();
        return redirect('/');
    }
}
