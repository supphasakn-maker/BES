<?php
	session_start();
	include_once "../../../config/define.php";
	include_once "../../../include/db.php";
	include_once "../../../include/oceanos.php";

	@ini_set('display_errors',DEBUG_MODE?1:0);
	date_default_timezone_set(DEFAULT_TIMEZONE);

	$dbc = new dbc;
	$dbc->Connect();
	$os = new oceanos($dbc);

	if($_POST['rate_difference']==""){
		echo json_encode(array(
			'success'=>false,
			'msg'=>'Please input rate diff!'
		));
	}else{
		$rate_difference = $os->save_variable("rate_difference",$_POST['rate_difference']);
		$os->save_log(0,$_SESSION['auth']['user_id'],"change-rate-difference",$_POST['rate_difference'],array(
			'exchange' => $_POST['rate_difference'],
			'date' => date('Y-m-d H:i:s')
		));
		echo json_encode(array('success'=>true));

		// Create a new cURL resource
			$ch = curl_init();

			// Set the cURL options
			curl_setopt($ch, CURLOPT_URL, "https://www.bowinsgroup.com/ipn/response_silver.php");
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
				'exchange' => $_POST['rate_difference'],
			]));
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Ignore SSL certificate verification

			// Execute the cURL request
			$response = curl_exec($ch);

			// Check for errors
			if (curl_errno($ch)) {
				$error_msg = curl_error($ch);
				// Handle the error
			}

			// Close the cURL resource
			curl_close($ch);

			// Process the response
			echo $response;
		
	}

	$dbc->Close();
?>
