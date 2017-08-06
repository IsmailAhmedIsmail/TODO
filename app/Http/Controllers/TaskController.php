<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Task;
use Tymon\JWTAuth\Facades\JWTAuth;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Task::public()->get();
    }
    public function feed()
    {
        $user = UserController::currentUser();
        $ownTasks = $user->tasks()->get()->toArray();
        $followedTasks = $user->followedTasks()->get()->toArray();
        return array_merge($ownTasks,$followedTasks);
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store()
    {
        $this->validate(request(),[
           'title' => 'required',
            'description' => 'required',
        ]);
        $task = new Task();
        $user= UserController::currentUser();
        $task->description = request('description');
        $task->user_id=$user->id;
        $task->title=request('title');
        $task->deadline = request()->has('deadline')? Carbon::createFromFormat('d/m/Y',request('deadline')): Carbon::now()->addWeek();
        $task->save();
        return response()->json(['response' => 'Task Created Successfully'],200);

    }

    public function setComplete(Task $task)
    {
        $task->completed = true;
        $task->save();
        return response()->json(['response' => 'Task has been set to complete'],200);
    }
    public function toggleComplete(Task $task)
    {
        $task->completed = !$task->completed;
        $task->save();
        return response()->json(['response' => 'Task status has been toggled'],200);
    }
    public function setIncomplete(Task $task)
    {
        $task->completed = false;
        $task->save();
        return response()->json(['response' => 'Task has been set to incomplete'],200);
    }
    public function setPublic(Task $task)
    {
        $task->private = false;
        $task->save();
        return response()->json(['response' => 'Task has been set to public'],200);
    }
    public function setPrivate(Task $task)
    {
        $task->private = true;
        $task->save();
        return response()->json(['response' => 'Task has been set to private'],200);
    }
    public function setDeadline(Task $task)
    {
        $task->deadline = Carbon::createFromFormat('d/m/Y',request('deadline'));
        $task->save();
        return response()->json(['response' => 'Task deadline has been updated'],200);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public static function show(Task $task)
    {
        if(!$task->private || UserController::currentUser()->id == $task->user_id )
            return $task;
        return response()->json(['response' => 'You do not have privilege to show this task'],401);

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Task $task)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Task $task)
    {
        $task->delete();
            return response()->json(['response' => 'Task deleted successfully'],200);
    }
    public function addFile (Task $task)
    {
        $file = request()->file('file');
        $ext = $file->guessClientExtension();
        $file->store('tasks/task'.$task->id);
        return response()->json(['response' => 'File added successfully'],200);
    }

}
