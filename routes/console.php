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
//    $tasks = Task::whereRaw('CONVERT(DATE_FORMAT( NOW(),\'%Y-%m-%d-%H:%i:00\'),datetime) - CONVERT(DATE_FORMAT(`created_at`,\'%Y-%m-%d-%H:%i:00\'),datetime) = ((CONVERT(DATE_FORMAT(`deadline`,\'%Y-%m-%d-%H:%i:00\'),datetime) - CONVERT(DATE_FORMAT(`created_at`,\'%Y-%m-%d-%H:%i:00\'),datetime) ) * 0.8 )')->get();
//    foreach ($tasks as $task)
//    {
//        event(new TaskAboutToEnd($task));
//    }
    dd("batee5a");

})->describe('sending emails for approaching deadlines.');