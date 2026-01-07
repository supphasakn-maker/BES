<?php
	session_start();
	include_once "../../../config/define.php";
	include_once "../../../include/db.php";
	
	@ini_set('display_errors',DEBUG_MODE?1:0);
	date_default_timezone_set(DEFAULT_TIMEZONE);
	
	$dbc = new dbc;
	$dbc->Connect();
	
	
	$transfer = $dbc->GetRecord("bs_transfers","*","id=".$_POST['id']);
?>
<div class="modal fade" id="dialog_usd_lookup" data-backdrop="static">
  	<div class="modal-dialog modal-full">
    	<div class="modal-content">
      		<div class="modal-header">
        		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4>Select USD</h4>
      		</div>
		    <div class="modal-body">
				<form name="form_addcontract">
				<input type="hidden" name="id" value="<?php echo $transfer['id'];?>">
				<input type="hidden" name="nonfixed_value" value="<?php echo $transfer['nonfixed_value'];?>">
				<input type="hidden" name="select_usd" value="">
				<input type="hidden" name="select_amount" value="">
				<input type="hidden" name="select_bank_date" value="">
				<input type="hidden" name="select_premium_date" value="">
				<input type="hidden" name="select_contact_no" value="">
				<input type="hidden" name="select_premium" value="">
				<table class="table">
					<tbody>
						<tr>
							<th class="text-center">ยอดคงเหลือ</th><td class="text-center"><?php echo $transfer['nonfixed_value'];?></td>
							<th class="text-center">ยอดรวม</th><td class="text-center" id="total"><?php echo 0;?></td>
							<th class="text-center">คงเหลือ</th><td class="text-center" id="remain"><?php echo 0;?></td>
						</tr>
					</tbody>
				</table>
				
				<table data-nonfixed="<?php echo $transfer['nonfixed_value'];?>" id="tblUsdLookup" class="table table-striped table-bordered table-hover table-middle" width="100%">
					<thead>
						<tr>
							<th class="text-center">Select</th>
							<th class="text-center">Date</th>
							<th class="text-center">Forward Contract No.</th>
							<th class="text-center">Interest</th>
							<th class="text-center">THB</th>
							<th class="text-center">Amount</th>
							<th class="text-center">Supplier</th>
							<th class="text-center">Due Date</th>
							<th class="text-center">Payment Date</th>
							<th class="text-center">Interest Day</th>
							<th class="text-center">Counter Rate</th>
							<th class="text-center">rate_exchange</th>
							<th class="text-center">Amount Paid</th>
						</tr>
					</thead>
					<tbody>
					</tbody>
				</table>
				</form>
		    </div>
			<div class="modal-footer">
				<button type="button" class="btn btn-outline-dark" data-dismiss="modal">Cancel</button>
				<button type="submit" class="btn btn-primary btnSelect">Select</button>
			</div>
	  	</div>
		</form>
	</div>
</div>

<?php
	$dbc->Close();
?>