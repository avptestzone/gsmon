<?php
set_time_limit(0);

require_once 'base.php';
require_once 'functions.php';

mysql_connect ($mysql_server,$mysql_login,$mysql_pass);
mysql_select_db ($dbname);
mysql_query("SET NAMES 'utf8'");

$mux = array();
$channels = array();
$audio_time=time();


$mux['num'] = $num = $argv[1];
$mux['port_ts']= $port_ts = 1400+$num;
$mux['ip_mux'] = $ip_mux =$argv[2];
$mux['port_mux'] = $port_mux = $argv[3];
$mux['minute'] = $minute = $argv[4];
$mux['scan'] = $argv[5];
$mux['err_no'] = $argv[6];
$mux['status']=0;
$tehno=$argv[7];

$fp = fsockopen ("127.0.0.1",$port_ts);
wait("Server");


if ($mux['scan']==1){
	$pids=get_pid($fp);

	include 'pids.php';
	include 'epg.php';
	
	$html_file = html_export($fp);
    
	check_programs($fp);
    check_pids($pids);

 
	if ($tehno!=1){
        mysql_query("DELETE FROM $epg_table WHERE mux=$num");
        xmltv_export($fp);		
	}

    $mux['scan']=0;
}
else{

	 
    $pids=get_pid($fp);	
}


$list_of_channel = mysql_query("SELECT id,sid,name,status,triger_mask,lcn,cce,lim,provider "
        . "FROM $channel_table WHERE mux='$num' AND type='0'"); 

while ($channel = mysql_fetch_array($list_of_channel)){
	
	$channels['id'][]=$channel['id'];
    $channels['sid'][]=$channel['sid'];
    $channels['name'][]=$channel['name'];
    $channels['old_status'][]=$channel['status'];
    $channels['triger_mask'][]=$channel['triger_mask'];
    $channels['lcn'][]=$channel['lcn'];
	$channels['cce'][]=$channel['cce'];
    $channels['lim'][]=$channel['lim'];
	$channels['old_provider'][]=$channel['provider'];
}		


astra_scan();

fputs ($fp, "tune $ip_mux $port_mux\r");
wait("restarted");
sleep(2);

echo 'preparation done\r\n';
for ($x=0;$x<count($channels['id']);$x++){
    
    $mask = str_split($channels['triger_mask'][$x]);
	
	if ($mask[3]==1){
	    $cce_per_min = count_cce_by_astra($x);
	}
	else {
	    $cce_per_min=0; 
	}
    
 
    if ($minute==10){    
		$mux['minute']=0;
        $cph_file = fopen("C:\\ts\\cph_temp\\cph_list_$num.txt", "a+");	
        cce_per_hour($x,$cce_per_min,$mask[3]);
		fclose($cph_file);
    }
    
	
    if ($mask[0]+$mask[1] == 2) {
        $video_code = load_video($x, $mask[4]);
    }
    else {
        $video_code=900;
    }

	 
    $channels['video'][$x]=$video_code;
    $channels['cpm'][$x]=$cce_per_min;
    	
	if ($video_code=='600'){
	    load_audio($x);	
	}
	else {
		$channels['audio'][$x]=10;
	}
	
	summary_status($x);
	$log_id = channel_logging ($x);
	
	if (preg_match('/6../',$channels['new_status'][$x])){
		copy_thumb($log_id,$channels['id'][$x]);
	}

	channel_bitrate($x,$pids);
    channel_to_base($x);   
    echo $channels['name'][$x].' done\r\n';
}
mux_bitrate($pids);
mux_to_base();
last_copy();
fclose($fp);
?>
