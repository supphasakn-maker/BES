<?php
global $dbc;

if($this->GetSection()=="prepare"){
	include "view/page.prepare.php";
	
}else{
	

?>

<div class="btn-area btn-group mb-2">
	<input id="date_filter" type="date" class="form-control" value="<?php echo date("Y-m-d");?>">
</div>
<table id="tblDelivery" class="table table-striped table-bordered table-hover table-middle" width="100%">
	<thead>
		<tr>
			<th class="text-center hidden-xs">
				<span type="checkall" control="chk_delivery" class="far fa-lg fa-square"></span>
			</th>
			<th class="text-center">Code</th>
			<th class="text-center">Order</th>
			<th class="text-center">Customer</th>
			<th class="text-center">Delviery Date</th>
			<th class="text-center">Weight</th>
			<th class="text-center">Total Item</th>
			<th class="text-center">Action</th>
		</tr>
	</thead>
	<tbody>
	</tbody>
</table>
<?php
}
?>