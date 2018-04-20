<?php
$control_point=htmlspecialchars($_GET['cp']);
require_once '../base.php';

mysql_connect ($mysql_server,$mysql_login,$mysql_pass);
mysql_select_db ($dbname);
mysql_query("SET NAMES 'utf8'");

$cph_query = mysql_query("SELECT * FROM $cph_table WHERE status=1");

while ($cph = mysql_fetch_array($cph_query)){
    
    $id = $cph['id'];
    $mux = $cph['mux'];
    
    $cce_query = mysql_fetch_array(mysql_query("SELECT cce FROM $channel_table WHERE id='$id'"));
    $cce=$cce_query['cce'];
    
    mysql_query("UPDATE $cph_table SET cph='0',p1='$cce',p2='$cce',"
            . "p3='$cce',p4='$cce',p5='$cce',"
            . "p6='$cce',status=0 WHERE id=$id");
    
}


?>
