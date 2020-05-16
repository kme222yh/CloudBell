<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    protected $guarded = ['id'];



    public function setBodyAttribute($value){
        $this->attributes['body'] = json_encode($value);
    }
    public function getBodyAttribute($value){
        return json_decode($value);
    }
}
