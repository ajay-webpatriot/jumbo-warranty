<?php

namespace App\Http\Controllers\Api\Technician;

use App\User;
use Hash;
use Validator;
use App\ServiceRequest;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreLoginApiController;
use App\Http\Requests\Admin\UpdateLoginApiController;


class LoginApiController extends Controller
{
    public function loginApi(Request $request)
    { 
        $email = $request['email'];
        $password =$request['password'];

        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if($validator->fails()){
            return $validator->errors();
        }

        $success = false;
        $message = 'Email or Password is not valid!';

        $query = User::where('role_id',config('constants.TECHNICIAN_ROLE_ID'))
                        ->where('email',$email)
                        ->first();

        $passwordExist = Hash::check($password, $query->password);

        if($passwordExist == 1){
            $success = true;
            $message = 'You are successfully login';
        }

        return response()->json([
            'success' => $success,
            'message' => $message
        ], 200);
    }
}