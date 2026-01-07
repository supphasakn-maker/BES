<?php

	$dbc = new dbc;
	$dbc->Connect();
	$os = new oceanos($dbc);

	global $ui_form,$dbc;
	$production = $dbc->GetRecord("bs_productions","*","id=".$_GET['production_id']);
	
	
?>
<div class="row">
	<div class="col-12">
        <div class="mb-1">
        <a type="button" onclick="fn.app.production_prepare.prepare.dialog_add_oven(<?php echo $production['id'];?>)" class="btn btn-primary mr-2">เพิ่มเตาอบ</a>
        </div>

	<table id="tblOven" data-id="<?php echo $production['id'];?>" class=" table table-striped table-bordered" style="width:100%">
		<thead class="bg-dark">
			<tr>
				<th></th>
				<th class="text-center font-weight-bold text-white">วันที่</th>
				<th class="text-center font-weight-bold text-white">เตาอบ</th>
				<th class="text-center font-weight-bold text-white">เวลาเปิด</th>
				<th class="text-center font-weight-bold text-white">เวลาปิด</th>
				<th class="text-center font-weight-bold text-white">อุณหภูมิ</th>
				<th class="text-center font-weight-bold text-white">remark</th>
				<th class="text-center font-weight-bold text-white">พนักงาน</th>
				<th class="text-center font-weight-bold text-white">สถานะ</th>
			</tr>
		</thead>
		<tbody>
		</tbody>
	</table>
	</div>
</div>