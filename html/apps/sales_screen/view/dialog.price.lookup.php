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
				<table id="tblPriceLookup" class="table table-striped table-bordered" cellspacing="0" width="100%">
					<thead>
						<tr>
							<th class="text-center">ลูกค้า</th>
							<th class="text-center">ฝากราคา</th>
							<th class="text-center">วันที่</th>
							<th class="text-center">รับฝาก</th>
							<th class="text-center">สาเหตุที่เอาราคาออก</th>
						</tr>
					</thead>
					<tbody>
					<?php
						$model = array(
							array("type" => "databank","value"=>"name"),
							array("type" => "number", "from"=>29000 , "to"=>32000,"number_format"=>2,"class"=>"text-center"),
							array("type" => "date","format"=>"d/m/Y","class"=>"text-center"),
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
	$modal->setModel("dialog_price_lookup","รายการฝากราคา");
	$modal->setButton(array(
		array("close","btn-secondary","Dismiss")
	));
	$modal->EchoInterface();
	
	$dbc->Close();
?>