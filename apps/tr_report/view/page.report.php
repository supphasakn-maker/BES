<?php
	global $dbc,$_GET;
	global $ui_form,$os;
	$today = time();
?>

<div class="card-body">
	<form name="filter" class="form-inline mb-3" onsubmit="return false;">
		<label class="col-sm-1 col-form-label text-right">วันที่</label>
		<input name="date" id="date" type="date" class="form-control" value="<?php echo date("Y-m-d");?>">
		<?php
			$ui_form->EchoItem(array(
				"type" => "comboboxdatabank",
				"class" => "mr-sm-2",
				"source" => "db_bank",
				"name" => "bank",
				"default" => array(
					"value" => "none",
					"name" => "เลือกธนาคาร"
				)
			));
			
		?>
		<button type="button" onclick="fn.app.tr_report.report.load_page()" class="btn btn-primary">Lookup</button>
	</form>
</div>
<div id="output">
	
</div>