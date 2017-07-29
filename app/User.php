<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    public function tasks()
    {
        return $this->hasMany(Task::class);
    }
    public function followedTasks()
    {
        return $this->belongsToMany(Task::class,'task_user_follow');
    }

    protected $fillable = [
        'name', 'username','email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token', 'api_token', 'created_at' , 'updated_at',
    ];
}
