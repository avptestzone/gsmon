<?php 
$cid=htmlspecialchars($_GET['cid']);
$control_point=htmlspecialchars($_GET['cp']);
require_once '../base.php';

mysql_connect ($mysql_server,$mysql_login,$mysql_pass);
mysql_select_db ($dbname);
mysql_query("SET NAMES 'utf8'");


mysql_query ("DELETE FROM $channel_table WHERE id='$cid'");
mysql_query ("DELETE FROM $pid_table WHERE id='$cid'");	
echo mysql_error();

?>