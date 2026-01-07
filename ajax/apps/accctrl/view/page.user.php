<?php
	global $os;
?>
<div class="btn-area btn-group mb-2"></div>
<table id="tblUser" class="table table-striped table-bordered table-hover table-middle" width="100%" account="<?php echo $this->auth['account'];?>">
	<thead>
		<tr>
			<th class="text-center hidden-xs">
				<span type="checkall" control="chk_user" class="far fa-lg fa-square"></span>
			</th>
			<th class="text-center"><?php echo $os->tr("user.username");?></th>
			<th class="text-center">Avatar</th>
			<th class="text-center hidden-xs">Fullname</th>
			<th class="text-center hidden-xs">Group</th>
			<th class="text-center hidden-xs">Phone</th>
			<th class="text-center hidden-xs">Email</th>
			<th class="text-center hidden-xs">Last Login</th>
			<th class="text-center">Action</th>
		</tr>
	</thead>
	<tbody>
	</tbody>
</table>
