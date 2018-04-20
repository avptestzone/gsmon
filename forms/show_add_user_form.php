<?php
require_once '../base.php';

mysql_connect ($mysql_server,$mysql_login,$mysql_pass);
mysql_select_db ($dbname);
mysql_query("SET NAMES 'utf8'");

$point_query=mysql_query("SELECT * FROM $point_table");

?>


<div class='forms'>
    <input type='text' maxlength=20 name='login' style='width:90px;'> Login 
	<input type='password' maxlength=20 name='pass' style='width:90px;'> Пароль
	<input type='text' maxlength=50 name='fio' style='width:90px;'> Имя 
	<p> Уровень доступа: <br>
	<select name='access' style='width:120px;' onchange='access(this)'>
		<option value='2'>Администратор</option>
		<option value='1'>Локальный администратор</option>
		<option value='0'>Дежурный</option>
	</select>
	<p> Точка по-умочанию: <br>
	<select name='default_point' style='width:120px;'>	    
	    <? while ($point=mysql_fetch_array($point_query)){
                echo "<option value='$point[short_name]'>$point[full_name]</option>";
	    	}
	    ?>	    
	</select> 
    <p> Точки: <br>
	<select id='user_point_select' multiple size=3 name='points' style='width:120px;' disabled>	    
	    <?  mysql_data_seek($point_query, 0);
	        while ($point=mysql_fetch_array($point_query)){
                echo "<option value='$point[short_name]'>$point[full_name]</option>";
	    	}
	    ?>	    
	</select></p><br><br>
	<img id='add_user' src='image/add(in_form).png' onclick="sendForm(this)">
</div>