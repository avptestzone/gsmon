<?php
require_once '../base.php';

mysql_connect ($mysql_server,$mysql_login,$mysql_pass);
mysql_select_db ($dbname);
mysql_query("SET NAMES 'utf8'");

$point_query=mysql_query("SELECT * FROM $point_table");

$point_select = "<select multiple size=3 name='points[]' style='width:120px;'>";

while($point=mysql_fetch_array($point_query)){
	$point_select.="<option value=$point[short_name]>$point[full_name]</option>";
}

$point_select.="</select>";
?>

<div class='forms'>
    <form action='base/search.php' method='POST' target='_blank'>
	<b>Точки:</b><br>
	<? echo $point_select?><br>
	<p><b>Поиск:</b><br> 
	<select name='type' style='width:120px;'>
	    <option value='name'>Имя</option>
	    <option value='reserve'>Резерв</option>
	    <option value='vcodec'>mpeg4</option>
	    <option value='acodec'>AC3</option>
	    <option value='sat'>Источник</option>
	    <option value='lnb'>LNB</option>
            <option value='report'>Отчет</option>
	</select> 
	<p><b>Значение:</b>
	<input style='width:120px;' type='text' name='value'><br>
	<input style='width:16px;position: absolute;bottom: 10px;right: 10px;' type='image' src='image/search(in_form).png'>
	
    </form>
</div>
