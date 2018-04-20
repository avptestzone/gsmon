<?php 
if ($create_point==1){
	echo "<p align='center'> На данный момент ни одна контрольная точка не создана.".
	"<br> Воспльзуйтесь ".
	"<a href='#'>инструкцией</a> по настройке сервера и внесите данные в базу нажав".
	"<img src='image/add_point.png'>";

}
else{

	if ($current_point!='alarm') {
		$mux_query = mysql_query("SELECT num,name,status,scan FROM $current_point"."_mux ORDER BY name");

        if(mysql_num_rows($mux_query)!=0) {

            while ($mux=mysql_fetch_array($mux_query)) {
            	
            	$mux_type=str_split($mux['name']);
            	if ($mux_type[0]=='_') {continue;}
            	if ($mux_type[0]=='*') {continue;}

            	include 'build/mux_small.php';
            }
            
            mysql_data_seek($mux_query, 0); 
            
            while ($mux=mysql_fetch_array($mux_query)) {
            	
            	$mux_type=str_split($mux['name']);
            	
            	if ($mux_type[0]=='_') {
            		include 'build/mux_small.php';
            	}    
            }  

            mysql_data_seek($mux_query, 0); 
            
            while ($mux=mysql_fetch_array($mux_query)) {
            	
            	$mux_type=str_split($mux['name']);
            	
            	if ($mux_type[0]=='*') {
            		include 'build/mux_small.php';
            	}    
            }  

        }  
        

    }		
}


?>