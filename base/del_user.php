<?php
require_once '../base.php';

mysql_connect ($mysql_server,$mysql_login,$mysql_pass);
mysql_select_db ($dbname);
mysql_query("SET NAMES 'utf8'");

$user=$_POST['user'];
mysql_query("DELETE FROM $user_table WHERE login='$user'");

if (mysql_error()){
  echo mysql_error();
}
else {
  echo "Пользователь удален из базы".
       "<img src='image/ok_point_(in_form).png' onclick='refresh_page()'>";
}
?>