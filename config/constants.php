<?php
return [
	'SUPER_ADMIN_ROLE_ID' => env('SUPER_ADMIN_ROLE_ID','1'),
	'ADMIN_ROLE_ID' => env('ADMIN_ROLE_ID','3'),
	'COMPANY_ADMIN_ROLE_ID' => env('COMPANY_ADMIN_ROLE_ID','4'),
	'SERVICE_ADMIN_ROLE_ID' => env('SERVICE_ADMIN_ROLE_ID','5'),
	'TECHNICIAN_ROLE_ID' => env('TECHNICIAN_ROLE_ID','6'),
	'COMPANY_USER_ROLE_ID' => env('COMPANY_USER_ROLE_ID','7'),
	'GOOGLE_MAPS_API_KEY' =>  env('GOOGLE_MAPS_API_KEY','AIzaSyBsNVGUNzn19onCo93Vb1aupJO45oGrVMc'),
	'PRE_ADDITIONAL_CHARGES_FOR' => array("Please select", "additional charge 1", "additional charge 2"),
	'SMS_API_KEY' => env('SMS_API_KEY',null),
	'SMS_SENDER_NAME' => env('SMS_SENDER_NAME',null),
	'SMS_USER_ID' => env('SMS_USER_ID',null),
	'SMS_API_PASSWORD' => env('SMS_API_PASSWORD',null),
	'SMS_PORTAL_PASSWORD' => env('SMS_PORTAL_PASSWORD',null),
	'APP_URL' => env('APP_URL',null),
];

?>