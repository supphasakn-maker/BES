<div class="mb-2">
	<form name="report">
		<div class="form-group row">
			<label class="col-sm-2 col-form-label text-right">ประเภทรายงาน</label>
			<div class="col-sm-2">
				<select name="type" class="form-control">
					<?php
					foreach ($aReportType as $key => $name) {
						echo '<option value="' . $key . '">' . $name . '</option>';
					}
					?>
				</select>
			</div>
			<div class="col-sm-2">
				<button type="button" class="btn btn-primary" onclick="fn.app.report_sigmargin_stx.generate();return false;">Search</button>
			</div>
		</div>
		<div class="form-group row">

		</div>
		<div class="form-group row display-group" display-group="daily">
			<label class="col-sm-2 col-form-label text-right">วัน</label>
			<div class="col-sm-2">
				<input name="date" type="date" class="form-control" value="<?php echo date("Y-m-d"); ?>">
			</div>
		</div>

	</form>
</div>