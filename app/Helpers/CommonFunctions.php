<?php
namespace App\Helpers;
use Log;

class CommonFunctions 
{
	public static function setDateFormat($date){
	    $date=date('d/m/Y',strtotime($date));
	    return $date;  
	}
	public static function setDateTimeFormat($date){
	    $date=date('d/m/Y h:i:sa',strtotime($date));
	    return $date;  
	}
	public static function crypto_rand_secure($min, $max)
    {
        $range = $max - $min;
        if ($range < 1) return $min; // not so random...
        $log = ceil(log($range, 2));
        $bytes = (int) ($log /8)+1; // length in bytes
        $bits = (int) $log + 1; // length in bits
        $filter = (int) (1 << $bits) - 1; // set all lower bits to 1
        do {
            $rnd = hexdec(bin2hex(openssl_random_pseudo_bytes($bytes)));
            $rnd = $rnd & $filter; // discard irrelevant bits
        } while ($rnd > $range);
        return $min + $rnd;
    }

    public static function getHashCode($length = 32)
    {
		$token = "";
		$codeAlphabet ="ABCDEFGHIJKLMNOPQRSTUVWXYZ";
		$codeAlphabet.= "0123456789";
		$codeAlphabet.= "abcdefghijklmnopqrstuvwxyz";

        $max = strlen($codeAlphabet); // edited

        for ($i=0; $i < $length; $i++) {
            $token .= $codeAlphabet[self::crypto_rand_secure(0, $max-1)];
        }
        return $token;
    }

    public static function postCURL($url,$fields=array())
    {
		$headers = array(
            'Content-Type: application/json'
        );

        // Open connection
        $ch = curl_init();

        // Set the url, number of POST vars, POST data
        // curl_setopt( $ch, CURLOPT_URL, $url );
        // curl_setopt( $ch, CURLOPT_HTTPHEADER, $headers);
        // curl_setopt( $ch, CURLOPT_CUSTOMREQUEST, "POST");
        // curl_setopt( $ch, CURLOPT_POST, 1);
        // curl_setopt( $ch, CURLOPT_POSTFIELDS, json_encode( $fields ));
        // curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
        // curl_setopt( $ch, CURLOPT_SSL_VERIFYHOST, 0);
        // curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, false);

        curl_setopt( $ch, CURLOPT_URL, $url );
        curl_setopt( $ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt( $ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt( $ch, CURLOPT_POSTFIELDS, json_encode( $fields ));
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
        curl_setopt( $ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, false);

        // Execute post
        $result = curl_exec($ch);

        if ($result === FALSE) {
            Log::error('Oops! FCM Send Error: ' . curl_error($ch));
            // die('Oops! FCM Send Error: ' . curl_error($ch));
        }

        // Close connection
        Log::info("== sending fields ==".json_encode( $fields ));
        Log::info("== sending result ==".json_encode( $result ));
        curl_close($ch);

        return $result;

    }
}

?>