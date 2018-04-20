<?php

function echo_button ($activity,$png,$onclick,$title) {
	global $current_point;
    
    if ($activity=='on'){
    	
    	if($png=='log.png'){
    		echo "<a href='log.php?cp=$current_point'><img src='image/$png' title='Журнал'></a>";
    	}
    	else {
    		echo "<img src='image/$png' onclick='$onclick' title='$title'>";
    	}
    }
    else {
        echo "<img src='image/$png' style='cursor:default;' >"; 
    } 

}

if ($current_point=='alarm') {

	echo "<div class='row_of_buttons'>";
	echo_button('off','plus_lite.png','showAllMux()','Раскрыть все муксы');
	echo_button('off','minus_lite.png','hideAllMux()','Свернуть все муксы');
	echo_button('off','scan_lite.png','scanAllMux()','Обновить данные на всех муксах');
	echo "</div>";
    echo "<div class='row_of_buttons'>";
	echo_button('off','log_lite.png','showForm(this)','Добавить пользователя');
	echo_button('on','search.png','showForm(this)','Поиск');
	echo_button('off','set_lite.png','showForm(this)','Вывод данных');
	echo "</div>";

    if (isset($root_access)) {
    	echo "<img id='shift_line' src='image/line.png'>";
    	echo "<div class='row_of_buttons'>";
	    echo_button('off','add_user_lite.png','showForm(this)','Добавить пользователя');
	    echo_button('off','add_point_lite.png','showForm(this)','Добавить контрольную точку');
	    echo_button('off','add_mux_lite.png','showForm(this)','Добавить транспондер');
	    echo "</div>";
	    echo "<div class='row_of_buttons'>";
	    echo_button('off','del_user_lite.png','showForm(this)','Удалить пользователя');
	    echo_button('off','del_point_lite.png','showForm(this)','Удалить контрольную точку');
	    echo_button('off','del_mux_lite.png','showForm(this)','Удалить транспондер');
	    echo "</div>";
    }

    if (isset($local_admin_access)) {
    	echo "<img id='shift_line' src='image/line.png'>";
	    echo "<div class='row_of_buttons'>";
        echo_button('off','add_mux_lite.png','showForm(this)','Добавить транспондер');
	    echo_button('off','del_mux_lite.png','showForm(this)','Удалить транспондер');
	    echo "</div>";
    }

    die();
}


if (isset($root_access)) {

	echo "<div class='row_of_buttons'>";
	echo_button('on','plus.png','showAllMux()','Раскрыть все муксы');
	echo_button('on','minus.png','hideAllMux()','Свернуть все муксы');
	echo_button('on','scan.png','scanAllMux()','Обновить данные на всех муксах');
	echo "</div>";
    echo "<div class='row_of_buttons'>";
	echo_button('on','log.png','showForm(this)','Добавить пользователя');
	echo_button('on','search.png','showForm(this)','Поиск');
	echo_button('on','set.png','showForm(this)','Вывод данных');
	echo "</div>";
	echo "<img id='shift_line' src='image/line.png'>";
	echo "<div class='row_of_buttons'>";
	echo_button('on','add_user.png','showForm(this)','Добавить пользователя');
	echo_button('on','add_point.png','showForm(this)','Добавить контрольную точку');
	echo_button('on','add_mux.png','showForm(this)','Добавить транспондер');
	echo "</div>";
	echo "<div class='row_of_buttons'>";
	echo_button('on','del_user.png','showForm(this)','Удалить пользователя');
	echo_button('on','del_point.png','showForm(this)','Удалить контрольную точку');
	echo_button('on','del_mux.png','showForm(this)','Удалить транспондер');
	echo "</div>";

}

if (isset($local_admin_access)) {

	echo "<div class='row_of_buttons'>";
	echo_button('on','plus.png','showAllMux()','Раскрыть все муксы');
	echo_button('on','minus.png','hideAllMux()','Свернуть все муксы');
	if (isset($la_on_point)){
	    echo_button('on','scan.png','scanAllMux()','Обновить данные на всех муксах');
    }
    else {
    	echo_button('off','scan_lite.png','scanAllMux()','Обновить данные на всех муксах');
    }
	echo "</div>";
    echo "<div class='row_of_buttons'>";
	echo_button('on','log.png','showForm(this)','Добавить пользователя');
	echo_button('on','search.png','showForm(this)','Поиск');
	echo_button('on','set.png','showForm(this)','Вывод данных');
	echo "</div>";
	echo "<img id='shift_line' src='image/line.png'>";
	echo "<div class='row_of_buttons'>";
	if (isset($la_on_point)){
        echo_button('on','add_mux.png','showForm(this)','Добавить транспондер');
	    echo_button('on','del_mux.png','showForm(this)','Удалить транспондер');		
	}
    else {
    	echo_button('off','add_mux_lite.png','showForm(this)','Добавить транспондер');
	    echo_button('off','del_mux_lite.png','showForm(this)','Удалить транспондер');
    }
	echo "</div>";
}

if (isset($duty_access)) {

	echo "<div class='row_of_buttons'>";
	echo_button('on','plus.png','showAllMux()','Раскрыть все муксы');
	echo_button('on','minus.png','hideAllMux()','Свернуть все муксы');
	echo_button('off','scan_lite.png','scanAllMux()','Обновить данные на всех муксах');
	echo "</div>";
    echo "<div class='row_of_buttons'>";
	echo_button('on','log.png','showForm(this)','Добавить пользователя');
	echo_button('on','search.png','showForm(this)','Поиск');
	echo_button('on','set.png','showForm(this)','Вывод данных');
	echo "</div>";

}


?>