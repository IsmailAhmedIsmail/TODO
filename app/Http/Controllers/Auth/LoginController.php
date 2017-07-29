<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Controllers\UserController;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use App\User;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use ThrottlesLogins;


    //protected $redirectTo = '/home';


//    protected function hasTooManyLoginAttempts(Request $request)
//    {
//        return $this->limiter()->tooManyAttempts(
//            $this->throttleKey($request), 4, 2
//        );
//    }
    public function username()
    {
        return 'email';
    }
    public function __construct()
    {
        //$this->middleware('WithToken')->except('authenticate');
        //$this->middleware('NoToken')->only('authenticate');
        $this->maxAttempts = 4;
        $this->decayMinutes = 2;
    }
    public function authenticate (Request $request)
    {
        $credentials = $request->only('email','password');

        try{
            if ($this->hasTooManyLoginAttempts($request)) {
                $this->fireLockoutEvent($request);

                return $this->sendLockoutResponse($request);
            }
            if(! $token = JWTAuth::attempt($credentials))
            {
                $this->incrementLoginAttempts($request);
                return response()->json(['error' => 'user credentials are not correct'],401);
            }
        }
        catch (JWTException $ex)
        {

            return response()->json(['error' => 'Something went wrong.'],500);
        }
        $user = (new UserController())->searchByEmail(request('email'));
        $user->api_token=$token;
        $user->save();
        return response()->json(['token' => $token],200);
    }


    public function logout(Request $request)
    {
        $user = JWTAuth::parseToken()->toUser();

        $user->api_token=null;
        $user->save();
        return response()->json(['response' => 'Logged out successfully.'],200);
    }
    /**
     * Redirect the user to the GitHub authentication page.
     *
     * @return Response
     */
    public function redirectToProvider()
    {
        return Socialite::driver('github')->redirect();
    }

    /**
     * Obtain the user information from GitHub.
     *
     * @return Response
     */
    public function handleProviderCallback()
    {
        $user = Socialite::driver('github')->user();

        // $user->token;
    }
}
