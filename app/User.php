<?php

namespace App;


use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Tymon\JWTAuth\Contracts\JWTSubject as AuthenticatableUserContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
class User extends Authenticatable implements
    CanResetPasswordContract,
    AuthenticatableUserContract
{
    use Notifiable;

    public function getJWTIdentifier()
    {
        return $this->getKey();  // Eloquent model method
    }
    public function getJWTCustomClaims()
    {
        return [
            'user' => [
                'id' => $this->id,
             ]
        ];
    }
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
        'name', 'username','email', 'password', 'github_id','avatar',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token', 'api_token', 'created_at' , 'updated_at','github_id','avatar'
    ];
}
