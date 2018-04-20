<?php 
$mid=$mux['num'];
$mname=$mux['name'];
$mstatus=$mux['status'];
$mscan=$mux['scan'];

if ($mscan==2){
   return;
}

if (isset($duty_access) and !isset($la_on_point)){
	$mscan=1;
}

if ($mscan==1){
	$mscan_button_src='image/refresh_light_small.png';
	$onclick="";
} 
else {
	$mscan_button_src='image/refresh_dark_small.png';
	$onclick="onclick='scanMux($mid,this)'";
}

?>

<div id='<?php echo $mid; ?>' class='mux' <? if ($mux_type[0]=='*'){echo "style='background:#9CCFDA'";} ?> >
	<div class='mtop'>
	    <div class='mname' ondblclick=changeValues('mname',<?php echo $mid; ?>,this)><?php echo $mname; ?></div>
	    <div class='mstatus'><span></span></div>
	    <div class='mscan'><img src='<?php echo $mscan_button_src; ?>' <? echo $onclick ?> class='scan_button'></div>
	    <div class='mexpand'><img src='image/plus_small.png' onclick='showMux(this)' class='show_button'></div>		
	</div>
</div>