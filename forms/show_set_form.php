<?php
session_start();
$mask=str_split($_SESSION['set_mask']);

($mask[0])?$show_mux="checked='checked'":$show_mux='';
($mask[1])?$show_thumb="checked='checked'":$show_thumb='';
($mask[2])?$show_lcn="checked='checked'":$show_lcn='';
($mask[3])?$show_sid="checked='checked'":$show_sid='';
($mask[4])?$show_bitrate="checked='checked'":$show_bitrate='';
($mask[5])?$show_lum="checked='checked'":$show_lum='';
($mask[6])?$show_ip="checked='checked'":$show_ip='';
($mask[7])?$show_lnb="checked='checked'":$show_lnb='';
($mask[8])?$show_card="checked='checked'":$show_card='';
?>

<div class='forms'>
   <input name="show_mux" type="checkbox" <?php echo $show_mux ?> style='width:20px;' /> Мультикаст</label><br>
   <input name="show_thumb" type="checkbox"<?php echo $show_thumb ?> style='width:20px;' /> Миниатюры</label><br>
   <input name="show_lcn" type="checkbox" <?php echo $show_lcn?> style='width:20px;' /> lcn</label><br>
   <input name="show_sid" type="checkbox" <?php echo $show_sid ?> style='width:20px;' /> sid/pmt</label><br>
   <input name="show_bitrate" type="checkbox" <?php echo $show_bitrate?> style='width:20px;' /> Битрейт</label><br>
   <input name="show_lum" type="checkbox" <?php echo $show_lum?> style='width:20px;'  /> Источник</label><br>
   <input name="show_ip" type="checkbox" <?php echo $show_ip?> style='width:20px;'  /> pim</label><br>
   <input name="show_lnb" type="checkbox" <?php echo $show_lnb?> style='width:20px;' /> lnb</label><br>
   <input name="show_card" type="checkbox" <?php echo $show_card?> style='width:20px;' /> Карта</label><br>   
   <img id='set' src='image/set(in_form).png' onclick="changeSet(this)"><br>
</div>



</div>