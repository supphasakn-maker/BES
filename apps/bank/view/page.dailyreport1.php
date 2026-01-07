<?php
	global $dbc,$_GET;
	$date = isset($_GET['date'])?$_GET['date']:date("Y-m-d");
?>

<div class="card-body">
	<form name="filter" class="form-inline mb-3" onsubmit="return false;">
		<label class="mr-sm-2">เลือกวันที่</label>
		<input name="date" type="date" class="form-control mr-sm-2" value="<?php echo $date?>" onchange="fn.app.bank.dailyreport1.load_page()">
		<button type="button" onclick="fn.app.bank.dailyreport1.load_page()" class="btn btn-primary">Lookup</button>
	</form>
</div>
<div id="output">
	
</div>
<form style="display:none;" name="import" enctype="multipart/form-data" >
	<input name="file" type="file">
</form>