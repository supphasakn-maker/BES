<?php
	if($this->GetSection()=="edit"){
		include "view/page.product.edit.php";
	}else{
		$today = time();
?>
<div class="btn-area btn-group mb-2">
</div>
<table id="tblProduct" class="table table-striped table-bordered table-hover table-middle" width="100%">
	<thead class="bg-dark">
		<tr>
			<th class="text-center text-white font-weight-bold">CLAIM ID</th>
			<th class="text-center text-white font-weight-bold">DATE</th>
			<th class="text-center text-white font-weight-bold">ORDER ID</th>
			<th class="text-left text-white font-weight-bold">CUSTOMER</th>
			<th class="text-left text-white font-weight-bold">TYPE</th>
			<th class="text-left text-white font-weight-bold">PROBLEM</th>
			<th class="text-center text-white font-weight-bold">AMOUNT</th>
			<th class="text-left text-white font-weight-bold">PRODUCT</th>
			<th class="text-center text-white font-weight-bold">STATUS</th>
			<th class="text-center text-white font-weight-bold"></th>
		</tr>
	</thead>
	<tbody>
	</tbody>
</table>
<div>
	<div>1.<span class="badge badge-secondary">Draft</span></div>
	<div>2.<span class="badge badge-warning">Submited</span></div>
	<div>3.<span class="badge badge-success">Approved</span></div>
	<div>4.<span class="badge badge-danger">Rejected</span></div>
	<div>5.<span class="badge badge-primary">Solved</span></div>
	<div>6.<span class="badge badge-dark">Closed</span></div>
</div>
<?php
	}
?>
