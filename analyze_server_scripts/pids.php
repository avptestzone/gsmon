<?php

function check_pids ($pid_list) {
	global $num;
   
	for ($i=1;$i<count($pid_list);$i++){   

		if (strstr($pid_list[$i],'ECM')){
		    continue;
	    }
		
		$pid_data = parsing_pid_string($pid_list[$i]);
		update_pid_table($pid_data,$num);
	}
	
}

function parsing_pid_string($stroka){
	
	$data[id] = mt_rand(100000, 999999);
	$word = explode(' ',$stroka);
	$data[pid] = hexdec($word[0]);
	$data[bitrate]=$word[4];
	$data[table]=get_table_name($word); 
	$data[codec] = substr($word[5],1);
    $data[sid]=get_sid($stroka);
	$data[lcn]=get_lcn($stroka);
    
	return $data;
	
}

function update_pid_table ($d,$m) {
    
	global $pid_table,$channel_table;
	
	if ($d['sid']){
		mysql_query ("INSERT INTO $pid_table (id,sid,pid,table_name,codec,bitrate,mux) VALUES ('$d[id]','$d[sid]','$d[pid]','$d[table]','$d[codec]','$d[bitrate]','$m')");
		
		if ($d['lcn']>=400){
			mysql_query ("UPDATE $channel_table SET lcn='$d[lcn]',type='1' WHERE id='$m$d[sid]'");
		}
		else{
		    mysql_query ("UPDATE $channel_table SET lcn='$d[lcn]' WHERE id='$m$d[sid]'");	
		}
		
	}
	else {
		mysql_query ("INSERT INTO $pid_table (id,sid,pid,table_name,codec,bitrate,error,mux) VALUES ('$d[id]','0','$d[pid]','$d[table]','$d[codec]','$d[bitrate]','$d[error]','$m')");
	}	
}

function update_pcr(){
	global $html_file,$pid_table,$channel_table,$scan_sid_array,$num;
	
	for($i=0;$i<count($scan_sid_array);$i++){
	    $id = mt_rand(10000, 99999);
	    
	    for($k=0;$k<count($html_file);$k++){
	        if(strstr($html_file[$k],"pmt_$scan_sid_array[$i]")){
			    
			    preg_match('/PCR PID: (\d+)/',$html_file[$k],$match1);
			    mysql_query ("INSERT INTO $pid_table (id,sid,pid,table_name,codec,bitrate,error,mux) VALUES ('$id','$scan_sid_array[$i]','$match1[1]','PCR','0','0','0','$num')");
				
				if (preg_match('/Aspect Ratio (\d{1,2}:\d{1})/',$html_file[$k],$match2)){
				    $aspect=$match2[1];
				}
				else {
					preg_match('/Resolution (\d{3,4} x \d{3,4})/',$html_file[$k],$match3);

					if ($match3[1]=='720 x 576'){ 
					    $aspect='4:3';
					}
					else {
						$aspect='16:9';
					}
				}
			    mysql_query ("UPDATE $channel_table SET aspect='$aspect' WHERE id='$num$scan_sid_array[$i]'");
			    break;
		    }	
	    }	
	}		
}

function update_channel_table ($s,$n,$m) {
    
	global $pid_table,$channel_table,$log_table;

	$channel_query = mysql_query ("SELECT id FROM $channel_table WHERE sid='$s' AND mux='$m'");
    $pid_query = mysql_query ("DELETE FROM $pid_table WHERE mux=$m");

	if (mysql_num_rows($channel_query)!=0){
		mysql_query("UPDATE $channel_table SET name='$n',cce=0,dcce=0 WHERE sid='$s' AND mux='$m'");		
	}
	else {
	    mysql_query ("INSERT INTO $channel_table (id,sid,name,mux,status,type,triger_mask,lim,source_name,source_href,multicast,lnb,card) VALUES ('$m$s','$s','$n','$m','110','0','111111','0','luminato','#','0.0.0.0','sputnic','free')");	
	}
   
    

}

function update_errors ($d,$m) {
	global $pid_table;
	mysql_query ("UPDATE $pid_table SET error='$d[error]' WHERE mux='$m'");
}


function get_lcn ($words){
	preg_match('/(\d+)\/(\d+)/',$words,$match);
	return $match[1];
}

function get_table_name ($words){
	
	if ($words[5]=='"Dolby'){
	    $tsreader_name=$words[7];	
	}
	else if ($words[5]=='"Teletext/VBI'){
		$tsreader_name='Teletext';
	}
	else {
		$tsreader_name=$words[6];
	}
	
	if ($tsreader_name=="Network"){$real_name="NIT";}
	else if ($tsreader_name=="Service"){$real_name="SDT";}
	else if ($tsreader_name=="Event"){$real_name="EIT";}
	else if ($tsreader_name=="Time"){$real_name="TDT";}
	else if ($tsreader_name=="Program"){$real_name="PAT";}
	else if ($tsreader_name=="13818-1"){$real_name="Privat";}
	else $real_name=$tsreader_name;
	return $real_name;
}

function check_programs($stream) {
	fputs ($stream,"program\r");
    sleep(1);
	$program_list=explode("202  ",wait("complete"));
	
	for ($i=1;$i<count($program_list);$i++) {
	    parsing_program_string ($program_list[$i]);	
	}
    update_pcr(); 	
}

 
function parsing_program_string ($stroka) {
    global $num,$scan_sid_array;
	
 	preg_match('/(\d{3,4})(.*)/',$stroka,$match);
	$sid=$match[1];
	$name=iconv("ISO-8859-5", "UTF-8", trim($match[2]));
	$scan_sid_array[]=$sid;
    update_channel_table($sid,$name,$num); 
}

function html_export ($stream) {
	global $mux,$mux_table;
	
	$mid=$mux['num'];
	
    fputs ($stream,"export html-12 export/html-$mid.htm\r");
    wait("exported");
	$html=file("export/html-$mid.htm");
	
	for ($i=0;$i<count($html);$i++){
		
		if(preg_match('/Transport Stream ID: (\d+)/',$html[$i],$match)){
			$mux['tsid']=$match[1];
		}
		
		if(preg_match('/Original Network ID: (\d+) \(.*\)/',$html[$i],$match)){
            $mux['nid']=$match[1];			
			break;
		}	
         
	}
	
    return $html;	
}

?>                                             