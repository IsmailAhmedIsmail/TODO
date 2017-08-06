<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Controllers\UserController;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Lang;
use Laravel\Socialite\Facades\Socialite;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use App\User;

class LoginController extends Controller
{
    use AuthenticatesUsers;
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



    protected $redirectTo = '/home';

    public function username()
    {
        return 'email';
    }
    public function __construct()
    {
        $this->maxAttempts = 4;
        $this->decayMinutes = 2;
    }
    public function login(Request $request)
    {
        $this->validateLogin($request);
        $credentials = $request->only('email','password');
        if ($this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);
            return $this->sendLockoutResponse($request);
        }
        if ($token = $this->guard()->attempt($credentials)) {
            return $this->sendLoginResponse($request,$token);
        }
        $this->incrementLoginAttempts($request);
        return $this->sendFailedLoginResponse($request);
    }

    protected function sendLoginResponse(Request $request, string $token)
    {
        $this->clearLoginAttempts($request);
        return $this->authenticated($request, Auth::guard()->user(), $token);
    }
    protected function authenticated(Request $request, $user, string $token)
    {
        return response()->json([
            'token' => $token,
        ],200);
    }
    protected function sendFailedLoginResponse(Request $request)
    {
        return response()->json([
            'response' => 'auth failed',
        ], 401);
    }
    public function logout(Request $request)
    {
        Auth::guard()->logout();
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
        $user = $this->getGithubUser($user);
        $token = JWTAuth::fromUser($user);
        return response()->json(['token' => $token],200);
    }
    private function getGithubUser($githubUser)
    {
        if ($user = User::where('github_id', $githubUser->id)->first()) {
            return $user;
        }

        return User::create([
            'username' => $githubUser->nickname,
            'name' => $githubUser->name,
            'email' => $githubUser->email,
            'github_id' => $githubUser->id,
            'avatar' => $githubUser->avatar
        ]);
    }

}
