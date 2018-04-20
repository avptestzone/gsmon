<html>

<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link rel="stylesheet" type="text/css" href="../css/search.css">
<script type="text/javascript" src="../java/search.js"></script>
</head>

<body>


<?php
require_once '../base.php';

mysql_connect ($mysql_server,$mysql_login,$mysql_pass);
mysql_select_db ($dbname);
mysql_query("SET NAMES 'utf8'");

$points=$_POST['points'];
$type=$_POST['type'];
$value=$_POST['value'];

if(!$points){
	echo "Ни одна контрольная точка не выбрана";
	die();
}

if(!$value and $type!='vcodec' and $type!='acodec' and $type!='reserve' and $type!='report'){
	echo "Не введено значение";
	die();
}

foreach ($points as $cp_short) {
	$ch_table=$cp_short."_channel";
	$m_table=$cp_short."_mux";
	$p_table=$cp_short."_pid";

    $cp_full=mysql_fetch_array(mysql_query("SELECT full_name FROM $point_table WHERE short_name='$cp_short'"));


	if ($type=='name') {
        $channel_query=mysql_query("SELECT * FROM $ch_table WHERE name LIKE '%$value%' ORDER BY name");
    }

    if ($type=='sat') {
    	$channel_query=mysql_query("SELECT * FROM $ch_table WHERE source_name LIKE '%$value%' ORDER BY name");
    } 

    if ($type=='lnb') {
    	$channel_query=mysql_query("SELECT * FROM $ch_table WHERE lnb LIKE '%$value%' ORDER BY name");
    }

    if ($type=='reserve') {
        $channel_query=mysql_query("SELECT * FROM $ch_table WHERE provider='reserve' ORDER BY name");
    }
    
    if ($type=='report') {
        $status_query=mysql_query("SELECT * FROM $ch_table WHERE status REGEXP '[368]..' ORDER BY status");
mail("padalko@orsk.ufanet.ru", "My Subject", "Line 1\nLine 2\nLine 3");
    }

    echo "<div><h3>$cp_full[full_name]</h3>";

    if ($type=='vcodec' or $type=='acodec') {
    	
    	if ($type=='vcodec') {$codec_name='h.264';$table_name='Video';}
    	if ($type=='acodec') {$codec_name='Dolby';$table_name='Audio';}

    	$codec_query=mysql_query("SELECT sid,mux FROM $p_table WHERE codec='$codec_name' AND table_name='$table_name'");
        
        $audio_channel=array(); 

    	while ($codec=mysql_fetch_array($codec_query)){
    		
            if (in_array($codec['sid'], $audio_channel)){
            	continue;
            }
            else {
                $audio_channel[]=$codec['sid'];	
            }
    		
    		$id=$codec['mux'].$codec['sid'];

            $channel=mysql_fetch_array(mysql_query("SELECT * FROM $ch_table WHERE id=$id"));
            $mux=mysql_fetch_array(mysql_query("SELECT name FROM $m_table WHERE num='$codec[mux]'"));

            /* Достаем данные о pmt,pcr,codec*/ 
            $pid_query=mysql_query("SELECT * FROM $p_table WHERE sid='$channel[sid]'");
            $acodec=array();

            while($pid=mysql_fetch_array($pid_query)){
                if ($pid['table_name']=='Video') {$vcodec=$pid['codec'];} 
                if ($pid['table_name']=='Audio') {$acodec[]=$pid['codec'];} 
                if ($pid['table_name']=='PMT') {$pmt=$pid['pid'];} 
                if ($pid['table_name']=='PCR') {$pcr=$pid['pid'];} 
            }

            create_div($channel,$mux['name'],$cp_full['full_name'],$vcodec,$acodec,$pmt,$pcr);

    	}

 	    echo "<div style='clear:both;'></div></div>";
 	    continue;
    }
    

    while($channel=mysql_fetch_array($channel_query)){
      	$mux=mysql_fetch_array(mysql_query("SELECT name FROM $m_table WHERE num='$channel[mux]'"));
            
        $pid_query=mysql_query("SELECT * FROM $p_table WHERE sid='$channel[sid]'");
        $acodec=array();

        while($pid=mysql_fetch_array($pid_query)){
            if ($pid['table_name']=='Video') {$vcodec=$pid['codec'];} 
            if ($pid['table_name']=='Audio') {$acodec[]=$pid['codec'];}
            if ($pid['table_name']=='PMT') {$pmt=$pid['pid'];} 
            if ($pid['table_name']=='PCR') {$pcr=$pid['pid'];} 
        }

       	create_div($channel,$mux['name'],$cp_full['full_name'],$vcodec,$acodec,$pmt,$pcr);
    }
    
    
    while ($status=mysql_fetch_array($status_query)){
        $status_type=str_split($status['status']);
        $name=$status['name'];
        $s_lcn=$status['lcn'];
        if ($status_type[0]==3){
            $error_text='Нет видео';
        }
        if ($status_type[0]==6){
            $error_text='Нет движения';
        }
        if ($status_type[0]==8){
            $error_text='Вещается плашка';
        }
        
        echo "<b>$name ($s_lcn)</b> - $error_text</br>";
               
    }
    echo "<div style='clear:both;'></div></div>";

}



function create_div ($c,$m,$p,$vc,$ac,$pm,$pc) {
	global $thumbs_url;  
    $audio_span='';

    for($i=0;$i<count($ac);$i++) {
    	$a=$ac[$i];
    	$n=$i+1;
    	$audio_span.="<span>Аудио кодек $n: $a</span><br/>";
    }

    $src="../image/logos/".iconv("UTF-8", "WINDOWS-1251", $c['name']).".png";

    if(!file_exists($src)){
    	$src="../image/logo.png";
    }
    else {
    	$src="../image/logos/$c[name].png";
    }

	echo "<div class='search_obj'>".
	     "<img src='$src'><br/>".	     
	     "<span><b>$c[name] $lcn</b></span><br/>".
	     "<span class='global'><b>$m</b></span><br/>".
	     "<div class='closed'>".
	     "<span>lcn: $c[lcn]</span><br/>".
	     "<span>sid: $c[sid]</span><br/>".
	     "<span>pmt: $pm</span><br/>".
	     "<span>pcr: $pc</span><br/>".
	     "<span>Luminato: <a href='$c[source_href]' target='_blank'>$c[source_name]</a></span><br/>".
	     "<span>Прием: $c[lnb]</span><br/>".
	     "<span>Видео кодек: $vc</span><br/>".$audio_span.
	     "<span>Aspect: $c[aspect]</span><br/>".
	     "<span>Карта: $c[card]</span><br/>".
	     "<span>&nbsp</span>".
	     "</div>".
             "<img class='button' src='../image/open.png' onclick='openInfo(this)'>".
	     "</div>";

}


?>

</body>
</html>