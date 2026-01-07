<?php
	global $ui_form,$dbc;
	$claim = $dbc->GetRecord("bs_claims","*","id=".$_GET['claim_id']);
	
?>
<form name="form_editproduct" onsubmit="fn.app.claim.product.edit();return false;">
	<input type="hidden" name="id" value="<?php echo $claim['id'];?>">
	<div class="row">
		<div class="col-2">
			<button type="button" onclick="window.history.back();" class="btn btn-danger">Back</button>
			<button type="button" onclick="fn.app.claim.product.edit();" name="round" class="btn btn-primary" type="button">บันทึก</button>
		</div>
	</div>
	<ul class="nav nav-tabs mt-3" id="myTab" role="tablist">
		<li class="nav-item" role="presentation">
			<a class="nav-link active" id="home-tab" data-toggle="tab" href="#home" role="tab" aria-controls="home" aria-selected="true">ปัญหาที่แจ้ง</a>
		</li>
		<li class="nav-item" role="presentation">
			<a class="nav-link" id="profile-tab" data-toggle="tab" href="#profile" role="tab" aria-controls="profile" aria-selected="false">วิธีการแก้ไขปัญหา</a>
		</li>
		<li class="nav-item" role="presentation">
			<a class="nav-link" id="photo-tab" data-toggle="tab" href="#photo" role="tab" aria-controls="photo" aria-selected="false">รูปภาพของปัญหา</a>
		</li>	
	</ul>
	<div class="tab-content" id="myTabContent">
		<div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
			<?php include "view/product/view.claim.php";?>	
		</div>
		<div class="tab-pane fade" id="profile" role="tabpanel" aria-labelledby="profile-tab">
			<?php include "view/product/view.problem.php";?>
		</div>
		<div class="tab-pane fade" id="photo" role="tabpanel" aria-labelledby="photo-tab">
			<?php include "view/product/view.photo.php";?>
		</div>
	</div>

</form>
<form name="form_uploader" style="display:none;">
	<input type="file" multiple name="file[]">
</form>





