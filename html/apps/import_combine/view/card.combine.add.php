<div class="card h-100">
	<div class="card-header border-0">
		<h6>ทำรายการสั่งซื้อ</h6>
	</div>
	<div class="card-body">
		<form name="form_addcombine">
			<input type="hidden" name="date" value="<?php echo date("Y-m-d");?>">
			<input type="hidden" name="select_import" value="">
			<table class="table table-bordered table-form">
				<tbody>
					<tr>
						<td><label>Remark</label></td>
						<td>
						<?php
							$ui_form->EchoItem(array(
								"type" => "textarea",
								"name" => "remark"
								
							));
						?>
						</td>
					</tr>
					
				</tbody>
				
			</table>
			<button class="btn btn-primary" type="button" onclick="fn.app.import_combine.combine.add()">Combine</button>
		</form>
	</div>
</div>