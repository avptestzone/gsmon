<?php


$mid=htmlspecialchars($_GET['mid']);
$control_point=htmlspecialchars($_GET['cp']);
require_once '../base.php';

mysql_connect ($mysql_server,$mysql_login,$mysql_pass);
mysql_select_db ($dbname);
mysql_query("SET NAMES 'utf8'");


$cid=mt_rand(0, 999999);

mysql_query ("INSERT INTO $channel_table (id,sid,name,mux,status,type,triger_mask,lim,source_name,source_href,multicast,lnb,card,aspect,provider,bitrate) VALUES ('$cid','2000','имя_канала','$mid','110','0','111111','0','luminato','#','0.0.0.0','sputnic','free','4/3','ufa','0')");
mysql_query ("INSERT INTO $pid_table (id,sid,pid,table_name,codec,bitrate,error,mux) VALUES ('$cid','2000','1000','PMT','0','0','0','$mid')");	
echo mysql_error();
?>