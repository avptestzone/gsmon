<?php

$id = htmlspecialchars($_GET['id']);
$mask = htmlspecialchars($_GET['mask']);
$control_point=htmlspecialchars($_GET['cp']);
require_once "../base.php";

mysql_connect ($mysql_server,$mysql_login,$mysql_pass);
mysql_select_db ($dbname);
mysql_query("SET NAMES 'utf8'");

if (mysql_query("UPDATE $channel_table SET triger_mask='$mask' WHERE id='$id'")){
    echo "Тригеры обновлены";
}
else {
    echo "ERROR ".mysql_errno()." ".mysql_error()."\n";
}

?>