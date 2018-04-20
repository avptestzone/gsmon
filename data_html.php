----------------------
<div>
    Порог ошибок:<br>
    <input id="lpm" type="text" value="<?php echo $limit_per_min ?>" style="width: 32px;margin-top:2px">&nbsp;За минуту<br>
    <input id="lph" type="text" value="<?php echo $limit_per_hour ?>" style="width: 32px;margin-top:2px">&nbsp;За час<br>
    <input type="submit" value="обновить" style="margin-top: 3px;" onclick="changeLimit(<?php echo "$id" ?>)">   
    <span id="ch_lm"></span>
<br>----------------------
</div>

<div>
   <label><input name="all" type="checkbox" <?php echo $all ?> /> Анализировать канал</label><br>
   <label><input name="video" type="checkbox"<?php echo $video ?> /> Анализировать видео</label><br>
   <label><input name="scramble" type="checkbox" <?php echo $scramble ?> /> Анализировать кодировку</label><br>
   <label><input name="error" type="checkbox" <?php echo $error?> /> Анализировать ошибки</label><br>
   <label><input name="move" type="checkbox" <?php echo $move?> /> Анализировать движение</label><br>
   <input type="submit" value="обновить" style="margin-left: 5px; margin-top: 5px; " onclick="changeMask(<?php echo "$id" ?>,this)"> 
   <br><span id="ch_ms"></span>
</div>