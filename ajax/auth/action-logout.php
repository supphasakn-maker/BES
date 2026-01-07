<?php
	session_start();
	include_once "../../config/define.php";
	include_once "../../include/db.php";
	include_once "../../include/concurrent.php";
	
	@ini_set('display_errors',DEBUG_MODE?1:0);
	date_default_timezone_set(DEFAULT_TIMEZONE);
	
	$dbc = new dbc;
	$dbc->Connect();
	$concurrent = new concurrent($dbc);
	
	unset($_SESSION['admin_mode']);
	unset($_SESSION['edit_mode']);
	
	unset($_SESSION['auth']);
	unset($_SESSION['exptime']);
	unset($_SESSION['concurrent']);
	
	$concurrent->unallocate();
	$dbc->Close();
?>