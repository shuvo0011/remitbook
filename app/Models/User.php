<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use PHPOpenSourceSaver\JWTAuth\Contracts\JWTSubject as ContractsJWTSubject;


// use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements ContractsJWTSubject
{
    use HasApiTokens, HasFactory, Notifiable;


    protected $table = 'user_info';

    protected $primarykey = 'user_info_key';


    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }



    protected $fillable = [
        'email',
        'password',
    ];


    protected $hidden = [
        'password',
    ];



}
