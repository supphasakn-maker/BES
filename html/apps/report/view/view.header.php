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
				<button type="button" class="btn btn-primary" onclick="fn.app.report.generate();return false;">Search</button>
			</div>
		</div>

		<div class="form-group row display-group" display-group="daily">
			<label class="col-sm-2 col-form-label text-right">วัน</label>
			<div class="col-sm-2">
				<input name="date" type="date" class="form-control" value="<?php echo date("Y-m-d"); ?>">
			</div>
		</div>
		<div class="form-group row display-group" display-group="monthly">
			<label class="col-sm-2 col-form-label text-right">เดือน</label>
			<div class="col-sm-2">
				<input name="month" type="month" class="form-control" value="<?php echo date("Y-m"); ?>">
			</div>
		</div>
		<div class="form-group row display-group" display-group="yearly">
			<label class="col-sm-2 col-form-label text-right">ปี</label>
			<div class="col-sm-2">
				<select name="year" class="form-control">
					<?php
					for ($i = date("Y"); $i > date("Y") - 5; $i--) {
						echo '<option>' . $i . '</option>';
					}
					?>
				</select>
			</div>
		</div>
		<div class="form-group row display-group" display-group="year_list">
			<label class="col-sm-2 col-form-label text-right">ปี</label>
			<div class="col-sm-2">
				<select name="year_list[]" class="form-control" multiple>
					<?php
					for ($i = date("Y"); $i > date("Y") - 5; $i--) {
						echo '<option>' . $i . '</option>';
					}
					?>
				</select>
			</div>
		</div>
		<div class="form-group row display-group" display-group="product">
			<label class="col-sm-2 col-form-label text-right">สินค้า</label>
			<div class="col-sm-2">
				<select name="product" class="form-control">
					<?php
					$sql = "SELECT * FROM bs_products";
					$rst = $dbc->Query($sql);
					while ($line = $dbc->Fetch($rst)) {
						echo '<option value="' . $line['id'] . '">' . $line['name'] . '</option>';
					}
					?>
				</select>
			</div>
		</div>
		<div class="form-group row display-group" display-group="customer">
			<label class="col-sm-2 col-form-label text-right">สินค้า</label>
			<div class="col-sm-2">
				<select name="customer_id" class="form-control">
					<?php
					$sql = "SELECT * FROM bs_customer_groups";
					$rst = $dbc->Query($sql);
					while ($line = $dbc->Fetch($rst)) {
						echo '<option value="' . $line['id'] . '">' . $line['name'] . '</option>';
					}
					?>
				</select>
			</div>
		</div>
		<div class="form-group row display-group" display-group="customer">
			<label class="col-sm-2 col-form-label text-right">ปริมาณการซื้อ </label>
			<div class="col-sm-2">
				<select name="bar_id" class="form-control">
					<option value="0">ALL</option>
					<option value="1200">1200</option>
					<option value="1000">1000</option>
					<option value="500">500</option>
					<option value="100">100</option>
				</select>
			</div>
		</div>
		<div class="form-group row display-group" display-group="customer_group">
			<label class="col-sm-2 col-form-label text-right">สินค้า</label>
			<div class="col-sm-2">
				<select name="customer_group_id" class="form-control">
					<?php
					$sql = "SELECT * FROM bs_customer_groups";
					$rst = $dbc->Query($sql);
					while ($line = $dbc->Fetch($rst)) {
						echo '<option value="' . $line['id'] . '">' . $line['name'] . '</option>';
					}
					?>
				</select>
			</div>
		</div>

		<div class="form-group row display-group" display-group="customer_group">
			<label class="col-sm-2 col-form-label text-right">Sales </label>
			<div class="col-sm-2">
				<select name="sale_group_id" class="form-control">
					<?php
					$sql = "SELECT * FROM bs_employees WHERE department = '1' ";
					$rst = $dbc->Query($sql);
					while ($line = $dbc->Fetch($rst)) {
						echo '<option value="' . $line['id'] . '">' . $line['fullname'] . '</option>';
					}
					?>
				</select>
			</div>
		</div>
		<div class="form-group row display-group" display-group="customer_group">
			<label class="col-sm-2 col-form-label text-right">ปริมาณการซื้อ </label>
			<div class="col-sm-2">
				<select name="bar_group_id" class="form-control">
					<option value="0">ALL</option>
					<option value="1200">1200</option>
					<option value="1000">1000</option>
					<option value="500">500</option>
					<option value="100">100</option>
				</select>
			</div>
		</div>
		<div class="form-group row display-group" display-group="newclient">
			<label class="col-sm-2 col-form-label text-right">สินค้า</label>
			<div class="col-sm-2">
				<select name="customer" class="form-control">
					<?php
					$sql = "SELECT * FROM bs_customer_groups";
					$rst = $dbc->Query($sql);
					while ($line = $dbc->Fetch($rst)) {
						echo '<option value="' . $line['id'] . '">' . $line['name'] . '</option>';
					}
					?>
				</select>
			</div>
		</div>
		<div class="form-group row display-group" display-group="newclient">
			<label class="col-sm-2 col-form-label text-right">Sales </label>
			<div class="col-sm-2">
				<select name="sale" class="form-control">
					<?php
					$sql = "SELECT * FROM bs_employees WHERE department = '1' ";
					$rst = $dbc->Query($sql);
					while ($line = $dbc->Fetch($rst)) {
						echo '<option value="' . $line['id'] . '">' . $line['fullname'] . '</option>';
					}
					?>
				</select>
			</div>
		</div>
		<div class="form-group row display-group" display-group="newclient">
			<label class="col-sm-2 col-form-label text-right">ปริมาณการซื้อ </label>
			<div class="col-sm-2">
				<select name="bar" class="form-control">
					<option value="0">ALL</option>
					<option value="1200">1200</option>
					<option value="1000">1000</option>
					<option value="500">500</option>
					<option value="100">100</option>
				</select>
			</div>
		</div>
		<div class="form-group row display-group" display-group="sale_group">
			<label class="col-sm-2 col-form-label text-right">Sales </label>
			<div class="col-sm-2">
				<select name="sale_group" class="form-control">
					<?php
					$sql = "SELECT * FROM bs_employees WHERE department = '1' ";
					$rst = $dbc->Query($sql);
					while ($line = $dbc->Fetch($rst)) {
						echo '<option value="' . $line['id'] . '">' . $line['fullname'] . '</option>';
					}
					?>
				</select>
			</div>
		</div>
		<div class="form-group row display-group" display-group="custom">
			<label class="col-sm-2 col-form-label text-right">ตั้งแต่วันที่</label>
			<div class="col-sm-2">
				<input name="date_from" type="date" class="form-control" value="<?php echo date("Y-m-d"); ?>">
			</div>
			<label class="col-sm-1 col-form-label text-right">ถึง</label>
			<div class="col-sm-2">
				<input name="date_to" type="date" class="form-control" value="<?php echo date("Y-m-d"); ?>">
			</div>
		</div>


	</form>
</div>