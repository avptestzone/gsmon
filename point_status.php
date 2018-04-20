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


function echo_error_channel ($m) {
	global $mux_array, $channel_table, $data_num;

	$channel_query = mysql_query("SELECT name,status,lcn,dcce FROM $channel_table WHERE mux='$m[num]'");
    
    while ($channel=mysql_fetch_array($channel_query)){
 
    	if (!preg_match('/[1479][19][09]/', $channel['status'])){
    	    $channel_status=str_split($channel['status']);
            $channel_name=$channel['name']." (".$channel['lcn'].") ";
            $mux_array[$data_num]['error'].=channel_error_text($channel_name,$channel_status,$channel['dcce']);
    	}
        
    }    

    if ($mux_array[$data_num]['error']==''){
        $mux_array[$data_num]['error']="<span>норма</span>";
    }
    
}

function channel_error_text ($name,$status,$dcce) {
    global $mux_array, $data_num, $red, $gray;

    $video='';
    $audio='';
    $error='';

    if ($status[0]==2){
        $video = "Отсутсвует в потоке";
        $red=1;
        $error_color='#ff7518';
    }
            
    if ($status[0]==3){
        $video = "Нет видео";
        $red=1;
        $error_color='#ff7518';
    }
                       
    if ($status[0]==6 and $status[1]!=2){
        $video = "Нет движения";
        $gray=1;
        $error_color='#bebebe';
    }
         
    if ($status[0]==8){
        $video = "Вещается плашка";
        $cce=0;
        $gray=1;
        $error_color='#bebebe';
    }
            
    if ($status[1]==2){
        $audio = "нет звука";
        $gray=1;
        $error_color='#bebebe';
    }

    if ($status[0]==6 and $status[1]==2){
        $video = "Нет движения";
        $red=1;
        $error_color='#ff7518';
    }
    
    if ($status[2]==1){
        $gray=1;
        $error = " +$dcce error";
        $error_color='#bebebe';
         
        if ($dcce>5) {
             $red=1;
             $error_color='#ff7518';
        }

    }
            
    if ($status[0]==5){
        $video = "Закодирован";
        $red=1;
        $error_color='#ff7518';
    }
    
    $text="<span style='background:$error_color;'>$name: $video $audio $error</span>";
    return $text; 

}

function echo_error_mux ($m) {
	global $mux_array, $data_num, $red, $gray;

	$err_no = str_split($m['err_no']);
	
	if ($err_no[0]==2){
	    $red=1;
            $mux_array[$data_num]['error'].="<span style='background:#ff7518;'>Битрейт транспондера превысил 48Мбит</span>";
	}

	if ($err_no[1]==2){
	    $gray=1;
            $mux_array[$data_num]['error'].="<span style='background:#bebebe;'>В потоке отсутсвует NIT</span>";
	}

	if ($err_no[2]==2){
	    $gray=1;
            $mux_array[$data_num]['error'].="<span style='background:#bebebe;'>В потоке отсутсвует EPG</span>";
	}

	if ($err_no[3]==2){
	    $gray=1;
            $mux_array[$data_num]['error'].="<span style='background:#bebebe;'>В потоке отсутсвует TDT</span>";
	}
	
}


$control_point=htmlspecialchars($_GET['cp']);
require_once "base.php";

mysql_connect ($mysql_server,$mysql_login,$mysql_pass);
mysql_select_db ($dbname);
mysql_query("SET NAMES 'utf8'");

$mux_array=array();
$data_num=0;

$mux_query = mysql_query("SELECT num,name,status,scan,reserve,err_no FROM $mux_table");

while ($mux=mysql_fetch_array($mux_query)) {

    if ($mux['scan']==2){
        continue;
    }

     
     $x=str_split($mux['name']);
     
     if ($x[0]=='*'){
         continue;   
     }

    if ($mux['reserve']==1){
        $yellow=1;
    }
    else {
        $yellow=0;
    }

    $mux_array[$data_num]['name']="$mux[num]"; 

    $red=0;
    $gray=0;
    $green=0;

    if ($mux['scan']==1){
        $mux_array[$data_num]['scan']="1";
    }
    else {
        $mux_array[$data_num]['scan']="0";
    }

    if (access()==0) {
        $mux_array[$data_num]['scan']="1";
    }


    if ($mux['status']==0) {
    	$green=1;
        $mux_array[$data_num]['error']="<span>норма</span>";
        $mux_array[$data_num]['sound']="off";    
    }

    if ($mux['status']==1) {
        echo_error_channel ($mux);      
    }

    if ($mux['status']==2) {
    	echo_error_mux ($mux);
    }

    if ($mux['status']==3) {
    	echo_error_mux ($mux);
    	echo_error_channel ($mux);
    }

    if ($red==1){
    	$mux_array[$data_num]['color']="#ff7518";
    }
    else if($gray==1){
    	$mux_array[$data_num]['color']="#bebebe";
    }
    else if($yellow==1){
        $mux_array[$data_num]['color']="#D5EC79";
    } 
    else if ($green==1) {
    	$mux_array[$data_num]['color']="#66ff00";
    }

    $data_num++;

}

$cph_query = mysql_query("SELECT mux,name,cph FROM $cph_table WHERE status=1 ORDER BY mux");

while ($channel_data=mysql_fetch_array($cph_query)){
    $data_text.="<b>$channel_data[name]</b> $channel_data[cph] cce/ч<br>";
}

$mux_array[$data_num]['name']='databox';
$mux_array[$data_num]['error']=$data_text;

echo json_encode($mux_array);

?>