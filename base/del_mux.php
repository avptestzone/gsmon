<?php 
$num=$_POST['del_mux_num'];
$control_point=$_POST[current_point];
require_once '../base.php';

mysql_connect ($mysql_server,$mysql_login,$mysql_pass);
mysql_select_db ($dbname);
mysql_query("SET NAMES 'utf8'");


$name_query=mysql_fetch_array(mysql_query("SELECT name FROM $mux_table WHERE num='$num'"));
$name=str_split($name_query['name']);

if ($name[0]=='*'){
	mysql_query("DELETE FROM $mux_table WHERE num='$num'");
	mysql_query("DELETE FROM $pid_table WHERE mux='$num'");
	mysql_query("DELETE FROM $channel_table WHERE mux='$num'");
}
else {
	mysql_query("UPDATE $mux_table SET scan=2 WHERE num='$num'");
}


if (mysql_error()){
  echo mysql_error();
}
else {
  echo "Транспондер удален из базы".
       "<img src='image/ok_point_(in_form).png' onclick='refresh_page()'>";
}

?>