<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

use Crypt;

class User extends Authenticatable
{
    protected $primaryKey = 'id';
    protected $keyType = 'string';
    public $incrementing = false;
    protected $guarded = ['created_at', 'updated_at'];


    public function setAccessTokenAttribute($value){
        $this->attributes['access_token'] = Crypt::encrypt($value);
    }
    public function getAccessTokenAttribute($value){
        return Crypt::decrypt($value);
    }

    public function setRefreshTokenAttribute($value){
        $this->attributes['refresh_token'] = Crypt::encrypt($value);
    }
    public function getRefreshTokenAttribute($value){
        return Crypt::decrypt($value);
    }

    public function setNameAttribute($value){
        $this->attributes['name'] = Crypt::encrypt($value);
    }
    public function getNameAttribute($value){
        return Crypt::decrypt($value);
    }
}
