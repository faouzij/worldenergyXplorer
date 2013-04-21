<?php

/**
 * @author 
 * @copyright 2013
 */

include("OpenWindApi.php");
 var_dump(gethighestwind());
if (isset($_POST["step"])){
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
    echo ("City : $result->name, Temperature : $result->temperature C,Humidity : $result->humidity%, Wind Speed : $result->wind_speed");
    }
    else
    echo 0;
}


}

function getHeated(){
    
    
}
 
?>