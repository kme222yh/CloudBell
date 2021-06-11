<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CalendarSchedule extends Model
{
    protected $guarded = ['id'];
    protected $table = 'calendar';



    public function plan(){
        return $this->belongsTo('App\Plan');
    }
}
