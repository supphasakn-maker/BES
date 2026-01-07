<?php
global $ui_form, $dbc;
$production = $dbc->GetRecord("bs_productions", "*", "id=" . $_GET['production_id']);

?>
<form name="form_editprepare" onsubmit="fn.app.prepare.produce.edit();return false;">
	<input type="hidden" name="id" value="<?php echo $production['id']; ?>">
	<div class="row">
		<div class="col-4">
			<table class="table table-bordered table-form">
				<tbody>
					<tr>
						<td><label>รอบที่ </label></td>
						<td><input name="round" type="text" class="form-control" value="<?php echo $production['round']; ?>" readonly></td>
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
			<a class="nav-link" id="profile-tab" data-toggle="tab" href="#profile" role="tab" aria-controls="profile" aria-selected="false">รายละเอียดผลิตเม็ดเงิน</a>
		</li>
		<li class="nav-item" role="presentation">
			<a class="nav-link" id="photo-tab" data-toggle="tab" href="#photo" role="tab" aria-controls="photo" aria-selected="false">รูปภาพ</a>
		</li>
		<li class="nav-item" role="presentation">
			<a class="nav-link" id="oven-tab" data-toggle="tab" href="#oven" role="tab" aria-controls="oven" aria-selected="false">เตาอบ</a>
		</li>
		<li class="nav-item" role="presentation">
			<a class="nav-link" id="scale-tab" data-toggle="tab" href="#scale" role="tab" aria-controls="scale" aria-selected="false">เครื่องชั่ง</a>
		</li>
		<li class="nav-item" role="presentation">
			<a class="nav-link" id="furnace-tab" data-toggle="tab" href="#furnace" role="tab" aria-controls="furnace" aria-selected="false">เตาหลอม</a>
		</li>
		<li class="nav-item" role="presentation">
			<a class="nav-link" id="incomingplan-tab" data-toggle="tab" href="#incomingplan" role="tab" aria-controls="incomingplan" aria-selected="false">Incoming Plan</a>
		</li>
		<li class="nav-item" role="presentation">
			<a class="nav-link" id="silver_save-tab" data-toggle="tab" href="#silver_save" role="tab" aria-controls="silver_save" aria-selected="false">แท่งเงินเข้าเซฟ</a>
		</li>
	</ul>
	<div class="tab-content" id="myTabContent">
		<div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
			<?php include "view/prepare/view.packing.php"; ?>

		</div>
		<div class="tab-pane fade" id="profile" role="tabpanel" aria-labelledby="profile-tab">
			<?php include "view/prepare/view.production.php"; ?>
		</div>
		<div class="tab-pane fade" id="photo" role="tabpanel" aria-labelledby="photo-tab">
			<?php include "view/prepare/view.photo.php"; ?>
		</div>
		<div class="tab-pane fade" id="oven" role="tabpanel" aria-labelledby="oven-tab">
			<?php include "view/prepare/view.oven.php"; ?>
		</div>
		<div class="tab-pane fade" id="scale" role="tabpanel" aria-labelledby="scale-tab">
			<?php include "view/prepare/view.scale.php"; ?>
		</div>
		<div class="tab-pane fade" id="furnace" role="tabpanel" aria-labelledby="furnace-tab">
			<?php include "view/prepare/view.furnace.php"; ?>
		</div>
		<div class="tab-pane fade" id="incomingplan" role="tabpanel" aria-labelledby="incomingplan-tab">
			<?php include "view/prepare/view.incomingplan.php"; ?>
		</div>
		<div class="tab-pane fade" id="silver_save" role="tabpanel" aria-labelledby="silver_save-tab">
			<?php include "view/prepare/view.silver_save.php"; ?>
		</div>
	</div>

</form>
<form name="form_uploader" style="display:none;">
	<input type="file" multiple name="file[]">
</form>