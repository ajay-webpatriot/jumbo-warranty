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
        $validator = Validator::make($request->all(), [
            'email'     => 'required|email',
            'password'  => 'required',
        ]);

        if($validator->fails()){
            return response()->json([
                'success'   => false,
                'message'   => 'Email or Password is not valid!',
                'data'     => ''
            ]);
        }

        $email          = $request['email'];
        $password       = $request['password'];
        
        $success        = false;
        $message        = 'Email or Password is not valid!';
        
        $UserArray      = array();
        $TechicianData  = '';

        //User object
        $user = new User();

        $LoginQueryResult = $user->where('role_id',config('constants.TECHNICIAN_ROLE_ID'))
        ->where('email',$email)
        ->where('status','Active')
        ->first();
        
        // password hash check with database
        $passwordExist = Hash::check($password, $LoginQueryResult->password);

        if($passwordExist == 1){

            //generate token
            $token = CommonFunctions::getHashCode();

            if(!empty($token) && $token != ''){

                //update access token in database
                $LoginQueryResult->access_token = $token;
                $LoginQueryResult->save();

                if($LoginQueryResult){

                    //Get technician all data
                    $UserArray = $user->select(
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
                    ->where('id',$LoginQueryResult->id)->first();
                    
                    $success        = true;
                    $message        = 'You are successfully login';
                    $TechicianData  = $UserArray;
                }
            }else{
                $message        = 'Token is not valid';
                $success        = false;
                $TechicianData  = '';
            }
        }

        return response()->json([
            'success'   => $success,
            'message'   => $message,
            'data'      => $TechicianData

        ]);
    }

    public function forgotpassword(Request $request)
    {
        //validate email
        $validator = Validator::make($request->all(), [
            'email' => 'required|email'
        ]);

        if($validator->fails()){
            return response()->json([
                'success'   => false,
                'message'   => 'Email is not valid!',
                'data'      => ''
            ]);
        }

        $email = $request['email'];

        $UserArray      = array();
        $TechicianData  = '';

        //User object
        $user = new User();

        $ForgotPasswordQuery = $user->select('email','id')
        ->where('role_id',config('constants.TECHNICIAN_ROLE_ID'))
        ->where('email',$email)
        ->where('status','Active')
        ->first();

        if(!empty($ForgotPasswordQuery) && $ForgotPasswordQuery != ''){
            
            //generate 6 digit otp string
            $otp = CommonFunctions::getHashCode(6);

            //send otp email
            $EmailSendResponse = SendMail::forgotpasswordApiMail($ForgotPasswordQuery->email,$otp);
               
            if($EmailSendResponse == 1){

                //update otp in table
                $ForgotPasswordQuery->otp = $otp;
                $ForgotPasswordQuery->save();

                if($ForgotPasswordQuery){

                    $UserArray = $user->select(
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

                    $success        = true;
                    $message        = 'Successfully sent OTP please check your email!';
                    $TechicianData  = $UserArray;
                }
            }else{
                $success        = false;
                $message        = 'Erro sending OTP!';
                $TechicianData  = '';
            }
        }else{
            $success        = false;
            $message        = 'Email is not valid!';
            $TechicianData  = '';
        }

        return response()->json([
            'success'   => $success,
            'message'   => $message,
            'data'      => $TechicianData
        ]);
    }

    public function verifyotp(Request $request)
    {   
        $validator = Validator::make($request->all(), [
            'otp'  => 'required'
        ]);

        if($validator->fails()){
            return response()->json([
                'success'   => false,
                'message'   => 'OTP is not valid!'
            ]);
        }

        $otp  = $request['otp'];

        $UserArray      = array();
        $TechicianData  = '';

        $success = false;
        $message = 'OTP is not valid!';

        //User object
        $user = new User();

        $VerifyOtpQuery = $user->where('role_id',config('constants.TECHNICIAN_ROLE_ID'))
        ->where('otp',$otp)
        ->where('status','Active')
        ->first();

        if(!empty($VerifyOtpQuery) && $VerifyOtpQuery != ''){

            $UserArray  = $VerifyOtpQuery->select(
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

            $success        = true;
            $message        = 'Successfully verify OTP';
            $TechicianData  = $UserArray;
        }

        return response()->json([
            'success'   => $success,
            'message'   => $message,
            'data'      => $TechicianData
        ]);
    }

    public function setpassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'new_password'     => 'required',
            'confirm_password' => 'required'
        ]);

        if($validator->fails()){
            return response()->json([
                'success'   => false,
                'message'   => 'Please fill the fields!',
                'data'      => ''
            ]);
        }
            
        $userId = $request['user_id'];
        $NewPassword = $request['new_password'];
        $ConfirmPassword = $request['confirm_password'];

        $UserArray      = array();
        $TechicianData  = '';

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
                    $updatePassword = User::find($userId);
                    $updatePassword->password = $NewPassword;
                    $updatePassword->update();

                    if($SetNewPasswordQuery){

                        $UserArray  = $user->select(
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
            
                        $success        = true;
                        $message        = 'Successfully change password';
                        $TechicianData  = $UserArray;
                    }else{
                        $success        = false;
                        $message        = 'Error updating OTP!';
                        $TechicianData  = '';
                    }
                }else{
                    $success        = false;
                    $message        = 'User is not valid!';
                    $TechicianData  = '';
                }
            }else{
                $success        = false;
                $message        = 'Password is not matched';
                $TechicianData  = '';
            }
        }else{
            $success        = false;
            $message        = 'User is not valid!';
            $TechicianData  = '';
        }

        return response()->json([
            'success'   => $success,
            'message'   => $message,
            'data'      => $TechicianData
        ]);
    }

}