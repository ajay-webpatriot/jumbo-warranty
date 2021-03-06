<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Socialite;
use Auth;
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

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login / registration.
     *
     * @var string
     */
    protected $redirectTo = '/admin/dashboard';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest', ['except' => 'logout']);
    }
    public function login(\Illuminate\Http\Request $request) {
        $this->validateLogin($request);

        // If the class is using the ThrottlesLogins trait, we can automatically throttle
        // the login attempts for this application. We'll key this by the username and
        // the IP address of the client making these requests into this application.
        if ($this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);
            return $this->sendLockoutResponse($request);
        }

        //check selected role_id
        switch($request->role_id){
            case 'Admin': 
                $roleToCheck = array(config('constants.SUPER_ADMIN_ROLE_ID'), config('constants.ADMIN_ROLE_ID')); 
                break;
            case 'Company': 
                $roleToCheck = array(config('constants.COMPANY_ADMIN_ROLE_ID'), config('constants.COMPANY_USER_ROLE_ID'));
                break;
            case 'Service Center': 
                $roleToCheck = array(config('constants.SERVICE_ADMIN_ROLE_ID'));
                break;
            case 'Technician': 
                $roleToCheck = array(config('constants.TECHNICIAN_ROLE_ID'));
                break;
            default :
                $roleToCheck = '';
                break;
        }

        if(!empty($roleToCheck) && $roleToCheck > 0){
            foreach($roleToCheck as $role_id){

                // This section is the only change
                $credentials = $this->credentials($request);
                $credentials['role_id'] = $role_id;

                // if ($this->guard()->validate($credentials)) {
                if ($this->guard()->attempt($credentials)) {
                   
                    $user = $this->guard()->getLastAttempted();

                    // Make sure the user is active
                    if ($this->attemptLogin($request)) {
                        // Send the normal successful login response
                        return $this->sendLoginResponse($request);
                    } else {
                        // Increment the failed login attempts and redirect back to the
                        // login form with an error message.
                        $this->incrementLoginAttempts($request);
                        return redirect()
                            ->back()
                            ->withInput($request->only($this->username(), 'remember'))
                            ->withErrors(['active' => 'You must be active to login.']);
                    }
                }
            }
        }

        // // This section is the only change
        // if ($this->guard()->validate($this->credentials($request))) {
        //     $user = $this->guard()->getLastAttempted();
        //     // Make sure the user is active
        //     if ($user['status'] == 'Active' && $this->attemptLogin($request)) {
        //         // Send the normal successful login response
        //         return $this->sendLoginResponse($request);
        //     } else {
        //         // Increment the failed login attempts and redirect back to the
        //         // login form with an error message.
        //         $this->incrementLoginAttempts($request);
        //         return redirect()
        //             ->back()
        //             ->withInput($request->only($this->username(), 'remember'))
        //             ->withErrors(['active' => 'You must be active to login.']);
        //     }
        // }

        // If the login attempt was unsuccessful we will increment the number of attempts
        // to login and redirect the user back to the login form. Of course, when this
        // user surpasses their maximum number of attempts they will get locked out.
        $this->incrementLoginAttempts($request);

        return $this->sendFailedLoginResponse($request);
    }

    /**
     * Validate the user login request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return void
     */
    protected function validateLogin(\Illuminate\Http\Request $request)
    {
        $this->validate($request, [
            $this->username() => 'required|string',
            'password' => 'required|string',
            'role_id'  => 'required'
        ]);
    }

    /**
     * Get the needed authorization credentials from the request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    protected function credentials(\Illuminate\Http\Request $request)
    {
        $credentials = $request->only($this->username(), 'password');
        $credentials['status'] = 'Active';
        return $credentials;
    }    
}
