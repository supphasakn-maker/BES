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

    $data = array(
        '#id' => "DEFAULT",
        "bank" => $_POST['bank'],
        "date" => $_POST['date'],
        "#loader" => $_POST['loader'],
        "#paid" => $_POST['paid'],
        "#tr_fix" => $_POST['tr_fix'],
        "#tr_non_fix" => $_POST['tr_non_fix'],
        "#load_balance" => $_POST['load_balance'],
        "#balance" => $_POST['balance'],
        "#tr_non_fix_usd" => $_POST['tr_non_fix_usd'],
        "#interest_summary" => $_POST['interest_summary'],
        "#interest_paid" => $_POST['interest_paid'],

    );

    if($dbc->Insert("bs_transfer_report",$data)){
        $claim_id = $dbc->GetID();
        echo json_encode(array(
            'success'=>true,
            'msg'=> $claim_id
        ));


        $claim = $dbc->GetRecord("bs_transfer_report","*","id=".$claim_id);
        $os->save_log(0,$_SESSION['auth']['user_id'],"tr_report-add",$claim_id,array("bs_transfer_report" => $claim));
    }else{
        echo json_encode(array(
            'success'=>false,
            'msg' => "Insert Error"
        ));
    }


$dbc->Close();
?>