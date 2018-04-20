<?php 
function echo_time($date,$x){
    
    $splitdate = preg_split('[\D]', $date);
    $year=$splitdate[0];
    $month=$splitdate[1];
    $day=$splitdate[2];

    if ($splitdate[4]=='0'){
        $minute=59;
        
        if ($splitdate[3]=='0'){
            $hour=23;    
        }
        else{
            $hour=$splitdate[3]-1;
        }    
    }
    else{
        $minute=$splitdate[4]-1;
        $hour=$splitdate[3];
    }
    
    $second=$splitdate[5];
    if ($x=='1'){
    	$result = date("d.m.y G:i:s", mktime($hour,$minute,$second,$month,$day,$year));
    	return $result;
    }
    if ($x=='2'){
        $result=time()-60-mktime($hour,$minute,$second,$month,$day,$year);
        return form_time($result);
    }
    

}

function form_time ($t) {
    
    $year = (floor($t/31622400)>0) ? floor($t/31622400)."г " : '';
    $month = (floor($t/2635200)>0) ? (floor($t/2635200) % 12)."м " : '';
    $day = (floor($t/86400)>0) ? (floor($t/86400) % 30)."д " : '';
    $hour = (floor($t/3600)>0) ? (floor($t/3600) % 24)."ч " : '';
    $min = (floor($t/60)%60>0)? (floor($t/60)%60)."м " : '';
    $sec= ($t%60)."с";
    
    if ($year){
        return $year.$month.$day;	
    }
    if ($month){
    	return $month.$day.$hour;
    }
    if ($day){
    	return $day.$hour.$min;
    }

    return $hour.$min.$sec;

}


function error_text($status,$cce){
    global $error_color;
    $video='';
    $audio='';
    $error='';
    
    if ($status[0]==2){
        $video = "Отсутсвует в потоке";
        $error_color='#ff7518';
    }
            
    if ($status[0]==3){
        $video = "Нет видео";
        $error_color='#ff7518';
    }
                      
    if ($status[0]==6 and $status[1]!=2){
        $video = "Нет движения";
        $error_color='#bebebe';
    }

    if ($status[0]==6 and $status[1]==2){
        $video = "Нет движения";
        $error_color='#ff7518';
    }
            
    if ($status[0]==8){
        $video = "Вещается плашка";
        $error_color='#bebebe';
    }
            
    if ($status[1]==2){
        $audio = "нет звука";
        $error_color='#bebebe';
    }

    if ($status[2]==1){
        $error = "+$cce error";
        $error_color='#bebebe';
         
        if ($cce>5) {
             $error_color='#ff7518';
        }

    }
    
    if ($status[0]==5){
        $video = "Закодирован";
        $error_color='#ff7518';
    }

    return "<span >$name: $video $audio $error</span>"; 
}



require_once 'base.php';

mysql_connect ($mysql_server,$mysql_login,$mysql_pass);
mysql_select_db ($dbname);
mysql_query("SET NAMES 'utf8'");

/*Переменная для расчета четности строки*/
$odd=1;

$point_query=mysql_query("SELECT * FROM $point_table ORDER BY num");

echo "<table id='alarm_table'>".
	    "<tr>".
		    "<th width='100px'>Точка</th>".
		    "<th width='180px'>Мультикаст</th>".
		    "<th width='480px'>Проблема</th>".
		    "<th width='130px'>Начало</th>".
		    "<th width='90px'>Время</th>".
	    "</tr>";

while($point=mysql_fetch_array($point_query)){
    
    $mux_array=array();
    $l_table=$point['short_name']."_log";
    $m_table=$point['short_name']."_mux"; 
	
    $log_query=mysql_query("SELECT * FROM $l_table WHERE close='0000-00-00 00:00:00' ORDER BY mux");
    $mux_query=mysql_query("SELECT num,name FROM $m_table ORDER BY name");
 
    while ($mux=mysql_fetch_array($mux_query)){
	$mux_array[$mux['num']]=$mux['name'];
    }
   
    while ($log=mysql_fetch_array($log_query)){

        $tr=$mux_array[$log['mux']]; 
        $begin=echo_time($log['open'],'1');
        $duration=echo_time($log['open'],'2');

        if ($log['type']==0){
            $text=$log['channel'].' '.error_text(str_split($log['error_type']),$log['dcce']);	
        }
        else if ($log['type']==1){

 	    if($log['channel']=='bit'){
                $e='Битрейт > 48Мбит';
	        $error_color='#ff7518';
	    }

 	    if($log['channel']=='nit'){
                $e='Отсутсвует NIT';
	        $error_color='#bebebe';
	    }

	    if($log['channel']=='eit'){
	        $e='Отсутсвует EIT';
	        $error_color='#bebebe';
	    }

	    if($log['channel']=='tdt'){
	        $e='Отсутсвует TDT';
	        $error_color='#bebebe';
	    }
              
	    $text=$e;
	 }
		 
	     
        if($odd%2==0){
            $class='even';
        }
        else {
            $class='odd';	
        }  
        $odd++;

	echo "<tr>".
	     "<td class=$class width='100px'>$point[full_name]</td>".
	     "<td class=$class width='180px'>$tr</td>".
	     "<td class=$class width='480px' style='background:$error_color;'>$text</td>".
	     "<td class=$class width='130px'>$begin</td>".
	     "<td class=$class width='90px'>$duration</td>".
	     "</tr>";
    }

}

echo "</table>";

?>

<div style='clear:both'></div>