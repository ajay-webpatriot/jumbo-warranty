<?php

namespace App\Http\Controllers\Api;

use App\User;
use Validator;
use App\ServiceRequest;
use App\Http\Controllers\Controller;

class ServiceRequestApiController extends Controller
{
    public function validateToken($user_id,$token)
    {
        $valid = false;

        if(isset($user_id) && !empty($user_id) && isset($token) && !empty($token)){
            $user = User::where('role_id',config('constants.TECHNICIAN_ROLE_ID'))
            ->where('id',$user_id)
            ->Where('access_token',$token)
            ->where('status','Active')
            ->first();

            if(count($user) > 0){
                $valid = true;
            }
        }
        return $valid;
    }

    public function dashboard()
    {
        $status    = 0;
        $message   = "Some error occurred. Please try again later!";
        $response  = (object)array();

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
            'user_id' => 'required',
            'token'   => 'required'
        ]);

        if($validator->fails()){
            return response()->json([
                'status'    => 0,
                'message'   => 'Parameter missing: user_id,token.',
                'data'      => (object)array()
            ]);
        }


        $user_id = trim($json['user_id']);
        $token  = trim($json['token']);

        $valid = $this->validateToken($user_id,$token);
        if(!$valid){
            return response()->json([
                'status'    => 0,
                'message'   => 'Invalid access token!',
                'data'      => (object)array()
            ]);
        }

        
        /* Service request object */
        $serviceRequest = new ServiceRequest();

        /* Assigned request count */
        $response->assignedCount  = $serviceRequest->getTechnicianAssignedRequest($user_id,'count');

        /* TodayDue request count */
        $response->todayDueCount  = $serviceRequest->getTechnicianDueRequest($user_id,'todaydue','count');

        /* OverDue request count */
        $response->overDueCount   = $serviceRequest->getTechnicianDueRequest($user_id,'overdue','count');

        /* Resolved ( Close ) request count */
        $response->resolvedCount  = $serviceRequest->getTechnicianResolvedRequest($user_id,'count');

        /* Json response */
        return response()->json([
            'status'    => 1,
            'message'   => '',
            'data'      => $response
        ]);
    }

    public function getAssignedRequestList()
    {
        $status    = 0;
        $message   = "Some error occurred. Please try again later!";
        $response  = (object)array();

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
            'user_id' => 'required',
            'token'   => 'required'
        ]);

        if($validator->fails()){
            return response()->json([
                'status'    => 0,
                'message'   => 'Parameter missing: user_id,token.',
                'data'      => (object)array()
            ]);
        }

        $user_id = trim($json['user_id']);
        $token  = trim($json['token']);

        $valid = $this->validateToken($user_id,$token);
        if(!$valid){
            return response()->json([
                'status'    => 0,
                'message'   => 'Invalid access token!',
                'data'      => (object)array()
            ]);
        }

        /* Service request object */
        $serviceRequest = new ServiceRequest();

        /* Assigned request list */
        $response = $serviceRequest->getTechnicianAssignedRequest($user_id);

        return response()->json([
            'status'    => 1,
            'message'   => '',
            'data'      => $response
        ]);
       
    }

    public function getTodayDueRequestList()
    {
        $status    = 0;
        $message   = "Some error occurred. Please try again later!";
        $response  = (object)array();

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
            'user_id' => 'required',
            'access_token'   => 'required'
        ]);

        if($validator->fails()){
            return response()->json([
                'status'    => 0,
                'message'   => 'Parameter missing: user_id,token.',
                'data'      => (object)array()
            ]);
        }
        
        $user_id = trim($json['user_id']);
        $token  = trim($json['access_token']);

        $valid = $this->validateToken($user_id,$token);
        if(!$valid){
            return response()->json([
                'status'    => 0,
                'message'   => 'Invalid access token!',
                'data'      => (object)array()
            ]);
        }

        /* Service request object */
        $serviceRequest = new ServiceRequest();

        /* Assigned request list */
        $response = (object)$serviceRequest->getTechnicianDueRequest($user_id,'todaydue');
        
        return response()->json([
            'status'    => 1,
            'message'   => '',
            'data'      => $response
        ]);
       
    }

    public function getOverDueRequestList()
    {
        $status    = 0;
        $message   = "Some error occurred. Please try again later!";
        $response  = (object)array();

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
            'user_id' => 'required',
            'access_token'   => 'required'
        ]);

        if($validator->fails()){
            return response()->json([
                'status'    => 0,
                'message'   => 'Parameter missing: user_id,token.',
                'data'      => (object)array()
            ]);
        }
        
        $user_id = trim($json['user_id']);
        $token  = trim($json['access_token']);

        $valid = $this->validateToken($user_id,$token);
        if(!$valid){
            return response()->json([
                'status'    => 0,
                'message'   => 'Invalid access token!',
                'data'      => (object)array()
            ]);
        }

        /* Service request object */
        $serviceRequest = new ServiceRequest();

        /* Assigned request list */
        $response = (object)$serviceRequest->getTechnicianDueRequest($user_id,'overdue');
        
        return response()->json([
            'status'    => 1,
            'message'   => '',
            'data'      => $response
        ]);
       
    }

    public function getResolvedRequestList()
    {
        $status    = 0;
        $message   = "Some error occurred. Please try again later!";
        $response  = (object)array();

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
            'user_id' => 'required',
            'access_token'   => 'required'
        ]);

        if($validator->fails()){
            return response()->json([
                'status'    => 0,
                'message'   => 'Parameter missing: user_id,token.',
                'data'      => (object)array()
            ]);
        }
        
        $user_id = trim($json['user_id']);
        $token  = trim($json['access_token']);

        $valid = $this->validateToken($user_id,$token);
        if(!$valid){
            return response()->json([
                'status'    => 0,
                'message'   => 'Invalid access token!',
                'data'      => (object)array()
            ]);
        }

        /* Service request object */
        $serviceRequest = new ServiceRequest();

        /* Assigned request list */
        $response = (object)$serviceRequest->getTechnicianResolvedRequest($user_id);
        
        return response()->json([
            'status'    => 1,
            'message'   => '',
            'data'      => $response
        ]);
       
    }
}