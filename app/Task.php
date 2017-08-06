<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
class Task extends Model
{

    protected $guarded=[];
    protected $hidden = [
        'created_at' , 'updated_at', 'warned',
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function followers()
    {
        return $this->belongsToMany(User::class,'task_user_follow');
    }
    public function scopeIncomplete($query)
    {
        return $query->where('completed',0);
    }
    public function scopeComplete($query)
    {
        return $query->where('completed', 1);
    }
    public function scopePublic($query)
    {
        return $query->where('private',0);
    }
    public function scopePrivate($query)
    {
        return $query->where('private',1);
    }
}
