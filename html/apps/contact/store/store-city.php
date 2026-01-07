<?php
	session_start();
	include_once "../../../config/define.php";
	include_once "../../../include/db.php";
	
	@ini_set('display_errors',DEBUG_MODE?1:0);
	date_default_timezone_set(DEFAULT_TIMEZONE);
	
	$dbc = new dbc;
	$dbc->Connect();


	$aColumns = array('id','name','country');
	$sIndexColumn = "id";
	
	$sTable = "db_cities";
	$sLimit = "";
	$sWhere = "";

	
	if ( isset($_REQUEST['filter'])){
		if ( $sWhere == "" ){
			$sWhere = "WHERE ";
		}else{
			$sWhere .= " AND ";
		}
		$sWhere .= $_REQUEST['filter']." ";
	}
	
	$sQuery = "
		SELECT SQL_CALC_FOUND_ROWS ".str_replace(" , ", " ", implode(", ", $aColumns))."
		FROM   $sTable
		$sWhere
	";
	
	$rResult = $dbc->Query($sQuery) or die(mysql_error());
	
	/* Data set length after filtering */
	$sQuery = "
		SELECT FOUND_ROWS()
	";
	$rResultFilterTotal = $dbc->Query( $sQuery) or die(mysql_error());
	$aResultFilterTotal = $dbc->Fetch($rResultFilterTotal);
	$iFilteredTotal = $aResultFilterTotal[0];
	
	/* Total data set length */
	$sQuery = "
		SELECT COUNT(".$sIndexColumn.")
		FROM   $sTable
	";
	$rResultTotal = $dbc->Query( $sQuery)  or die(mysql_error());
	$aResultTotal = $dbc->Fetch($rResultTotal);
	$iTotal = $aResultTotal[0];
	
	
	/*
	 * Output
	 */
	$output = array(
		"iTotalRecords" => $iTotal,
		"iTotalDisplayRecords" => $iFilteredTotal,
		"aaData" => array()
	);
	
	while ( $aRow = $dbc->Fetch( $rResult ) ){
		$row = array();
		for ( $i=0 ; $i<count($aColumns) ; $i++ ){
			$row[] = $aRow[$i];
			if($i==0)$row["DT_RowId"] = $aRow[$i];
			
		}
		$output['aaData'][] = $row;
	}
	
	echo json_encode( $output );
	
	$dbc->Close();

?>





