<?php
/* Модуль возвращает данные о канале. Сид, пиды, кодеки, ошибки по пидам */
session_start();

function access () {

    if ($_SESSION['user_access']==0){
        $access=0;
    }
    if ($_SESSION['user_access']==1 and !strstr($_SESSION['admin_points'],$current_point)){
        $access=0;
    }
    if ($_SESSION['user_access']==1 and strstr($_SESSION['admin_points'],$current_point)){
        $access=1;
    }
    if ($_SESSION['user_access']==2){
        $access=1;
    }
    return $access;
}

$id=htmlspecialchars($_GET['id']);
$control_point=htmlspecialchars($_GET['cp']);
require_once "base.php";

mysql_connect ($mysql_server,$mysql_login,$mysql_pass);
mysql_select_db ($dbname);
mysql_query("SET NAMES 'utf8'");

$triger = mysql_fetch_array(mysql_query("SELECT * FROM $channel_table WHERE id='$id'"));
echo mysql_error();
$cph_lim = mysql_fetch_array(mysql_query("SELECT lim FROM $cph_table WHERE id='$id'"));
echo mysql_error();
$sid=$triger['sid'];
$mid=$triger['mux'];
$name=$triger['name'];
$cname=  urlencode($name);

$sum = mysql_fetch_array (mysql_query("SELECT SUM(error) "
                                     . "FROM $pid_table WHERE sid='$sid' AND mux='$mid'"));
$pid_query = mysql_query("SELECT * FROM $pid_table WHERE sid='$sid' AND mux='$mid' ORDER BY bitrate");

echo "<div style='font-size:20px;'>$name <img src='image/epg.png' onclick='epg($id)' style='cursor:pointer;'> <a href=http://gs-mon.ors.o56.ru/rrd/bitrate.php?cp=$control_point&cid=$id&cname=$cname target='_blank'><img src='image/bitrate.png'></a></div><br>";
echo "sid : $sid : $sum[0] <br>------------------<br>";

while ($pid=mysql_fetch_array($pid_query)){

	if ($pid['table_name']=='Video' or $pid['table_name']=='Audio') {
		$codec=strtolower($pid['codec']);
	    echo "$pid[table_name] : $pid[pid] ($codec) : $pid[error]<br>";
    }
    else {
    	echo "$pid[table_name] : $pid[pid] : $pid[error]<br>";
    }
    
}

$limit_per_min=$triger['lim'];
$limit_per_hour=$cph_lim['lim'];
$mask=str_split($triger['triger_mask']);

($mask[0])?$all="checked='checked'":$all='';
($mask[1])?$video="checked='checked'":$video='';
($mask[2])?$scramble="checked='checked'":$scramble='';
($mask[3])?$error="checked='checked'":$error='';
($mask[4])?$move="checked='checked'":$move='';
($mask[5])?$sound="checked='checked'":$sound='';

if (access()==1) {
  include 'data_html.php';
}
 
?>

