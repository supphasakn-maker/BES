<?php
	session_start();
	include_once "../../../config/define.php";
	include_once "../../../include/db.php";
	include_once "../../../include/oceanos.php";
	include_once "../../../include/iface.php";

	@ini_set('display_errors',DEBUG_MODE?1:0);
	date_default_timezone_set(DEFAULT_TIMEZONE);

	$dbc = new dbc;
	$dbc->Connect();

	$os = new oceanos($dbc);

	class myModel extends imodal{
		function body(){
			$dbc = $this->dbc;
			$payment = $dbc->GetRecord("bs_payments","*","id=".$this->param['id']);
			$customer = $dbc->GetRecord("bs_customers","*","id=".$payment['customer_id']);
			echo '<table class="table table-sm table-bordered">';
				echo '<tr>';
					echo '<th class="text-center"></th>';
					echo '<th class="text-center">Date</th>';
					echo '<td class="text-center">'.$payment['datetime'].'</td>';
					echo '<th class="text-center">Method</th>';
					echo '<td class="text-center">'.$payment['payment'].'</td>';
					echo '<th class="text-center">Amount</th>';
					echo '<td class="text-center">'.number_format($payment['amount'],2).'</td>';
				echo '</tr>';
				echo '<tr>';
					echo '<td class="text-center">Customer</td>';
					echo '<td class="text-center" colspan="5">'.$customer['name'].'</td>';
				echo '</tr>';
			echo '</table>';
			echo '<div class="form-inline">';
				echo '<a onclick="fn.app.finance.payment.dialog_order_lookup('.$customer['id'].')" class="btn btn-sm btn-primary mr-2">Add Order</a>';
				echo '<select name="payment_type_append" class="form-control form-control-sm mr-1">';
					$sql = "SELECT * FROM bs_payment_types";
					$rst = $dbc->Query($sql);
					while($item = $dbc->Fetch($rst)){
						echo '<option value="'.$item['id'].'">'.$item['name'].'</option>';
					}
				echo '</select>';
				echo '<a onclick="fn.app.finance.payment.append_item()" class="btn btn-sm btn-primary mr-2">Append Item</a>';
				echo '<a onclick="fn.app.finance.payment.append_deposit()" class="btn btn-sm btn-warning mr-2">Append Deposit</a>';
				echo '<a onclick="fn.app.finance.payment.dialog_deposit_lookup('.$customer['id'].')" class="btn btn-sm btn-info mr-2">Use Deposit</a>';
		
				
			echo '</div>';
			echo '<form class="mt-2" name="form_mappingpayment">';
				echo '<input type="hidden" name="id" value="'.$payment['id'].'">';
				echo '<input type="hidden" name="amount" value="'.$payment['amount'].'">';
				echo '<input type="hidden" name="customer_id" value="'.$payment['customer_id'].'">';
				echo '<table id="tblMapping" class="table table-sm table-bordered">';
					echo '<thead>';
						echo '<tr>';
							echo '<th class="text-center">Order ID</th>';
							echo '<th class="text-center">date</th>';
							echo '<th class="text-center">จำนวนเงิน</th>';
							echo '<th class="text-center">จ่ายจริง</th>';
							echo '<th class="text-center">คงเหลือ</th>';
						echo '</tr>';
					echo '</thead>';
					echo '<tbody>';
					$sql = "SELECT * FROM bs_payment_orders WHERE payment_id = ".$payment['id'];
					$rst = $dbc->Query($sql);
					while($item = $dbc->Fetch($rst)){
						$order = $dbc->GetRecord("bs_orders","*","id=".$item['order_id']);
						echo '<tr>';
							echo '<td class="text-center"><a class="btn btn-danger btn-danger-remove" onclick="$(this).parent().parent().remove()">Remove</a></td>';
							
							echo '<td class="text-center"><input type="hidden" name="order_id[]" value="'.$order['id'].'">'.$order['code'].'</td>';
							echo '<td class="text-center">'.$order['date'].'</td>';
							echo '<td><input class="form-control text-right" xname="net" readonly name="net[]" value="'.number_format($order['net'],2).'"></td>';
							echo '<td><input onchange="fn.app.finance.payment.calculate()" class="form-control text-right" xname="paid" name="paid[]" value="'.number_format($item['amount'],2).'"></td>';
							echo '<td><input class="form-control text-right" xname="remain" readonly name="remain[]" value="0"></td>';
						echo '</tr>';
					}
					echo '</tbody>';
				echo '</table>';
				echo '<table id="tblMappingItem" class="table table-sm table-bordered">';
					echo '<thead>';
						echo '<tr>';
							echo '<th class="text-center"></th>';
							echo '<th class="text-center">Type</th>';
							echo '<th class="text-center">จำนวนเงิน</th>';
							echo '<th class="text-center">Remark</th>';
						echo '</tr>';
					echo '</thead>';
					echo '<tbody>';
					
					$sql = "SELECT * FROM bs_payment_items WHERE payment_id = ".$payment['id'];
					$rst = $dbc->Query($sql);
					while($item = $dbc->Fetch($rst)){
						$type = $dbc->GetRecord("bs_payment_types","*","id=".$item['type_id']);
						echo '<tr>';
							echo '<td class="text-center"><a class="btn btn-danger btn-danger-remove" onclick="$(this).parent().parent().remove()">Remove</a></td>';
							echo '<td class="text-center"><input type="hidden" name="type_id[]" value="'.$type['id'].'">'.$type['name'].'</td>';
							echo '<td><input class="form-control text-right" name="amount[]" value="'.$item['amount'].'"></td>';
							echo '<td><input class="form-control text-right" name="remark[]" value="'.$item['remark'].'"></td>';
						echo '</tr>';
					}
					
					echo '</tbody>';
				echo '</table>';
				echo '<table id="tblMappingDeposit" class="table table-sm table-bordered">';
					echo '<thead>';
						echo '<tr>';
							echo '<th class="text-center"></th>';
							echo '<th class="text-center">จำนวนเงิน</th>';
						echo '</tr>';
					echo '</thead>';
					echo '<tbody>';
					
					$sql = "SELECT * FROM bs_payment_deposits WHERE payment_id = ".$payment['id'];
					$rst = $dbc->Query($sql);
					while($item = $dbc->Fetch($rst)){
						
						echo '<tr>';
							echo '<td class="text-center">';
								echo '<input xname="deposit_id" type="hidden" name="deposit_id[]" value="'.$item['id'].'">';
								echo '<input xname="deposit_action" type="hidden" name="deposit_action[]" value="">';
								echo '<a class="btn btn-danger btn-danger-remove" onclick="fn.app.finance.payment.deposit_remove(this)">Remove</a>';
							echo '</td>';
							echo '<td><input class="form-control text-right" name="deposit[]" value="'.$item['amount'].'"></td>';
						echo '</tr>';
					}
					echo '</tbody>';
				echo '</table>';
				echo '<table id="tblMappingDepositUsed" class="table table-sm table-bordered">';
					echo '<thead>';
						echo '<tr>';
							echo '<th class="text-center"></th>';
							echo '<th class="text-center">จำนวนเงิน</th>';
						echo '</tr>';
					echo '</thead>';
					echo '<tbody>';
					
					$sql = "SELECT * FROM bs_payment_deposit_use WHERE payment_id = ".$payment['id'];
					$rst = $dbc->Query($sql);
					while($item = $dbc->Fetch($rst)){
						$deposit = $dbc->GetRecord("bs_payment_deposits","*","id=".$item['deposit_id']);
						
						echo '<tr>';
							echo '<td class="text-center">';
								echo '<input xname="deposit_use_id" type="hidden" name="deposit_use_id[]" value="'.$item['id'].'">';
								echo '<input xname="deposit_use_deposit_id" type="hidden" name="deposit_use_deposit_id[]" value="'.$item['deposit_id'].'">';
								echo '<input xname="deposit_use_action" type="hidden" name="deposit_use_action[]" value="">';
								echo '<a class="btn btn-danger btn-danger-remove" onclick="fn.app.finance.payment.deposit_use_remove(this)">Remove</a>';
							echo '</td>';
							echo '<td><input class="form-control text-right" name="deposit_deposit[]" value="'.$deposit['amount'].'" readonly></td>';
							echo '<td><input class="form-control text-right" name="deposit_use[]" value="'.$item['amount'].'"></td>';
						echo '</tr>';
					}
					echo '</tbody>';
				echo '</table>';
			echo '</form>';
			
			
			
		}
	}

	$modal = new myModel($dbc,$os->auth);
	$modal->setParam($_POST);
	$modal->setExtraClass("modal-lg");
	$modal->setModel("dialog_mapping_payment","Mapping Payment");
	$modal->setButton(array(
		array("close","btn-secondary","Dismiss")
	));
	$modal->EchoInterface();

	$dbc->Close();
?>
