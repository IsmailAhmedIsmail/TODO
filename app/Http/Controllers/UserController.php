<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Middleware\GetUserFromToken;

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


    public function store()
    {
        //Validation
        $this->validate(request(),[
            'name' => 'required',
            'username' => 'required|unique:users',
            'email' => 'required|unique:users|email',
            'password' => 'required|confirmed'
        ]);
        // Creation
        return $user= User::create([
            'name' => request('name'),
            'email' => request('email'),
            'password' => bcrypt(request('password')),
            'username' => request('username'),
        ]);

    }

    public function searchByUsername($username)
    {
        return User::where('username',$username)->first();
    }
    public function searchByName($name)
    {
        return User::where('name',$name)->first();
    }
    public function searchByEmail($email)
    {
        return User::where('email',$email)->first();
    }

    public function show(User $user)
    {
        return $user;
    }

    public static function  currentUser()
    {
        return  JWTAuth::parseToken()->toUser();
    }

    public function updatePassword()
    {
        $user = self::currentUser();
        $this->validate(request(),[
           'old_password' => 'required',
            'new_password' => 'required|confirmed'
        ]);
        $user = self::currentUser();
        if(password_verify(request('old_password') , $user->password))
        {
            $user->password = bcrypt(request('new_password'));
            $user->save();
            return response()->json('Password updated successfully.',200);
        }
        else
        {
            return response()->json('Wrong old password.',403);
        }
    }

}
