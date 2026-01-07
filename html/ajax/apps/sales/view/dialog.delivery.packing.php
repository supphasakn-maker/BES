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
	$aPacking = json_decode($os->load_variable("aPacking","json"),true);

	class myModel extends imodal{
		
		function body(){
			global $aPacking;
			$dbc = $this->dbc;
			$delivery = $dbc->GetRecord("bs_deliveries","*","id=".$this->param['id']);
			$order = $dbc->GetRecord("bs_orders","*","delivery_id=".$delivery['id']);
			$items = array();
		
			
			if($delivery['status']==1){
				$edit_mode = true;
				$sql = "SELECT * FROM bs_delivery_items WHERE delivery_id=".$delivery['id'];
				$rst = $dbc->Query($sql);
				while($item = $dbc->Fetch($rst)){
					array_push($items,$item);
				}
			}else{
				$edit_mode = false;
			}
		
			echo '<ul class="list-group list-group-horizontal mb-3">';
				echo '<li class="list-group-item flex-fill text-center">';
					echo '<div class="text-secondary">ประเภท</div><strong>';
					if($delivery['type']==1){
						echo 'แบบธรรมดา';
					}else{
						echo 'แบบรวม';
					}
					echo '</strong>';
				echo '</li>';
				echo '<li class="list-group-item flex-fill text-center">';
					echo '<div class="text-secondary">คำสั่งซื้อ</div><strong>';
						if($delivery['type']==2){
							$sql = "SELECT * FROM bs_orders WHERE delivery_id=".$order['delivery_id'];
							$rst = $dbc->Query($sql);
							while($item = $dbc->Fetch($rst)){
								echo '<div>'.$item['code'].'</div>';
							}
						}else{
							echo $order['code'];
						}
					echo '</strong>';
				echo '</li>';
				echo '<li class="list-group-item flex-fill text-center">';
					echo '<div class="text-secondary">วันที่สั่งซื้อ</div><strong>'.$order['created'].' </strong>';
				echo '</li>';
				echo '<li class="list-group-item flex-fill text-center">';
					echo '<div class="text-secondary">วันที่ส่ง</div><strong>'.$delivery['delivery_date'].' </strong>';
				echo '</li>';
			echo '</ul>';
			
			?>
			<form name="form_packing" data-items='<?php echo $edit_mode?json_encode($items,JSON_UNESCAPED_UNICODE):"";?>'>
				<div class="form-inline">
				<input type="hidden" name="id" value="<?php echo $delivery['id'];?>">
				<input type="hidden" name="amount_limit" value="<?php echo $delivery['amount'];?>">
				<input type="hidden" name="edit" value="<?php echo $edit_mode?"true":"false";?>">
				<select name="packtype" class="form-control mr-2">
				<?php
					foreach($aPacking as $pack){
						$readonly = isset($pack['readonly'])?$pack['readonly']:true;
						
						echo '<option data-value="'.$pack['value'].'" data-readonly="'.($readonly?"true":"false").'">'.$pack['name'].'</option>';
					}
				?>
				</select>
				<a id="addpack" class="btn btn-primary" href="javascript:;" onclick="fn.app.sales.delivery.packing_append()">เพิ่มถุง</a>
				</div>
			<hr>
			
			<table id="tblPacking" class="mt-3 table table-striped table-bordered" cellspacing="0" width="100%">
				<thead>
					<tr>
						<th class="text-center">ลำดับ</th>
						<th class="text-center">รายการ</th>
						<th class="text-center">ขนาด</th>
						<th class="text-center">จำนวน</th>
						<th class="text-center">รวมเป็น</th>
						<th class="text-center">เพิ่มเติม</th>
						<th class="text-center">ดำเนินการ</th>
					</tr>
				</thead>
				<tfoot>
					<tr>
						<th class="text-right" colspan="3">ยอดรวม</th>
						<th class="text-center">
							<div class="input-group input-group-sm">
								<input name="total" type="text" class="form-control text-right" readonly value="0">
								<div class="input-group-append">
									<span class="input-group-text">กิโลกรัม</span>
								</div>
							</div>
						</th>
						<th></th>
					</tr>
					<tr>
						<th class="text-right" colspan="3">คงเหลือ</th>
						<th class="text-center">
							<div class="input-group input-group-sm">
								<input name="remain" type="text" class="form-control text-right" readonly value="500">
								<div class="input-group-append">
									<span class="input-group-text">กิโลกรัม</span>
								</div>
							</div>
						</th>
						<th></th>
					</tr>
				</tfoot>
				<tbody>
				</tbody>
			</table>
			</form>
			<script>
			$("select[name=packtype]").unbind("change").change(function(){
				switch($(this).val()){
					case "1":$("select[name=packcustom]").hide();$("select[name=packsize]").show();break;
					case "2":$("select[name=packcustom]").show();$("select[name=packsize]").hide();break;
					case "3":$("select[name=packcustom]").hide();$("select[name=packsize]").hide();break;
				}
			}).change();
			</script>
			<?php
		}
	}

	$modal = new myModel($dbc,$os->auth);
	$modal->setParam($_POST);
	$modal->setModel("dialog_packing_delivery","Packing Delivery");
	$modal->setExtraClass("modal-full");
	$modal->setButton(array(
		array("close","btn-secondary","Dismiss"),
		array("action","btn-danger","Packing","fn.app.sales.delivery.packing()")
	));
	$modal->EchoInterface();

	$dbc->Close();
?>
