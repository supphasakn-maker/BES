<?php
	session_start();
	include "../../include/demo.php";
	$demo = new demo;
	
?>

<div class="card">
	<div class="card-body">
		<div class="btn-area btn-group mb-2">
			<button type="submit" onclick="fn.dialog.open('apps/product_claim/view/dialog.claim.add.php','#dialog_add')"  class="btn btn-primary mr-sm-2">Add</button>
				
		</div>
		<table id="example" class="table table-striped table-bordered dt-responsive nowrap">
			<!-- Filter columns -->
			<thead>
				<tr>
					<th class="text-center">หมายเลขคำร้อง</th>
					<th class="text-center">วันที่เรียกร้อง</th>
					<th class="text-center">หมายเลขสั่งซื้อ</th>
					<th class="text-center">ลููกค้า</th>
					<th class="text-center">ปัญหาที่แจ้ง</th>
					<th class="text-center">จำนวนที่ต้องการเคลม</th>
					<th class="text-center">สถานะ</th>
					<th class="text-center"></th>
				</tr>
			</thead>											
			<!-- /Filter columns -->
			<tbody>
				<tr>
					<td class="text-center">C-000001</th>
					<td class="text-center">01/10/2021</th>
					<td class="text-center">O-000025</th>
					<td class="text-center">บริษัท ทำเงิน จำกัด</th>
					<td class="text-center">เม็ดชื้น</th>
					<th class="text-center">20</th>
					<th class="text-center"><span class="badge badge-danger">รับแจ้ง</sapn></th>
					<th class="text-center">
						<button class="btn btn-sm btn-primary">แก้ไข</button>
						<button class="btn btn-sm btn-primary">ยินยัน</button>
					</th>
				</tr>
				<tr>
					<td class="text-center">C-000002</th>
					<td class="text-center">01/10/2021</th>
					<td class="text-center">O-000025</th>
					<td class="text-center">บริษัท ทำเงิน จำกัด</th>
					<td class="text-center">เม็ดชื้น</th>
					<th class="text-center">20</th>
					<th class="text-center"><span class="badge badge-warning">ยินยันปัญหา</sapn></th>
					<th class="text-center">
						<button class="btn btn-sm btn-warning">สิ้นสุด</button>
					</th>
				</tr>
				<tr>
					<td class="text-center">C-000003</th>
					<td class="text-center">01/10/2021</th>
					<td class="text-center">O-000025</th>
					<td class="text-center">บริษัท ทำเงิน จำกัด</th>
					<td class="text-center">เม็ดชื้น</th>
					<th class="text-center">20</th>
					<th class="text-center"><span class="badge badge-success">เสร็จสิ้น</sapn></th>
					<th class="text-center">
					</th>
				</tr>
			</tbody>
		</table>
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

		}).then(() => App.stopLoading())
	</script>