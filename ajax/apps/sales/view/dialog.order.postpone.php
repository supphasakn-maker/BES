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
			$order = $dbc->GetRecord("bs_orders","*","id=".$this->param['id']);
			$iform = new iform($this->dbc,$this->auth);
			?>
			<form name="form_postponeorder">
				<table class="table table-bordered table-form">
					<input type="hidden" name="id" value="<?php echo $order['id'];?>">
					<tbody>
						<tr>
							<td><label>หมายเลข Order</label></td>
							<td class="p-2"><?php echo $order['code'];?></td>
							
						</tr>
						<tr>
							<td><label>วันที่ส่งเดืม</label></td>
							<td class="p-2"><?php echo date("d/m/Y",strtotime($order['delivery_date']));?></td>
						</tr>
						<tr>
							<td><label>วันที่ส่งใหม่</label></td>
							<td><input name="delivery_date" type="date" class="form-control text-right" value="<?php echo $order['delivery_date'];?>"></td>
						
						</tr>
						<tr>
							<td><label>เหตุผลของลูกค้า</label></td>
							<td >
							<?php
								$iform->EchoItem(array(
									"type" => "comboboxdatabank",
									"source" => "db_postpone_reason_customer",
									"name" => "reason_customer",
								));
							?>
							</td>
						</tr>
						<tr>
							<td><label>เหตุผลของบริษัท</label></td>
							<td>
								<?php
								$iform->EchoItem(array(
									"type" => "comboboxdatabank",
									"source" => "db_postpone_reason_company",
									"name" => "reason_company",
								));
							?>
							</td>
						</tr>
						
				</table>
			</form>
				
			<?php
		}
	}
	
	$modal = new myModel($dbc,$os->auth);
	$modal->setParam($_POST);
	$modal->setModel("dialog_postpone_order","เลื่อนการส่ง");
	$modal->setButton(array(
		array("close","btn-secondary","Dismiss"),
		array("action","btn-outline-dark","ย้ายวัน","fn.app.sales.order.postpone()")
	));
	$modal->EchoInterface();
	
	$dbc->Close();
?>