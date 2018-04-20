<?php
session_start();
require_once '../base.php';

mysql_connect ($mysql_server,$mysql_login,$mysql_pass);
mysql_select_db ($dbname);
mysql_query("SET NAMES 'utf8'");

$user_query=mysql_query("SELECT * FROM $user_table");

?>


<div class='forms'>
	<select name='user' style='width:120px;'>	    
	    <?  while ($user=mysql_fetch_array($user_query)){
	    	    if($user['login']=='root' or $user['login']==$_SESSION['user'] or $user['login']=='duty') {
	    		    continue;
	    	    }
                echo "<option value='$user[login]'>$user[fio]</option>";
	    	}
	 
	    ?>	    
	</select><br> 
	<img id='del_user' src='image/del(in_form).png' onclick="sendForm(this)">
</div>
