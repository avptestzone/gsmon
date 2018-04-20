<?php 

function convert_time ($sec) {
	$timestamp = mktime(null, null, $sec);
	return date("H:i:s",$timestamp);
}

function echo_tr ($s,$t,$n,$d){
	echo "<tr>".
    	 "<td  align='center'>".date('d.m.Y G:i:s',$s)."</td>".
    	 "<td  align='center'>".convert_time($t)."</td>".
    	 "<td  align='center'>".$n."</td>".
    	 "<td>".$d."</td>".
         "</tr>";
}


$cid=htmlspecialchars($_GET['cid']);
$control_point=htmlspecialchars($_GET['cp']);
require_once '../base.php';

mysql_connect ($mysql_server,$mysql_login,$mysql_pass);
mysql_select_db ($dbname);
mysql_query("SET NAMES 'utf8'");

$time=time();

$sid=mysql_fetch_array(mysql_query("SELECT sid FROM $channel_table WHERE id=$cid"));

$programm_query=mysql_query("SELECT * FROM $epg_table WHERE start<$time AND stop>$time AND sid=$sid[0]");

if (mysql_num_rows($programm_query)){
    $current_programm=mysql_fetch_array($programm_query);
}
else {
    echo '<p> Пересканируйте мукс<span id="close_window" onclick="closeWindow()">X</span>';
    die();
}


?>
<span id="close_window" onclick="closeWindow()">X</span> 
<table>
	<caption>Сейчас</caption>
	<tr>
		<th width='82px'>Начало</th>
		<th width='60px'>Время</th>
		<th width='140px'>Название</th>
		<th width='490px'>Описание</th>
	</tr>
    <tr>
    	<td  align="center"><? echo date("d.m.Y H:i:s",$current_programm[start])?></td>
    	<td  align="center"><? echo convert_time($current_programm[stop]-$current_programm[start])?></td>
    	<td  align="center"><? echo $current_programm[name]?></td>
    	<td><? echo $current_programm[description]?></td>
    </tr>
</table>
<br><br>
<table>
	<caption>Далее</caption>
	<tr>
        <th width='82px'>Начало</th>
        <th width='60px'>Время</th>
        <th width='140px'>Название</th>
        <th width='490px'>Описание</th>
	</tr>
    <?php
    $all_programm=mysql_query("SELECT * FROM $epg_table WHERE start>$time AND sid=$sid[0]"); 
    
    $i=0;

    while($programm=mysql_fetch_array($all_programm)){
        
        if ($i>9){break;}
        $i++;
        $length=$programm['stop']-$programm['start'];
        echo_tr($programm['start'],$length,$programm['name'],$programm['description']); 
    }  
    ?>
</table>

