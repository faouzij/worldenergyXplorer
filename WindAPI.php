<?php
require_once("config.php");
/**
 * @author 
 * @copyright 2013
 */
set_time_limit ( 50000 );

function GetInfo($location){
    global $key;
    
    
    
$url="http://api.wunderground.com/api/$key/conditions/q/$location.json";
$ch = curl_init();
curl_setopt($ch,CURLOPT_URL,$url);
curl_setopt($ch,CURLOPT_FOLLOWLOCATION,false);
curl_setopt ($ch,CURLOPT_RETURNTRANSFER,true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch,CURLOPT_POST,false);
//curl_setopt($ch,CURLOPT_POSTFIELDS,"refresh_token=$refresh&client_id=$CLIENT_ID&client_secret=$CLIENT_SECRET&grant_type=refresh_token");
$data = curl_exec($ch);
$data = json_decode($data);
//$time = time();
//$data->time = $time;
$newdata  = $data->current_observation;
//var_dump($newdata);
$result= new stdClass();

$result->latitude = $newdata->display_location->latitude;
$result->longtitude = $newdata->display_location->longitude;
$result->elevation = $newdata->display_location->elevation;
$result->temperature = $newdata->temp_c;
$result->humidity = $newdata->relative_humidity;
$result->wind_direction = $newdata->wind_dir;
$result->wind_degree = $newdata->wind_degrees;
$result->wind_speed = $newdata->wind_kph;
curl_close($ch);
return $result;
}





$result =GetInfo("marrakech");





for ($i=0;$i<=90;$i++){
    for ($j=0;$j>=-180;$j=$j-3){
    $result =GetInfo("$i,$j");
    echo ("$result->latitude $result->longtitude $result->elevation $result->temperature $result->humidity $result->wind_direction $result->wind_degree $result->wind_speed<br/>");
    }
}

for ($i=0;$i<=90;$i++){
    for ($j=0;$j<=180;$j=$j+3)
{
    $result =GetInfo("$i,$j");
    echo ("$result->latitude $result->longtitude $result->elevation $result->temperature $result->humidity $result->wind_direction $result->wind_degree $result->wind_speed<br/>");
    }
}

for ($i=0;$i>=-90;$i--){
    for ($j=0;$j>=-180;$j=$j-3)
{
    $result =GetInfo("$i,$j");
    echo ("$result->latitude $result->longtitude $result->elevation $result->temperature $result->humidity $result->wind_direction $result->wind_degree $result->wind_speed<br/>");
    }
}

for ($i=0;$i>=-90;$i--){
    for ($j=0;$j<=180;$j=$j+3)
{
    $result =GetInfo("$i,$j");
    echo ("$result->latitude $result->longtitude $result->elevation $result->temperature $result->humidity $result->wind_direction $result->wind_degree $result->wind_speed<br/>");
    }
}

?>