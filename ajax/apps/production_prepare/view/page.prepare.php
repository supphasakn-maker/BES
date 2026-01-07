<?php
	if($this->GetSection()=="edit"){
		include "view/page.prepare.edit.php";
	}else{
		$today = time();
?>
<div class="btn-area btn-group mb-2">
	<form name="filter" class="form-inline mr-2" onsubmit="return false;">
		<label class="mr-sm-2">From</label>
		<input name="from" type="date" class="form-control mr-sm-2" value="<?php echo date("Y-m-d",$today-(86400*30));?>">
		<label class="mr-sm-2">To</label>
		<input name="to" type="date" class="form-control mr-sm-2" value="<?php echo date("Y-m-d",$today+(86400*30));?>">
		<button type="button" class="btn btn-primary" onclick='$("#tblPrepare").DataTable().draw();'>Lookup</button>
	</form>
</div>
<table id="tblPrepare" class="table table-striped table-bordered table-hover table-middle" width="100%">
	<thead>
		<tr>
			<th class="text-center">วันที่เปิดรอบการผลิต</th>
			<th class="text-center">รอบที่</th>
			<th class="text-center">จำนวนถุง</th>
			<th class="text-center">จำนวนกระสอบ</th>
			<th class="text-center">นำหนักเข้าหลอม</th>
			<th class="text-center">น้ำหนักออก</th>
			<th class="text-center">ผลต่าง</th>
			<th class="text-center">น้ำหนักแพ็ค</th>
			<th class="text-center">บันทึก</th>
			<th class="text-center"></th>
		</tr>
	</thead>
	<tbody>
	</tbody>
</table>
<?php
	}
?>