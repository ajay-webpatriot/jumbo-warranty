<?php

namespace App\Http\Controllers\Api;

use App\User;
use Validator;
use Hash;
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
            'access_token'   => 'required'
        ]);

        if($validator->fails()){
            return response()->json([
                'status'    => 0,
                'message'   => 'Parameter missing: user_id,access_token.',
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

        /* Validate token and user id*/
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

    public function setRequestStatus()
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
            'is_accept' => 'required'
        ]);

        if($validator->fails()){
            return response()->json([
                'status'    => 0,
                'message'   => 'Parameter missing: is_accept',
                'data'      => (object)array()
            ]);
        }

        $user_id = trim($json['user_id']);
        $token  = trim($json['access_token']);

        /* Validate token and user id*/
        $valid = $this->validateToken($user_id,$token);

        $status = $json['is_accept'];
        $request_id = $json['service_request_id'];

        /* Service request object */
        $serviceRequest = new ServiceRequest();

        if(!$valid){
            return response()->json([
                'status'    => 0,
                'message'   => 'Invalid access token!',
                'data'      => (object)array()
            ]);
        }

        /* Request status */
        $StatusChangeResponse = $serviceRequest->requestStatus($request_id,$status);
        
        if($StatusChangeResponse == 1){

            /* Service request object, all data */
            $serviceRequestDetail = ServiceRequest::findOrFail($request_id);

            /* Service additional charge */
            $additional_charge_array=json_decode($serviceRequestDetail['additional_charges']);
            $additional_charge_title="";
            $additional_charges="";

            if(!empty($additional_charge_array))
            {
                /* Worked to display json value in edit page */ 
                foreach ($additional_charge_array as $key => $value) {
                    $additional_charge_title = str_replace('_empty_', '', $key);
                    $additional_charges      = $value;
                }
            }
            
            $serviceRequestDetail->additional_charges = $additional_charges;

            if(!empty($additional_charge_title) && !empty($service_request->additional_charges)){
                $additionalCharges = $serviceRequestDetail->additional_charges;
                $response->additionalChargesFor = $serviceRequestDetail->additional_chargesFor;
            }else{
                $additionalCharges = 0;
                $response->additionalChargesFor = '';
            }

            /* Ttransportation charge */
            if($serviceRequestDetail->transportation_charge > 0){
                $transportationCharges  = $serviceRequestDetail->transportation_charge;
                $kilometersCharges      = $serviceRequestDetail->km_charge;
            }else{
                $transportationCharges  = 0;
                $kilometersCharges      = 0;
            }

            $address_1 = '';
            $address_2 = '';
            $city      = '';
            $state     = '';
            $zipcode   = '';

            /* Check blank address line one */
            if($serviceRequestDetail->customer->address_1 != '' || $serviceRequestDetail->customer->address_1 != NULL){
                $address_1 = $serviceRequestDetail->customer->address_1.',';
            }

            /* Check blank address line two */
            if($serviceRequestDetail->customer->address_2 != '' || $serviceRequestDetail->customer->address_2 != NULL){
                $address_2 = $serviceRequestDetail->customer->address_2.',';
            }

            /* Check blank city */
            if($serviceRequestDetail->customer->city != '' || $serviceRequestDetail->customer->city != NULL){
                $city = $serviceRequestDetail->customer->city.',';
            }

            /* Check blank state */
            if($serviceRequestDetail->customer->state != '' || $serviceRequestDetail->customer->state != NULL){
                $state = $serviceRequestDetail->customer->state.',';
            }

            /* Check blank zipcode */
            if($serviceRequestDetail->customer->zipcode != '' || $serviceRequestDetail->customer->zipcode != NULL){
                $state = $serviceRequestDetail->customer->zipcode.'.';
            }
            
            /* Overview data */
            $overview = (object)array(
                "product_title" => ucfirst($serviceRequestDetail->service_type).' - '.$serviceRequestDetail->product->name,
                "created_at"    => date('Y-m-d H:i:s',strtotime($serviceRequestDetail->created_at)),
                "address"       => trim($address_1.''.$address_2.''.$city.''.$state.''.$zipcode)
            );   
            
            $response->overview = $overview;

            /* Service request status */
            $response->serviceRequestCurrentStatus = $serviceRequestDetail->status;

            $response->serviceRequestStatusList = (object)array();

            /* Service request type according to service request status */
            if($serviceRequestDetail->service_type == 'installation'){

                /* Service request status (Type = 'installation') */
                $response->serviceRequestStatusList = ServiceRequest::$enum_installation_status;

            }else if($serviceRequestDetail->service_type == 'repair'){

                /* Service request status (Type = 'repair') */
                $response->serviceRequestStatusList = ServiceRequest::$enum_repair_status;
            }

            /* Unset customer data */
            unset($serviceRequestDetail->customer->created_at);
            unset($serviceRequestDetail->customer->updated_at);

            /* Unset service center data */
            unset($serviceRequestDetail->service_center->created_at);
            unset($serviceRequestDetail->service_center->updated_at);

            /* Unset product data */
            unset($serviceRequestDetail->product->created_at);
            unset($serviceRequestDetail->product->updated_at);

            /* Unset product data */
            foreach ($serviceRequestDetail->servicerequestlog as $key => $unsetvalue) {
                unset($unsetvalue->action_made);
                unset($unsetvalue->action_made_company);
            }
            
            /* Customer data */
            $customer           = $serviceRequestDetail->customer;
            $response->customer = $customer;

            /* Service center data */
            $serviceCenter           = $serviceRequestDetail->service_center;
            $response->serviceCenter = $serviceCenter;

            /* Technician data */
            $response->technician_name = $serviceRequestDetail->technician->name;

            /* Call data */
            $response->call_type     = $serviceRequestDetail->call_type;
            $response->call_location = $serviceRequestDetail->call_location;
            $response->callPriority  = $serviceRequestDetail->priority;

            /* Product data */
            $product           = $serviceRequestDetail->product;
            $response->product = $product;

            /* Other data */
            $response->is_item_in_warrenty  = $serviceRequestDetail->is_item_in_warrenty;
            $response->model_no             = $serviceRequestDetail->model_no;
            $response->serial_no            = $serviceRequestDetail->serial_no;
            $response->mop                  = $serviceRequestDetail->mop;
            $response->purchase_from        = $serviceRequestDetail->purchase_from;
            $response->make                 = $serviceRequestDetail->make;
            $response->bill_date            = $serviceRequestDetail->bill_date;
            $response->note                 = $serviceRequestDetail->note;
            $response->service_type         = $serviceRequestDetail->service_type;

            /* Complain data */
            $complain = $serviceRequestDetail->complain_details;  
            $response->complain = $complain;

            /* Completion date */
            $response->completion_date = $serviceRequestDetail->completion_date;

            /* Service request log data */
            $servicerequestlog = $serviceRequestDetail->servicerequestlog;

            foreach ($servicerequestlog as $key => $servicerequestlogSingleValue) {
                $servicerequestlog[$key]->action_taken_by = $servicerequestlogSingleValue->user->name;
            }

            $response->serviceRequestLog = (object)$servicerequestlog;

            /* All charges */
            $charges = (object)array(
                "serviceCharge"             => $serviceRequestDetail->service_charge,
                "installationCharge"        => $serviceRequestDetail->installation_charge,
                "kilometersCharges"         => $kilometersCharges,
                "transportationCharges"     => $transportationCharges,
                "additionalCharges"         => $additionalCharges,
                "totalAmount"               => $serviceRequestDetail->amount
            );

            $response->charges = $charges;

            $status  = 1;
            $message = 'Request status change';
        }

        return response()->json([
            'status'    => $status,
            'message'   => $message,
            'data'      => $response
        ]);
    }

    public function getRequestDetail()
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
            'service_request_id' => 'required'
        ]);

        if($validator->fails()){
            return response()->json([
                'status'    => 0,
                'message'   => 'Parameter missing: service_request_id!',
                'data'      => (object)array()
            ]);
        }

        $user_id = trim($json['user_id']);
        $token   = trim($json['access_token']);

        /* Validate token and user id*/
        $valid = $this->validateToken($user_id,$token);

        $serviceRequestId = trim($json['service_request_id']);
       
        $serviceRequest = new ServiceRequest();

        if(!$valid){
            return response()->json([
                'status'    => 0,
                'message'   => 'Invalid access token!',
                'data'      => (object)array()
            ]);
        }

        if(isset($serviceRequestId) && $serviceRequestId != '' && $serviceRequestId != 0){

            /* Service request object, all data */
            $serviceRequestDetail = ServiceRequest::findOrFail($serviceRequestId);

            /* Service additional charge */
            $additional_charge_array=json_decode($serviceRequestDetail['additional_charges']);
            $additional_charge_title="";
            $additional_charges="";

            if(!empty($additional_charge_array))
            {
                /* Worked to display json value in edit page */ 
                foreach ($additional_charge_array as $key => $value) {
                    $additional_charge_title = str_replace('_empty_', '', $key);
                    $additional_charges      = $value;
                }
            }
            
            $serviceRequestDetail->additional_charges = $additional_charges;

            if(!empty($additional_charge_title) && !empty($service_request->additional_charges)){
                $additionalCharges = $serviceRequestDetail->additional_charges;
                $response->additionalChargesFor = $serviceRequestDetail->additional_chargesFor;
            }else{
                $additionalCharges = 0;
                $response->additionalChargesFor = '';
            }

            /* Ttransportation charge */
            if($serviceRequestDetail->transportation_charge > 0){
                $transportationCharges  = $serviceRequestDetail->transportation_charge;
                $kilometersCharges      = $serviceRequestDetail->km_charge;
            }else{
                $transportationCharges  = 0;
                $kilometersCharges      = 0;
            }

            $address_1 = '';
            $address_2 = '';
            $city      = '';
            $state     = '';
            $zipcode   = '';

            /* Check blank address line one */
            if($serviceRequestDetail->customer->address_1 != '' || $serviceRequestDetail->customer->address_1 != NULL){
                $address_1 = $serviceRequestDetail->customer->address_1.',';
            }

            /* Check blank address line two */
            if($serviceRequestDetail->customer->address_2 != '' || $serviceRequestDetail->customer->address_2 != NULL){
                $address_2 = $serviceRequestDetail->customer->address_2.',';
            }

            /* Check blank city */
            if($serviceRequestDetail->customer->city != '' || $serviceRequestDetail->customer->city != NULL){
                $city = $serviceRequestDetail->customer->city.',';
            }

            /* Check blank state */
            if($serviceRequestDetail->customer->state != '' || $serviceRequestDetail->customer->state != NULL){
                $state = $serviceRequestDetail->customer->state.',';
            }

            /* Check blank zipcode */
            if($serviceRequestDetail->customer->zipcode != '' || $serviceRequestDetail->customer->zipcode != NULL){
                $state = $serviceRequestDetail->customer->zipcode.'.';
            }
            
            /* Overview data */
            $overview = (object)array(
                "product_title" => ucfirst($serviceRequestDetail->service_type).' - '.$serviceRequestDetail->product->name,
                "created_at"    => date('Y-m-d H:i:s',strtotime($serviceRequestDetail->created_at)),
                "address"       => trim($address_1.''.$address_2.''.$city.''.$state.''.$zipcode)
            );   
            
            $response->overview = $overview;

            /* Service request status */
            $response->serviceRequestCurrentStatus = $serviceRequestDetail->status;

            $response->serviceRequestStatusList = (object)array();

            /* Service request type according to service request status */
            if($serviceRequestDetail->service_type == 'installation'){

                /* Service request status (Type = 'installation') */
                $response->serviceRequestStatusList = ServiceRequest::$enum_installation_status;

            }else if($serviceRequestDetail->service_type == 'repair'){

                /* Service request status (Type = 'repair') */
                $response->serviceRequestStatusList = ServiceRequest::$enum_repair_status;
            }

            /* Unset customer data */
            unset($serviceRequestDetail->customer->created_at);
            unset($serviceRequestDetail->customer->updated_at);

            /* Unset service center data */
            unset($serviceRequestDetail->service_center->created_at);
            unset($serviceRequestDetail->service_center->updated_at);

            /* Unset product data */
            unset($serviceRequestDetail->product->created_at);
            unset($serviceRequestDetail->product->updated_at);

            /* Unset product data */
            foreach ($serviceRequestDetail->servicerequestlog as $key => $unsetvalue) {
                unset($unsetvalue->action_made);
                unset($unsetvalue->action_made_company);
            }
            
            /* Customer data */
            $customer           = $serviceRequestDetail->customer;
            $response->customer = $customer;

            /* Service center data */
            $serviceCenter           = $serviceRequestDetail->service_center;
            $response->serviceCenter = $serviceCenter;

            /* Technician data */
            $response->technician_name = $serviceRequestDetail->technician->name;

            /* Call data */
            $response->call_type     = $serviceRequestDetail->call_type;
            $response->call_location = $serviceRequestDetail->call_location;
            $response->callPriority  = $serviceRequestDetail->priority;

            /* Product data */
            $product           = $serviceRequestDetail->product;
            $response->product = $product;

            /* Other data */
            $response->is_item_in_warrenty  = $serviceRequestDetail->is_item_in_warrenty;
            $response->model_no             = $serviceRequestDetail->model_no;
            $response->serial_no            = $serviceRequestDetail->serial_no;
            $response->mop                  = $serviceRequestDetail->mop;
            $response->purchase_from        = $serviceRequestDetail->purchase_from;
            $response->make                 = $serviceRequestDetail->make;
            $response->bill_date            = $serviceRequestDetail->bill_date;
            $response->note                 = $serviceRequestDetail->note;
            $response->service_type         = $serviceRequestDetail->service_type;

            /* Complain data */
            $complain = $serviceRequestDetail->complain_details;  
            $response->complain = $complain;

            /* Completion date */
            $response->completion_date = $serviceRequestDetail->completion_date;

            /* Service request log data */
            $servicerequestlog = $serviceRequestDetail->servicerequestlog;

            foreach ($servicerequestlog as $key => $servicerequestlogSingleValue) {
                $servicerequestlog[$key]->action_taken_by = $servicerequestlogSingleValue->user->name;
            }

            $response->serviceRequestLog = (object)$servicerequestlog;

            /* All charges */
            $charges = (object)array(
                "serviceCharge"             => $serviceRequestDetail->service_charge,
                "installationCharge"        => $serviceRequestDetail->installation_charge,
                "kilometersCharges"         => $kilometersCharges,
                "transportationCharges"     => $transportationCharges,
                "additionalCharges"         => $additionalCharges,
                "totalAmount"               => $serviceRequestDetail->amount
            );

            $response->charges = $charges;

            $status = 1;
            $message = '';
        }

        return response()->json([
            'status'    => $status,
            'message'   => $message,
            'data'      => $response
        ]);
    }

    public function changepassword()
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
            'new_password'  => 'required',
            'old_password'  => 'required',
            'user_id'       => 'required',
            'access_token'  => 'required',
        ]);

        if($validator->fails()){
            return response()->json([
                'status'    => 0,
                'message'   => 'Parameters missing!',
                'data'      => (object)array()
            ]);
        }

        /* Validate token and user id*/
        $valid = $this->validateToken(trim($json['user_id']),trim($json['access_token']));
        
        if(!$valid){
            return response()->json([
                'status'    => 0,
                'message'   => 'Invalid access token!',
                'data'      => (object)array()
            ]);
        }

        $user_id     = trim($json['user_id']);
        $token       = trim($json['access_token']);
        $oldPassword = trim($json['old_password']);
        $newPassword = trim($json['new_password']);
        
        $checkOldPassword = User::where('id',$user_id)
        ->where('access_token',$token)
        ->first();

        /* Old password check */
        $passwordExist = Hash::check($oldPassword, $checkOldPassword->password);
        
        if($passwordExist == 1){

            /* Update password */
            $updateArray = array(
                'password' => Hash::make($newPassword)
            );

            /* Update query */
            $updateOldpassword = User::where('id',$user_id)
            ->where('access_token',$token)
            ->update($updateArray);

            if($updateOldpassword == 1){
                $status  = 1;
                $message = 'Password change successfully.';
                $response = (object)array();
            }
            
        }else{
            $message = "Incorrect Old password!";
        }

        return response()->json([
            'status'    => $status,
            'message'   => $message,
            'data'      => $response
        ]);
    }

    public function setfirebasetoken()
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
            'firebase_token'  => 'required',
            'user_id'         => 'required',
            'access_token'    => 'required'
        ]);

        if($validator->fails()){
            return response()->json([
                'status'    => 0,
                'message'   => 'Parameters missing!',
                'data'      => (object)array()
            ]);
        }

        $user_id        = trim($json['user_id']);
        $token          = trim($json['access_token']);
        $firebaseToken  = trim($json['firebase_token']);

        /* Validate token and user id*/
        $valid = $this->validateToken($user_id,$token);
        
        if(!$valid){
            return response()->json([
                'status'    => 0,
                'message'   => 'Invalid access token!',
                'data'      => (object)array()
            ]);
        }

        /* Update firebase token */
        $updateArray = array(
            'firebase_token' => $firebaseToken
        );

        $updateFirbaseToken = User::where('id',$user_id)
        ->where('access_token',$token)
        ->update($updateArray);

        if($updateFirbaseToken == 1){
            $status  = 1;
            $message = 'Successfully set firebase token';
            $response = (object)array();
        }

        return response()->json([
            'status'    => $status,
            'message'   => $message,
            'data'      => $response
        ]);

    }
}