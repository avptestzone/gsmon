<?php 
require_once '../base.php';

mysql_connect ($mysql_server,$mysql_login,$mysql_pass);
mysql_select_db ($dbname);
mysql_query("SET NAMES 'utf8'");

$short_name=$_POST[del_point_name];

mysql_query("DELETE FROM point_table WHERE short_name='$short_name'");
mysql_query("DROP TABLE $short_name"."_mux");
mysql_query("DROP TABLE $short_name"."_channel");
mysql_query("DROP TABLE $short_name"."_log");
mysql_query("DROP TABLE $short_name"."_pid");
mysql_query("DROP TABLE $short_name"."_cph");
mysql_query("DROP TABLE $short_name"."_epg");

if (mysql_error()){
  echo mysql_error();
}
else {
  echo "Контрольная точка успешно удалена".
       "<img src='image/ok_point_(in_form).png' onclick='refresh_page()'>";
}

?>