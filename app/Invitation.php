<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Invitation extends Model
{
    protected $guarded = [];
    public function inviting()
    {
        return $this->hasOne(User::class);
    }
    public function invited()
    {
        return $this->hasOne(User::class);
    }
    public function task()
    {
        return $this->hasOne(Task::class);
    }

}
