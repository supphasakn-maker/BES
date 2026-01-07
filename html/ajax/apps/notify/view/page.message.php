<?php
	$section = isset($_GET['section'])?$_GET['section']:"";
	if($section == "view"){
		include "view/page.message.view.php";
	}else{
		
?>
<div class="btn-area btn-group mb-2"></div>
<table id="tblMessage" class="table table-striped table-bordered table-hover table-middle" width="100%">
	<thead>
		<tr>
			<th class="text-center">ID</th>
			<th class="text-center">Source</th>
			<th class="text-center">Destination</th>
			<th class="text-center">Type</th>
			<th class="text-center">Message</th>
			<th class="text-center">Created</th>
			<th class="text-center">Updated</th>
			<th class="text-center">Opened</th>
			<th class="text-center">Acknowledge</th>
			<th class="text-center">Action</th>
		</tr>
	</thead>
	<tbody>
	</tbody>
</table>
<?php
	}
?>