<?php

/**
 * @author 
 * @copyright 2013
 */

$hostname="127.0.0.1";
$db_username = "root";
$db_password= "";
$db_name="explorer";
$key ="03b23d338dc92a50ccc79857767e6b74";

if (!@mysql_connect($hostname,$db_username,$db_password)){
    die ("Error Connecting To Database");
}
else{
    if (!@mysql_select_db($db_name)){
        die ("Couldn't Locate Database");
    }
}

?>