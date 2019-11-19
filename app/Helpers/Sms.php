<?php

namespace App\Helpers;

class Sms
{
    public static function sendsmscustomer($customerNumber, $message_body) {
        
        // Main Credential of send sms.
        $sender = config('constants.SMS_SENDER_NAME');
        $user = config('constants.SMS_USER_ID'); 
        $ApiPassword = config('constants.SMS_API_PASSWORD');

        // $smsGatewayUrl = 'http://apps.smslane.com';
        // $password = config('constants.SMS_PORTAL_PASSWORD');

        // $apiKey = config('constants.SMS_API_KEY');

        // Message encode.
        $textmessage = urlencode($message_body);

        // Api Parameters.
        $data = '?user='.$user.'&password='.$ApiPassword.'&msisdn='.$customerNumber.'&sid='.$sender.'&msg='.$textmessage.'&fl=0&gwid=2';
        
        // CURl call.
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'http://apps.smslane.com/vendorsms/pushsms.aspx'.$data);
        curl_setopt($ch, CURLOPT_POST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        
        // Check for CURL error.
        if(curl_exec($ch) === false){
            return curl_error($ch);
        }
        
        // Get response or CURl 
        $response = curl_exec($ch);

        // Close CURL connection.
        curl_close($ch);

        // Return response.
        return $response;
	}
}
?>