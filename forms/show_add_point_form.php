<?php
require_once '../base.php';

mysql_connect ($mysql_server,$mysql_login,$mysql_pass);
mysql_select_db ($dbname);
mysql_query("SET NAMES 'utf8'");

$user_query=mysql_query("SELECT * FROM $user_table");

?>

<div class='forms'>
    <input type='text' maxlength=15 name='full_name'> Полное имя
	<input type='text' maxlength=3 name='short_name'> Короткое имя*
    <p>Локальные админы:</p>
    <select name='admins[]' style='width:120px;' multiple size=3>	    
	    <?  while ($user=mysql_fetch_array($user_query)){

	    	    if($user['access']=='1') {
	    		     echo "<option value='$user[login]'>$user[fio]</option>";
	    	    }
	    	}
	 
	    ?>	    
	</select>
    <p> *строчная латиница, не более трех букв </p>
	<img id='add_point' src='image/add(in_form).png' onclick="sendForm(this)">
</div>