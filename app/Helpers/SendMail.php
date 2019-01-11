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
		$service_request=$service_request[0];
		// echo $service_request->additional_charges;
		 // echo "<pre>"; print_r ($service_request); echo "</pre>"; exit();
		$additional_charge_array=json_decode($service_request['additional_charges']);
        $additional_charge_title="";
        $additional_charges="";
        if(!empty($additional_charge_array))
        {
            // Worked to display json value in edit page
            foreach ($additional_charge_array as $key => $value) {
                $additional_charge_title=str_replace('_empty_', '', $key);
                $additional_charges=$value;
            }
        }
       
        $service_request['additional_charges']=$additional_charges;
        $service_request['additional_charges_title']=$additional_charge_title;


		$company_admin= \App\User::where('company_id',$service_request->company_id)->where('role_id',config('constants.COMPANY_ADMIN_ROLE_ID'))->get()->first();
// echo "<pre>"; print_r ($company_admin); echo "</pre>"; exit();
		$company_admin_email=$company_admin->email;

// 		$customer= \App\Customer::where('id',$service_request->customer_id)->get()->first();
// echo "<pre>"; print_r ($customer); echo "</pre>"; exit();
// 		$customer_email=$customer->email;

		$admin= \App\User::where('role_id',config('constants.ADMIN_ROLE_ID'))->get()->first();

		$admin_email=$admin->email;

		

        $receiver_email=array($admin_email,$company_admin_email,'hinal.webpatriot@gmail.com'
		);

		$data=array('subject' => 'Request Creation Receive',
					'user_name' => 'Hinal patel',
					'service_request' => $service_request,
					'receiver_email' => $receiver_email
					);
		
		Mail::send('admin.emails.service_request',$data, function ($message)  use ( $receiver_email){
              $message->to($receiver_email)
                ->subject('Request Create Receive')
                ->from('xyz@gmail.com','Virat Gandhi');
                // here comes what you want
                // ->setBody('Hi, welcome user!'); // assuming text/plain
                // or:
                // ->setBody('<h1>Hi, welcome user!</h1>', 'text/html'); // for HTML rich messages
        });
	} 

	
}

?>