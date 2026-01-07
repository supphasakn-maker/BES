<?php
	session_start();
	include_once "../../../config/define.php";
	@ini_set('display_errors',DEBUG_MODE?1:0);
	date_default_timezone_set(DEFAULT_TIMEZONE);

	include_once "../../../include/db.php";
	include_once "../../../include/oceanos.php";
	include_once "../../../include/iface.php";

	$dbc = new dbc;
	$dbc->Connect();

	$os = new oceanos($dbc);
	$order = $dbc->GetRecord("bs_orders","*","id=".$_POST['id']);


	class myModel extends imodal{
		function body(){
			global $os;
			$dbc = $this->dbc;
			$order = $dbc->GetRecord("bs_orders","*","id=".$this->param['id']);
			?>
			<table class="table table- table-bordered mb-0">
				<tbody>
					<tr>
						<th class="text-right" width="30%">หมายเลขจัดส่ง</th><td><?php echo $order['code'];?></td>
					</tr>
					<tr>
						<th class="text-right">ชื่อลูกค้า</th><td><?php echo $order['customer_name'];?></td>
					</tr>
					
					<tr>
						<th class="text-right">วันที่สั่งซื้อ</th><td><?php echo $order['created'];?></td>
					</tr>
					<tr>
						<th class="text-right">จำนวนที่จัดก่อนแบ่ง</th><td><?php echo $order['amount'];?></td>
					</tr>
				</tbody>
			</table>
			<div class="m-2">
				<a href="javascript:;" class="btn btn-primary" onclick="fn.app.sales.order.append_split()">แยกบิล</a>
			</div>
			<form name="form_splitorder" >
				<input type="hidden" name="id" value="<?php echo $this->param['id'];?>">
				<input type="hidden" name="total" value="<?php echo $order['amount'];?>">
				<table class="table table-bordered table-form">
					<tbody>
						<tr>
							<td><label>ใบที่หนึ่ง</label></td>
							<td><input data-name="amount" name="amount[]" type="text" class="form-control text-right" value="<?php echo $order['amount'];?>"></td>
							<td><label>วันส่ง</label></td>
							<td><input data-name="date" name="date[]" type="date" class="form-control text-right" value="<?php echo $order['delivery_date'];?>"></td>
							
						</tr>
						<tr>
							<td><label>ใบแยก</label></td>
							<td><input data-name="amount" name="amount[]" type="text" class="form-control text-right" value="0"></td>
							<td><label>วันส่ง</label></td>
							<td><input data-name="date" name="date[]" type="date" class="form-control text-right" value="<?php echo $order['delivery_date'];?>"></td>
							
						</tr>
						
				</table>
			</form>
			<?php
			
			
		}
	}

	$modal = new myModel($dbc,$os->auth);

	$modal->setModel("dialog_split_order","Split Order");
	$modal->initiForm("form_splitorder");
	$modal->setExtraClass("modal-lg");
	$modal->setParam($_POST);
	$modal->setButton(array(
		array("close","btn-secondary","Dismiss"),
		array("action","btn-outline-dark","Save Change","fn.app.sales.order.split()")
	));

	$modal->EchoInterface();
	$dbc->Close();
?>
