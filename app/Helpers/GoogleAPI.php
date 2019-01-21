<?php
namespace App\Helpers;
class GoogleAPI 
{
	public static function getLatLong($zipcode){

		// get lat and long using zipcode 
	   $url = "https://maps.googleapis.com/maps/api/geocode/json?address=
		".urlencode($zipcode)."&sensor=true&key=".config('constants.GOOGLE_MAPS_API_KEY');
		$result_string = file_get_contents($url);
		$result = json_decode($result_string, true);
		// echo "<pre>";
		// print_r($result);exit;
		if(count($result['results']))
		{
			$result1[]=$result['results'][0];
			$result2[]=$result1[0]['geometry'];
			$result3[]=$result2[0]['location'];
			return $result3[0];
		}
		else
		{
			return false;
		}
		
	} 

	public static function distance($lat1, $lon1, $lat2, $lon2) {
	 	
	 	// echo $lat1." , ".$lon1." , ".$lat2." , ".$lon2;
	 	if($lat1 == 0 || $lat2 == 0 || $lon1 == 0 || $lon2 == 0)
	 	{
	 		return 0;
	 	}
	 	else
	 	{
	 		// $unit="K";		
			$theta = $lon1 - $lon2;
			$dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
			$dist = acos($dist);
			$dist = rad2deg($dist);
			$miles = $dist * 60 * 1.1515;
			// $unit = strtoupper($unit);

			// if ($unit == "K") {
				return round(($miles * 1.609344),2);
			// } else if ($unit == "N") {
			// 	return round(($miles * 0.8684),2);
			// } else {
			// 	return round($miles,2);
			// }
		}
	}

	
}

?>