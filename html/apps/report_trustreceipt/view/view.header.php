<div class="mb-2">
	<form name="report">
		<div class="form-group row">
			<label class="col-sm-2 col-form-label text-right">ประเภทรายงาน</label>
			<div class="col-sm-2">
				<select name="type" class="form-control">
				<?php
				foreach($aReportType as $key => $name){
					echo '<option value="'.$key.'">'.$name.'</option>';
				}
				?>
				</select>
			</div>
			<label class="col-sm-1 col-form-label text-right">รูปแบบรายงาน</label>
			<div class="col-sm-2">
				<select class="form-control" name="period">
				<?php
				foreach($aReportPeriod as $key => $name){
					echo '<option value="'.$key.'">'.$name.'</option>';
				}
				
				?>
				</select>
			</div>
			<div class="col-sm-2">
				<button type="button" class="btn btn-primary" onclick="fn.app.report_trustreceipt.generate();return false;">Search</button>
			</div>
		</div>
		<div class="form-group row">
			
		</div>
		<div class="form-group row display-group" display-group="daily">
			<label class="col-sm-2 col-form-label text-right">วัน</label>
			<div class="col-sm-2">
				<input name="date" type="date" class="form-control" value="<?php echo date("Y-m-d");?>">
			</div>
		</div>
		<div class="form-group row display-group" display-group="monthly">
			<label class="col-sm-2 col-form-label text-right">เดือน</label>
			<div class="col-sm-2">
				<input name="month" type="month" class="form-control" value="<?php echo date("Y-m");?>">
			</div>
		</div>
		<div class="form-group row display-group" display-group="yearly">
			<label class="col-sm-2 col-form-label text-right">ปี</label>
			<div class="col-sm-2">
				<select name="year" class="form-control">
					<option>2020</option>
					<option>2021</option>
					<option>2022</option>
					<option>2023</option>
				</select>
			</div>
		</div>
		<div class="form-group row display-group" display-group="custom">
			<label class="col-sm-2 col-form-label text-right">ตั้งแต่วันที่</label>
			<div class="col-sm-2">
				<input name="date_from" type="date" class="form-control" value="<?php echo date("Y-m-d");?>">
			</div>
			<label class="col-sm-1 col-form-label text-right">ถึง</label>
			<div class="col-sm-2">
				<input name="date_to" type="date" class="form-control" value="<?php echo date("Y-m-d");?>">
			</div>
		</div>
	</form>
</div>