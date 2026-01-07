<div class="row">
	<div class="col-xl-12">
		<div class="panel">
			<div class="panel-container show">
				<div class="panel-content">
					<form name="search_filter" class="form-horizontal" onsubmit="fn.app.logger.log.search();return false;">
						<div class="form-group row">
							<label class="col-sm-1 col-form-label text-right">Period</label>
							<div class="col-sm-2">
								<select name="period" class="form-control">
									<option value="today">Today</option>
									<option value="yesterday">Yesterday</option>
									<option value="week">This Week</option>
									<option value="month">This Month</option>
									<option value="quarter">This Quarter</option>
									<option value="year">This Year</option>
									<option value="custom">Custom</option>
								</select>
							</div>
							<label class="col-sm-1 col-form-label text-right">From</label>
							<div class="col-sm-2">
								<input type="date" class="form-control" name="from">
							</div>
							<div class="col-sm-2">
								<input type="date" class="form-control" name="to">
							</div>
							<label class="col-sm-1 col-form-label text-right">Group By</label>
							<div class="col-sm-2">
								<select name="groupby" class="form-control">
									<option value="none">None</option>
									<option value="user">User</option>
									<option value="action">Behaviour</option>
								</select>
							</div>
							<div class="col-sm-1">
								<button class="btn btn-success" type="submit">Search</button>
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>
		<div>
		</div>
	</div>
	<div id="report_zone" class="col-xl-12">
	</div>
	
</div>

