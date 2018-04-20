<?php 
require_once '../base.php';

mysql_connect ($mysql_server,$mysql_login,$mysql_pass);
mysql_select_db ($dbname);
mysql_query("SET NAMES 'utf8'");

$mux_table=$_POST['current_point']."_mux";
$name=$_POST['name'];
$slam=str_split($name);
$ip=$_POST['ip'];
$port=$_POST['port'];
$network=$_POST['network'];
$anet=$_POST['anet'];

if ($slam[0]=='*'){
    mysql_query("INSERT INTO $mux_table (name,ip,port,network,frequency,nid,tsid,minute,status,scan,lum_name,lum_href)".
  " VALUES ('$name','0.0.0.0',0,0,0,0,0,0,0,1,'qam','#')");

    if (mysql_error()){
        echo mysql_error();
    }
    else {
        echo "Транспондер успешно добавлен".
             "<img src='image/ok_point_(in_form).png' onclick='refresh_page()'>";
    }
    return;
}

/* Если нет то, проводим всевозможные проверки на правильность ввода */

if (!$name or !$ip or !$port or !$network) {
    echo "Не указан один из аргументов<br/><br/>".
    "<input type='text' maxlength=15 name='name' value=$name> Имя <br/>".
    "<input type='text' maxlength=15 name='ip' value=$ip> Мультикаст ip".
    "<input type='text' maxlength=5 name='port' value=$port> Порт<br/>".
    "<input type='text' maxlength=15 name='network' value=$network> Интерфейс<br/>".
    "<input type='text' maxlength=15 name='anet' value=$anet> Астра<br/><br/>".
    "<img id='add_mux' src='image/add(in_form).png' onclick='sendForm(this)'>";
    
    return;
}


if(!preg_match("/^[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}$/",$ip)){
    echo "Неверно введен multicast ip<br/><br/>".
    "<input type='text' maxlength=15 name='name' value=$name> Имя <br/>".
    "<input type='text' maxlength=15 name='ip'> Мультикаст ip".
    "<input type='text' maxlength=5 name='port' value=$port> Порт<br/>".
    "<input type='text' maxlength=15 name='network' value=$network> Интерфейс<br/>".
    "<input type='text' maxlength=15 name='anet' value=$anet> Астра<br/><br/>".
    "<img id='add_mux' src='image/add(in_form).png' onclick='sendForm(this)'>";
    return;
}


if(!preg_match("/^[0-9]+$/",$port)){
    echo "Неверно указан multicast порт<br/><br/>".
    "<input type='text' maxlength=15 name='name' value=$name> Имя <br/>".
    "<input type='text' maxlength=15 name='ip' value=$ip> Мультикаст ip".
    "<input type='text' maxlength=5 name='port' > Порт<br/>".
    "<input type='text' maxlength=15 name='network' value=$network> Интерфейс<br/>".
    "<input type='text' maxlength=15 name='anet' value=$anet> Астра<br/><br/>".
    "<img id='add_mux' src='image/add(in_form).png' onclick='sendForm(this)'>";
    return;
}

if(!preg_match("/^[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}$/",$network)){
    echo "Неверно введен ip сетевой карты<br/><br/>".
    "<input type='text' maxlength=15 name='name' value=$name> Имя <br/>".
    "<input type='text' maxlength=15 name='ip' value=$ip> Мультикаст ip".
    "<input type='text' maxlength=5 name='port' value=$port> Порт<br/>".
    "<input type='text' maxlength=15 name='network' > Интерфейс<br/>".
    "<input type='text' maxlength=15 name='anet' value=$anet> Астра<br/><br/>".
    "<img id='add_mux' src='image/add(in_form).png' onclick='sendForm(this)'>";
    return;
}

if(!preg_match("/^[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}$/",$anet)){
    echo "Неверно введен ip сетевой карты astra<br/><br/>".
    "<input type='text' maxlength=15 name='name' value=$name> Имя <br/>".
    "<input type='text' maxlength=15 name='ip' value=$ip> Мультикаст ip".
    "<input type='text' maxlength=5 name='port' value=$port> Порт<br/>".
    "<input type='text' maxlength=15 name='network' value=$network> Интерфейс<br/>".
    "<input type='text' maxlength=15 name='anet'> Астра<br/><br/>".
    "<img id='add_mux' src='image/add(in_form).png' onclick='sendForm(this)'>";
    return;
}

$test=mysql_query("SELECT * FROM $mux_table WHERE ip='$ip'");

if (mysql_num_rows($test)!=0){

    echo "Транспондер с таким ip уже существует<br/><br/>".
    "<input type='text' maxlength=15 name='name'> Имя <br/>".
    "<input type='text' maxlength=15 name='ip' value=$ip> Мультикаст ip".
    "<input type='text' maxlength=5 name='port' value=$port> Порт<br/>".
    "<input type='text' maxlength=15 name='network' value=$network> Интерфейс<br/><br/>".
    "<img id='add_mux' src='image/add(in_form).png' onclick='sendForm(this)'>";

    return;
}
mysql_query("INSERT INTO $mux_table (name,ip,port,network,anet,frequency,nid,tsid,minute,status,scan,lum_name,lum_href)".
  " VALUES ('$name','$ip','$port','$network','$anet',0,0,0,0,0,1,'qam','#')");

if (mysql_error()){
  echo mysql_error();
}
else {
  echo "Транспондер будет добавлен в течении двух минут".
       "<img src='image/ok_point_(in_form).png' onclick='refresh_page()'>";
}
?>