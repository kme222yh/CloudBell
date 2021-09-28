<?php

namespace App\Http\Controllers;

use Database\Seeders\DemoUserSeeder;

use Illuminate\Http\Request;

class DemoController extends Controller
{
    public function index(Request $request){
        return redirect('/')->cookie('id', DemoUserSeeder::$GUEST);
    }
}
