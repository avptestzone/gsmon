<?php
require_once 'base.php';

mysql_connect ($mysql_server,$mysql_login,$mysql_pass);
mysql_select_db ($dbname);
mysql_query("SET NAMES 'utf8'");

function exec_nowait($cmd) {
   pclose(popen('start "" /B '.$cmd, "r"));
}

function exec_scan($cmd) {
   pclose(popen('start "" /REALTIME /MIN '.$cmd, "r"));
}


if(!file_exists('ts_start.bat')){
    $ts_bat = fopen('ts_start.bat','w');
    fputs($ts_bat,"taskkill /f /im:tsreader.exe\r\nping localhost -w 1000 -n 3\r\n");
	$ts_bat_text='';
	fclose($ts_bat);
}
else {
	$ts_bat_array=file('ts_start.bat');
	$ts_bat_text=implode(' ',$ts_bat_array);

}

$g=0;

$mux_query=mysql_query("SELECT * FROM $mux_table ORDER BY name");
while($mux=mysql_fetch_array($mux_query)){
	
	$num=$mux['num'];
    $ip=$mux['ip'];
    $m_port=$mux['port'];
    $ts_port=1400+$num;
    $network=$mux['network'];
	$minute=$mux['minute'];
	$scan=$mux['scan'];
	$err_no=$mux['err_no'];
    
	if ($scan==2){
		for ($i=0;$i<count($ts_bat_array);$i++){

			if(strpos($ts_bat_array[$i],"$ip $m_port")){
				unset($ts_bat_array[$i]);
				$ts_bat = fopen('ts_start.bat','w+');
				fputs($ts_bat,implode('',$ts_bat_array));
				fclose($ts_bat);
				mysql_query("DELETE FROM $mux_table WHERE num=$num");
				mysql_query("DELETE FROM $channel_table WHERE mux=$num");
				mysql_query("DELETE FROM $pid_table WHERE mux=$num");
				mysql_query("DELETE FROM $epg_table WHERE mux=$num");
				mysql_query("DELETE FROM $log_table WHERE mux=$num");
				mysql_query("DELETE FROM $cph_table WHERE mux=$num");
				
				
				$del_astra=fopen($astra_url."delete.php?cp=$control_point"."&mid=$num","r");

	            while(!feof($del_astra)) {
                    $content .= fread($del_astra,1024);
                }

	            fclose($del_astra); 
				
			}
		}
		continue;
	}
	
	$t=str_split($mux['name']);

	 
	if ($t[0]=='*') { 
	    continue;
	}
	
	if ($t[0]=='_'){
		$tehno=1;
	}
    else {
		$tehno=0;
	}	

    if (!strpos($ts_bat_text,$mux[ip])){
		$ts_bat = fopen('ts_start.bat','a');
        fputs($ts_bat,"start /high C:\\\"Program Files (x86)\"\\COOL.STF\\TSReader\\tsreader.exe -c $ts_port $ip $m_port $network\r\n");
		fclose($ts_bat);
		

		$cph = fopen("C:\\ts\\cph_temp\\cph_list_$num.txt", "w+");
		fclose($cph);
		$command="C:\\\"Program Files (x86)\"\\COOL.STF\\TSReader\\tsreader.exe -c $ts_port $ip $m_port $network";
		exec_nowait($command);
		continue;
	}

	$command="C:\\OpenServer\\modules\\php\\PHP-5.4\\php.exe -c C:\\OpenServer\\userdata\\temp\\config\\php.ini -q -f C:\\ts\\mux_rescan.php $num $ip $m_port $minute $scan $err_no $tehno";
	if ($scan==1){
		exec_scan($command);
	}
	else {
       exec_scan($command);		
	}
	
    sleep(1);
	
}
	

?>
