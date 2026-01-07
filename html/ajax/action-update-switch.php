<?php
	session_start();
	$_SESSION['auto_update'] = $_POST['status']=="false"?false:true;
?>

