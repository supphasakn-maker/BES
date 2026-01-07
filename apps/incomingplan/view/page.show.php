<?php
	
?>

<div class="card-head">
	<h3 class="text-center">วางแผนนำเข้าสินค้า</h3>
</div>
<div class="card-body">
	<div class="float-right mb-2 ">
		<button class="btn btn-danger" onclick="fn.app.incomingplan.alert()">แจ้งเตือน</button>
	</div>
	<div class="btn-group mb-2">
		<div class="input-group mr-2 mb-3">
			<div class="input-group-prepend"><span class="input-group-text">วันที่สร้างเอกสาร</span></div>
			<input type="date" name="created_date_form" class="form-control" value="<?php echo date("Y-m-d",time()-(86400*30))?>">
			<div class="input-group-prepend"><span class="input-group-text">To</span></div>
			<input type="date" name="created_date_to" class="form-control" value="<?php echo date("Y-m-d")?>">
		</div>
		<div class="input-group mr-2 mb-3">
			<div class="input-group-prepend"><span class="input-group-text">วันที่นำเข้า</span></div>
			<input type="date" name="date_form" class="form-control" value="<?php echo date("Y-m-d")?>">
			<div class="input-group-prepend"><span class="input-group-text">To</span></div>
			<input type="date" name="date_to" class="form-control" value="<?php echo date("Y-m-d",time()+(86400*7))?>">
		<button class="btn btn-primary" onclick="fn.app.incomingplan.plan.show()">Reload</button>
		</div>
	</div>
	<div id="display_area">
	</div>
</div>
