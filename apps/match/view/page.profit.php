<?php
	global $dbc;
?>
<div class="btn-area btn-group mb-2">
	<form onsubmit="return false;" name="filter" class="form-inline mr-2" >
		<select name="type" class="form-control">

			<option value="daily">ข้อมูลรายวัน</option>
			<option value="monthly">ข้อมูลรายเดือน</option>
			<option value="yearly">ข้อมูลรายปี</option>
		</select>
		<input type="month" name="month" class="form-control mr-2" value="<?php echo $value_month;?>">
		<button type="button" class="btn btn-primary" onclick="fn.app.match.profit.search()" >Search</button>
	</form>
</div>
<div id="output" class="row gutters-sm">
	
</div>