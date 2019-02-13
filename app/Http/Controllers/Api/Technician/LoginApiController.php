<?php

namespace App\Http\Controllers\Api\Technician;

use App\User;
use App\Helpers\CommonFunctions;
use App\Helpers\SendMail;
use Hash;
use Validator;
use App\ServiceRequest;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreLoginApiController;
use App\Http\Requests\Admin\UpdateLoginApiController;


class LoginApiController extends Controller
{
    public function login()
    { 
        $status    = 0;
        $message   = "Some error occurred. Please try again later.";
        $response  = (object)array();
        $UserArray = '';

        $json  = json_decode(file_get_contents("php://input"),true);

        if($json == null || count($json) == 0 || empty($json)) {
            return response()->json([
                'status'    => $status,
                'message'   => $message,
                'data'      => ''
            ]);
        }

        $validator = Validator::make($json, [
            'email'     => 'required|email',
            'password'  => 'required',
        ]);

        if($validator->fails()){
            return response()->json([
                'status'    => $status,
                'message'   => 'Email or Password is not valid!',
                'data'      => ''
            ]);
        }

        $email    = $json['email'];
        $password = $json['password'];

        //User object
        $user = new User();

        $LoginQueryResult = $user->where('role_id',config('constants.TECHNICIAN_ROLE_ID'))
        ->where('email',$email)
        ->where('status','Active')
        ->first();

        if(!empty($LoginQueryResult)){

            $passwordExist = Hash::check($password, $LoginQueryResult->password);

            if($passwordExist == 1){
            
                //First time login set value
                if($LoginQueryResult->is_first_login == 0){
                    $LoginQueryResult->is_first_login = 1;
                }

                //Generate Token
                $token = CommonFunctions::getHashCode();

                if(!empty($token) && $token != ''){

                    $LoginQueryResult->access_token = $token;
                    $LoginQueryResult->save();

                    if($LoginQueryResult){

                        //Get technician all data
                        $response->UserArray = $user->select(
                            'id',
                            'name',
                            'access_token',
                            'role_id',
                            'company_id',
                            'is_first_login',
                            'service_center_id',
                            'phone',
                            'address_1',
                            'address_2',
                            'location_address',
                            'city',
                            'state',
                            'zipcode'
                        )
                        ->where('id',$LoginQueryResult->id)->first();

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

        return response()->json([
            'status'   => $status,
            'message'   => $message,
            'data'      => $UserArray

        ]);
    }

    public function forgotpassword()
    {

        $status    = 0;
        $message   = "Some error occurred. Please try again later.";
        $response  = (object)array();
        $UserArray = '';

        $json  = json_decode(file_get_contents("php://input"),true);

        if($json == null || count($json) == 0 || empty($json)) {
            return response()->json([
                'status'    => $status,
                'message'   => $message,
                'data'      => ''
            ]);
        }

        $validator = Validator::make($json, [
            'email' => 'required|email'
        ]);

        if($validator->fails()){
            return response()->json([
                'status'    => $status,
                'message'   => 'Email is not valid!',
                'data'      => ''
            ]);
        }

        $email = $json['email'];

        //User object
        $user = new User();

        $ForgotPasswordQuery = $user->select('email','id')
        ->where('role_id',config('constants.TECHNICIAN_ROLE_ID'))
        ->where('email',$email)
        ->where('status','Active')
        ->first();
        
        if(!empty($ForgotPasswordQuery)){
            
            //generate 6 digit otp string
            $otp = CommonFunctions::getHashCode(6);

            //send otp email
            $EmailSendResponse = SendMail::forgotpasswordApiMail($ForgotPasswordQuery->email,$otp);
               
            if($EmailSendResponse == 1){

                //update otp in table
                $ForgotPasswordQuery->otp = $otp;
                $ForgotPasswordQuery->save();

                if($ForgotPasswordQuery){

                    $response->UserArray = $user->select(
                        'id',
                        'name',
                        'access_token',
                        'role_id',
                        'company_id',
                        'service_center_id',
                        'phone',
                        'address_1',
                        'address_2',
                        'location_address',
                        'city',
                        'state',
                        'zipcode'
                    )
                    ->where('id',$ForgotPasswordQuery->id)->first();
                    
                    $UserArray = $response->UserArray;
                    $status    = 1;
                    $message   = 'Successfully sent OTP please check your email!';                   
                }
            }else{
                $message = 'Error sending OTP!';
            }
        }else{
            $message = 'Email is not valid!';
        }

        return response()->json([
            'success'   => $status,
            'message'   => $message,
            'data'      => $UserArray
        ]);
    }

    public function verifyotp()
    {   
        $status    = 0;
        $message   = 'Some error occurred. Please try again later.';
        $response  = (object)array();
        $UserArray = '';

        $json  = json_decode(file_get_contents("php://input"),true);

        if($json == null || count($json) == 0 || empty($json)) {
            return response()->json([
                'status'    => $status,
                'message'   => $message,
                'data'      => ''
            ]);
        }

        $validator = Validator::make($json, [
            'otp'  => 'required',
            'email' => 'required|email'
        ]);

        if($validator->fails()){
            return response()->json([
                'success'   => $status,
                'message'   => $message,
                'data'      => ''
            ]);
        }

        $otp  = $json['otp'];
        $email  = $json['email'];

        //User object
        $user = new User();

        $VerifyOtpQuery = $user->where('role_id',config('constants.TECHNICIAN_ROLE_ID'))
        ->where('otp',$otp)
        ->where('email',$email)
        ->where('status','Active')
        ->first();
        
        if(!empty($VerifyOtpQuery)){

            $response->UserArray  = $VerifyOtpQuery->select(
                'id',
                'name',
                'access_token',
                'role_id',
                'company_id',
                'service_center_id',
                'phone',
                'address_1',
                'address_2',
                'location_address',
                'city',
                'state',
                'zipcode'
            )
            ->where('otp',$otp)
            ->first();

            $status     = 1;
            $message    = 'Successfully verify OTP';
            $UserArray  = $response->UserArray;

            //Reset OTP
            $VerifyOtpQuery->otp = '';
            $VerifyOtpQuery->save();

        }

        return response()->json([
            'status'    => $status,
            'message'   => $message,
            'data'      => $UserArray
        ]);
    }

    public function setpassword()
    {
        $status    = 0;
        $message   = 'Some error occurred. Please try again later.';
        $response  = (object)array();
        $UserArray = '';

        $json  = json_decode(file_get_contents("php://input"),true);

        if($json == null || count($json) == 0 || empty($json)) {
            return response()->json([
                'status'    => $status,
                'message'   => $message,
                'data'      => ''
            ]);
        }
        
        $validator = Validator::make($json, [
            'new_password'     => 'required',
            'confirm_password' => 'required'
        ]);

        if($validator->fails()){
            return response()->json([
                'status'   => 0,
                'message'   => 'Please fill the fields!',
                'data'      => ''
            ]);
        }
            
        $userId = $json['user_id'];
        $NewPassword = $json['new_password'];
        $ConfirmPassword = $json['confirm_password'];

        //User object
        $user = new User();
                 
        if(isset($userId)){

            //Match both password is equal or not
            $MatchPassword = strcmp($NewPassword,$ConfirmPassword);

            if($MatchPassword == 0){

                $SetNewPasswordQuery = $user->where('role_id',config('constants.TECHNICIAN_ROLE_ID'))
                ->where('id',$userId)
                ->where('status','Active')
                ->first();
                
                if($SetNewPasswordQuery){

                    if($SetNewPasswordQuery->is_first_login == 0){
                        $SetNewPasswordQuery->is_first_login = 1;
                        $SetNewPasswordQuery->update();
                    }
                    
                    //Update New Password
                    $updatePassword = User::find($userId);
                    $updatePassword->password = $NewPassword;
                    $updatePassword->update();
                    
                    if($updatePassword != ''){

                        $response->UserArray  = $user->select(
                            'id',
                            'name',
                            'access_token',
                            'role_id',
                            'company_id',
                            'service_center_id',
                            'phone',
                            'address_1',
                            'address_2',
                            'location_address',
                            'city',
                            'state',
                            'zipcode'
                        )
                        ->where('id',$userId)
                        ->first();
            
                        $status     = 1;
                        $message    = 'Successfully change password';
                        $UserArray  = $response->UserArray;

                    }else{

                        $message = 'Error updating password!';
                    }
                }else{

                    $message = 'User is not valid!';
                }
            }else{

                $message = 'Password is not matched';
            }
        }else{

            $message = 'User is not valid!';
        }

        return response()->json([
            'status'   => $status,
            'message'   => $message,
            'data'      => $UserArray
        ]);
    }
}