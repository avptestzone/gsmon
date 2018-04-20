<?php
$m_num=htmlspecialchars($_GET['mux']);
$control_point=htmlspecialchars($_GET['cp']);
require_once '../base.php';

mysql_connect ($mysql_server,$mysql_login,$mysql_pass);
mysql_select_db ($dbname);
mysql_query("SET NAMES 'utf8'");

mysql_query("UPDATE $mux_table SET scan=1,status=0 WHERE num='$m_num'");

?>