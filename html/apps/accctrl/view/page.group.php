<?php
	global $os;
?>
<div class="btn-area btn-group mb-2"></div>
<table id="tblGroup" class="table table-striped table-bordered table-hover table-middle" width="100%" account="<?php echo $this->auth['account'];?>">
	<thead>
		<tr>
			<th class="text-center hidden-xs">
				<span type="checkall" control="chk_group" class="far fa-lg fa-square"></span>
			</th>
			<th class="text-center"><?php echo $os->tr("group.name");?></th>
			<th class="text-center"><?php echo $os->tr("group.account");?></th>
			<th class="text-center hidden-xs"><?php echo $os->tr("group.created");?></th>
			<th class="text-center hidden-xs"><?php echo $os->tr("group.updated");?></th>
			<th class="text-center"><?php echo $os->tr("group.action");?></th>
		</tr>
	</thead>
	<tbody>
	</tbody>
</table>
