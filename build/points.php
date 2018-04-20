<?php 
 
$point_query = mysql_query("SELECT * FROM point_table ORDER BY num");

if (mysql_num_rows($point_query)==0){
	$create_point=1;
}
else {
        $create_point=0;
        
	while ($point=mysql_fetch_array($point_query)){
            create_point_menu($point,$current_point); 		
	}	
}


if ($current_point=='alarm'){
    echo "<div id='alarm' style='background:#537A13;' class='current_point' onclick='showPoint(this)'>Alarm</div>";
}
else {
    echo "<div id='alarm' style='background:#537A13;' class='point' onclick='showPoint(this)'>Alarm</div>";
}


function create_point_menu ($p,$cs){
	if ($p['short_name']==$cs){
		$class='current_point';
	}
	else {
		$class='point';
	}
	echo "<div id='$p[short_name]' class='$class' onclick='showPoint(this)' ondblclick='show'>$p[full_name]</div>";	
}

?>