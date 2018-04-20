<?php 
ini_set('display_errors','On');
error_reporting('E_ALL');
$control_point='ors';
require_once '../base.php';

mysql_connect ($mysql_server,$mysql_login,$mysql_pass);
mysql_select_db ($dbname);
mysql_query("SET NAMES 'utf8'");

mysql_query("UPDATE $mux_table SET ip = REPLACE(ip,'228.50.','225.7.')");
mysql_query("UPDATE $mux_table SET port = REPLACE(port,'1234','2048')");


echo mysql_error();
?>