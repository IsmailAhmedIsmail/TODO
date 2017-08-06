<?php

namespace App\Http\Controllers;

use App\Events\InviteToFollow;
use App\Events\UserFollowedTask;
use App\Invitation;
use App\Task;
use App\User;
use Illuminate\Http\Request;

class FollowTaskController extends Controller
{
    private $invited;
    public function __construct()
    {
        $this->invited = false;
    }

    public function follow(Task $task)
    {
        $user = UserController::currentUser();
        //user wants to follow his own task
        if($user->id == $task->user_id)
        {
            return response()->json(['response' =>'Task is already yours, nothing updated.'],200);
        }
        else if($task->private == false || $this->invited)
        {
            $user->followedTasks()->syncWithoutDetaching($task);
            event(new UserFollowedTask($task,$user));
            return response()->json(['response' => 'Task followed successfully'],200);
        }
        else
        {
            return response()->json(['response' => 'You are not allowed to follow this task.'],401);
        }
    }

    public function invite(Task $task, User $invited)
    {
        $user = UserController::currentUser();
        $invitation = Invitation::where(['inviting_id' => $user->id,'invited_id' => $invited->id,'task_id' => $task->id])->first();
        if($invitation != null)
            return response()->json(['response' => 'Invitation Exists Already!'],200);
        $invitation = Invitation::create([
            'inviting_id' => $user->id,
            'invited_id' => $invited->id,
            'task_id' => $task->id,
        ]);
        event(new InviteToFollow($invitation));
        return response()->json(['response' => 'Invitation sent successfully'],200);
    }
    public function acceptInvitation($invitation)
    {
        $invitation = Invitation::find($invitation);
        $user = UserController::currentUser();
        if($invitation != null)
        {
            if($user->id != $invitation->invited_id)
            {
                return response()->json(['response' =>'You are not a part of this invitation.'],401);
            }
            $task = Task::find($invitation->task_id);
            $this->invited=true;
            $response = $this->follow($task);
            $invitation->delete();
            return $response;
        }
        else
        {
            return response()->json(['response' =>'Invitation is invalid or expired.'],404);
        }
    }
    public function rejectInvitation($invitation)
    {
        $invitation = Invitation::find($invitation);
        $user = UserController::currentUser();
        if($invitation != null)
        {
            if($user->id != $invitation->invited_id)
            {
                return response()->json(['response'=>'You are not a part of this invitation.'],401);
            }
            $invitation->delete();
            return response()->json(['response' =>'Invitation rejected.'],200);
        }
        else
        {
            return response()->json(['response'=>'Invitation is invalid or expired'],404);
        }

    }
}
