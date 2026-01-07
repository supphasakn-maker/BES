<?php
	$dbc = $this->dbc;
?>
<div class="row">
	<div class="col-lg-12 col-xl-12 order-lg-12 order-xl-12">
		<div id="panel-variable" class="panel">
			<div class="panel-hdr mb-2">
				<h2>Variable<span class="fw-300"><i>Editor</i></span></h2>
				<button class="btn btn-primary" onclick="fn.app.zystem.core.variable.dialog_add()" style="margin-right:5px;">Add Variable</button>
			</div>
			<div class="panel-container show">
				<div class="panel-content">
					<table class="table table-borderd table-stripe">
						<thead>
							<tr>
								<th>Name</th>
								<th>Value</th>
								<th width="90">Action</th>
							</tr>
						</thead>
						<tbody>
						<?php
							$sql = "SELECT * FROM os_variable";
							$rst = $dbc->Query($sql);
							while($variable = $dbc->Fetch($rst)){
								echo '<tr>';
									echo '<td>'.$variable['name'].'</td>';
									echo '<td style="word-break: break-word;">'.$variable['value'].'</td>';
									echo '<td>';
										echo '<button type="button" class="btn btn-xs btn-outline-dark btn-icon" onclick="fn.app.zystem.core.variable.dialog_edit('.$variable['id'].')"><span class="fa fa-pen"></span></button>';
										echo '<button type="button" class="btn btn-xs btn-danger btn-icon" onclick="fn.app.zystem.core.variable.dialog_remove('.$variable['id'].')"><span class="fa fa-trash"></span></button>';
									echo '</td>';
								echo '</tr>';
							}
						?>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>
<style>
	#panel-variable table td .btn-icon{
		margin-left:5px;
	}
</style>