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

    if(!empty($_POST["id"])){ 
        // Fetch state data based on the specific country 
        $query = "SELECT * FROM bs_products_type WHERE product_id = ".$_POST['id']." AND status = 1 "; 
        $result = $dbc->query($query); 
         
        // Generate HTML of state options list 
        if($result->num_rows > 0){ 
            echo '<option value="">Select Product Type</option>'; 
            while($row = $result->fetch_assoc()){  
                echo '<option value="'.$row['id'].'">'.$row['name'].'</option>'; 
            } 
        }else{ 
            echo '<option value="">Product Type not available</option>'; 
        } 
    }
	$dbc->Close();
?>
