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
	
	$credit = 220000000;
	
	
	$sql = "SELECT
		SUM(bs_purchase_usd.unpaid) AS unpaid,
		COUNT(bs_purchase_usd.id) AS total_tr
	FROM bs_purchase_usd
	LEFT JOIN bs_transfers ON bs_purchase_usd.transfer_id = bs_transfers.id
	WHERE
		bs_transfers.bank ='".$_POST['bank']."'
		AND bs_transfers.transfer_date BETWEEN '".$_POST['from']."' AND '".$_POST['to']."'";
	$rst = $dbc->Query($sql);
	$purchase_usd = $dbc->Fetch($rst);
	
	$sql = "SELECT
		SUM(bs_usd_payment.interest) AS interest,
		SUM(bs_usd_payment.paid_thb) AS paid_thb
	FROM bs_usd_payment
	LEFT JOIN bs_transfers ON bs_usd_payment.transfer_id = bs_transfers.id
	WHERE
		bs_transfers.bank ='".$_POST['bank']."'
		AND bs_transfers.transfer_date BETWEEN '".$_POST['from']."' AND '".$_POST['to']."'";
	$rst = $dbc->Query($sql);
	$payment = $dbc->Fetch($rst);
	
	$sql = "SELECT
		SUM(bs_usd_payment.paid_thb) AS paid_thb,
		SUM(bs_usd_payment.interest) AS interest,
		bs_transfers.rate_counter AS rate_counter
	FROM bs_usd_payment
	LEFT JOIN bs_transfers ON bs_usd_payment.transfer_id = bs_transfers.id
	WHERE
		bs_transfers.bank ='".$_POST['bank']."'
		AND bs_transfers.transfer_date = '".date("Y-m-d")."'";
	$rst = $dbc->Query($sql);
	$today_payment = $dbc->Fetch($rst);
	
	
	echo '<table class="table table-sm table-bordered">';
		echo '<tbody>';
			echo '<tr>';
				echo '<td class="text-center">Credit</td>';
				echo '<td class="text-center">Unpaid TR</td>';
				echo '<td class="text-center">Today Payment</td>';
				echo '<td class="text-center">Counter Rate</td>';
				echo '<td class="text-center">Add TR</td>';
				echo '<td class="text-center">Total Unpaid</td>';
				echo '<td class="text-center">Net Credit</td>';
				echo '<td class="text-center">Interest Expense</td>';
				echo '<td class="text-center">Interest Paid</td>';
				echo '<td class="text-center">Total Interest</td>';
			echo '</tr>';
			echo '<tr>';
				echo '<td class="text-center">220,000,000.00</td>';
				echo '<td class="text-center">'.$purchase_usd['unpaid'].'</td>';
				echo '<td class="text-center">'.$today_payment['paid_thb'].'</td>';
				echo '<td class="text-center">'.$today_payment['rate_counter'].'</td>';
				echo '<td class="text-center">'.$purchase_usd['total_tr'].'</td>';
				echo '<td class="text-center">'.$purchase_usd['unpaid'].'</td>';
				echo '<td class="text-center">'.($credit-$today_payment['paid_thb']).'</td>';
				echo '<td class="text-center">'.($payment['interest']-$today_payment['interest']).'</td>';
				echo '<td class="text-center">'.$today_payment['interest'].'</td>';
				echo '<td class="text-center">'.$payment['interest'].'</td>';
			echo '</tr>';
		echo '</tbody>';
	echo '</table>';
	
	echo '<table class="table table-sm table-bordered">';
		echo '<thead>';
			echo '<tr class="bg-dark text-white">';
				echo '<th class="text-center"></th>';
				echo '<th class="text-center">TR Date</th>';
				echo '<th class="text-center">Type</th>';
				echo '<th class="text-center">TR ID</th>';
				echo '<th class="text-center">Interest</th>';
				echo '<th class="text-center">USD</th>';
				echo '<th class="text-center">THB</th>';
				echo '<th class="text-center">Supplier</th>';
				echo '<th class="text-center">Counter Rate</th>';
				echo '<th class="text-center">USD Paid</th>';
				echo '<th class="text-center">THB Paid</th>';
				echo '<th class="text-center">Action</th>';
			echo '</tr>';
		echo '</thead>';
		echo '<tbody>';
		$data = array();
		$sql = "SELECT * FROM bs_transfers WHERE bank ='".$_POST['bank']."' AND transfer_date BETWEEN '".$_POST['from']."' AND '".$_POST['to']."'";
		$rst = $dbc->Query($sql);
		while($transfer = $dbc->Fetch($rst)){
			$supplier = $dbc->GetRecord("bs_suppliers","*","id=".$transfer['supplier_id']);
			$payment = $dbc->GetRecord("bs_usd_payment","SUM(interest),SUM(paid_thb)","transfer_id=".$transfer['id']);
						
			echo '<tr>';
				echo '<td class="text-center">';
					echo '<button class="btn btn-sm btn-warning" onclick="$(this).parent().parent().next().toggleClass(\'d-none\')">Toggle</button>';
				echo '</td>';
				echo '<td class="text-center">'.$transfer['transfer_date'].'</td>';
				echo '<td>'.$transfer['type'].'</td>';
				echo '<td>'.$transfer['id'].'</td>';
				echo '<td class="text-right pr-2">'.$payment[0].'</td>';
				echo '<td class="text-right pr-2">'.($transfer['fixed_value']+$transfer['nonfixed_value']).'</td>';
				echo '<td class="text-right pr-2">'.$transfer['net_good_value'].'</td>';
				echo '<td>'.$supplier['name'].'</td>';
				echo '<td class="text-center">'.$transfer['rate_counter'].'</td>';
				echo '<td class="text-center">-</td>';
				echo '<td class="text-right pr-2">'.$payment[1].'</td>';
				echo '<td class="text-center">';
					echo '<button onclick="fn.app.trust_receipt.tr.dialog_payusd('.$transfer['id'].')" class="btn btn-sm btn-danger mr-1">จ่าย Non Fixed</button>';
				echo '</td>';
				
			echo '</tr>';
			echo '<tr class="d-none">';
				echo '<td class="pl-5" colspan="17">';
				echo '<table class="table table-sm table-bordered">';
					echo '<thead>';
						echo '<tr>';
							echo '<th class="text-center">TR Number</th>';
							echo '<th class="text-center">USD</th>';
							echo '<th class="text-center">THB</th>';
							echo '<th class="text-center">Due Date</th>';
							echo '<th class="text-center">Payment Date</th>';
							echo '<th class="text-center">USD Paid</th>';
							echo '<th class="text-center">THB Paid</th>';
							echo '<th class="text-center">Interest</th>';
							echo '<th class="text-center">Remain</th>';
							echo '<th class="text-center">Action</th>';
						echo '</tr>';
					echo '</thead>';
					echo '<tbody>';
					$sql = "SELECT * FROM bs_purchase_usd WHERE transfer_id=".$transfer['id'];
					$rst_usd = $dbc->Query($sql);
					while($usd = $dbc->Fetch($rst_usd)){
						
						$payment = $dbc->GetRecord("bs_usd_payment","SUM(interest),SUM(paid_thb)","purchase_id=".$usd['id']);
						echo '<tr>';
							echo '<td class="text-center">'.$usd['fw_contract_no'].'</td>';
							echo '<td class="text-right pr-2">'.$usd['amount'].'</td>';
							echo '<td class="text-right pr-2">'.$usd['amount']*$usd['rate_exchange'].'</td>';
							echo '<td class="text-center">'.$usd['bank_date'].'</td>';
							echo '<td class="text-center">'.$usd['premium_start'].'</td>';
							
							//echo '<td><input type="date" class="form-control form-control-sm" value=""></td>';
							//echo '<td><input type="date" class="form-control form-control-sm" value=""></td>';
							echo '<td class="text-center">-</td>';
							echo '<td class="text-right pr-2">'.$payment[1].'</td>';
							echo '<td class="text-right pr-2">'.$payment[0].'</td>';
							echo '<td class="text-right pr-2">'.$usd['unpaid'].'</td>';
							echo '<td class="text-center">';
								echo '<button onclick="fn.app.trust_receipt.tr.dialog_payment('.$usd['id'].')" class="btn btn-sm btn-danger mr-1">Pay</button>';
								echo '<button onclick="fn.app.trust_receipt.tr.dialog_interest('.$usd['id'].')" class="btn btn-sm btn-success">Interest</button>';
							echo '</td>';
						echo '</tr>';
					}
					
					if($transfer['nonfixed_value'] > 0){
						echo '<tr>';
							echo '<td class="text-center"><span class="badge badge-info">Non Fixed</span></td>';
							echo '<td class="text-right pr-2">'.$transfer['nonfixed_value'].'</td>';
							echo '<td class="text-right pr-2">'.$transfer['nonfixed_value']*$transfer['rate_counter'].'</td>';
							echo '<td class="text-center">-</td>';
							echo '<td class="text-center">-</td>';
							echo '<td class="text-center">-</td>';
							echo '<td class="text-right pr-2">-</td>';
							echo '<td class="text-right pr-2">'.$transfer['nonfixed_value'].'</td>';
							echo '<td class="text-right pr-2">-</td>';
							echo '<td class="text-center">';
								echo '<button class="btn btn-sm btn-warning mr-1" onclick="fn.app.trust_receipt.usd.dialog_lookup('.$transfer['id'].')">เพิ่ม TR</button>';
								
								echo '<button onclick="fn.app.trust_receipt.tr.dialog_payusd('.$transfer['id'].')" class="btn btn-sm btn-danger mr-1">จ่าย Non Fixed</button>';
								echo '</td>';
						echo '</tr>';
						
						
					}
					echo '</tbody>';
				echo '</table>';
			echo '</td>';
			echo '</tr>';
		}
		echo '</tbody>';
	echo '</table>';
	
	//echo json_encode($data);

	$dbc->Close();
?>