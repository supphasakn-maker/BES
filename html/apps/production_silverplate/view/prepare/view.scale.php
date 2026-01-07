<?php

$dbc = new dbc;
$dbc->Connect();
$os = new oceanos($dbc);

global $ui_form, $dbc;
$production = $dbc->GetRecord("bs_productions", "*", "id=" . $_GET['production_id']);


?>
<div class="row">
	<div class="col-12">
		<div class="mb-1">
			<a type="button" onclick="fn.app.production_silverplate.prepare.dialog_add_scale(<?php echo $production['id']; ?>)" class="btn btn-primary mr-2">เพิ่มเครื่องชั่ง</a>
		</div>

		<table id="tblScale" data-id="<?php echo $production['id']; ?>" class=" table table-striped table-bordered" style="width:100%">
			<thead class="bg-dark">
				<tr>
					<th></th>
					<th class="text-center font-weight-bold text-white">วันที่</th>
					<th class="text-center font-weight-bold text-white">เครื่องชั่ง</th>
					<th class="text-center font-weight-bold text-white">ผู้ชั่ง</th>
					<th class="text-center font-weight-bold text-white">ผู้แพ็ค</th>
					<th class="text-center font-weight-bold text-white">ผู้เช็ค</th>
					<th class="text-center font-weight-bold text-white">หมายเหตุ</th>
					<th class="text-center font-weight-bold text-white">สถานะ</th>
				</tr>
			</thead>
			<tbody>
			</tbody>
		</table>
	</div>
</div>