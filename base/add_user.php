<?php
require_once '../base.php';

mysql_connect ($mysql_server,$mysql_login,$mysql_pass);
mysql_select_db ($dbname);
mysql_query("SET NAMES 'utf8'");

$login=$_POST['login'];
$pass=md5($_POST['pass']);
$fio=addslashes($_POST['fio']);
$access=$_POST['access'];
$default=$_POST['default_point'];
$points=$_POST['points'];

if (!$login or !$pass or !$fio){
    echo "Не введен один из параметров".
         "<img src='image/ok_point_(in_form).png' onclick='refresh_page()'>";
    return;     
}

if (!preg_match("/^[0-9a-z]+$/",$login)){
	echo "Логин должен состоять из латинских символов".
         "<img src='image/ok_point_(in_form).png' onclick='refresh_page()'>";
    return;	
}

if ($access==1 and !$points) {
	echo "Не указаны точки администрирования".
         "<img src='image/ok_point_(in_form).png' onclick='refresh_page()'>";
    return;
}

mysql_query("INSERT INTO $user_table (login,pass,fio,access,default_point,admin_points,set_mask) VALUES ('$login','$pass','$fio','$access','$default','$points','111111111')");

if (mysql_error()){
  echo mysql_error();
}
else {
  echo "Пользователь добавлен в базу".
       "<img src='image/ok_point_(in_form).png' onclick='refresh_page()'>";
}

?>