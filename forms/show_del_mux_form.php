<?php
require_once '../base.php';

mysql_connect ($mysql_server,$mysql_login,$mysql_pass);
mysql_select_db ($dbname);
mysql_query("SET NAMES 'utf8'");
$cp=$_GET['cp'];

?>

<div class='forms'>
    <select name='del_mux_num' style='width:120px;'>
       <?php 
        $mux_query = mysql_query("SELECT * FROM $cp"."_mux");
        while($mux=mysql_fetch_array($mux_query)){
        	
        	if ($mux['scan']==2){
        		continue;
        	}
        	echo "<option value='$mux[num]'>$mux[name]</option>";
        }
       ?>
    </select>   
	<img id='del_mux' src='image/del(in_form).png' onclick="sendForm(this)">
</div>