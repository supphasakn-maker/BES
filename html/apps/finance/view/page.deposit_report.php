<?php
	global $dbc,$_GET;
	$today = time();
?>

<div class="card-body">
	<form name="filter" class="form-inline mb-3" onsubmit="return false;">
		<label class="col-sm-1 col-form-label text-right">FROM</label>
		<input name="from" id="from" type="date" class="form-control mr-sm-2" value="<?php echo date("Y-m-d",$today-(86400*7));?>">
		<label class="col-sm-1 col-form-label text-right">TO</label>
		<input name="to" id="to" type="date" class="form-control" value="<?php echo date("Y-m-d");?>">
		<button type="button" onclick="fn.app.finance.deposit_report.load_page()" class="btn btn-primary">Lookup</button>
	</form>
</div>
<div id="output">
	
</div>
<form style="display:none;" name="import" enctype="multipart/form-data" >
	<input name="file" type="file">
</form>