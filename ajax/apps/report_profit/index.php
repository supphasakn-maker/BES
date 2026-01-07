<?php
	session_start();
	include "../../include/demo.php";
	$demo = new demo;
	$section = isset($_GET['view'])?$_GET['view']:"";
	
	switch($section){
		case "report.custom":
			include "view/page.report.custom.php";
			break;
		case "report.daily":
			include "view/page.report.daily.php";
			break;
		case "report.tomorrow":
			include "view/page.report.tomorrow.php";
			break;
		default:
?>

<div class="card">
	<div class="card-body">
		<div class="mb-2">
			<form name="report">
				<div class="form-group row">
					<label class="col-sm-2 col-form-label text-right">ประเภทรายงาน</label>
					<div class="col-sm-8">
						<select name="type" class="form-control">
							<option value="view.spot_fifo.php">รายงาน SPOT FIFO</option>
							<option value="view.usd_fifo.php">รายงาน USD FIFO</option>
							<option value="view.spot_balance.php">SPOT Balance</option>
							<option value="view.usd_balance.php">USD Balance</option>
							<option value="view.profit_report.php">Profit Report</option>
						</select>
					</div>
					<div class="col-sm-2">
						<button type="button" class="btn btn-primary" onclick="$('select[name=type]').change();return false;">Search</button>
					</div>
				</div>
				<div class="form-group row">
					<label class="col-sm-2 col-form-label text-right">รูปแบบรายงาน</label>
					<div class="col-sm-2">
						<select class="form-control" name="period">
							<option value="daily">รายงานประจำวัน</option>
							<option value="monthly">รายงานประจำเดือน</option>
							<option value="yearly">รายงานประจำปี</option>
							<option value="custom">รายงานตามช่วงเวลา</option>
						</select>
					</div>
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
		<hr>
		<div id="report_zone">
		</div>
	</div>
</div>


<script>
		var plugins = [
			'plugins/datatables/dataTables.bootstrap4.min.css',
			'plugins/datatables/responsive.bootstrap4.min.css',
			'plugins/datatables/jquery.dataTables.bootstrap4.responsive.min.js',
		]
		App.loadPlugins(plugins, null).then(() => {
			App.checkAll()
			// Run datatable
			var table = $('#example').DataTable({
				drawCallback: function() {
					$('.dataTables_paginate > .pagination').addClass('pagination-sm') // make pagination small
				}
			})
			// Apply column filter
			$('#example .dt-column-filter th').each(function(i) {
				$('input', this).on('keyup change', function() {
					if (table.column(i).search() !== this.value) {
						table
							.column(i)
							.search(this.value)
							.draw()
					}
				})
			})
			// Toggle Column filter function
			var responsiveFilter = function(table, index, val) {
				var th = $(table).find('.dt-column-filter th').eq(index)
				val === true ? th.removeClass('d-none') : th.addClass('d-none')
			}
			// Run Toggle Column filter at first
			$.each(table.columns().responsiveHidden(), function(index, val) {
				responsiveFilter('#example', index, val)
			})
			// Run Toggle Column filter on responsive-resize event
			table.on('responsive-resize', function(e, datatable, columns) {
				$.each(columns, function(index, val) {
					responsiveFilter('#example', index, val)
				})
			})
			
			<?php
				include "control/controller.report.js";
			?>
			

		}).then(() => App.stopLoading())
	</script>
<?php
		break;
		
	}
?>