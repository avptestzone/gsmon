<?php
session_start();
$mask = htmlspecialchars($_GET['mask']);
require_once "../base.php";

mysql_connect ($mysql_server,$mysql_login,$mysql_pass);
mysql_select_db ($dbname);
mysql_query("SET NAMES 'utf8'");


$login=$_SESSION['user'];
mysql_query("UPDATE $user_table SET set_mask='$mask' WHERE login='$login'");
$_SESSION['set_mask']=$mask;
echo mysql_error();

?>