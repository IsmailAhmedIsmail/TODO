<?php

use Illuminate\Foundation\Inspiring;
use App\Task;
use App\Events\TaskAboutToEnd;
/*
|--------------------------------------------------------------------------
| Console Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of your Closure based console
| commands. Each Closure is bound to a command instance allowing a
| simple approach to interacting with each command's IO methods.
|
*/

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->describe('Display an inspiring quote');
Artisan::command('DeadlineCheck',function(){
    $tasks = Task::whereRaw("((  Timestamp(NOW()) - Timestamp(`created_at`)    ) >= ((  Timestamp(`deadline`) - Timestamp(`created_at`)    ) * 0.8)) and `warned` = 0")->get();
    foreach ($tasks as $task)
    {
        event(new TaskAboutToEnd($task));
        $task->warned = true;
        $task->save();
    }
})->describe('sending emails for approaching deadlines.');