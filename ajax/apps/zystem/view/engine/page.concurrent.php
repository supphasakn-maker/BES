<?php
	$con = $this->dbc->GetRecord("os_concurrents","COUNT(id)");
?>
<h3>
    Add Reset Concurrent
</h3>
<p>
	<form name="concurrent" class="form-inline" onsubmit="fn.app.zystem.engine.concurrent.reset();return false;">
		<div class="form-group mb-2">
			<label>Number of Concurrent</label>
		</div>
		<div class="form-group mx-sm-3 mb-2">
			<input type="text" class="form-control" name="concurrent" value="<?php echo $con[0];?>">
		</div>
		<button type="submit" class="btn btn-danger mb-2">Reset Concurrent</button>
	</form>
<p>