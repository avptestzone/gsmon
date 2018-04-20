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

function thumb_url ($p){

    if($p=='u1'){
       $url='http://192.168.111.38/ts_ufa_1';
    }
    
    if($p=='u2'){
       $url='http://192.168.111.38/ts_ufa_2';
    }
    
    if($p=='ors'){
       $url='../ts_ors';
    }
    
    return $url;
}


$m_num=htmlspecialchars($_GET['mux']);
$control_point=htmlspecialchars($_GET['cp']);
require_once '../base.php';
$thumbs_url=thumb_url($control_point);

mysql_connect ($mysql_server,$mysql_login,$mysql_pass);
mysql_select_db ($dbname);
mysql_query("SET NAMES 'utf8'");
    
$mux = mysql_fetch_array(mysql_query("SELECT * FROM $mux_table WHERE num='$m_num'"));
$m_name=urlencode($mux['name']);
$mname=str_split($mux['name']);
$mux_bitrate = round($mux['bitrate'],3);
$mcast=$mux['ip'];
$mport=$mux['port'];
$network=$mux['network'];
$frequency=$mux['frequency'];
$nid=$mux['nid'];
$tsid=$mux['tsid'];
$lum_name=$mux['lum_name'];
$lum_href=$mux['lum_href'];

/* Маска:
 * mask[0] - инормация о мультикасте
 * mask[1] - миниатюра с аспектом
 * mask[2] - lcn
 * mask[3] - sid/pmt
 * mask[4] - битрейт
 * mask[5] - источник
 * mask[6] - ip мультикаста
 * mask[7] - параметры приема канала (спутниковые или др)
 * mask[8] - карта
 */

$mask=str_split($_SESSION['set_mask']);


if ($mname[0]=='*'){
    $mcast_span="<div ondblclick=changeValues('mcast',$m_num,this) >$mcast</div>:".
                "<div ondblclick=changeValues('mport',$m_num,this) >$mport</div>";

    $tsid_span="<div ondblclick=changeValues('tsid',$m_num,this) >$tsid</div>";
    $nid_span="<div ondblclick=changeValues('nid',$m_num,this) >$nid</div>";
    
    $mask[1]=0;        
}
else {
    $mcast_span=$mcast.':'.$mport;
    $tsid_span=$tsid;
    $nid_span=$nid;
}


if ($mask[0]==1){

	echo "<div class='mux_info'>".
	          "<span>Мультикаст: <b>$mcast_span</b></span>".
	          "<span>Частота: <b><div ondblclick=changeValues('frequency',$m_num,this)>$frequency</div> МГц</b></span>".
	          "<a href=http://gs-mon.ors.o56.ru/rrd/bitrate.php?cp=$control_point&mux=$m_num&mname=$m_name target='_blank'><span>Bitrate: <b>$mux_bitrate Мбит</b></span></a>".
	          "<span>NID: <b>$nid_span</b></span>".
	          "<span>TSID: <b>$tsid_span</b></span>".
	          "<span ondblclick=changeValues('qam_lum',$m_num,this)>Lum: <a href=$lum_href target='_blank'>$lum_name</a></span>".
          "</div>";
}


$channel_query = mysql_query("SELECT * FROM $channel_table WHERE mux=$m_num ORDER BY type");
echo "<div class='thumbnails'>";
	
while ($channel = mysql_fetch_array($channel_query)) {
    $status = str_split($channel['status']);
    $lcn= $channel['lcn'];
    $sid=$channel['sid'];
    $provider=$channel['provider'];

    if ($channel['provider']=='reserve' and $control_point!='ors') {
        $name = $channel['name'].' (Орск)';
    }
    else {
        $name = $channel['name'];
    }
    
    $radio = $channel['type'];
    $id=$channel['id'];
    $aspect=$channel['aspect'];
    $bitrate=$channel['bitrate'];
    $source_name=$channel['source_name'];
    $source_href=$channel['source_href'];
    $card=$channel['card'];
    $lnb=$channel['lnb'];
    $multicast=$channel['multicast'];

     
    $heigth=0; 

    if ($mask[2]==1){
        
        if($mname[0]=='*'){
            if (access()==1){
                $del_img="<img class='del_sham' src='../image/del(in_form).png' onclick=delChannel($id,this)>";
            }
            else {
                $del_img="";
            }
            $name_whithout_span="<b><div class='sham' ondblclick=changeValues('chname',$id,this) >$name</div>".
                                " (<div class='sham' ondblclick=changeValues('lcn',$id,this) >$lcn</div>)".
                                " $del_img</b>";
        }
        else {
            $name_whithout_span="<b>$name ($lcn)</b>";
        }

        $heigth+=20;	
    }
    else {

        if($mname[0]=='*'){
            $name_whithout_span="<b><div class='sham' ondblclick=changeValues('chname',$id,this) >$name</div>".
                                "<img class='del_sham' src='../image/del(in_form).png' onclick=delChannel($id,this)></b>";
        }
        else {
            $name_whithout_span = "<b>$name</b>";
        }
    	
    	$heigth+=20;
    }

    if ($mask[3]==1){
    	$pmt_query=mysql_fetch_array(mysql_query("SELECT * FROM $pid_table WHERE sid=$sid AND table_name='PMT' AND mux='$m_num'"));
        $pmt=$pmt_query['pid'];

       if($mname[0]=='*'){
            $sidpmt="<span><div class='sham' ondblclick=changeValues('sid',$id,this) >$sid</div>".
                    "/<div class='sham' ondblclick=changeValues('pmt',$id,this) >$pmt</div></span>";
        }
        else {
            $sidpmt="<span>$sid/$pmt</span>";
        }

        $heigth+=20;	
    }
    else {
    	$sidpmt='';
    }
    

    if ($mask[4]==1){
        $bitrate_span="<span>Битрейт: $bitrate Mbit</span>";
        $heigth+=20;	
    }
    else {
    	$bitrate_span='';
    }


    if ($mask[5]==1){
        $sourse_span="<span ondblclick=changeValues('sat_lum',$id,this)>Источник: <a href='$source_href' target='_blank'>$source_name</a></span>";
        $heigth+=20;	
    }
    else {
    	$sourse_span=='';
    }


    if ($mask[6]==1){
        $multicast_span="<span>Мультикаст: <div class='change_value' ondblclick=changeValues('ch_mcast',$id,this)>$multicast</div></span>";
        $heigth+=20;    
    }
    else {
        $multicast_span='';
    }


    if ($mask[7]==1){
        $lnb_span="<span>lnb: <div class='change_value' ondblclick=changeValues('lnb',$id,this)>$lnb</div></span>";
        $heigth+=20;    
    }
    else {
        $lnb_span='';
    }

    
    if ($mask[8]==1){
        $card_span="<span>Карта: <div class='change_value' ondblclick=changeValues('card',$id,this)>$card</div></span>";
        $heigth+=20;	
    }
    else {
    	$card_span='';
    }


	if ($status[0]==3) {
		$video_thumb="<img id='$id' onclick='showData(this)' src='$thumbs_url/$id.jpg?".mt_rand(1,10000)."'>";
		$heigth+=220;
	}
	else if ($status[0]==2) {
        $video_thumb="<img class='delete_channel' onclick='deleteChannel($id,this)' src='image/minus_red.png'>".
	    "<img id='$id' onclick='showData(this)' src='$thumbs_url/$id.jpg?".mt_rand(1,10000)."'>";
	    $heigth+=220;
	}
	else if ($status[0]==9) {
	    $video_thumb="<img id='$id' onclick='showData(this)' src='image/no_analyse.jpg'>";
	    $heigth+=220;
	}
    else if ($status[0]==7) {
	    $video_thumb="<img id='$id' onclick='showData(this)' src='image/rental.jpg'>";
	    $heigth+=220;
	}
	else {
		$video_thumb="<img id='$id' onclick='showData(this)' src='$thumbs_url/$id.jpg?".mt_rand(1,10000)."'>".
		"<span class='aspect'>$aspect</span>";
		$heigth+=220;
	}
    

    if ($radio==1){
        $audio_thumb="<img id='$id' onclick='showData(this)' src='$thumbs_url/audio/$m_num"."_$sid"."_0.jpg'>";
        $video_thumb='';
    }
    else {
    	$audio_thumb=''; 
    }

     
    if ($mask[1]==0){
    	$video_thumb='';
    	$audio_thumb='';

        if($mname[0]=='*'){
             $name_span="<span>$name_whithout_span</span>";
        }
        else {
            $name_span="<span id='$id' onclick='showData(this)' style='cursor:pointer;'>$name_whithout_span</span>";
        }
    	
    	$heigth-=220;
    	$width='width:240px;';
    	$color='background:#B4FFB8;';
    } 
    else {
    	$name_span="<span>$name_whithout_span</span>";
    }


	echo "<div class='thumb' style='height:".$heigth."px;$width$color'>".$video_thumb.$audio_thumb.$name_span.$sidpmt.$bitrate_span.$sourse_span.$multicast_span.$lnb_span.$card_span."</div>";
  
}


if ($mname[0]=='*' and access()==1) {
    echo "<div class='thumb' style='width:240px;height:40px;'><img src='image/plus.png' onclick='addChannel(this)'></div>";
}
echo '</div>';	

?>

