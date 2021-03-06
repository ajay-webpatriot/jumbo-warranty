<?php

namespace App\Http\Controllers\Api;

use App\User;
use Validator;
use Hash;
use App\ServiceRequest;
use SendMailHelper;
use CommonFunctionsHelper;
use App\ServiceRequestLog;
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

            // if(count($user) > 0){
            if(!empty($user) > 0){
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
            'is_accept'          => 'required',
            'service_request_id' => 'required',
            'user_id'            => 'required',
            'access_token'       => 'required'
        ]);

        if($validator->fails()){
            return response()->json([
                'status'    => 0,
                'message'   => 'Parameter missing',
                'data'      => (object)array()
            ]);
        }

        $user_id = trim($json['user_id']);
        $token  = trim($json['access_token']);

        /* Validate token and user id*/
        $valid = $this->validateToken($user_id,$token);

        $is_Accept = $json['is_accept'];
        $serviceRequestId = $json['service_request_id'];

        /* Service request object */
        $serviceRequest = new ServiceRequest();

        if(!$valid){
            return response()->json([
                'status'    => 0,
                'message'   => 'Invalid access token!',
                'data'      => (object)array()
            ]);
        }

        /* Check request assign or not */
        $requestAssigend = ServiceRequest::where('id',$serviceRequestId)
        ->where('technician_id','=',$user_id)
        ->where('status','!=','Closed')
        ->get()->toArray();
        
        if($requestAssigend == '' || empty($requestAssigend) || count($requestAssigend) < 0){

            return response()->json([
                'status'    => 0,
                'message'   => 'Request not assign yet!',
                'data'      => (object)array()
            ]);
        }

        /* Request status */
        $StatusChangeResponse = $serviceRequest->requestStatus($serviceRequestId,$is_Accept);
        
        if($StatusChangeResponse == 1){

            $requestdetail = $this->getRequestDetailJson_v2($serviceRequestId);
            if($requestdetail != ''){

                $technician_name = User::where('role_id',config('constants.TECHNICIAN_ROLE_ID'))
                ->where('id',$user_id)
                ->first();

                $technician_name=ucwords($technician_name->name);

                /**
                 * send request status mail.
                 */
                $url = config('constants.APP_URL').'/sendMailCurl';
                $postFields = array(
                    'functionName' => 'requestStatusApi',
                    'servicerequestId' => $serviceRequestId,
                    'technicianName' => $technician_name
                );
                $jsondata = CommonFunctionsHelper::postCURL($url,$postFields);

                // SendMailHelper::sendRequestAcceptRejectMail($serviceRequestId,$technician_name);

                $status = 1;
                $message = 'Request status change';
                $response = $requestdetail;
            }
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

        /* Check request assign or not */
        $requestAssigend = ServiceRequest::where('id',$serviceRequestId)
        ->where('technician_id','=',$user_id)
        ->get()->toArray();

        if($requestAssigend == '' || empty($requestAssigend) || count($requestAssigend) < 0){

            return response()->json([
                'status'    => 0,
                'message'   => 'Request not assign yet!',
                'data'      => (object)array()
            ]);
        }
       
        $serviceRequest = new ServiceRequest();

        if(!$valid){
            return response()->json([
                'status'    => 0,
                'message'   => 'Invalid access token!',
                'data'      => (object)array()
            ]);
        }

        if(isset($serviceRequestId) && $serviceRequestId != '' && $serviceRequestId != 0){

            /* Get request data */
            $requestdetail = $this->getRequestDetailJson($serviceRequestId);
            if($requestdetail != ''){
                $status = 1;
                $message = '';
                $response = $requestdetail;
            }
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

    public function updateRequestDetail()
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
            'service_request_id'  => 'required',
            'request_status'      => 'required',
            'access_token'        => 'required',
            'user_id'             => 'required',
        ]);

        if($validator->fails()){
            return response()->json([
                'status'    => 0,
                'message'   => 'Parameters missing!',
                'data'      => (object)array()
            ]);
        }
        $user_id        = trim($json['user_id']);
        $access_token   = trim($json['access_token']);
        $request_status = trim($json['request_status']);

        /* Validate token and user id*/
        $valid = $this->validateToken($user_id,$access_token);
        
        if(!$valid){
            return response()->json([
                'status'    => 0,
                'message'   => 'Invalid access token!',
                'data'      => (object)array()
            ]);
        }

        $serviceRequestId = trim($json['service_request_id']);

        /* Check request assign or not */
        $requestAssigend = ServiceRequest::where('id',$serviceRequestId)
        ->where('technician_id','=',$user_id)
        ->get()->toArray();

        if($requestAssigend == '' || empty($requestAssigend) || count($requestAssigend) < 0){

            return response()->json([
                'status'    => 0,
                'message'   => 'Request not assign yet!',
                'data'      => (object)array()
            ]);
        }

        /* Service request object, all data */
        $serviceRequestDetail = ServiceRequest::findOrFail($serviceRequestId);

        $additional_charge_title = [];
        $additional_charge = [];
        $predefine_additional_charge_array['option'] = [];
        $predefine_additional_charge_array['other'] = [];
        
        $total_amount=$serviceRequestDetail->installation_charge + $serviceRequestDetail->service_charge;
        $total_amount+=($serviceRequestDetail->transportation_charge == "") ? 0 : number_format((float)$serviceRequestDetail->transportation_charge, 2, '.', '');

        $pre_additional_charge_array = config('constants.PRE_ADDITIONAL_CHARGES_FOR');
        $additional_charge_array=json_decode($serviceRequestDetail->additional_charges);

        if(isset($additional_charge_array->option) && !empty($additional_charge_array->option)){

            foreach ($additional_charge_array->option as $OptionKey => $value) {
                
                $AdditionalChargeTitle =  key((array)$value);
                
                foreach($pre_additional_charge_array as $PreArrayKey => $arr_val){
                    
                    if($AdditionalChargeTitle === $arr_val){
                        
                        $predefine_additional_charge_array['option'][$OptionKey] = array($arr_val => number_format((float)$value->$arr_val, 2, '.', ''));
                        $total_amount+= $value->$arr_val;
                    }
                }
            }
        }

        if(isset($json['additionalChargesFor']) && !empty($json['additionalChargesFor']) && $json['additionalChargesFor'] != ''){
            if(isset($json['additionalCharges']) && !empty($json['additionalCharges']) && $json['additionalCharges'] != 0){

                
                
                // // $total_amount=$serviceRequestDetail->installation_charge + $serviceRequestDetail->service_charge +(($json['additionalCharges'] == "")?0:number_format((float)$json['additionalCharges'], 2, '.', ''));
                // $total_amount+=(($json['additionalCharges'] == "")?0:number_format((float)$json['additionalCharges'], 2, '.', ''));

                // // $total_amount += $serviceRequestDetail->transportation_charge;
                
                // $additional_charges = json_encode(array($json['additionalChargesFor'] => number_format((float)$json['additionalCharges'], 2, '.', '')));

                

                $total_amount+=(($json['additionalCharges'] == "")?0:number_format((float)$json['additionalCharges'], 2, '.', ''));

                $predefine_additional_charge_array['other'] = array($json['additionalChargesFor'] => number_format((float)$json['additionalCharges'], 2, '.', ''));
            }
        }
        // $additional_charges = json_encode(array($json['additionalChargesFor'] => number_format((float)$json['additionalCharges'], 2, '.', '')));
        $additional_charges =  json_encode($predefine_additional_charge_array);

        $serviceRequestDetail->additional_charges = $additional_charges;
        $serviceRequestDetail->amount = $total_amount;
        $serviceRequestDetail->update();

        /* Update service request status */
        $serviceRequestDetailStatusUpdate = ServiceRequest::findOrFail($serviceRequestId);
        $serviceRequestDetail->status     = $request_status;
        $serviceRequestDetail->update();

        /* Get request data */
        $requestdetail = $this->getRequestDetailJson($serviceRequestId);

        if($requestdetail != ''){
            $status = 1;
            $message = 'Request status changed';
            $response = $requestdetail;
        }

        return response()->json([
            'status'    => $status,
            'message'   => $message,
            'data'      => $response
        ]);
    }

    public function getRequestDetailJson($serviceRequestId)
    {
        if(!isset($serviceRequestId) && empty($serviceRequestId)){
            return response()->json([
                'data' => (object)array()
            ]);
        }

        $response = (object)array();

        /* Service request object, all data */
        $serviceRequestDetail = ServiceRequest::findOrFail($serviceRequestId);

        /* Service additional charge */
        $additional_charge_title = "";
        $additional_charges      = "";
        $additional_charge_array = json_decode($serviceRequestDetail['additional_charges']);

        if(!empty($additional_charge_array->other))
        {
            /* Worked to display json value in edit page */ 
            foreach ($additional_charge_array->other as $key => $value) {
                $additional_charge_title = str_replace('_empty_', '', $key);
                $additional_charges      = $value;
            }
        }
        
        $serviceRequestDetail->additional_charges = $additional_charges;

        if(!empty($additional_charge_title) && !empty($serviceRequestDetail->additional_charges)){
            $additionalCharges = $serviceRequestDetail->additional_charges;
            $response->additionalChargesFor = $additional_charge_title;
        }else{
            $additionalCharges = number_format((float)00, 2, '.', '');
            $response->additionalChargesFor = '';
        }

        /* Ttransportation charge */
        if($serviceRequestDetail->transportation_charge > 0){
            $transportationCharges  = $serviceRequestDetail->transportation_charge;
            $kilometersCharges      = $serviceRequestDetail->km_charge;
        }else{
            $transportationCharges  = number_format((float)00, 2, '.', '');
            $kilometersCharges      = number_format((float)00, 2, '.', '');
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
            $zipcode = $serviceRequestDetail->customer->zipcode.'.';
        }
        
        /* Overview data */
        $overview = (object)array(
            "product_title" => ucfirst($serviceRequestDetail->service_type).' - '.$serviceRequestDetail->product->name,
            "created_at"    => date('Y-m-d H:i:s',strtotime($serviceRequestDetail->created_at)),
            "address"       => trim($address_1.''.$address_2.''.$city.''.$state.''.$zipcode),
            "service_request_number" => 'JW'.sprintf("%04d", $serviceRequestDetail->id)
        );   
        
        $response->overview = $overview;

        /* Service request status */
        $response->serviceRequestCurrentStatus = $serviceRequestDetail->status;

        /* Service request status is accepted */
        $response->is_accepted = $serviceRequestDetail->is_accepted;

        $response->serviceRequestStatusList = (object)array();

        /* Service request type according to service request status */
        if($serviceRequestDetail->service_type == 'installation'){

            /* Service request status (Type = 'installation') */
            $changeKey = ServiceRequest::$enum_technician_installation_status;
            $newStatusarray = array_values($changeKey);

        }else if($serviceRequestDetail->service_type == 'repair'){

            /* Service request status (Type = 'repair') */
            $changeKey = ServiceRequest::$enum_technician_repair_status;
            $newStatusarray = array_values($changeKey);
        }

        $newStatusArrayChangeKey = array();

        for ($i=0; $i <count($newStatusarray) ; $i++) { 
            $newStatusArrayChangeKey[$i] = $newStatusarray[$i];
        }

        $response->serviceRequestStatusList = (object)$newStatusArrayChangeKey;

        /* Status color */
        $response->serviceRequestStatusColor = ServiceRequest::$enum_status_color_code;

        /* Unset customer data */
        unset($serviceRequestDetail->customer->created_at);
        unset($serviceRequestDetail->customer->updated_at);

        /* Unset service center data */
        unset($serviceRequestDetail->service_center->created_at);
        unset($serviceRequestDetail->service_center->updated_at);

        /* Unset product data */
        unset($serviceRequestDetail->product->created_at);
        unset($serviceRequestDetail->product->updated_at);

        /* Unset servicerequestlog data */
        foreach ($serviceRequestDetail->servicerequestlog as $key => $unsetvalue) {
            unset($unsetvalue->action_made);
            unset($unsetvalue->action_made_company);
        }
        
        /* Customer data */
        $response->customer = $serviceRequestDetail->customer;

        /* Service center data */
        $response->serviceCenter = $serviceRequestDetail->service_center;

        /* Technician data */
        $response->technician_name = '';
        if(!empty($serviceRequestDetail->technician)){
            $response->technician_name = $serviceRequestDetail->technician->name;
        }

        /* Call data */
        $response->call_type     = $serviceRequestDetail->call_type;
        $response->call_location = $serviceRequestDetail->call_location;
        $response->callPriority  = $serviceRequestDetail->priority;

        /* Product data */
        $response->product = $serviceRequestDetail->product;

        /* Product parts data*/
        $product_parts = array();
        if($serviceRequestDetail->service_type == 'repair'){
            $product_parts = (object)$serviceRequestDetail->parts;
        }
        $response->product_parts = $product_parts;
        
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
        $response->bill_no              = $serviceRequestDetail->bill_no;

        /* Complain data */
        $response->complain = $serviceRequestDetail->complain_details;

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

        return $response;
    }

    public function getRequestDetail_v2()
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

        /* Check request assign or not */
        $requestAssigend = ServiceRequest::where('id',$serviceRequestId)
        ->where('technician_id','=',$user_id)
        ->get()->toArray();

        if($requestAssigend == '' || empty($requestAssigend) || count($requestAssigend) < 0){

            return response()->json([
                'status'    => 0,
                'message'   => 'Request not assign yet!',
                'data'      => (object)array()
            ]);
        }
       
        $serviceRequest = new ServiceRequest();

        if(!$valid){
            return response()->json([
                'status'    => 0,
                'message'   => 'Invalid access token!',
                'data'      => (object)array()
            ]);
        }

        if(isset($serviceRequestId) && $serviceRequestId != '' && $serviceRequestId != 0){

            /* Get request data */
            $requestdetail = $this->getRequestDetailJson_v2($serviceRequestId);
            if($requestdetail != ''){
                $status = 1;
                $message = '';
                $response = $requestdetail;
            }
        }

        return response()->json([
            'status'    => $status,
            'message'   => $message,
            'data'      => $response
        ]);
    }

    public function updateRequestDetail_v2()
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
            'service_request_id'  => 'required',
            'request_status'      => 'required',
            'access_token'        => 'required',
            'user_id'             => 'required',
        ]);

        if($validator->fails()){
            return response()->json([
                'status'    => 0,
                'message'   => 'Parameters missing!',
                'data'      => (object)array()
            ]);
        }
        $user_id        = trim($json['user_id']);
        $access_token   = trim($json['access_token']);
        $request_status = trim($json['request_status']);

        /* Validate token and user id*/
        $valid = $this->validateToken($user_id,$access_token);
        
        if(!$valid){
            return response()->json([
                'status'    => 0,
                'message'   => 'Invalid access token!',
                'data'      => (object)array()
            ]);
        }

        $serviceRequestId = trim($json['service_request_id']);

        /* Check request assign or not */
        $requestAssigend = ServiceRequest::where('id',$serviceRequestId)
        ->where('technician_id','=',$user_id)
        ->get()->toArray();

        if($requestAssigend == '' || empty($requestAssigend) || count($requestAssigend) < 0){

            return response()->json([
                'status'    => 0,
                'message'   => 'Request not assign yet!',
                'data'      => (object)array()
            ]);
        }

        /* Service request object, all data */
        $serviceRequestDetail = ServiceRequest::findOrFail($serviceRequestId);
        $pre_additional_charge_array = config('constants.PRE_ADDITIONAL_CHARGES_FOR');

        $additional_charges = NULL;
        $additional_charge_title = NULL;
        $total_amount=$serviceRequestDetail->installation_charge + $serviceRequestDetail->service_charge;
        $total_amount+=($serviceRequestDetail->transportation_charge == "") ? 0 : number_format((float)$serviceRequestDetail->transportation_charge, 2, '.', '');

        if(isset($json['additionalChargesFor']) && $json['additionalChargesFor'] != ''){

            $additional_charges_both['option'] = [];
            $additional_charges_both['other'] = [];

            if(isset($json['additionalChargesFor']['option']) && $json['additionalChargesFor']['option'] != ''){

                foreach ($json['additionalChargesFor']['option'] as $OptionKey => $OptionValue) {

                    if(isset($OptionValue['label']) && isset($OptionValue['amount'])){
                        if((!empty($OptionValue['label']) && $OptionValue['label'] != '') && ((!empty($OptionValue['amount']) && $OptionValue['amount'] != '')) && $OptionValue['amount'] > 0){
                    
                            $additional_charges_both['option'][$OptionKey] =  array($OptionValue['label'] => number_format((float)$OptionValue['amount'], 2, '.', ''));

                            $total_amount+=(($OptionValue['amount'] == "")?0:number_format((float)$OptionValue['amount'], 2, '.', ''));
                        }
                    }

                }
            }
            
            if(isset($json['additionalChargesFor']['other']) && $json['additionalChargesFor']['other'] != ''){

                $otherCharges = $json['additionalChargesFor']['other'];

                if(isset($otherCharges['label']) && isset($otherCharges['amount'])){
                    if((!empty($otherCharges['label']) && $otherCharges['label'] != '') && ((!empty($otherCharges['amount']) && $otherCharges['amount'] != '')) && $otherCharges['amount'] > 0){
                        
                        $additional_charges_both['other'] =  array($otherCharges['label'] => number_format((float)$otherCharges['amount'], 2, '.', ''));

                        $total_amount+=(($otherCharges['amount'] == "")?0:number_format((float)$otherCharges['amount'], 2, '.', ''));
                    }
                }
            }
            $additional_charges= json_encode($additional_charges_both);
        }
        
        /* old additional charge store procedure */
        // if(isset($json['additionalChargesFor']) && $json['additionalChargesFor'] != ''){

        //     if(isset($json['additionalCharges']) && !empty($json['additionalCharges']) && $json['additionalCharges'] != 0){
        //         $additional_charge_title['option'] = [];
        //         $additional_charges['option'] = [];
        //         if((isset($json['additionalChargesFor']['option']) && !empty($json['additionalChargesFor']['option'])) && (isset($json['additionalCharges']['option']) && !empty($json['additionalCharges']['option'])) && $json['additionalCharges']['option'] != 0){

        //             foreach ($json['additionalChargesFor']['option'] as $OptionKey => $value) {
                
        //                 if($value != ''){
        //                     if(isset($json['additionalCharges']['option'][$OptionKey])){
                            
        //                         $additional_charge_title['option'][$OptionKey]= $value;
        //                         $additional_charges['option'][$OptionKey] = $json['additionalCharges']['option'][$OptionKey];

        //                         $total_amount+=(($json['additionalCharges']['option'][$OptionKey] == "")?0:number_format((float)$json['additionalCharges']['option'][$OptionKey], 2, '.', ''));

        //                         $additional_charges['option'][$OptionKey] = array($json['additionalChargesFor']['option'][$OptionKey] => number_format((float)$json['additionalCharges']['option'][$OptionKey], 2, '.', ''));
        //                     }
        //                 }
        //             } 
        //         }

        //         $additional_charge_title['other'] = [];
        //         $additional_charges['other'] = [];
        //         if(isset($json['additionalChargesFor']['other']) && !empty($json['additionalChargesFor']['other'])){

        //             if(isset($json['additionalCharges']['other']) && !empty($json['additionalCharges']['other']) && $json['additionalCharges']['other'] != 0){

        //                 $additional_charge_title['other']=  $json['additionalChargesFor']['other'];
        //                 $additional_charges['other'] =$json['additionalCharges']['other'];
                    

        //                 $total_amount+=(($json['additionalCharges']['other'] == "")?0:number_format((float)$json['additionalCharges']['other'], 2, '.', ''));

        //                 $additional_charges['other'] = array($json['additionalChargesFor']['other'] => number_format((float)$json['additionalCharges']['other'], 2, '.', ''));
        //             }
                    
        //         }
        //         $additional_charges= json_encode($additional_charges);
        //     }
            
        // }
    
        $serviceRequestDetail->additional_charges = $additional_charges;
        $serviceRequestDetail->amount = $total_amount;
        $serviceRequestDetail->update();

        /* Update service request status */
        $serviceRequestDetailStatusUpdate = ServiceRequest::findOrFail($serviceRequestId);

        /* service request log for status */
        if($serviceRequestDetailStatusUpdate->status != $request_status){
            $insertServiceRequestLogArr =  array(
                'action_made' => "Status is changed from ".$serviceRequestDetailStatusUpdate->status." to ".$request_status.".", 
                'action_made_company' => "Status is changed from ".$serviceRequestDetailStatusUpdate->status." to ".$request_status.".", 
                'action_made_service_center' => "Status is changed from ".$serviceRequestDetailStatusUpdate->status." to ".$request_status.".", 
                'service_request_id' => $serviceRequestId,
                'user_id' => $user_id
            );

            ServiceRequestLog::create($insertServiceRequestLogArr);

            //send mail on every status change
            $msg='Status is changed from '.$serviceRequestDetailStatusUpdate->status.' to '.$request_status.'.';

            /**
             * send request status mail.
             */
            $url = config('constants.APP_URL').'/sendMailCurl';
            $postFields = array(
                'functionName' => 'updateRequestDetailV2',
                'servicerequestId' => $serviceRequestId,
                'message' => $msg
            );
            $jsondata = CommonFunctionsHelper::postCURL($url,$postFields);

            /* send mail */
            // SendMailHelper::sendRequestUpdateMail($serviceRequestId,$msg);
        }

        $serviceRequestDetail->status = $request_status;
        $serviceRequestDetail->update();

        /* Get request data */
        $requestdetail = $this->getRequestDetailJson_v2($serviceRequestId);

        if($requestdetail != ''){
            $status = 1;
            $message = 'Request status changed';
            $response = $requestdetail;
        }

        return response()->json([
            'status'    => $status,
            'message'   => $message,
            'data'      => $response
        ]);
    }

    public function getRequestDetailJson_v2($serviceRequestId)
    {
        if(!isset($serviceRequestId) && empty($serviceRequestId)){
            return response()->json([
                'data' => (object)array()
            ]);
        }

        $response = (object)array();

        /* Service request object, all data */
        $serviceRequestDetail = ServiceRequest::findOrFail($serviceRequestId);

        /* Service additional charge */
        $additional_charge_title = "";
        $additional_charges      = "";
        $additional_charge_array = json_decode($serviceRequestDetail['additional_charges']);
       
        /* Pre additional charge array */
        $pre_additional_charge_array = config('constants.PRE_ADDITIONAL_CHARGES_FOR');

        $additional_charge_both['option'] = [];
        $additional_charge_both['other'] = (object)array();
        $otherArray = [];

        if(!empty($additional_charge_array)) {

            if(!empty($additional_charge_array->option) && isset($additional_charge_array->option)){
                foreach ($additional_charge_array->option as $OptionKey => $value) {

                    $AdditionalChargeTitle =  key((array)$value);

                    foreach($pre_additional_charge_array as $PreArrayKey => $arr_val){
                        if($AdditionalChargeTitle === $arr_val){
                            $additional_charge_both['option'][$OptionKey]['label'] = $AdditionalChargeTitle;
                            $additional_charge_both['option'][$OptionKey]['amount'] = $value->$arr_val;
                        }
                    }
                } 
            }
          
            // if((!empty($additional_charge_array->other) && isset($additional_charge_array->other)) && $additional_charge_array->other != '' ) {
   
            if(!empty((array)$additional_charge_array->other)){

                foreach ((array)$additional_charge_array->other as $key => $value) {
                    $otherArray[] = str_replace('_empty_', '', $key);
                    $otherArray[] = $value;
                }
                if(!empty($otherArray[0]) && $otherArray[1] != 0){
                    $additional_charge_both['other']->label = $otherArray[0];
                    $additional_charge_both['other']->amount = $otherArray[1];
                }else{
                    $additional_charge_both['other'] = (object)array();
                }
                // $AdditionalChargeOtherLabel =  key((array)$additional_charge_array->other);

                // $AdditionalChargeOtherAmount =  array_values((array)$additional_charge_array->other);
               
                // $additional_charge_both['other']->label = $AdditionalChargeOtherLabel;
                // $additional_charge_both['other']->amount = $AdditionalChargeOtherAmount[0];
            }
        }

        $response->additionalChargesFor = $additional_charge_both;
        // if(!empty($additional_charge_array))
        // {
        //     $additional_charge_title = [];
        //     $additional_charges = [];

        //     if(!empty($additional_charge_array->option)){
        //         foreach ($additional_charge_array->option as $OptionKey => $value) {
                    
        //             $AdditionalChargeTitle =  key((array)$value);
        //             foreach($pre_additional_charge_array as $PreArrayKey => $arr_val){
        //                 if($AdditionalChargeTitle === $arr_val){

        //                     $additional_charge_title['option'][$OptionKey] = $AdditionalChargeTitle;
        //                     $additional_charges['option'][$OptionKey] = $value->$arr_val;
                        
        //                 }
        //             }
        //         }
        //     }else{
        //         $additional_charge_title['option'] = [];
        //         $additional_charges['option'] = '0.00';
        //     }
            
        //     if(!empty($additional_charge_array->other)){
        //         foreach ($additional_charge_array->other as $key => $value) {
                    
        //             $additional_charge_title['other'] = str_replace('_empty_', '', $key);
        //             $additional_charges['other'] = $value;
        //         }                                      
        //     }else{
        //         $additional_charge_title['other'] = [];
        //         $additional_charges['other'] = '0.00';
        //     }
        // }
        
        
        // $serviceRequestDetail->additional_charges = $additional_charges;

        // $response->additionalChargesFor = $additional_charge_title;

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
            $zipcode = $serviceRequestDetail->customer->zipcode.'.';
        }
        
        /**
         * Re-open request check and set label in mobile.
         */
        $reopenRequest = '';
        if($serviceRequestDetail->is_reopen){
            $reopenRequest = ' (Re-opened)';
        }
        
        /* Overview data */
        $overview = (object)array(
            "product_title" => 'JW'.sprintf("%04d", $serviceRequestDetail->id).' '.ucfirst($serviceRequestDetail->service_type).' - '.$serviceRequestDetail->product->name.''.$reopenRequest,
            "created_at"    => date('Y-m-d H:i:s',strtotime($serviceRequestDetail->created_at)),
            "address"       => trim($address_1.''.$address_2.''.$city.''.$state.''.$zipcode),
            "service_request_number" => 'JW'.sprintf("%04d", $serviceRequestDetail->id)
        );

        $response->overview = $overview;

        /* Service request status */
        $response->serviceRequestCurrentStatus = $serviceRequestDetail->status;

        /* Service request status is accepted */
        $response->is_accepted = $serviceRequestDetail->is_accepted;

        $response->serviceRequestStatusList = (object)array();

        /* Service request type according to service request status */
        if($serviceRequestDetail->service_type == 'installation'){

            /* Service request status (Type = 'installation') */
            $changeKey = ServiceRequest::$enum_technician_installation_status;
            $newStatusarray = array_values($changeKey);

        }else if($serviceRequestDetail->service_type == 'repair'){

            /* Service request status (Type = 'repair') */
            $changeKey = ServiceRequest::$enum_technician_repair_status;
            $newStatusarray = array_values($changeKey);
        }

        $newStatusArrayChangeKey = array();

        for ($i=0; $i <count($newStatusarray) ; $i++) { 
            $newStatusArrayChangeKey[$i] = $newStatusarray[$i];
        }

        $response->serviceRequestStatusList = (object)$newStatusArrayChangeKey;

        /* Status color */
        $response->serviceRequestStatusColor = ServiceRequest::$enum_status_color_code;

        /* Unset customer data */
        unset($serviceRequestDetail->customer->created_at);
        unset($serviceRequestDetail->customer->updated_at);

        /* Unset service center data */
        unset($serviceRequestDetail->service_center->created_at);
        unset($serviceRequestDetail->service_center->updated_at);

        /* Unset product data */
        unset($serviceRequestDetail->product->created_at);
        unset($serviceRequestDetail->product->updated_at);

        /* Unset servicerequestlog data */
        foreach ($serviceRequestDetail->servicerequestlog as $key => $unsetvalue) {
            unset($unsetvalue->action_made);
            unset($unsetvalue->action_made_company);
        }
        
        /* Customer data */
        $response->customer = $serviceRequestDetail->customer;

        /* Service center data */
        $response->serviceCenter = $serviceRequestDetail->service_center;

        /* Technician data */
        $response->technician_name = '';
        if(!empty($serviceRequestDetail->technician)){
            $response->technician_name = $serviceRequestDetail->technician->name;
        }

        /* Call data */
        $response->call_type     = $serviceRequestDetail->call_type;
        $response->call_location = $serviceRequestDetail->call_location;
        $response->callPriority  = $serviceRequestDetail->priority;

        /* Product data */
        $response->product = $serviceRequestDetail->product;

        /* Product parts data*/
        $product_parts = array();
        if($serviceRequestDetail->service_type == 'repair'){
            $product_parts = (object)$serviceRequestDetail->parts;
        }
        $response->product_parts = $product_parts;
        
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
        $response->bill_no              = $serviceRequestDetail->bill_no;
      
        $serialNumber = null;
        $warrantyCardNumber = null;

        if($serviceRequestDetail->call_type == "Warranty"){
            if(!empty($serviceRequestDetail->online_serial_number)){
                $serialNumber = trim($serviceRequestDetail->online_serial_number);
            }

            if(!empty($serviceRequestDetail->warranty_card_number)){
                $warrantyCardNumber = trim($serviceRequestDetail->warranty_card_number);
            }
        }
        
        /**
         * Based on calltype card number and serial number active. 
         */
        $response->online_serial_number = $serialNumber;
        $response->warranty_card_number = $warrantyCardNumber;

        /* Complain data */
        $response->complain = $serviceRequestDetail->complain_details;

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
            // "additionalCharges"         => $additionalCharges,
            // "additionalCharges"         => $additional_charges,
            "totalAmount"               => $serviceRequestDetail->amount
        );

        $response->charges = $charges;
        $additionalChargeArray = [];
        if(!empty($pre_additional_charge_array)){
            foreach($pre_additional_charge_array as $key => $value){
                if($key != 0){
                    $additionalChargeArray[] = $value;
                }
            }
        }
        
        // $response->defaultAdditionalChargesTitle = $pre_additional_charge_array;

        $response->defaultAdditionalChargesTitle = $additionalChargeArray;

        return $response;
    }
}