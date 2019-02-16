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
    public function login()
    { 
        $status    = 0;
        $message   = "Please fill required fields!";
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
                'status'    => $status,
                'message'   => 'Email or Password is not valid!',
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
        $message   = "Please fill required fields!";
        $response  = (object)array();
        $UserArray = '';

        /* Json input */
        $json  = json_decode(file_get_contents("php://input"),true);

        if($json == null || count($json) == 0 || empty($json)) {
            return response()->json([
                'status'    => $status,
                'message'   => $message,
                'data'      => ''
            ]);
        }

        /* Validate input */
        $validator  = Validator::make($json, [
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
                        'city',
                        'state',
                        'zipcode'
                    )
                    ->where('id',$ForgotPasswordQuery->id)->first();
                    
                    $UserArray = $response->UserArray;
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
        $message   = "Please fill required fields!";
        $response  = (object)array();
        $UserArray = '';

        /* Json input */
        $json  = json_decode(file_get_contents("php://input"),true);

        if($json == null || count($json) == 0 || empty($json)) {
            return response()->json([
                'status'    => $status,
                'message'   => $message,
                'data'      => ''
            ]);
        }

        /* Validate input */
        $validator  = Validator::make($json, [
            'otp'   => 'required',
            'email' => 'required|email'
        ]);

        if($validator->fails()){
            return response()->json([
                'success'   => $status,
                'message'   => $message,
                'data'      => ''
            ]);
        }

        $otp    = $json['otp'];
        $email  = $json['email'];

        /* User object */
        $user = new User();

        $VerifyOtpQuery = $user->where('role_id',config('constants.TECHNICIAN_ROLE_ID'))
        ->where('otp',$otp)
        ->where('email',$email)
        ->where('status','Active')
        ->first();
        
        if(!empty($VerifyOtpQuery)){

            /* Get technician all data */
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
                'city',
                'state',
                'zipcode'
            )
            ->where('otp',$otp)
            ->first();

            $status     = 1;
            $message    = 'OTP is verifed successfully.';
            $UserArray  = $response->UserArray;

            /* Reset OTP */
            $VerifyOtpQuery->otp = '';
            $VerifyOtpQuery->save();

        }else{
            $message = "Please enter valid OTP!";
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
        $message   = "Please fill required fields!";
        $response  = (object)array();
        $UserArray = '';

        /* Json input */
        $json  = json_decode(file_get_contents("php://input"),true);

        if($json == null || count($json) == 0 || empty($json)) {
            return response()->json([
                'status'    => $status,
                'message'   => $message,
                'data'      => ''
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
                'data'      => ''
            ]);
        }
            
        $userId          = $json['user_id'];
        $NewPassword     = $json['new_password'];
        $ConfirmPassword = $json['confirm_password'];

        /* User object */
        $user = new User();
                 
        if(isset($userId)){

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
                            'city',
                            'state',
                            'zipcode'
                        )
                        ->where('id',$userId)
                        ->first();
            
                        $status     = 1;
                        $message    = 'Password is changed successfully.';
                        $UserArray  = $response->UserArray;

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

    public function dashboard()
    {
        $status    = 0;
        $message   = "Some error occurred. Please try again later!";
        $response  = (object)array();
        $UserArray = '';

        /* Json input */
        $json  = json_decode(file_get_contents("php://input"),true);

        if($json == null || count($json) == 0 || empty($json)) {
            return response()->json([
                'status'    => $status,
                'message'   => $message,
                'data'      => ''
            ]);
        }
        
        /* Validate input */
        $validator = Validator::make($json, [
            'user_id' => 'required',
            'token'   => 'required'
        ]);

        if($validator->fails()){
            return response()->json([
                'status'    => 0,
                'message'   => 'Some error occurred. Please try again later!',
                'data'      => ''
            ]);
        }

        $userId = $json['user_id'];
        $token  = $json['token'];
        
        /* All count response */
        $CountResult                    = 0;
        $response->AssignedCount = 0;
        $response->todayDueCount      = 0;
        $response->overDueCount  = 0;
        $response->ResolvedCount = 0;

        $TodayDate = date('Y-m-d H:i:s');

        /* User object */
        $user = new User();
                 
        if(isset($userId)){

            $DashBoardQuery = $user->where('role_id',config('constants.TECHNICIAN_ROLE_ID'))
            ->where('id',$userId)
            ->Where('access_token', 'like', '%' . $token . '%')
            ->where('status','Active')
            ->first();
            
            if(!empty($DashBoardQuery)){

                /* Service request object */
                $serviceRequest = new ServiceRequest();

                /* Assigned request count */
                $response->AssignedCount  = $serviceRequest->AssignedRequest($userId,'count');

                /* Assigned request list */
                $response->AssignedList   = $serviceRequest->AssignedRequest($userId);

                /* TodayDue request count */
                $response->todayDueCount  = $serviceRequest->getTechnicianDueRequest($userId,'todaydue','count');
                
                /* OverDue request count */
                $response->overDueCount   = $serviceRequest->getTechnicianDueRequest($userId,'overdue','count');

                /* TodayDue request List */
                $response->todayDueList   = $serviceRequest->getTechnicianDueRequest($userId,'todaydue');

                /* OverDue request List */
                $response->overDueList    = $serviceRequest->getTechnicianDueRequest($userId,'overdue');

                /* Resolved request list */
                $response->ResolvedList   = $serviceRequest->ResolvedRequest($userId);

                /* Resolved request count */
                $response->ResolvedCount   = $serviceRequest->ResolvedRequest($userId,'count');

                $status     = 1;
                $UserArray  = $response;
                $message    = '';
            }
        }

        /* Json response */
        return response()->json([
            'status'    => $status,
            'message'   => $message,
            'data'      => $UserArray
        ]);
    }
}