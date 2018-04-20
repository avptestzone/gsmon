<?php 
ini_set('session.gc_maxlifetime', 120960);
ini_set('session.cookie_lifetime', 120960);
session_start();  

 
if(isset($_GET['logout'])){
    unset($_SESSION['user']);
    unset($_SESSION['user_access']);
    unset($_SESSION['user_fio']);
    unset($_SESSION['user_point']);
    session_destroy();
    echo "<meta http-equiv='REFRESH' content='0;url=index.php'>";
}

require_once 'base.php';
mysql_connect ($mysql_server,$mysql_login,$mysql_pass);
mysql_select_db ($dbname);
mysql_query("SET NAMES 'utf8'");

require_once("auth/authorization.php");

if (!isset($_SESSION['user'])){
    
    if (isset($_SESSION['auth_text'])){
        $auth_text=$_SESSION['auth_text'];    
    }
    else{
        $auth_text='* Введите учетные данные';
    }
    include "auth/login.php";
}
else {
    (isset($_GET['cp']))?$current_point=$_GET['cp']:$current_point=$_SESSION['default_point'];
    include "auth/access.php";
	include "body.php";
}
mysql_close();
?>




