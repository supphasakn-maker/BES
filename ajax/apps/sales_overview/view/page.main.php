<div class="row gutters-sm">
	<div class="col-xl-6 mb-3">
		<div class="card mb-3">
			<div class="card-body">
				<form class="form-inline mb-3">
					<label class="mr-sm-2">เลือกวันที่</label>
					<input name="date" type="date" class="form-control mr-sm-2" value="<?php echo $date;?>" onchange="$(this).parent().submit()">
					<button type="submit" class="btn btn-primary">Lookup</button>
				</form>
				<?php include "view/card.overview.php";?>
				
			</div>
		</div>
		<div class="card mb-3">
			<div class="card-body">
				<?php include "view/card.delivery.php";?>
				
			</div>
		</div>
		<div class="card mb-3">
			<div class="card-body">
				<?php include "view/card.delivery_future.php";?>
			</div>
		</div>
	</div>
	<div class="col-xl-6 mb-3">
		<div class="card">
			<div class="card-body">
				<?php include "view/card.sales.php";?>
			</div>
		</div>
		<div class="card">
			<div class="card-body">
				<?php include "view/card.removed.php";?>
			</div>
		</div>
	</div>

	
</div>