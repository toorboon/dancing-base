<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'role_id'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function videoscreated(){
        return $this->hasMany('App\Video');
    }

    public function videos(){
        return $this->belongsToMany('App\Video', 'progress', 'user_id', 'video_id')
            ->withPivot('progress_index')
            ->withTimestamps();;
    }

    public function role(){
        return $this->belongsTo('App\Role');
    }

    public function hasRole($role){
        if ($this->role()->where('name', $role)->first()){
            return true;
        }
        return false;
    }
}
