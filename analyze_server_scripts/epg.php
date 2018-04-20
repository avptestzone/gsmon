<?php

function read_time($str){
	
	$x=explode(' ',$str);
	$y=explode('.',$x[0]);
	$z=explode(':',$x[1]);
	
	$day=$y[0];
	$month=$y[1];
	$year=$y[2];
	$hour=$z[0];
	$minute=$z[1];
	
	//Отнимаем 5 часов, чтобы получить UTC
	$result = mktime($hour,$minute,0,$month,$day,$year) - 25200;
	return $result;	
}    

function read_length ($str) {
	$x=explode(':',$str);
	$hour=(int)$x[0];
	$min=(int)$x[1];
	
	$duration=($hour*60+$min)*60;
	return $duration;
	
	
}


function xmltv_export ($stream) {
	global $num,$epg_table,$epg_file;
    fputs ($stream,"export html-20 export/epg-$num.htm\r");
    wait("exported");
	$file=file("export/epg-$num.htm");
	$mega_epg_query= "INSERT INTO $epg_table (sid,mux,start,stop,name,description,content) VALUES ";
	
	foreach ($file as $stroka) {

		if(preg_match('/Channel (\d{1,3}\/\d{4})/',$stroka,$channel)){
			$temp_sid=explode('/',$channel[1]);
			$sid=$temp_sid[1];

			preg_match_all('/Starts: (\d{1,2}\.\d{2}\.\d{4} \d{1,2}:\d{1,2})/',$stroka,$start_array);
			preg_match_all('/Length: (\d{2}:\d{2}:\d{2})/',$stroka,$length_array);
			preg_match_all('/<BR>Name: (.*?)<BR>(.*?)<FONT SIZE/',$stroka,$info_array);
			preg_match_all('/Content: (.*?)\(general\)/',$stroka,$content_array);
			
			for ($i=0;$i<count($start_array[1]);$i++){
			    
				$start=read_time($start_array[1][$i]);
				$length=read_length($length_array[1][$i]);
				$stop=$start+$length;
				$name=addslashes(iconv("ISO-8859-5", "UTF-8", $info_array[1][$i]));
				$description=addslashes(strip_tags(iconv("ISO-8859-5", "UTF-8", $info_array[2][$i])));
				$content=addslashes($content_array[1][$i]);

				$mega_epg_query.="('$sid','$num','$start','$stop','$name','$description','$content'),";
				
			}
			
		}
	}
	mysql_query(substr($mega_epg_query,0,-1));

}

?> 