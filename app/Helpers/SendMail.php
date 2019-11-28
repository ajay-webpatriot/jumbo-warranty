<?php
namespace App\Helpers;

use Illuminate\Support\Facades\Mail;
// models
use App\ServiceRequest;
use SMSHelper;

class SendMail 
{
	public static function sendRequestCreationMail($request_id){

      // Mail::send([], [], function ($message) {
      //             $message->to('hinal.webpatriot@gmail.com')
      //               ->subject('test')
      //               // here comes what you want
      //               ->setBody('Hi, welcome user!') // assuming text/plain
      //               // or:
      //               ->setBody('<h1>Hi, welcome user!</h1>', 'text/html'); // for HTML rich messages
      //       });
        $service_request = ServiceRequest::findOrFail($request_id);

    		// $service_request=$service_request[0];
    		// echo "<pre>"; print_r ($service_request); echo "</pre>"; exit();
    		// echo $service_request->additional_charges;
         // echo "<pre>"; print_r ($service_request); echo "</pre>"; exit();

        // if(auth()->user()->role_id != config('constants.COMPANY_ADMIN_ROLE_ID') && auth()->user()->role_id != config('constants.COMPANY_USER_ROLE_ID')){
         
          $additional_charge_array=json_decode($service_request['additional_charges']);
          $pre_additional_charge_array = config('constants.PRE_ADDITIONAL_CHARGES_FOR');
         
          if(!empty($additional_charge_array->option)){
              foreach ($additional_charge_array->option as $OptionKey => $value) {

                  $AdditionalChargeTitle =  key((array)$value);
                  foreach($pre_additional_charge_array as $PreArrayKey => $arr_val){
                      if($AdditionalChargeTitle === $arr_val){

                          $additional_charge_title['option'][$OptionKey] = $AdditionalChargeTitle;
                          $additional_charges['option'][$OptionKey] = $value->$arr_val;
                          // $additional_charge_title['option'][$PreArrayKey] = $AdditionalChargeTitle;
                          // $additional_charges['option'][$PreArrayKey] = $value->$arr_val;
                      
                      }
                  }
              }
          }  
         
          if(!empty($additional_charge_array->other)){
              foreach ($additional_charge_array->other as $key => $value) {
                
                  
                  $additional_charge_title['other'] = str_replace('_empty_', '', $key);
                  $additional_charges['other'] = $value;
              }                                      
          }
          // $additional_charge_title=[];
          // $additional_charges=[];
          

          // if(!empty($additional_charge_array))
          // {
          //     $keyVlaue = [];
          //     $AmountVlaue = [];
          //     if(isset($additional_charge_array->option)){
          //       foreach ($additional_charge_array->option as $key => $value) {
                
          //         $keyVlaue[$key] = key((array)$value);
          //         $Vlaue = $keyVlaue[$key];
          //         $AmountVlaue[] = $value->$Vlaue;
          //       } 
          //     }

          //     if(isset($additional_charge_array->other)){
          //       if (strpos(key((array)$additional_charge_array->other),'_empty_') === false) {
          //         array_push($keyVlaue,key((array)$additional_charge_array->other));
          //         $value = key((array)$additional_charge_array->other);
          //         $AmountVlaue[] = $additional_charge_array->other->$value;
          //       }
          //     }
          //     $additional_charge_title=$keyVlaue;
          //     $additional_charges=$AmountVlaue;
              // Worked to display json value in edit page
              // foreach ($additional_charge_array as $key => $value) {
              //     $key = $keyVlaue[$key];
              //     if (strpos($key,'_empty_') === false) {
              //       $additional_charge_title[]=str_replace('_empty_', '', $key);
              //       $additional_charges[]=$value->$key;
              //     }
              // }
          // }

          $service_request['additional_charges']=$additional_charges;
          $service_request['additional_charges_title']=$additional_charge_title;
        // }

        $admin_email= "";
        $company_admin_email = "";
        $customer_email = "";
        $customer_phone = "";

        // get receiver user data
    		$company_admin= \App\User::where('company_id',$service_request->company_id)
                                  ->where('status','Active')
                                  ->where('role_id',config('constants.COMPANY_ADMIN_ROLE_ID'))
                                  ->get()->pluck('email')->toArray();
        if(!empty($company_admin)){
          // $company_admin_email=$company_admin->email;
          $company_admin_email=$company_admin;
        }
    		

    		$customer= \App\Customer::where('id',$service_request->customer_id)
                                  ->where('status','Active')
                                  ->get()->first();
        if(!empty($customer)){
          if($customer->email != ''){
            $customer_email=$customer->email;
          }

          // Send SMS to customer.
          if(trim($customer->phone) != ''){
            
            // Remove special character allow only numbers.
            $customer_phone = preg_replace('/[^0-9]/', '', $customer->phone);

            // Message parameters. 
            $msg_requestNumber = 'JW'.sprintf("%04d", $service_request->id);
            $msg_requestType = $service_request->service_type;
            $msg_requestProductName = $service_request->product->name;

            // Message body ( sms template which was approved by provider ).
            $message_body = "We have received your service request to ".$msg_requestType." ".$msg_requestProductName.". Your service request number is ".$msg_requestNumber.".";
            
            // Send sms to customer only with fix (approve) template.
            $resultSMS = SMSHelper::sendsmscustomer($customer_phone,$message_body);
          }
        }

    		$admin= \App\User::where('role_id',config('constants.ADMIN_ROLE_ID'))
                          ->where('status','Active')
                          ->get()->pluck('email')->toArray();
        if(!empty($admin)){
          // $admin_email=$admin->email;
          $admin_email=$admin;
        }

        $receiver_email=array('admin' => $admin_email,
                              'company_admin' => $company_admin_email,
                              'customer' => $customer_email,
                              'rajdip' => 'rajdip.webpatriot@gmail.com',
                            );
        // $receiver_email=array('admin' => 'rajdipadmin@yopmail.com',
        //   'company_admin' => 'rajdipcompanyadmin@yopmail.com',
        //   'customer' => 'rajdipcustomer@yopmail.com',
        //   'rajdip' => 'rajdip.webpatriot@gmail.com',
        // );
        foreach ($receiver_email as $key => $value) {
          if(!empty($value))
          {
            $role_id = "";
            if($key == 'admin')
            {
              // $username = $admin->name;
              $role_id = config('constants.ADMIN_ROLE_ID');
            }
            else if($key == 'company_admin'){
              // $username = $company_admin->name;
              $role_id = config('constants.COMPANY_ADMIN_ROLE_ID');
            }
            else if($key == 'customer'){
              // $username = $customer->firstname.' '.$customer->lastname;
              $role_id = "customer";
            }
            $to_email=$value;
            $data=array('subject' => 'New Service Request Created',
                  // 'user_name' => ucwords($username),
                  'role_id' => $role_id,
                  'service_request' => $service_request,
                  'receiver_email' => $value
                  // 'pre_additional_charge_array' => $pre_additional_charge_array
                  );

            Mail::send('admin.emails.service_request_detail_email',$data, function ($message)  use ( $to_email){
                      $message->to($to_email)
                        ->subject('New Service Request Created')
                        // ->from('info.emailtest1@gmail.com','Jumbo-Warranty');
                        // ->from(env('MAIL_USERNAME'),env('APP_NAME'));
                        ->from(config('mail.from.address'),config('mail.from.name'));
                        // here comes what you want
                        // ->setBody('Hi, welcome user!'); // assuming text/plain
                        // or:
                        // ->setBody('<h1>Hi, welcome user!</h1>', 'text/html'); // for HTML rich messages
                }); 
          }       
        }
    		
	}

  public static function sendRequestUpdateMail($request_id,$update_message,$requestStatus = NULL){

    $service_request = ServiceRequest::findOrFail($request_id);

    \Log::info("== request result ==".json_encode( $service_request ));
   
    $customer_email = "";
    $customer_phone = "";
    $admin_email = "";
    $company_admin_email = "";
    $service_center_admin_email = "";
    $technician_email = "";

    if( $service_request->status != "Service center assigned" && $service_request->status != 'Technician assigned'){

      \Log::info("== request in if loop to send sms ==".json_encode( $service_request->status ));

      $customer = \App\Customer::where('id',$service_request->customer_id)->where('status','Active')->get()->first();

      if(!empty($customer)){
        if($customer->email != '') {
          $customer_email=$customer->email;
        }

        // Send SMS to customer.
        if(trim($customer->phone) != '') {

          // Remove special character allow only numbers. 
          $customer_phone = preg_replace('/[^0-9]/', '', $customer->phone);

          // Message parameters.
          $msg_requestNumber = 'JW'.sprintf("%04d", $service_request->id);
          $msg_requestType = $service_request->service_type;
          $msg_requestProductName = $service_request->product->name;
          $msg_requestStatus = $service_request->status;

          // Change request status.
          if(isset($requestStatus) && $requestStatus != NULL) {
            $msg_requestStatus = $requestStatus;
          }
          
          // Message body ( sms template which was approved by provider ).
          $message_body = "Your service request ".$msg_requestNumber." to ".$msg_requestType." ".$msg_requestProductName." is having status ".$msg_requestStatus.".";
          
          //Send sms to customer only with fix (approve) template.
          $resultSMS = SMSHelper::sendsmscustomer($customer_phone,$message_body);
        }
      }
    }

    $admin = \App\User::where('role_id',config('constants.ADMIN_ROLE_ID'))
    ->where('status','Active')
    ->get()->pluck('email')->toArray();

    if(!empty($admin)) {                  
      // $admin_email=$admin->email;
      $admin_email=$admin;
    }

    $receiver_email = array('admin' => $admin_email,
      // 'company_admin' => $company_admin_email,
      'rajdip' => 'rajdip.webpatriot@gmail.com'
    );

    if( $service_request->status != "Service center assigned" && $service_request->status != 'Technician assigned'){

      \Log::info("== request in if loop to send email to customer ==".json_encode( $service_request->status ));

      $receiver_email['customer'] =  $customer_email;
    }
    
    $technician = \App\User::where('id',$service_request->technician_id)
    ->where('status','Active')
    ->where('role_id',config('constants.TECHNICIAN_ROLE_ID'))
    ->get()->first();

    if($service_request->status == "Closed") {
      $company_admin = \App\User::where('company_id',$service_request->company_id)
      ->where('status','Active')
      ->where('role_id',config('constants.COMPANY_ADMIN_ROLE_ID'))
      ->get()->pluck('email')->toArray();

      if(!empty($company_admin)) {  
        // $company_admin_email=$company_admin->email;
        $company_admin_email = $company_admin;
        $receiver_email['company_admin'] = $company_admin_email;
      }

      // $technician = \App\User::where('id',$service_request->technician_id)
      // ->where('status','Active')
      // ->where('role_id',config('constants.TECHNICIAN_ROLE_ID'))
      // ->get()->first();

      if(!empty($technician)) {
        $technician_email=$technician->email;
        $receiver_email['technician'] = $technician_email;
      }
    }else if(isset($requestStatus) && $requestStatus != NULL && $requestStatus == 'Re-opened') {
     
      // Re-opened request status mail send to Technician.
      if(!empty($technician)) {
        $technician_email=$technician->email;
        $receiver_email['technician'] = $technician_email;
      }
    }else if($service_request->status == 'Technician assigned' || $service_request->status == 'Service center assigned'){
      if(!empty($technician)) {
        $technician_email=$technician->email;
        $receiver_email['technician'] = $technician_email;
      }
    }

    $service_center_admin= \App\User::where('service_center_id',$service_request->service_center_id)
    ->where('status','Active')
    ->where('role_id',config('constants.SERVICE_ADMIN_ROLE_ID'))
    ->get()->pluck('email')->toArray();

    if(!empty($service_center_admin)){

      \Log::info("== request in if loop to send email to service center admin ==".json_encode( $service_request->status ));
      
      // $service_center_admin_email=$service_center_admin->email;
      $service_center_admin_email=$service_center_admin;
      $receiver_email['service_center_admin'] = $service_center_admin_email;
    }

    // \Log::info("== email send to this array ==".json_encode( $receiver_email ));

    foreach ($receiver_email as $key => $value) {
      if(!empty($value)){ 
        $role = "";
        // if($key == 'admin')
        // {
        //   $username = $admin->name;
        // }
        // else if($key == 'company_admin'){
        //   $username = $company_admin->name;
        // }
        // else if($key == 'service_center_admin'){
        //   $username = $service_center_admin->name;
        // }
        // else if($key == 'technician'){
        //   $username = $technician->name;
        // }
        // else if($key == 'customer'){
        //   $username = $customer->firstname.' '.$customer->lastname;
        // }

        if($key == 'customer') {
          $role = 'customer';
        }

        $to_email=$value;

        $data=array('subject' => 'Service Request Status Changed',
          // 'user_name' => ucwords($username),
          'service_request' => $service_request,
          'receiver_email' => $value,
          'update_message' => $update_message,
          'role' => $role,
        );

        Mail::send('admin.emails.service_request_update_email',$data, function ($message)  use ( $to_email) {
          $message->to($to_email)
          ->subject('Service Request Status Changed')
          // ->from('info.emailtest1@gmail.com','Jumbo-Warranty');
          // ->from(env('MAIL_USERNAME'),env('APP_NAME'));
          ->from(config('mail.from.address'),config('mail.from.name'));
        });   
      }     
    }
  }

  public static function sendRequestAcceptRejectMail($request_id,$technician_name){
        // send mail to service center admin and admin
        $service_request = ServiceRequest::findOrFail($request_id);
        $service_center_admin_email = "";
        $admin_email = "";

        // $technician= \App\User::where('id',$service_request->technician_id)->where('status','Active')->where('role_id',config('constants.TECHNICIAN_ROLE_ID'))->get()->first();
        // $technician_name=ucwords($technician->name);

        $service_center_admin= \App\User::where('service_center_id',$service_request->service_center_id)
                              ->where('status','Active')
                              ->where('role_id',config('constants.SERVICE_ADMIN_ROLE_ID'))
                              ->get()->pluck('email')->toArray();
        if(!empty($service_center_admin))
        {
          // $service_center_admin_email=$service_center_admin->email;
          $service_center_admin_email=$service_center_admin;
        }

        $admin= \App\User::where('role_id',config('constants.ADMIN_ROLE_ID'))
                        ->where('status','Active')
                        ->get()->pluck('email')->toArray();
        if(!empty($admin))
        {
          // $admin_email=$admin->email;
          $admin_email=$admin;
        }

        if($service_request->is_accepted){
          $subject = "Service Request Accepted";
          $update_message = $technician_name." has accepted service request.";
        }
        else
        {
          $subject = "Service Request Rejected";
          $update_message = $technician_name." has rejected service request.";
        }
    

        $receiver_email=array('admin' => $admin_email,
                              'service_center_admin' => $service_center_admin_email,
                              'rajdip' => 'rajdip.webpatriot@gmail.com'
                            );
       
        foreach ($receiver_email as $key => $value) {
            if(!empty($value)){

              // if($key == 'admin')
              // {
              //   $username = $admin->name;
              // }
              // else if($key == 'service_center_admin'){
              //   $username = $service_center_admin->name;
              // }
              $to_email=$value;
              $data=array('subject' => $subject,
                    // 'user_name' => ucwords($username),
                    'service_request' => $service_request,
                    'receiver_email' => $value,
                    'update_message' => $update_message
                    );
          
              Mail::send('admin.emails.service_request_acceptReject_email',$data, function ($message)  use ( $to_email,$subject){
                    $message->to($to_email)
                      ->subject($subject)
                      // ->from('info.emailtest1@gmail.com','Jumbo-Warranty');
                      // ->from(env('MAIL_USERNAME'),env('APP_NAME'));
                      ->from(config('mail.from.address'),config('mail.from.name'));
                      // here comes what you want
                      // ->setBody('Hi, welcome user!'); // assuming text/plain
                      // or:
                      // ->setBody('<h1>Hi, welcome user!</h1>', 'text/html'); // for HTML rich messages
              });   
            }     
        }
  }
  
  public static function forgotpasswordApiMail($email,$OTP)
  { 
    $query = \App\User::select('name','email')
    ->where('role_id',config('constants.TECHNICIAN_ROLE_ID'))
    ->where('email',$email)
    ->where('status','Active')
    ->first();

    $response = 1;

    $to_email = $query->email;
    $name     = $query->name;

    $data = array(
      'subject' => 'Forgot password OTP',
      'user_name' => ucwords($name),
      'receiver_email' => $query->email,
      'OTP' => $OTP
    );

    Mail::send('admin.emails.otp',$data, function ($message)  use ( $to_email,$name){
      $message->to($to_email,$name)
      ->subject('Forgot password OTP')
      // ->from('info.emailtest1@gmail.com','Jumbo-Warranty');
      // ->from(env('MAIL_USERNAME'),env('APP_NAME'));
      ->from(config('mail.from.address'),config('mail.from.name'));
    });

    if (Mail::failures()) {
      $response = 0;
    }
    
    return $response;
    
  }


	
}

?>