<?php
$hostname_Connection = "localhost";
$database_Connection = "chatdemo";
$username_Connection = "chatdemo";
$password_Connection = "##########";

$Connection = mysql_pconnect($hostname_Connection, $username_Connection, $password_Connection) or trigger_error(mysql_error(),E_USER_ERROR); 
mysql_select_db($database_Connection);

?>
