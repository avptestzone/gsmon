<?php
require_once '../base.php';

mysql_connect ($mysql_server,$mysql_login,$mysql_pass);
mysql_select_db ($dbname);
mysql_query("SET NAMES 'utf8'");
?>

<div class='forms' >
    <select name='del_point_name' style='width:120px;'>
       <?php 
        $point_query = mysql_query("SELECT * FROM point_table");
        while($point=mysql_fetch_array($point_query)){
        	echo "<option>$point[short_name]</option>";
        }
       ?>
    </select>   
	<img id='del_point' src='image/del(in_form).png' onclick="sendForm(this)">
</div>