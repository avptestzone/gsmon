<?php
session_start();

function access () {
    global $control_point;

    if ($_SESSION['user_access']==0){
        $access=0;
    }
    if ($_SESSION['user_access']==1 and !strstr($_SESSION['admin_points'],$control_point)){
        $access=0;
    }
    if ($_SESSION['user_access']==1 and strstr($_SESSION['admin_points'],$control_point)){
        $access=1;
    }
    if ($_SESSION['user_access']==2){
        $access=1;
    }
    return $access;
}

$control_point=htmlspecialchars($_GET['cp']);
if (access()==0){die();}

$value=htmlspecialchars($_GET['value']);
$object=htmlspecialchars($_GET['object']);
$data=htmlspecialchars($_GET['data']);


if ($data==''){
    $data='-';
}


require_once '../base.php';

mysql_connect ($mysql_server,$mysql_login,$mysql_pass);
mysql_select_db ($dbname);
mysql_query("SET NAMES 'utf8'");

if ($object=='mname') {
    mysql_query("UPDATE $mux_table SET name='$data' WHERE num='$value'");
    break;
}

if ($object=='mcast') {
    mysql_query("UPDATE $mux_table SET ip='$data' WHERE num='$value'");
    break;
}

if ($object=='mport') {
    mysql_query("UPDATE $mux_table SET port='$data' WHERE num='$value'");
    break;
}

if ($object=='tsid') {
    mysql_query("UPDATE $mux_table SET tsid='$data' WHERE num='$value'");
    break;
}

if ($object=='nid') {
    mysql_query("UPDATE $mux_table SET nid='$data' WHERE num='$value'");
    break;
}


if ($object=='network') {
    mysql_query("UPDATE $mux_table SET network='$data' WHERE num='$value'");
    break;
}

if ($object=='frequency') {
    mysql_query("UPDATE $mux_table SET frequency='$data' WHERE num='$value'");
    break;
}

if ($object=='qam_name') {
    mysql_query("UPDATE $mux_table SET lum_name='$data' WHERE num='$value'");
    break;
}

if ($object=='qam_href') {
    mysql_query("UPDATE $mux_table SET lum_href='$data' WHERE num='$value'");
    break;
}

if ($object=='ch_mcast') {
    mysql_query("UPDATE $channel_table SET multicast='$data' WHERE id='$value'");
    break;
}

if ($object=='lnb') {
    mysql_query("UPDATE $channel_table SET lnb='$data' WHERE id='$value'");
    break;
}

if ($object=='card') {
    mysql_query("UPDATE $channel_table SET card='$data' WHERE id='$value'");
    break;
}

if ($object=='sat_name') {
    mysql_query("UPDATE $channel_table SET source_name='$data' WHERE id='$value'");
    break;
}

if ($object=='sat_href') {
    mysql_query("UPDATE $channel_table SET source_href='$data' WHERE id='$value'");
    break;
}

if ($object=='chname') {
    mysql_query("UPDATE $channel_table SET name='$data' WHERE id='$value'");
    break;
}

if ($object=='lcn') {
    mysql_query("UPDATE $channel_table SET lcn='$data' WHERE id='$value'");
    break;
}

if ($object=='sid') {
    mysql_query("UPDATE $channel_table SET sid='$data' WHERE id='$value'");
    mysql_query("UPDATE $pid_table SET sid='$data' WHERE id='$value'");
    break;
}

if ($object=='pmt') {
    mysql_query("UPDATE $pid_table SET pid='$data' WHERE id='$value'");
    break;
}

?>