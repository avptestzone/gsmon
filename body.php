<html>

<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link rel="stylesheet" type="text/css" href="css/main.css">
<script type="text/javascript" src="java/main.js"></script>
</head>

<body>

<div id='main'>
    <div id='left_column'>
        <div id='points'><?php  include 'build/points.php';  ?></div>
	    <div id='monitor'><?php include 'build/monitor.php'; ?></div>
    </div>
    <div id='right_column'>
        <div id='user'><?php include 'build/user.php'; ?></div>
	    <div id='buttons'><?php include 'build/buttons.php'; ?></div>
	    <div id='data_box'><?php include 'build/data_box.php'; ?></div>
	    <div id='triger_box'></div>
    </div>
</div>
<div id='overlay'></div>
<div id='wrapp'></div>
</body>
</html>