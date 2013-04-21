<?php

/**
 * @author 
 * @copyright 2013
 */
require_once("config.php");
/**
 * @author 
 * @copyright 2013
 */
set_time_limit ( 5000000 );
if (isset($_GET["work"])){
    if ($_GET["work"]==1){
        $all =  new stdClass();
//var_dump(GetInfo(30 ,-8, 10)); 
$all->id = array();
$all->name = array();
$all->temperature = array();
$all->humidity = array();
$all->latitude= array();
$all->longitude= array();
$all->wind_degree = array();
$all->wind_speed = array();
$all->id = array();
//$all->id[0] = 1;

 $cnt=0;
for ($i=72;$i>=-58;$i--){
    for ($j=180;$j>=-180;$j=$j-10){
        echo ("$i,$j <br/>");
        
    $result =GetInfo($i,$j,150);
   for ($k=0;$k<$result->cnt;$k++){
    $id =$result->id[$k];
    $name = $result->name[$k];
    $la = $result->latitude[$k];
    $lo = $result->longtitude[$k];
    $tem = $result->temperature[$k];
    $hum = $result->humidity[$k];
    $wd =$result->wind_degree[$k];
    $ws = $result->wind_speed[$k];
    if (!in_array($id,$all->id)){
array_push($all->id,$id);
array_push($all->name,$name);
array_push($all->latitude,$la);
array_push($all->longitude,$lo); 
array_push($all->temperature,$tem); 
array_push($all->humidity,$hum); 
array_push($all->wind_degree,$wd); 
array_push($all->wind_speed,$ws); 
writedbsingle($id,$name,$la,$lo,$tem,$hum,$wd,$ws);
}
 
   }
    //var_dump($all);  
   }
    }
    }
}
if (isset($_GET["average"])){
    average();
}
else if (isset($_POST["step"])){
    if ($_POST["step"]==1){
        $x = $_POST["x"];
         $y = $_POST["y"];
         $zoom = $_POST["zoom"];
         switch($zoom){
            case 5:
             $result =  GetsingleInfo($x,$y,200);
             break;
             case 6:
              $result =  GetsingleInfo($x,$y,40);
             break;
             case 7:
              $result =  GetsingleInfo($x,$y,20);
             break;
             default:
              $result =  GetsingleInfo($x,$y);
             break;
         }
       
          if ($result->status==1){
    echo ("City : $result->name, Temperature : $result->temperature C,Humidity : $result->humidity%, Wind Speed : $result->wind_speed<br/>");
    }
    else
    echo 0;
}


}

function gethighestwind(){

      $newquery = "select * from info order by w_speed desc limit 10 " ;     


 $grades =new stdClass();
$ex = mysql_query($newquery);
$number = mysql_num_rows($ex);
$num=0;
 $grades->number=$number;

      while  ($get = mysql_fetch_assoc($ex)) {
      $grades->id[$num]=$get["ID"];
      $grades->name[$num]=$get["City"];
      $grades->w_speed[$num]=$get["w_speed"];
      $grades->temp[$num]=$get["temperature"];  
      $grades->lat[$num]=$get["lat"]; 
      $grades->long[$num]=$get["long"];   
      $num++;
         }
         return $grades;
         
        

}


function gethighestheat(){

      $newquery = "select * from info order by temperature desc limit 10 " ;     


 $grades =new stdClass();
$ex = mysql_query($newquery);
$number = mysql_num_rows($ex);
$num=0;
 $grades->number=$number;

      while  ($get = mysql_fetch_assoc($ex)) {
      $grades->id[$num]=$get["ID"];
      $grades->name[$num]=$get["City"];
      $grades->temperature[$num]=$get["temperature"];  
      $grades->lat[$num]=$get["lat"]; 
      $grades->long[$num]=$get["long"];   
      $num++;
         }
         return $grades;
         
        

}

function average(){
    
}


function writedbsingle($id,$name,$la,$lo,$tem,$hum,$wd,$ws){
    $time = time();
     $newquery = "INSERT INTO  info 
VALUES (
$id ,  '$name',  '$la', '$lo' ,'$tem',  '$hum', '$wd', '$ws',  '$time'
)";
$ex = mysql_query($newquery);   
   $currenttime = intval(CoordinatesToTime($la,$lo));
        $sun = (GetSunriseSunset($la,$lo));
        if ($currenttime>=intval($sun->sunrise) && $currenttime<=intval($sun->sunset)){
        $newquery = "INSERT INTO `explorer`.`solar` (
`id` ,
`lat` ,
`long` ,
`temperature` ,
`w_speed`,
`date`
)
VALUES (
'$id', '$lat','$long','$tem', '$ws','$time'
);";
$ex = mysql_query($newquery);  
            }
   
    }

function writedb($all){
    $time = time();
    for ($k=0;$k<count($all->id);$k++){
        
    $id =$all->id[$k];
    $name = $all->name[$k];
    $la = $all->latitude[$k];
    $lo = $all->longtitude[$k];
    $tem = $all->temperature[$k];
    $hum = $all->humidity[$k];
    $wd =$all->wind_degree[$k];
    $ws = $all->wind_speed[$k];
     $newquery = "INSERT INTO  info 
VALUES (
$id ,  '$name',  '$la', '$lo' ,'$tem',  '$hum', '$wd', '$ws',  '$time'
)";
$ex = mysql_query($newquery);   
    }
    

}
function CoordinatesToTime($lat,$long){
    $xml = simplexml_load_file("http://www.earthtools.org/timezone/$lat/$long");
$date =  ($xml->localtime);
$stamp = strtotime ($date);
$hour = date("H:i:s",$stamp);
//var_dump($xml->timezone->children()) ;
return $hour;
}

function GetSunriseSunset($lat,$long){
    
$date =  time();
$day = date("d",$date);
$month = date("m",$date);
$xml = simplexml_load_file("http://www.earthtools.org/sun/$lat/$long/$day/$month/99/0");
//var_dump($xml);
$result = new stdClass();
$result->sunrise =$xml->morning->sunrise ;
$result->sunset = $xml->evening->sunset ;
return $result;
}


function GetInfo($lat,$long,$cnt=""){
    global $key;
    
    if ($cnt=="")
    $cnt = 1;
    
    
$url="http://api.openweathermap.org/data/2.1/find/city?lat=$lat.0&lon=$long&cnt=$cnt&units=metric&APPID=$key";

$ch = curl_init();
curl_setopt($ch,CURLOPT_URL,$url);
curl_setopt($ch,CURLOPT_FOLLOWLOCATION,false);
curl_setopt ($ch,CURLOPT_RETURNTRANSFER,true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch,CURLOPT_POST,false);
//curl_setopt($ch,CURLOPT_POSTFIELDS,"refresh_token=$refresh&client_id=$CLIENT_ID&client_secret=$CLIENT_SECRET&grant_type=refresh_token");
$data = curl_exec($ch);
//echo $data;
$data = json_decode($data);
curl_close($ch);
//var_dump( $data);
//$time = time();
//$data->time = $time;
//$newdata  = $data->current_observation;
//var_dump($newdata);
$result= new stdClass();
$result->cnt = $data->cnt;
for ($i=0;$i<$result->cnt;$i++){
if (isset($data->list[$i]))
{
$result->status[$i] = 1;
$newdata=$data->list[$i];
$result->latitude[$i] = $newdata->coord->lat;
$result->longtitude[$i] = $newdata->coord->lon;
//$result->elevation = $newdata->display_location->elevation;
$result->id[$i] = $newdata->id;
$result->name[$i] = $newdata->name;
$result->temperature[$i] = (($newdata->main->temp - 273) );
$result->humidity[$i] =  $newdata->main->humidity;
$result->wind_degree[$i] = $newdata->wind->deg;
$result->wind_speed[$i] =  $newdata->wind->speed;
   $time = time();



}
}
return $result;
}


function GetsingleInfo($lat,$long,$r=""){
    global $key;
    
    if ($r=="")
    $cnt = 10;
    
    
$url="http://api.openweathermap.org/data/2.1/find/city?lat=$lat&lon=$long&radius=$r&units=metric&APPID=$key";

$ch = curl_init();
curl_setopt($ch,CURLOPT_URL,$url);
curl_setopt($ch,CURLOPT_FOLLOWLOCATION,false);
curl_setopt ($ch,CURLOPT_RETURNTRANSFER,true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch,CURLOPT_POST,false);
//curl_setopt($ch,CURLOPT_POSTFIELDS,"refresh_token=$refresh&client_id=$CLIENT_ID&client_secret=$CLIENT_SECRET&grant_type=refresh_token");
$data = curl_exec($ch);
//echo $data;
$data = json_decode($data);
curl_close($ch);
//var_dump( $data);
//$time = time();
//$data->time = $time;
//$newdata  = $data->current_observation;
//var_dump($newdata);
$result= new stdClass();
$result->cnt = $data->cnt;

if (isset($data->list[0]))
{
$result->status = 1;
$newdata=$data->list[0];
$result->latitude = $newdata->coord->lat;
$result->longtitude = $newdata->coord->lon;
//$result->elevation = $newdata->display_location->elevation;
$result->id = $newdata->id;
$result->name = $newdata->name;
$result->temperature = (($newdata->main->temp - 273) );
$result->humidity =  $newdata->main->humidity;
$result->wind_degree = $newdata->wind->deg;
$result->wind_speed =  $newdata->wind->speed;
$time = time();



}
else
$result->status = 0;

return $result;
}


   //writedb($all);


?>