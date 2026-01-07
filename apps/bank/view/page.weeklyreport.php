<?php
	$today = time();
?>

<div class="card-body">
	<form name="filter" class="form-inline mb-3" onsubmit="return 0;">
		<label class="col-sm-1 col-form-label text-right">ตั้งแต่วันที่</label>
		<input name="date_from" id="date_from" type="date" class="form-control" value="<?php echo date("Y-m-d",$today-(86400*7));?>">
		<label class="col-sm-1 col-form-label text-right">ถึง</label>
		<input name="date_to" id="date_to" type="date" class="form-control" value="<?php echo date("Y-m-d");?>">
		<button type="button" onclick="fn.app.bank.weeklyreport.load_page()" class="btn btn-primary">Lookup</button>
	</form>
</div>
<div id="output">
	
</div>
<form style="display:none;" name="import" enctype="multipart/form-data" >
	<input name="file" type="file">
</form>