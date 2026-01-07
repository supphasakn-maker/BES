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
				<table id="tblUserLookup" class="table table-striped table-bordered" cellspacing="0" width="100%">
					<thead>
						<tr>
							<th class="text-center">Select</th>
							<th class="text-center">ชื่อเรียก</th>
							<th class="text-center">ชื่อลูกค้า</th>
							<th class="text-center">เบอร์โทรศัพท์</th>
							<th class="text-center">แฟร์ก</th>
							<th class="text-center">ผู้ติดต่อ</th>
							<th class="text-center">หมายเหตุ</th>
						</tr>
					</thead>
					<tbody>
					<?php
						$model = array(
							array("type" => "text","value"=>'<span type="checkall" class="far fa-lg fa-square"></span>',"class"=>"text-center"),
							array("type" => "databank","value"=>"name"),
							array("type" => "databank","value"=>"name"),
							array("type" => "phone","value"=>"name"),
							array("type" => "phone","value"=>"fax"),
							array("type" => "databank","value"=>"user"),
							array("type" => "text","value"=>"-","class"=>"text-center"),
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
	$modal->setModel("dialog_customer_lookup","Customer Lookup");
	$modal->setButton(array(
		array("close","btn-secondary","Dismiss"),
		array("action","btn-danger","Select","")
	));
	$modal->EchoInterface();
	
	$dbc->Close();
?>