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
    public function login(Request $request)
    { 
        $email      = $request['email'];
        $password   = $request['password'];

        $validator = Validator::make($request->all(), [
            'email'     => 'required|email',
            'password'  => 'required',
        ]);

        if($validator->fails()){
            return $validator->errors();
        }

        $success    = false;
        $message    = 'Email or Password is not valid!';
        $token      = '';

        $LoginQueryResult = User::select('id','role_id','password')
        ->where('role_id',config('constants.TECHNICIAN_ROLE_ID'))
        ->where('email',$email)
        ->first();
        
        //password hash check with database
        $passwordExist = Hash::check($password, $LoginQueryResult->password);

        if($passwordExist == 1){

            //generate token
            $token = CommonFunctions::getHashCode();

            if(!empty($token) && $token != ''){

                //update access token in database
                $InsertNewTokenInTable = User::where('id',$LoginQueryResult->id)
                ->update(array(
                    'access_token' => $token
                ));

                if($InsertNewTokenInTable == 1){
                    $success    = true;
                    $message    = 'You are successfully login';
                    $token      = $token;
                }
            }else{
                $message = 'Token is not valid';
                $success = false;
            }
        }

        return response()->json([
            'success'   => $success,
            'message'   => $message,
            'token'     => $token
        ], 200);
    }

    public function forgotpassword(Request $request)
    {
        $email = $request['email'];
       
        //validate email
        $validator = Validator::make($request->all(), [
            'email' => 'required|email'
        ]);

        if($validator->fails()){
            return $validator->errors();
        }

        $ForgotPasswordQuery = User::select('email','id')
        ->where('role_id',config('constants.TECHNICIAN_ROLE_ID'))
        ->where('email',$email)
        ->first();

        if(!empty($ForgotPasswordQuery) && $ForgotPasswordQuery != ''){
            
            //generate 6 digit otp string
            $otp = CommonFunctions::getHashCode(6);

            //send otp email
            $EmailSendResponse = SendMail::forgotpasswordApiMail($ForgotPasswordQuery->email,$otp);
               
            if($EmailSendResponse == 1){

                //update otp in table
                $InsertNewOtpInTable = User::where('id',$ForgotPasswordQuery->id)
                ->update(array(
                    'otp' => $otp
                ));

                if($InsertNewOtpInTable == 1){
                    $success = true;
                    $message = 'Successfully sent OTP please check you email!';
                }
            }else{
                $success = false;
                $message = 'Erro sending OTP!';
            }
        }else{
            $success = false;
            $message = 'Email or Password is not valid!';
        }

        return response()->json([
            'success'   => $success,
            'message'   => $message
        ], 200);
    }

    // public function verifyotp(Request $request)
    // {
    //     $otp = $request['otp'];

    //     $VerifyOtpQuery = User::select('otp')
    //     ->where('role_id',config('constants.TECHNICIAN_ROLE_ID'))
    //     ->where('otp',$otp)
    //     ->first();

    //     if(!empty($VerifyOtpQuery) && $VerifyOtpQuery != ''){

    //     }
    // }

}