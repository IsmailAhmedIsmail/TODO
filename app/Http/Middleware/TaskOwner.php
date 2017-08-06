<?php

namespace App\Http\Middleware;

use App\Http\Controllers\UserController;
use Closure;
use App\Task;
class TaskOwner
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $task = Task::find($request->segment(3));
        $user = UserController::currentUser();
        if($task->user_id == $user->id)
            return $next($request);
        else
            return response()->json(['response' => 'You do not have privilege to edit this task'],401);
    }
}
