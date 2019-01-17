<?php
namespace App\Helpers;

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


	
}

?>