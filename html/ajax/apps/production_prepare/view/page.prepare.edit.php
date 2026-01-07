<?php
	global $ui_form,$dbc;
	$production = $dbc->GetRecord("bs_productions","*","id=".$_GET['production_id']);
	
?>
<form name="form_editprepare" onsubmit="fn.app.prepare.produce.edit();return false;">
	<input type="hidden" name="id" value="<?php echo $production['id'];?>">
	<div class="row">
		<div class="col-4">
			<table class="table table-bordered table-form">
				<tbody>
					<tr>
						<td><label>รอบที่ </label></td>
						<td><input name="round" type="text" class="form-control" value="<?php echo $production['round'];?>"></td>
					</tr>
				</tbody>
			</table>
		</div>
		<div class="col-2">
			<button type="button" onclick="window.history.back();" class="btn btn-danger">Back</button>
			<button type="button" onclick="fn.app.production_prepare.prepare.edit();" name="round" class="btn btn-primary" type="button">ทำรายการ</button>
		</div>
	</div>
	<ul class="nav nav-tabs" id="myTab" role="tablist">
		<li class="nav-item" role="presentation">
			<a class="nav-link active" id="home-tab" data-toggle="tab" href="#home" role="tab" aria-controls="home" aria-selected="true">การจัดการถุง</a>
		</li>
		<li class="nav-item" role="presentation">
			<a class="nav-link" id="profile-tab" data-toggle="tab" href="#profile" role="tab" aria-controls="profile" aria-selected="false">รายละเอียดเต้าหลอม</a>
		</li>
		<li class="nav-item" role="presentation">
			<a class="nav-link" id="photo-tab" data-toggle="tab" href="#photo" role="tab" aria-controls="photo" aria-selected="false">รูปภาพ</a>
		</li>
		
	</ul>
	<div class="tab-content" id="myTabContent">
		<div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
			<?php include "view/prepare/view.packing.php";?>
		
		</div>
		<div class="tab-pane fade" id="profile" role="tabpanel" aria-labelledby="profile-tab">
			<?php include "view/prepare/view.production.php";?>
		</div>
		<div class="tab-pane fade" id="photo" role="tabpanel" aria-labelledby="photo-tab">
			<?php include "view/prepare/view.photo.php";?>
		</div>
	</div>

</form>
<form name="form_uploader" style="display:none;">
	<input type="file" multiple name="file[]">
</form>







