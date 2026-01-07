<div class="card">
	<div class="card-body">
		<div class="row">
			<div class="col-4">
				<form>
					<table class="table table-bordered table-form">
						<tbody>
							<tr>
								<td><label>รอบที่ </label></td>
								<td><input type="text" class="form-control" value="35"></td>
							</tr>
						</tbody>
					</table>
				</form>
			</div>
			<div class="col-2">
				<button onclick="window.history.back();" class="btn btn-danger">Back</button>
			</div>
		</div>
		<ul class="nav nav-tabs" id="myTab" role="tablist">
			<li class="nav-item" role="presentation">
				<a class="nav-link active" id="home-tab" data-toggle="tab" href="#home" role="tab" aria-controls="home" aria-selected="true">การจัดการถุง</a>
			</li>
			<li class="nav-item" role="presentation">
				<a class="nav-link" id="profile-tab" data-toggle="tab" href="#profile" role="tab" aria-controls="profile" aria-selected="false">รายละเอียดเบ้าหลอม</a>
			</li>
			
			</ul>
			<div class="tab-content" id="myTabContent">
			<div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
				<?php include "view/subpage.production.pack.php";?>
			
			</div>
			<div class="tab-pane fade" id="profile" role="tabpanel" aria-labelledby="profile-tab">
				<?php include "view/subpage.production.produce.php";?>
			</div>
				
			
			
		</div>
		<button class="btn btn-secondary" type="reset">ยกเลิก</button>
		<button class="btn btn-primary" type="button">ทำรายการ</button>










    
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