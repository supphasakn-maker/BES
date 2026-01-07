<?php
	session_start();
	include_once "../../../config/define.php";
	include_once "../../../include/db.php";
	include_once "../../../include/oceanos.php";
	
	@ini_set('display_errors',DEBUG_MODE?1:0);
	date_default_timezone_set(DEFAULT_TIMEZONE);
	
	$dbc = new dbc;
	$dbc->Connect();

	
	if($_FILES['file']['name']==""){
		echo json_encode(array(
			'success'=>false,
			'msg' => "Please upload photo"
		));
	}else{
		
		if($dbc->HasRecord("variable","name='iAvatarNumber'")){
			$line = $dbc->GetRecord("variable","value","name='iAvatarNumber'");
			$iAvatarNumber = $line['value'];
		}else{
			$iAvatarNumber = 0;
			$dbc->Insert("variable",array(
				"#id" => "DEFAULT",
				"name" => "iAvatarNumber",
				"value" => $iAvatarNumber,
				"#updated" => "NOW()"
			));
		}
		$iAvatarNumber++;
		
		$path = "img/avatar/$iAvatarNumber.png";
		move_uploaded_file($_FILES['file']['tmp_name'],"../../../".$path);
		
		if($dbc->HasRecord("variable","name='iconTrademark'")){
			$dbc->Update("variable",array(
					"name" => "iconTrademark",
					"value" => $path,
					"#updated" => "NOW()"
			),"name='iconTrademark'");
			
		}else{
			$dbc->Insert("variable",array(
					"#id" => "DEFAULT",
					"name" => "iconTrademark",
					"value" => $path,
					"#updated" => "NOW()"
			));
			
		}
		$dbc->Update("variable",array("value" => $iAvatarNumber,"#updated" => "NOW()"),"name='iAvatarNumber'");
		
		echo json_encode(array(
			'success'=>true
		));
	}
	
?>

<?php
	
	$dbc->Close();
?>