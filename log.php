<?php
$control_point=htmlspecialchars($_GET['cp']);
require_once "base.php";

mysql_connect ($mysql_server,$mysql_login,$mysql_pass);
mysql_select_db ($dbname);
mysql_query("SET NAMES 'utf8'");
?>

<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link rel="stylesheet" type="text/css" href="css/sort.css">
<script type="text/javascript" src="java/sort.js"></script>
<script type="text/javascript" src="java/calendar.js"></script>
</head>    

<body>
    <div id='log'>
        <table class='sortable'>
            <tr>
                <th>Начало</th>
                <th>Завершение</th>
                <th>Мукс</th>
                <th>Канал</th>
                <th>Ошибка</th>
            </tr>
<?php

function convert_date($date){
    
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
    return date ("d M H:i:s",mktime($hour,$minute,$second,$month,$day,$year));
}

$channel = htmlspecialchars($_GET['channel']); 
$day1 = htmlspecialchars($_GET['day1']); 
$day2 = htmlspecialchars($_GET['day2']);

$today = date("n:j");

if ($channel){
    $sql="SELECT * FROM $log_table WHERE channel LIKE '%$channel%' ORDER BY open";
}  
else if ($day1){

    if ($day2==''){
        $sql="SELECT * FROM $log_table WHERE DATE_FORMAT(open,'%d-%m-%Y') = '$day1' OR "
            . "close='0000-00-00 00:00:00' ORDER BY open";
    }
    else {
        $sql="SELECT * FROM $log_table WHERE (DATE_FORMAT(open,'%d-%m-%Y') >= '$day1' "
            . "AND DATE_FORMAT(open,'%d-%m-%Y') <='$day2') OR "
            . "(close='0000-00-00 00:00:00' AND DATE_FORMAT(open,'%d-%m-%Y')"
            . " <='$day1')  ORDER BY open";
    }

} else {
    $sql="SELECT * FROM $log_table WHERE DATE_FORMAT(open,'%c:%e') = '$today' "
            . "OR close='0000-00-00 00:00:00' ORDER BY open";
}

$spisok = mysql_query($sql);

/**/
$mux_query = mysql_query("SELECT num,name FROM $mux_table");

while ($m=mysql_fetch_array($mux_query)){
    $mux_array[$m['num']]=$m['name'];
}

echo mysql_error();
while ($stroka = mysql_fetch_array($spisok)){
    
    $css_class = '';
    $css_id = '';
    $open_time = convert_date($stroka['open']);
    $channel = $stroka['channel'];
    $status = str_split($stroka['error_type']);
    $dcce=$stroka['dcce'];
    $id=$stroka['id'];
    $mux=$mux_array[$stroka['mux']];
    $video='';
    $audio='';
    $error='';
    
    ($stroka['close'] != '0000-00-00 00:00:00')?$close_time = convert_date($stroka['close']):$close_time="-- не восст. --";
    

    if($stroka['type']==1){

        if($stroka['channel']=='bit') {
            $text_error='Битрейт > 48Мбит';
            $error_color='#FBC199';
        } 

        if($stroka['channel']=='nit') {
            $text_error='Отсутсвует NIT';
            $error_color='#fff';
        }

        if($stroka['channel']=='eit') {
            $text_error='Отсутсвует EIT';
            $error_color='#fff';
        }

        if($stroka['channel']=='tdt') {
            $text_error='Отсутсвует TDT';
            $error_color='#fff';
        } 

        echo    "<tr  $css_class $css_id style='background:$error_color;text-align:center;'>"
              . "<td width='140px'><$open_time></td>"
              . "<td width='140px'><$close_time></td>"
              . "<td width='140px'></td>"
              . "<td width='200px'>$mux</td>"
              . "<td width='220px'>$text_error</td>"
              . "</tr>" ;

        continue;      
    }
    
    if ($status[0]==2){
        $video = "Отсутсвует в потоке";
        $error_color='#FBC199';
    }
            
    if ($status[0]==3){
        $video = "Нет видео";
        $error_color='#FBC199';
    }
            
    if ($status[0]==5){
        $video = "Закодирован";
        $error_color='#FBC199';
    }
       
    if ($status[0]==6){
        $video = "Нет движения";
        $css_class='class = \'nomove\''; $css_id = "id='$id'";
        $error_color='#fff';
    }

    if ($status[0]==8){
        $video = "Включена плашка";
    }
    
    if ($status[1]==2){
        $audio = "нет звука";
        $error_color='#FBC199';
    }

    if ($status[2]==1){
        $error = "+ $dcce error";
        $error_color='#FFF';
        
        if ($dcce>=5){
            $error_color='#FBC199';
        }
    }

    if ($status[2]==3){
        $error='Канал сменил источник';
        $error_color='#FFF';
    }
             
    $text_error="$video $audio $error";   
    
    
    echo    "<tr  $css_class $css_id style='background:$error_color;text-align:center;'>"
            . "<td width='140px'><$open_time></td>"
            . "<td width='140px'><$close_time></td>"
            . "<td width='140px'>$mux</td>"
            . "<td width='200px'>$channel</td>"
            . "<td width='220px'>$text_error</td>"
            . "</tr>" ;
}


?>

</table>
   </div> 
   <a href="log.php?cp=<?php echo $control_point ?>"><button>Сегодня</button></a>
   <a href="http://gs-mon.ors.o56.ru/index.php?cp=<?php echo $control_point?>"><button>Вернуться</button></a>
    <div>
        <form action="log.php" method="GET">
           <p><big>Выборка за период</big><br>
           <input name='day1' type="text"onfocus="this.select();lcs(this)" onclick="event.cancelBubble=true;this.select();lcs(this)">
            - <input name='day2' type="text" onfocus="this.select();lcs(this)" onclick="event.cancelBubble=true;this.select();lcs(this)">
            <input type='hidden' value="<?php echo $control_point ?>" name="cp">
           <input type='submit' value= 'Сформировать'>
        </form>
    </div>
    <div>
        <form action="log.php" method="GET">
           <p><big>Выборка по каналу</big><br>
           <input type="text" id='channel' name='channel'><br>
           <input type='submit' value="Сформировать">
           <input type='hidden' value="<?php echo $control_point ?>" name="cp">
        </form>
    </div>
   <p> * Чтобы проверить, действительно ли не было движения, кликни по строке с проблемой.
   <div id='monitor'></div>    
</body>
</html>
