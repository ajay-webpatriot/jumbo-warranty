<?php
namespace App\Helpers;

use Illuminate\Support\Facades\Mail;
// models
use App\ServiceRequest;

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
    		$additional_charge_array=json_decode($service_request['additional_charges']);
        $additional_charge_title="";
        $additional_charges="";
        if(!empty($additional_charge_array))
        {
            // Worked to display json value in edit page
            foreach ($additional_charge_array as $key => $value) {
                if (strpos($key,'_empty_') === false) {

                  $additional_charge_title=str_replace('_empty_', '', $key);
                  $additional_charges=$value;
                }
            }
        }
       
        $service_request['additional_charges']=$additional_charges;
        $service_request['additional_charges_title']=$additional_charge_title;

    		$company_admin= \App\User::where('company_id',$service_request->company_id)->where('role_id',config('constants.COMPANY_ADMIN_ROLE_ID'))->get()->first();
    		$company_admin_email=$company_admin->email;

    		$customer= \App\Customer::where('id',$service_request->customer_id)->get()->first();
    		$customer_email=$customer->email;

    		$admin= \App\User::where('role_id',config('constants.ADMIN_ROLE_ID'))->get()->first();

    		$admin_email=$admin->email;

		

        $receiver_email=array('admin' => $admin_email,
                              'company_admin' => $company_admin_email,
                              'customer' => $customer_email
		                        );
        foreach ($receiver_email as $key => $value) {

          if($key == 'admin')
          {
            $username = $admin->name;
          }
          else if($key == 'company_admin'){
            $username = $company_admin->name;
          }
          else if($key == 'customer'){
            $username = $customer->firstname.' '.$customer->lastname;
          }
          $to_email=$value;
          $data=array('subject' => 'New Service Request Created',
                'user_name' => ucwords($username),
                'service_request' => $service_request,
                'receiver_email' => $value
                );
      
          Mail::send('admin.emails.service_request_detail_email',$data, function ($message)  use ( $to_email,$username){
                    $message->to($to_email,$username)
                      ->subject('New Service Request Created')
                      ->from('info.emailtest1@gmail.com','Jumbo-Warranty');
                      // here comes what you want
                      // ->setBody('Hi, welcome user!'); // assuming text/plain
                      // or:
                      // ->setBody('<h1>Hi, welcome user!</h1>', 'text/html'); // for HTML rich messages
              });        
        }
    		
	}

  public static function sendRequestUpdateMail($request_id,$update_message){

        $service_request = ServiceRequest::findOrFail($request_id);
        
       
        $customer= \App\Customer::where('id',$service_request->customer_id)->get()->first();
        $customer_email=$customer->email;

        $admin= \App\User::where('role_id',config('constants.ADMIN_ROLE_ID'))->get()->first();

        $admin_email=$admin->email;

    

        $receiver_email=array('admin' => $admin_email,
                              // 'company_admin' => $company_admin_email,
                              'customer' => $customer_email,
                            );
        if($service_request->status == "Closed")
        {
          $company_admin= \App\User::where('company_id',$service_request->company_id)->where('role_id',config('constants.COMPANY_ADMIN_ROLE_ID'))->get()->first();
          $company_admin_email=$company_admin->email;
          $receiver_email['company_admin'] = $company_admin_email;

          $service_center_admin= \App\User::where('service_center_id',$service_request->service_center_id)->where('role_id',config('constants.SERVICE_ADMIN_ROLE_ID'))->get()->first();
          $service_center_admin_email=$service_center_admin->email;
          $receiver_email['service_center_admin'] = $service_center_admin_email;

          $technician= \App\User::where('id',$service_request->technician_id)->where('role_id',config('constants.TECHNICIAN_ROLE_ID'))->get()->first();
          $technician_email=$technician->email;
          $receiver_email['technician'] = $technician_email;
        }
       
        foreach ($receiver_email as $key => $value) {

          if($key == 'admin')
          {
            $username = $admin->name;
          }
          else if($key == 'company_admin'){
            $username = $company_admin->name;
          }
          else if($key == 'service_center_admin'){
            $username = $service_center_admin->name;
          }
          else if($key == 'technician'){
            $username = $technician->name;
          }
          else if($key == 'customer'){
            $username = $customer->firstname.' '.$customer->lastname;
          }
          $to_email=$value;
          $data=array('subject' => 'Service Request Status Changed',
                'user_name' => ucwords($username),
                'service_request' => $service_request,
                'receiver_email' => $value,
                'update_message' => $update_message
                );
      
          Mail::send('admin.emails.service_request_update_email',$data, function ($message)  use ( $to_email,$username){
                    $message->to($to_email,$username)
                      ->subject('Service Request Status Changed')
                      ->from('info.emailtest1@gmail.com','Jumbo-Warranty');
              });        
        }
        
  
  }

  public static function sendRequestAcceptRejectMail($request_id){
        // send mail to service center admin and admin
        $service_request = ServiceRequest::findOrFail($request_id);
        
        $technician= \App\User::where('id',$service_request->technician_id)->where('role_id',config('constants.TECHNICIAN_ROLE_ID'))->get()->first();
        $technician_name=ucwords($technician->name);

        $service_center_admin= \App\User::where('service_center_id',$service_request->service_center_id)->where('role_id',config('constants.SERVICE_ADMIN_ROLE_ID'))->get()->first();
        $service_center_admin_email=$service_center_admin->email;

        $admin= \App\User::where('role_id',config('constants.ADMIN_ROLE_ID'))->get()->first();
        $admin_email=$admin->email;

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
                              'service_center_admin' => $service_center_admin_email
                            );
       
        foreach ($receiver_email as $key => $value) {

          if($key == 'admin')
          {
            $username = $admin->name;
          }
          else if($key == 'service_center_admin'){
            $username = $service_center_admin->name;
          }
          $to_email=$value;
          $data=array('subject' => 'Service Request',
                'user_name' => ucwords($username),
                'service_request' => $service_request,
                'receiver_email' => $value,
                'update_message' => $update_message
                );
      
          Mail::send('admin.emails.service_request_acceptReject_email',$data, function ($message)  use ( $to_email,$username,$subject){
                    $message->to($to_email,$username)
                      ->subject($subject)
                      ->from('info.emailtest1@gmail.com','Jumbo-Warranty');
                      // here comes what you want
                      // ->setBody('Hi, welcome user!'); // assuming text/plain
                      // or:
                      // ->setBody('<h1>Hi, welcome user!</h1>', 'text/html'); // for HTML rich messages
              });        
        }
  }
  
  public static function forgotpasswordApiMail($email,$OTP)
  { 
    $query = \App\User::select('name','email')
    ->where('role_id',config('constants.TECHNICIAN_ROLE_ID'))
    ->where('email',$email)
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
      ->from('info.emailtest1@gmail.com','Jumbo-Warranty');
    });

    if (Mail::failures()) {
      $response = 0;
    }
    
    return $response;
    
  }


	
}

?>