<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use Illuminate\Support\Facades\Storage;
use Tymon\JWTAuth\Facades\JWTAuth;


class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return User::all();
    }


    public function searchByUsername()
    {
        return User::where('username',request('username'))->first();
    }
    public function searchByName()
    {
        return User::where('name',request('name'))->first();
    }
    public function searchByEmail()
    {
        return User::where('email',request('email'))->first();
    }

    public function show(User $user)
    {
        return $user;
    }

    public static function  currentUser()
    {
        return  auth()->user();
    }

    public function updatePassword()
    {
        $this->validate(request(),[
           'old_password' => 'required',
            'new_password' => 'required|confirmed'
        ]);
        $user = self::currentUser();
        if(password_verify(request('old_password') , $user->password))
        {
            $user->password = bcrypt(request('new_password'));
            $user->save();
            return response()->json(['response' =>'Password updated successfully.'],200);
        }
        else
        {
            return response()->json(['response' =>'Wrong old password.'],403);
        }
    }
    public function avatar()
    {
        $user = self::currentUser();

        $file = request()->file('avatar');
        $ext = $file->guessClientExtension();
        $dest=$file->storeAs('avatars','avatar.'.$user->id.'.'.$ext);
        $user->avatar = $dest;
        $user->save();
        return response()->json(['response'=>'Avatar Updated'],200);
    }

}
