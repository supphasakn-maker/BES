<?php
	session_start();
	include_once "../../../config/define.php";
	include_once "../../../include/db.php";
	include_once "../../../include/oceanos.php";
	include_once "../../../include/iface.php";
	include_once "../../../include/demo.php";
	
	@ini_set('display_errors',DEBUG_MODE?1:0);
	date_default_timezone_set(DEFAULT_TIMEZONE);
	
	$dbc = new dbc;
	$dbc->Connect();
	
	$os = new oceanos($dbc);

	class myModel extends imodal{
		function body(){
			$dbc = $this->dbc;
			$demo = new demo;
			?>
				<table id="tblPurchaseLookup" class="table table-striped table-bordered" cellspacing="0" width="100%">
					<thead>
						<tr>
							<th class="text-center">วัน เวลาสั่ง</th>
							<th class="text-center">วันส่ง</th>
							<th class="text-center">กิโล</th>
							<th class="text-center">บาท/กิโล</th>
							<th class="text-center">ราคารวม</th>
							<th class="text-center">ผู้ขาย</th>
							<th class="text-center">สถานะการส่ง</th>
						</tr>
					</thead>
					<tbody>
					<?php
						$model = array(
							array("type" => "date","format"=>"d/m/Y H:i:s","class"=>"text-center"),
							array("type" => "date","format"=>"d/m/Y","class"=>"text-center"),
							array("type" => "number", "from"=>1 , "to"=>30,"number_format"=>0,"class"=>"text-center"),
							array("type" => "number", "from"=>29000 , "to"=>32000,"number_format"=>2,"class"=>"text-center"),
							array("type" => "number", "from"=>29000 , "to"=>32000,"number_format"=>2,"class"=>"text-center"),
							array("type" => "databank","value"=>"user"),
							array("type" => "text","value"=>"-","class"=>"text-center")
						);
						$demo->loop_table($model,12);
					?>
					</tbody>
				</table>
			<?php
		}
	}
	
	$modal = new myModel($dbc,$os->auth);
	$modal->setParam($_POST);
	$modal->setExtraClass("modal-xl");
	$modal->setModel("dialog_purchase_lookup","ประวัติการสั่งซื้อ");
	$modal->setButton(array(
		array("close","btn-secondary","Dismiss")
	));
	$modal->EchoInterface();
	
	$dbc->Close();
?>