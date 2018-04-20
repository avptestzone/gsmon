<?php

function wait($for_what='') {
    global $fp;	
    $r=''; 
    
    if ($for_wait){
	
        do {  
            $r.=fgetc($fp);
	        $s=socket_get_status($fp);
            if (strstr($r,$for_what)){
                break;
            }
        } 
        while ($s['unread_bytes']);
	}
	else {
	
        do { 
            $r.=fgetc($fp);
	        $s=socket_get_status($fp);
        } 
        while ($s['unread_bytes']);
	}

    return $r;
}

function astra_scan () {
	global $astra_url,$mux,$control_point;
	$mid=$mux['num'];
	
	$http=fopen($astra_url."rescan.php?cp=$control_point"."&mid=$mid","r");

	while(!feof($http)) {
        $content .= fread($http,1024);
    }

	fclose($http); 
}


function count_cce_by_astra($index){ 
    global $pid_table,$channels,$num;
    	
    $sid=$channels['sid'][$index];
	$cce=$channels['cce'][$index];
    $limit=$channels['lim'][$index];
	
	
    $sum = mysql_fetch_array (mysql_query("SELECT SUM(error) "
            . "FROM $pid_table WHERE sid='$sid' AND mux='$num'"));
  
    $dcce=$sum[0]-$cce;
	 
    if ($dcce<0) {                                                              
        mysql_query("UPDATE $pid_table SET count=0 WHERE sid='$sid' AND mux='$num'");           
        $channels['cce'][$index]=0;   
        return 12345;
    }
    else if ($dcce>$limit){
        $channels['cce'][$index]=$sum[0];
        return $dcce;        
    } 
    else {
		$channels['cce'][$index]=$sum[0];
        return 0;
    }
    
   
    
}


function cce_per_hour($index,$cpm,$m){
	global $cph_table,$cph_file,$mux,$channels;
	
	$cid=$channels['id'][$index];
	$name=$channels['name'][$index];
	$mid=$mux['num'];
	$cce_full=$channels['cce'][$index];
    
    $graf_query = mysql_query("SELECT * FROM $cph_table WHERE id=$cid");
    
   
    if (mysql_num_rows($graf_query)==0){
        mysql_query("INSERT INTO $cph_table (id,mux,name,toggle,lim,status,cph,p1,p2,p3,p4,p5,p6) "
                . "VALUES ('$cid','$mid','$name','2','10','0','0','$cce_full','$cce_full','$cce_full','$cce_full','$cce_full','$cce_full')");
	return;		
    }
    
    $graf = mysql_fetch_array($graf_query);
        

    $toggle=$graf['toggle']; 
    $p="p$toggle";
    $cce_old=$graf[$p];
    $limit=$graf['lim'];

    $cph=$cce_full-$cce_old;
    
    if ($toggle==6){
        $toggle=1;
    }
    else{
        $toggle++;
    }	
	
    if ($cph>$limit AND $m=1){
        mysql_query("UPDATE $cph_table SET $p=$cce_full, toggle=$toggle, status=1, cph=$cph WHERE id=$cid");       
		fputs($cph_file, "$cid  $cph  \r\n");
    }
    else{
        mysql_query("UPDATE $cph_table SET $p=$cce_full, toggle=$toggle, status=0, cph=$cph WHERE id=$cid");
        fputs($cph_file, "$cid  norm  \r\n");
    }
}

function load_video ($index,$nmf) {
    
    global $fp,$channels;
    
    $cid=$channels['id'][$index];
	$sid=$channels['sid'][$index];
	
	$img = $cid.'.jpg';
    $src = 'C:\\ts\\'.$img;
	
    for ($i=0;$i<20;$i++) {                                                      
       
        fputs ($fp, "thumbnail $sid $img\r");                               
        $output = wait();
        $code = substr($output,0,3);                                                                            

        if ($code == "531") { 
            copy("C:\\ts\\pattern\\no_video.jpg","C:\\ts\\$cid.jpg"); 	
            return 200;                                                           
        }                                                                        
	  
	    if (($code == "532") and ($i==19)) { 
            copy("C:\\ts\\pattern\\no_video.jpg","C:\\ts\\$cid.jpg");
            return 300;                                                          
        }                                                                       
		
	    if ($code == "555" ) {
	        return 400; 
        }

        if ($code == "313") {        

            if (md5_file($src) == '0428d5ee094051402e898ae53a39af2e') {
                return 500;                                       
            }
            if ($nmf) {                                                          
                $df=diff($img);

                if($df<20){
                    return 600;
                }

                
				if($df==22222222){
                    return 800;
                }
            }
	        return 100;   
        
	    }     
        
		usleep(500000); 
    }
	return 300;
}

function load_audio ($index) {
    
    global $fp,$mux,$channels,$audio_export,$audio_time;
    
	$mid=$mux['num'];
    $sid=$channels['sid'][$index];
               
        
    if ($audio_export==0){  
        fputs ($fp, "export html audio/$mid.htm\r");                           
        wait("exported");         
    }  

    $channels['audio'][$index]=20; 
    $channels['audio_name'][$index]=0;                                                  
 
    for($k=0;$k<5;$k++){                                                     
  
        $audio_file = "C:\\ts\\audio\\$mid"."_$sid"."_$k.jpg";                        
	
        if(file_exists($audio_file) and filemtime($audio_file)>=$audio_time){
                                           
            $image = new Imagick($audio_file);
            $height = $image->getImageHeight();
                    
            if ($height==52){
                        
                $name = "C:\\ts\\audio\\$mid"."_$sid"."_$k.jpg";
				$a1 = new Imagick($name);
                $a2 = new Imagick("C:\\ts\\pattern\\no_audio.jpg");
				$no_audio = $a1->compareImages($a2, Imagick::METRIC_MEANSQUAREERROR);
				$delta = round($no_audio[1]*100000);
				
                    
                if ($delta == 0){
                    $channels['audio'][$index]=20;
		            $channels['audio_name'][$index]=$name;                             
                    break;                                                 
                }
                else {
                    $channels['audio'][$index]=10;
                    $channels['audio_name'][$index]=0;
	                break;    
                }
            }
                
        }
             
            
    }       
}

function diff ($thumb_name){
	
    $old_thumb = "C:\\ts\\old_thumbs\\$thumb_name";    
    $new_thumb = "C:\\ts\\$thumb_name";       
    $plashka_sd = "C:\\ts\\pattern\\plashka_sd.jpg";
    $plashka_hd = "C:\\ts\\pattern\\plashka_hd.jpg";
	$green_screen = "C:\\ts\\pattern\\green_screen.jpg";
    

    $image2 = new Imagick($new_thumb);
    $plashka1 = new Imagick($plashka_sd);
    $plashka2 = new Imagick($plashka_hd);
	$gs = new Imagick($green_screen);
    
    $height2 = $image2->getImageHeight();
    
    if ($height2==216){
        $sd = $image2->compareImages($plashka1, Imagick::METRIC_MEANSQUAREERROR);
        $d = round($sd[1]*100000);

        if ($d<400){ 
            return 22222222;
        }
    }
    
    if ($height2==180){
        $hd = $image2->compareImages($plashka2, Imagick::METRIC_MEANSQUAREERROR);
        $d = round($hd[1]*100000);

        if ($d<400){
            return 22222222;
        }
		
		/*Если hd изображение зеленый экран - крах кодера*/
		$mc = $image2->compareImages($gs, Imagick::METRIC_MEANSQUAREERROR);
		$d = round($mc[1]*100000);
		
		if ($d<200){
            return 33333333;
        }
		
    }
    
   
    if(!file_exists($old_thumb)){
        return 11111111;
    }    
        
    $image1 = new Imagick($old_thumb);
    $height1 = $image1->getImageHeight();
    
    if ($height1!=$height2){
        return 11111111;
    }
    
    $result = $image1->compareImages($image2, Imagick::METRIC_MEANSQUAREERROR);
    $d = round($result[1]*100000);
    
    return $d;     
 
    
}

function copy_thumb($ind,$cid){
 
    if (!is_dir("C:\\ts\\no_movie")){
        mkdir("C:\\ts\\no_movie");  
    }

    mkdir("C:\\ts\\no_movie\\$ind");
	  
	$old_thumb = "C:\\ts\\old_thumbs\\$cid.jpg";
    $new_thumb = "C:\\ts\\$cid.jpg";
    $first_thumb = "C:\\ts\\no_movie\\$ind\\1.jpg";
    $second_thumb = "C:\\ts\\no_movie\\$ind\\2.jpg"; 
        
	copy($old_thumb,$first_thumb);
    copy($new_thumb,$second_thumb);       
}


function zabbix_file($index){
    global $channels,$mux,$zabbix;
	
	$mid=$mux['num'];
	$new_status = $channels['new_status'][$index];
	$status = str_split($channels['new_status'][$index]);
	$cid = $channels['id'][$index];
	$name = $channels['name'][$index];
	$lcn = $channels['lcn'][$index];
	$cce = $channels['cpm'][$index];

    $cph_string = file("C:\\ts\\cph_temp\\cph_list_$mid.txt");
	
    // разбираем файл с данными об ошибках за час
    for ($k=0;$k<count($cph_string);$k++){
        $str=explode('  ', $cph_string[$k]);
        if ($str[0] == "$cid"){
            if ($str[1]!='norm'){
                $cph_text = "$str[1] cce/час";    
            }
            else {
                $cph_text='';
            }
           
        }
    }

    $video='';
    $audio='';
    $error='';
    
    if ($status[0]==2){
        $video = "Отсутсвует в потоке";
    }
    
    if ($status[0]==3){
        $video = "Нет видео";
    }
    
    if ($status[0]==5){
        $video = "Закодирован";
    }
        
    if ($status[0]==6){
        $video = "Нет движения";
    }
    
    if ($status[0]==8){
        $video = "Вещается плашка";
    }
    if ($status[1]==2){
        $audio = "и звука";
    }

    if ($status[2]==1){
        if($cph_text!=''){
            $error = "$cph_text + $e";
        }else{
            $error = "+$e cce";    
        }
        
    }
    
    if (!preg_match('/[1479][19][09]/', $new_status)){
        $text.="$video $audio $error";    
    }
    else if($cph_text!=''){
        $text.="$cph_text";       
    }   
    else {
        $text='norm';
    }
    
    fputs($zabbix, "- \"name.[$lcn - $name]\" \"$text\"\r\n");
    //echo 'zabbix_file done\r\n';	
}

function summary_status($index){                                                  
    
    global $mux,$mux_table,$channels;
	
	$mid=$mux['num'];                 
    $triger = $channels['triger_mask'][$index];
    $video=$channels['video'][$index];
    $audio=$channels['audio'][$index];

        
    if ($channels['cpm'][$index]>0){                                                 
        $error = 1;    
    }
    else {
        $error = 0;
    }                                                                        

		 
    if (preg_match('/0...../', $triger)){                                    
        $video=900;                                                            
        $audio=90;                                                           
        $error=9;                                                           
    }
        
    if (preg_match('/10..../', $triger)){
        $video=900;
    }
        
    if (preg_match('/1....0/', $triger)){
        $audio=90;
    }
        
    if (preg_match('/1..0../', $triger)){
        $error=9;
    }
        
    if ($video==500 and preg_match('/110.../', $triger)){                    
        $video=700;                                                         
    }
        
		
    if ($video==200){                             
        $audio=90;                                                     
        $error=9;     
    }
        
		
    if($video == 600){                                                  
        $error=0;
    }
        
		
    if($video==300){                                                     
        $audio=10;                                                        
        $error=0;
    }
        
        
    if($video==800){                                                         
        $audio=10;                                                          
        $error=0;
    }
        
    $channels['new_status'][$index]=$video+$audio+$error;
	
    if (check_error($channels['new_status'][$index])){
	    
		if($mux['status']!=1){
		    mysql_query("UPDATE $mux_table SET status=1 WHERE num='$mid'");	
			$mux['status']=1;
		}
    }
}


function channel_logging($index) {
	global $log_table,$channel_table,$mux,$channels;
    
	$mid=$mux['num'];
	$cid=$channels['id'][$index];
	$name=$channels['name'][$index];
	$new_status=$channels['new_status'][$index];
	$old_status=$channels['old_status'][$index];
	$cpm=$channels['cpm'][$index];
	
    $new = check_error($new_status);
    $old = check_error($old_status);
    $id = mt_rand(100000, 999999);
  
	
    if ($new and !$old){
	    	
        $open_sql = "INSERT INTO "
                . "$log_table (id,open,channel, mux,  type, error_type, dcce)"
                . " VALUES ('$id',NOW(),'$name','$mid','0','$new_status','$cpm')";
	
        mysql_query($open_sql);                                                  
    }

    if (($new and $old)and($new_status == $old_status)){

	    $update_sql = "UPDATE $log_table SET dcce=dcce+$cpm "
            . "WHERE channel='$name'"
            . "AND close='0000-00-00 00:00:00'";
	
        mysql_query($update_sql);                                               
    }

	 
    if (($new and $old)and($new_status != $old_status)){
	
        $open_sql="INSERT INTO "
                  ."$log_table (id,open,channel, mux, type, error_type, dcce)"
                  ."VALUES ('$id',NOW(),'$name','$mid','0','$new_status','$cpm')";

        $close_sql="UPDATE $log_table SET close = NOW() "
                 . "WHERE channel='$name' "
                 . "AND close='0000-00-00 00:00:00'";
				 
        mysql_query($close_sql);                                                
        mysql_query($open_sql);                                                
         
    }

	 
    if (!$new and $old){

        $close_sql="UPDATE $log_table SET close = NOW() "
                 . "WHERE channel='$name' "
                 . "AND close='0000-00-00 00:00:00'";
				 
        mysql_query($close_sql);                                                                                         
    }                                                                              
	return $id;		
}

function mux_logging ($old_status,$new_status) {
	global $mux,$log_table;
	$old = str_split($old_status);
	$new = str_split($new_status);
	$mid=$mux['num'];
	
	for ($i=0;$i<count($old);$i++) {
		
		if ($old[$i]==1 and $new[$i]==1) {continue;}
		if ($old[$i]==2 and $new[$i]==2) {continue;}
		
		switch ($i){
			case 0:$name='bit';break;
			case 1:$name='nit';break;
			case 2:$name='eit';break;
			case 3:$name='tdt';break;
		}

		if ($old[$i]==1 and $new[$i]==2) {
			$id = mt_rand(100000, 999999);
			mysql_query ("INSERT INTO $log_table (id,open,channel,mux,type,error_type,dcce) VALUES ('$id',NOW(),'$name','$mid','1','$new_status','0')");	
		}
	
		if ($old[$i]==2 and $new[$i]==1) {
		    mysql_query("UPDATE $log_table SET close = NOW() WHERE mux='$mid' AND channel='$name' AND close='0000-00-00 00:00:00'");
		}
		echo mysql_error();
	}
} 

function check_error($stat) {

    if (preg_match('/[1479][159][09]/', $stat)){
        return 0;   
    }
    else {
        return 1;
    }
}


 function get_pid ($stream) {

	fputs ($stream, "pids\r");  
    sleep(1);	
	$p=explode("209 0x",wait("complete"));
	
    return $p;	
 }
 
 
function get_sid ($words){
	preg_match('/(\d+)\/(\d+)/',$words,$match_with);
		
	if ($match_with[2]) {
	    return $match_with[2];
	}
	else {
	    preg_match('/program (\d+)/',$words,$match_without);
		return $match_without[1];
	}
	
}

function channel_bitrate($index,$pid_list) {
	global $channels;
	
	for($i;$i<count($pid_list);$i++){
	    $sid=trim(get_sid($pid_list[$i]));
        
		if ($sid==$channels['sid'][$index]) {
			$word = explode(' ',$pid_list[$i]);
			$channels['bitrate'][$index]+=$word[4];

		}		
	}

}

function mux_bitrate ($pid_list) {
	global $mux,$tehno;
	 
	$mux_bitrate=0;
	$nit=1;
	$eit=1;
	$tdt=1;
	
	for($i;$i<count($pid_list);$i++){
	    $word = explode(' ',$pid_list[$i]);
        
		if (hexdec($word[0])!='8191'){
			$mux_bitrate+=$word[4];
		}
		
		if ($tehno==1){
			$nit=1;
			$eit=1;
			$tdt=1;
			continue;
		}
		
		if (hexdec($word[0])=='16'){
			$nit=1;
		}
		if (hexdec($word[0])=='18') {
			$eit=1;
		}		
		if (hexdec($word[0])=='20') {
			$tdt=1;
		}		
		
	}
	
 
	if ($mux_bitrate>480){
		$overscribe=2;	
	}
    else {
		$overscribe=1;
	}	

	$err_no=$overscribe.$nit.$eit.$tdt;
	
	mux_logging($mux['err_no'],$err_no);
	
	if($overscribe+$nit+$eit+$tdt>4){
	
	    $mid=$mux['num']; 
	    $tdt_log=fopen("C:\\ts\\log\\tdt_$mid".".txt","a+");
		fputs($tdt_log,print_r($pid_list,true));
		fclose ($tdt_log);
	
		$mux['bitrate']=$mux_bitrate;
		$mux['status']=$mux['status']+2;
		$mux['err_no']=$err_no;
	}
	else {
		$mux['bitrate']=$mux_bitrate;
		$mux['err_no']=$err_no;
	}
}
                    

function mux_to_base(){
	global $mux,$mux_table;
	
	$mid=$mux['num'];
	$status=$mux['status'];
	$bitrate=$mux['bitrate'];
	$err_no=$mux['err_no'];
	$scan=$mux['scan'];
	$minute=$mux['minute']+1;

	
	if ($mux['tsid'] && $mux['nid']){
		$tsid=$mux['tsid'];
		$nid=$mux['nid'];	
		
		mysql_query("UPDATE $mux_table SET status='$status',bitrate='$bitrate',err_no='$err_no',scan=$scan,minute=$minute,nid=$nid,tsid=$tsid,reserve=0 WHERE num='$mid'");
	}
	else {
	    mysql_query("UPDATE $mux_table SET status='$status',bitrate='$bitrate',err_no='$err_no',scan=$scan,minute=$minute,reserve=0 WHERE num='$mid'");	
	}
	//echo 'mux_to_base done\r\n';
}

function channel_to_base($index){
	global $mux,$channels,$channel_table,$log_table;
	
    $mid=$mux['num'];	
	$cid=$channels['id'][$index];
	$status=$channels['new_status'][$index];
	$bitrate=$channels['bitrate'][$index];
	$cce=$channels['cce'][$index];
	$cpm=$channels['cpm'][$index];
	$name=$channels['name'][$index];

	mysql_query("UPDATE $channel_table SET status='$status',bitrate='$bitrate',cce='$cce',dcce=$cpm,provider='reserve' WHERE id='$cid'");
		

}


 function provider ($stream) {
	global $channels,$html_file,$mux;
    
	if (!$html_file) {
		$mid=$mux['num'];
		
	    fputs ($stream,"export html-12 export/html-$mid.htm\r");
        wait("exported");
	    $html_file=file("export/html-$mid.htm");
	}
    	
    for ($i=0;$i<count($html_file);$i++){
		
		if(preg_match('/Provider Name: (.*?)<BR>/',$html_file[$i],$provider)){
			
			if ($provider[1]=='reserve'){
				$mux['reserve']=1;
			}
			
			preg_match('/sdt_(\d+)/',$html_file[$i],$sid);
			
			for ($z=0;$z<count($channels['sid']);$z++){
			    
				if ($channels['sid'][$z]==$sid[1]){
					$channels['new_provider'][$z]=$provider[1];
				}	
			}

		}
         
	}
 }

 function last_copy(){
	global $channels;
	
	for ($i=0;$i<count($channels['id']);$i++){
		$cid=$channels['id'][$i];
		copy("C:\\ts\\$cid.jpg","C:\\ts\\old_thumbs\\$cid.jpg"); 
	}
 }
 
 

?>