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

	echo '<div class="mb-3">';
		echo '<table class="table table-sm table-bordered">';
			echo '<thead>';
				echo '<tr>';
					echo '<th class="text-center">วันที่</th>';
					echo '<th class="text-center">Brand</th>';
					echo '<th class="text-center">Lot Number</th>';
					echo '<th class="text-center">น้ำหนัก</th>';
					echo '<th class="text-center">Producer</th>';
					echo '<th class="text-center">Product</th>';
					echo '<th class="text-center">INVOICE</th>';
					echo '<th class="text-center">Pre CoC</th>';
					echo '<th class="text-center">COUNTRY</th>';
					echo '<th class="text-center">Type</th>';
					
				echo '</tr>';
			echo '</thead>';
			echo '<tbody>';
			$sql="SELECT * FROM bs_incoming_plans WHERE created BETWEEN '".$_POST['created_date_form']." 00:00:00' AND '".$_POST['created_date_to']." 23:59:59' 
			AND import_date BETWEEN '".$_POST['date_form']." 00:00:00' AND '".$_POST['date_to']." 23:59:59' 
			AND status=1 ORDER BY import_date ASC";
			$rst = $dbc->Query($sql);
			while($plan = $dbc->Fetch($rst)){
				$product = $dbc->GetRecord("bs_products","*","id=".$plan['product_type_id']);
				$type_import = $dbc->GetRecord("bs_products_import","*","code=".$plan['remark']);
				echo '<tr>';
					echo '<td class="text-center">'.$plan['import_date'].'</td>';
					echo '<td class="text-center">'.$plan['import_brand'].'</td>';
					echo '<td class="text-center">'.$plan['import_lot'].'</td>';
					echo '<td class="text-right">'.$plan['amount'].'</td>';
					if ($plan['factory'] == 'BWS'){
					echo '<td class="text-center"><span class="badge badge-primary">'.$plan['factory'].'</span></td>';
					}else{
						echo '<td class="text-center"><span class="badge badge-danger">'.$plan['factory'].'</span></td>';
					}
					echo '<td class="text-center">'.$product['name'].'</span></td>';
					echo '<td class="text-center">'.$plan['coa'].'</td>';
					echo '<td class="text-center">'.$plan['coc'].'</td>';
					echo '<td class="text-center">'.$plan['country'].'</td>';
					echo '<td class="text-center">'.$type_import['name'].'</td>';
					
				echo '<tr>';
			}
		echo '</table>';
	echo '</div>';


	

	$dbc->Close();
?>
