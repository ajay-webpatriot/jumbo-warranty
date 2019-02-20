<?php

namespace App\Http\Controllers\Api;

use App\User;
use App\Helpers\CommonFunctions;
use App\Helpers\SendMail;
use Hash;
use Validator;
use App\ServiceRequest;
use App\Http\Controllers\Controller;

class LoginApiController extends Controller
{
    public function userData($user_id)
    {  
        $getUserData = '';
        if(isset($user_id) && !empty($user_id) && $user_id != 0){

            $getUserData = User::select(
                'id',
                'name',
                'access_token',
                'role_id',
                'company_id',
                'service_center_id',
                'phone',
                'address_1',
                'address_2',
                'city',
                'state',
                'zipcode'
            )
            ->where('role_id',config('constants.TECHNICIAN_ROLE_ID'))
            ->where('id',$user_id)
            ->where('status','Active')
            ->first();
        }
       
       return $getUserData;
        
    }
    public function login()
    { 
        $status    = 0;
        $message   = "Some error occurred. Please try again later!";
        $response  = (object)array();
        $UserArray = (object)array();

        /* Json input */
        $json  = json_decode(file_get_contents("php://input"),true);
        
        if($json == null || count($json) == 0 || empty($json)) {
            return response()->json([
                'status'    => $status,
                'message'   => $message,
                'data'      => (object)array()
            ]);
        }

        /* Validate input */
        $validator = Validator::make($json, [
            'email'     => 'required|email',
            'password'  => 'required',
        ]);

        if($validator->fails()){
            return response()->json([
                'status'    => 0,
                'message'   => 'Parameter missing: email,password!',
                'data'      => (object)array()
            ]);
        }

        $email    = $json['email'];
        $password = $json['password'];

        /* User object */
        $user = new User();

        $LoginQueryResult = $user->where('role_id',config('constants.TECHNICIAN_ROLE_ID'))
        ->where('email',$email)
        ->where('status','Active')
        ->first();

        if(!empty($LoginQueryResult)){

            $passwordExist = Hash::check($password, $LoginQueryResult->password);

            if($passwordExist == 1){

                /* Generate token */
                $token = CommonFunctions::getHashCode();

                if(!empty($token) && $token != ''){

                    /* Insert new Access token */
                    $LoginQueryResult->access_token = $token;
                    $LoginQueryResult->save();

                    if($LoginQueryResult){

                        /* Get technician all data */
                        $response->UserArray = $this->userData($LoginQueryResult->id);

                        $UserArray  = $response->UserArray;
                        $status     = 1;
                        $message    = "Successfully Login.";
                    }
                }else{
                    $message = "Invalid token!";
                }
            }else{
                $message = "Wrong password!";
            }
        }else{
            $message = "Email is not valid!";
        }

        /* Json response */
        return response()->json([
            'status'    => $status,
            'message'   => $message,
            'data'      => $UserArray

        ]);
    }

    public function forgotpassword()
    {

        $status    = 0;
        $message   = "Some error occurred. Please try again later!";
        $response  = (object)array();
        $UserArray = (object)array();

        /* Json input */
        $json  = json_decode(file_get_contents("php://input"),true);

        if($json == null || count($json) == 0 || empty($json)) {
            return response()->json([
                'status'    => $status,
                'message'   => $message,
                'data'      => (object)array()
            ]);
        }

        /* Validate input */
        $validator  = Validator::make($json, [
            'email' => 'required|email'
        ]);

        if($validator->fails()){
            return response()->json([
                'status'    => $status,
                'message'   => 'Parameter missing: email!',
                'data'      => (object)array()
            ]);
        }

        $email = $json['email'];

        /* User object */
        $user = new User();

        $ForgotPasswordQuery = $user->select('email','id')
        ->where('role_id',config('constants.TECHNICIAN_ROLE_ID'))
        ->where('email',$email)
        ->where('status','Active')
        ->first();
        
        if(!empty($ForgotPasswordQuery)){
            
            /* Generate 6 digit otp string */
            $otp = CommonFunctions::getHashCode(6);

            /* Send otp email */
            $EmailSendResponse = SendMail::forgotpasswordApiMail($ForgotPasswordQuery->email,$otp);
               
            if($EmailSendResponse == 1){

                /* Update otp in table */
                $ForgotPasswordQuery->otp = $otp;
                $ForgotPasswordQuery->save();

                if($ForgotPasswordQuery){

                    /* Get technician all data */
                    // $response->UserArray = $this->userData($ForgotPasswordQuery->id);
                    
                    // $UserArray = $response->UserArray;
                    $status    = 1;
                    $message   = 'OTP sent successfully, please check your email.';                   
                }
            }else{
                $message = 'Error sending OTP!';
            }
        }else{
            $message = 'Email is not valid!';
        }

        /* Json response */
        return response()->json([
            'success'   => $status,
            'message'   => $message,
            'data'      => $UserArray
        ]);
    }

    public function verifyotp()
    {   
        $status    = 0;
        $message   = "Some error occurred. Please try again later!";
        $response  = (object)array();
        $UserArray = (object)array();

        /* Json input */
        $json  = json_decode(file_get_contents("php://input"),true);

        if($json == null || count($json) == 0 || empty($json)) {
            return response()->json([
                'status'    => $status,
                'message'   => $message,
                'data'      => (object)array()
            ]);
        }

        /* Validate input */
        $validator  = Validator::make($json, [
            'otp'   => 'required'
        ]);

        if($validator->fails()){
            return response()->json([
                'success'   => $status,
                'message'   => 'Parameter missing',
                'data'      => (object)array()
            ]);
        }

        $otp    = $json['otp'];
        

        /* User object */
        $user = new User();
        
        if(isset($json['email']) && $json['email'] != ''){
            
            $email  = $json['email'];

            $VerifyEmailQuery = $user->where('role_id',config('constants.TECHNICIAN_ROLE_ID'))
            ->where('email',$email)
            ->where('status','Active')
            ->first();

            if(!empty($VerifyEmailQuery)){

                $VerifyOtpQuery = $user->where('role_id',config('constants.TECHNICIAN_ROLE_ID'))
                ->where('otp',$otp)
                ->where('email',$email)
                ->where('status','Active')
                ->first();

                if(!empty($VerifyOtpQuery)){

                    /* Get technician all data */
                    $response->UserArray  = $VerifyOtpQuery->select('id')
                    ->where('otp',$otp)
                    ->first();
                    // $response->UserArray  = $VerifyOtpQuery->select(
                    //     'id',
                    //     'name',
                    //     'access_token',
                    //     'role_id',
                    //     'company_id',
                    //     'service_center_id',
                    //     'phone',
                    //     'address_1',
                    //     'address_2',
                    //     'city',
                    //     'state',
                    //     'zipcode'
                    // )

                    $status     = 1;
                    $message    = 'OTP is verifed successfully.';
                    $UserArray  = $response->UserArray;

                    /* Reset OTP */
                    $VerifyOtpQuery->otp = '';
                    $VerifyOtpQuery->save();
                }else{
                    $message = "Please enter valid OTP!";
                }
            }
        }

        /* Json response */
        return response()->json([
            'status'    => $status,
            'message'   => $message,
            'data'      => $UserArray
        ]);
    }

    public function setpassword()
    {
        $status    = 0;
        $message   = "Some error occurred. Please try again later!";
        $response  = (object)array();
        $UserArray = (object)array();

        /* Json input */
        $json  = json_decode(file_get_contents("php://input"),true);

        if($json == null || count($json) == 0 || empty($json)) {
            return response()->json([
                'status'    => $status,
                'message'   => $message,
                'data'      => (object)array()
            ]);
        }
        
        /* Validate input */
        $validator = Validator::make($json, [
            'new_password'     => 'required',
            'confirm_password' => 'required'
        ]);

        if($validator->fails()){
            return response()->json([
                'status'    => 0,
                'message'   => 'Please fill required fields!',
                'data'      => (object)array()
            ]);
        }
            
        $NewPassword     = $json['new_password'];
        $ConfirmPassword = $json['confirm_password'];

        /* User object */
        $user = new User();
                 
        if(isset($json['user_id']) && $json['user_id'] != 0){

            $userId          = $json['user_id'];

            /* Match both password is equal or not */
            $MatchPassword = strcmp($NewPassword,$ConfirmPassword);

            if($MatchPassword == 0){

                $SetNewPasswordQuery = $user->where('role_id',config('constants.TECHNICIAN_ROLE_ID'))
                ->where('id',$userId)
                ->where('status','Active')
                ->first();
                
                if($SetNewPasswordQuery){
                    
                    /* Update New Password */
                    $updatePassword = User::find($userId);
                    $updatePassword->password = $NewPassword;
                    $updatePassword->update();
                    
                    if($updatePassword != ''){

                        /* Get technician all data */
                        // $response->UserArray = $this->userData($userId);
            
                        $status     = 1;
                        $message    = 'Password is changed successfully.';
                        // $UserArray  = $response->UserArray;

                    }else{

                        $message = 'Error updating password!';
                    }
                }else{

                    $message = 'User is not valid!';
                }
            }else{

                $message = 'Password is not matched!';
            }
        }else{

            $message = 'User is not valid!';
        }

        /* Json response */
        return response()->json([
            'status'    => $status,
            'message'   => $message,
            'data'      => $UserArray
        ]);
    }

}