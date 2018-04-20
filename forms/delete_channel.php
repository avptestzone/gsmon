<?php
$cid=htmlspecialchars($_GET['cid']);
$control_point=htmlspecialchars($_GET['cp']);
require_once '../base.php';

mysql_connect ($mysql_server,$mysql_login,$mysql_pass);
mysql_select_db ($dbname);
mysql_query("SET NAMES 'utf8'");

$channel=mysql_fetch_array(mysql_query("SELECT * FROM $channel_table WHERE id=$cid"));
mysql_query("DELETE FROM $channel_table WHERE id=$cid");
mysql_query ("UPDATE $log_table SET close = NOW() WHERE channel='$channel[name]' AND mux='$channel[mux]'");
?>