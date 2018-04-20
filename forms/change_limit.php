<?php

$id = htmlspecialchars($_GET['id']);
$lpm = htmlspecialchars($_GET['lpm']);
$lph = htmlspecialchars($_GET['lph']);
$control_point=htmlspecialchars($_GET['cp']);
require_once "../base.php";

mysql_connect ($mysql_server,$mysql_login,$mysql_pass);
mysql_select_db ($dbname);
mysql_query("SET NAMES 'utf8'");


if (mysql_query("UPDATE $channel_table SET lim='$lpm' WHERE id='$id'")){
    echo "<br>Порог за минуту обновлен<br>";
}
else {
    echo "ERROR ".mysql_error()."<br>";
}

if (mysql_query("UPDATE $cph_table SET lim='$lph' WHERE id='$id'")){
    echo "Порог за час обновлен";
}
else {
    echo "ERROR ".mysql_error();
}


?>