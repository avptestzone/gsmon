<?php 
require_once '../base.php';

mysql_connect ($mysql_server,$mysql_login,$mysql_pass);
mysql_select_db ($dbname);
mysql_query("SET NAMES 'utf8'");

$full_name=$_POST['full_name'];
$short_name=$_POST['short_name'];


if (!$full_name or !$short_name){

	echo "Данные указаны не верно<br/><br/>".
	"<input type='text' maxlength=15 name='full_name'> Полное имя".
	"<input type='text' maxlength=3 name='short_name'> Короткое имя*".
	"<p> *латиница, не более трех букв </p>".
	"<img id='add_point' src='image/add(in_form).png' onclick='sendForm(this)'>";

	return;
}

if (!preg_match("/^[0-9a-z]+$/",$short_name)){

	echo "Короткое имя только строчной латиницей!<br/><br/>".
	"<input type='text' maxlength=15 name='full_name' value='$full_name'> Полное имя".
	"<input type='text' maxlength=3 name='short_name'> Короткое имя*".
	"<p> *латиница, не более трех букв </p>".
	"<img id='add_point' src='image/add(in_form).png' onclick='sendForm(this)'>";

	return;
}

$test=mysql_query("SELECT * FROM point_table WHERE short_name='$short_name' OR full_name='$full_name'");

if (mysql_num_rows($test)!=0){

	echo "Точка c таким именем уже существует<br/><br/>".
	"<input type='text' maxlength=15 name='full_name'> Полное имя".
	"<input type='text' maxlength=3 name='short_name'> Короткое имя*".
	"<p> *латиница, не более трех букв </p>".
	"<img id='add_point' src='image/add(in_form).png' onclick='sendForm(this)'>";

	return;
}


foreach ($_POST['admins'] as $admin) {
    $a=mysql_fetch_array(mysql_query("SELECT admin_points FROM $user_table WHERE login='$admin'"));

    if ($a['admin_poins']){
       
    }
    else {
       $b=$short_name;
    }
   
   mysql_query("UPDATE $user_table SET admin_points='$b' WHERE login='$admin'");
}

mysql_query("INSERT INTO point_table (full_name,short_name) VALUES ('$full_name','$short_name')");

mysql_query("CREATE TABLE IF NOT EXISTS `$short_name"."_mux` ( ".
  "`num` int(5) NOT NULL AUTO_INCREMENT,".
  "`name` char(20) COLLATE utf8_unicode_ci NOT NULL,".
  "`ip` char(15) COLLATE utf8_unicode_ci NOT NULL,".
  "`port` int(4) NOT NULL,".
  "`network` char(15) COLLATE utf8_unicode_ci NOT NULL,".
  "`anet` char(15) COLLATE utf8_unicode_ci NOT NULL,".
  "`frequency` int(6) NOT NULL,".
  "`lum_name` char(15) COLLATE utf8_unicode_ci NOT NULL,".
  "`lum_href` varchar(100) COLLATE utf8_unicode_ci NOT NULL,".
  "`nid` int(4) NOT NULL,".
  "`tsid` int(4) NOT NULL,".
  "`bitrate` char(6) COLLATE utf8_unicode_ci NOT NULL,".
  "`minute` int(2) NOT NULL,".
  "`status` int(1) NOT NULL,".
  "`scan` int(1) NOT NULL,".
  "`err_no` int(4) NOT NULL,".
  "`reserve` int(1) NOT NULL,".
  "PRIMARY KEY (`num`)".
  ") ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1;");
echo mysql_error();

mysql_query("CREATE TABLE IF NOT EXISTS `$short_name"."_channel` ( ".
  "`id` int(7) NOT NULL,".
  "`name` char(30) NOT NULL,".
  "`lcn` int(3) NOT NULL,".
  "`sid` int(4) NOT NULL,".
  "`status` int(3) NOT NULL,".
  "`type` int(1) NOT NULL,".
  "`triger_mask` char(6) NOT NULL,".
  "`mux` int(5) NOT NULL,".
  "`lim` int(3) NOT NULL,".
  "`cce` int(10) NOT NULL,".
  "`dcce` int(5) NOT NULL,".
  "`aspect` char(4) NOT NULL,".
  "`bitrate` char(6) NOT NULL,".
  "`source_name` char(15) NOT NULL,".
  "`source_href` varchar(50) NOT NULL,".
  "`multicast` char(20) NOT NULL,".
  "`lnb` varchar(100) NOT NULL,".
  "`card` char(25) NOT NULL,".
  "`provider` char(20) NOT NULL,".
  "PRIMARY KEY (`id`)".
  ") ENGINE=InnoDB DEFAULT CHARSET=utf8;");

mysql_query("CREATE TABLE IF NOT EXISTS `$short_name"."_cph` ( ".
  "`id` int(4) NOT NULL,".
  "`mux` int(5) NOT NULL,".
  "`name` char(30) COLLATE utf8_unicode_ci NOT NULL,".
  "`toggle` int(1) NOT NULL,".
  "`lim` int(5) NOT NULL,".
  "`status` int(5) NOT NULL,".
  "`cph` int(5) NOT NULL,".
  "`p1` int(10) NOT NULL,".
  "`p2` int(10) NOT NULL,".
  "`p3` int(10) NOT NULL,".
  "`p4` int(10) NOT NULL,".
  "`p5` int(10) NOT NULL,".
  "`p6` int(10) NOT NULL,".
  "PRIMARY KEY (`id`)".
  ") ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;");

mysql_query("CREATE TABLE IF NOT EXISTS `$short_name"."_log` (".
  "`id` int(6) NOT NULL,".
  "`open` datetime NOT NULL,".
  "`close` datetime NOT NULL,".
  "`channel` char(30) COLLATE utf8_unicode_ci NOT NULL,".
  "`mux` int(5) NOT NULL,".
  "`type` int(1) NOT NULL,".
  "`error_type` int(4) NOT NULL,".
  "`dcce` int(5) NOT NULL,".
  "PRIMARY KEY (`id`)".
  ") ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;");

mysql_query("CREATE TABLE IF NOT EXISTS `$short_name"."_pid` (". 
  "`id` int(6) NOT NULL,".
  "`sid` int(4) NOT NULL,".
  "`pid` int(6) NOT NULL,".
  "`table_name` char(15) CHARACTER SET utf8 NOT NULL,".
  "`codec` char(15) COLLATE utf8_unicode_ci NOT NULL,".
  "`bitrate` float NOT NULL,".
  "`mux` int(2) NOT NULL,".
  "`error` int(10) NOT NULL,".
  "PRIMARY KEY (`id`)".
  ") ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;");

mysql_query("CREATE TABLE IF NOT EXISTS `$short_name"."_epg` (". 
  "`id` int(10) NOT NULL AUTO_INCREMENT,".
  "`mux` int(5) NOT NULL,".
  "`sid` int(4) NOT NULL,".
  "`start` int(15) NOT NULL,".
  "`stop` int(15) NOT NULL,".
  "`name` varchar(100) NOT NULL,".
  "`description` varchar(500) NOT NULL,".
  "`content` varchar(100) NOT NULL,".
  "PRIMARY KEY (`id`)".
  ") ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;");


if (mysql_error()){
	echo mysql_error();
}
else {
	echo "Контрольная точка успешно добавлена".
       "<img src='image/ok_point_(in_form).png' onclick='refresh_page()'>";
}

?>