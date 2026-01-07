<?php
session_start();
include_once "../../../config/define.php";
include_once "../../../include/db.php";
include "../../../include/oceanos.php";
include "../../../include/iface.php";
include_once "../../../include/session.php";

@ini_set('display_errors', DEBUG_MODE ? 1 : 0);
date_default_timezone_set(DEFAULT_TIMEZONE);

$dbc = new dbc;
$dbc->Connect();
$os = new oceanos($dbc);
$ui_form = new iform($dbc, $os->auth);

$transfer = $dbc->GetRecord("bs_transfers", "*", "id=" . $_POST['id']);
?>
<div class="modal fade" id="dialog_usd_lookup" data-backdrop="static">
	<div class="modal-dialog modal-full">
		<div class="modal-content">
			<div class="modal-header">
				<h4>Select USD</h4>
			</div>
			<div class="modal-body">
				<form name="form_addcontract">
					<input type="hidden" name="id" value="<?php echo $transfer['id']; ?>">
					<input type="hidden" name="previous_value_usd_fixed" value="<?php echo $transfer['value_usd_fixed']; ?>">
					<input type="hidden" name="previous_value_usd_nonfixed" value="<?php echo $transfer['value_usd_nonfixed']; ?>">
					<input type="hidden" name="previous_value_thb_fixed" value="<?php echo $transfer['value_thb_fixed']; ?>">
					<input type="hidden" name="previous_value_thb_premium" value="<?php echo $transfer['value_thb_premium']; ?>">
					<input type="hidden" name="previous_value_thb_net" value="<?php echo $transfer['value_thb_net']; ?>">
					<input type="hidden" name="value_thb_transaction" value="<?php echo $transfer['value_thb_transaction']; ?>">
					<input type="hidden" name="paid_thb" value="<?php echo $transfer['paid_thb']; ?>">
					<input type="hidden" name="paid_usd" value="<?php echo $transfer['paid_usd']; ?>">

					<table class="table table-bordered table-form">
						<tbody>
							<tr>
								<td class="bg-warning"><label>Date Added</label></td>
								<td colspan="3">
									<?php
									$ui_form->EchoItem(array(
										"type" => "date",
										"name" => "date_transfer",
										"value" => date("Y-m-d")
									));
									?>
								</td>
							</tr>
							<tr>
								<td class="bg-warning"><label>Total USD</label></td>
								<td colspan="3">
									<?php
									$ui_form->EchoItem(array(
										"name" => "value_usd_total",
										"class" => "text-right",
										"readonly" => true,
										"value" => number_format($transfer['value_usd_total'], 2, '.', '')
									));
									?>
								</td>
							</tr>
							<tr>
								<td><label>Non Fixed</label></td>
								<td>
									<?php
									$ui_form->EchoItem(array(
										"name" => "value_usd_nonfixed",
										"value" => number_format($transfer['value_usd_nonfixed'], 2, '.', ''),
										"class" => "text-right",
										"readonly" => true
									));
									?>
								</td>
								<td><label>Counter Rate</label></td>
								<td>
									<?php
									$ui_form->EchoItem(array(
										"name" => "rate_counter",
										"class" => "text-right",
										"value" => $transfer['rate_counter']
									));
									?>
								</td>


							</tr>
							<tr>
								<td><label>Fixed Value</label></td>
								<td>
									<?php
									$ui_form->EchoItem(array(
										"name" => "value_usd_fixed",
										"value" => number_format($transfer['value_usd_fixed'], 2, '.', ''),
										"class" => "text-right",
										"readonly" => true
									));
									?>
								</td>
								<td><label>(THB)</label></td>
								<td>
									<?php
									$ui_form->EchoItem(array(
										"name" => "value_thb_fixed",
										"value" => number_format($transfer['value_thb_fixed'], 2, '.', ''),
										"class" => "text-right",
										"readonly" => true
									));
									?>
								</td>
							</tr>
							<tr>
								<td></td>
								<td></td>
								<td><label>Total Premium (THB)</label></td>
								<td>
									<?php
									$ui_form->EchoItem(array(
										"name" => "value_thb_premium",
										"value" => number_format($transfer['value_thb_premium'], 2, '.', ''),
										"class" => "text-right",
										"readonly" => true
									));
									?>
								</td>
							</tr>
							<tr>
								<td></td>
								<td></td>
								<td><label>Total (THB)</label></td>
								<td>
									<?php
									$ui_form->EchoItem(array(
										"name" => "value_thb_net",
										"value" => number_format($transfer['value_thb_net'], 2, '.', ''),
										"class" => "text-right",
										"readonly" => true
									));
									?>
								</td>
							</tr>
						</tbody>
					</table>
					<div>
						<select class="form-control" name="premium_type">
							<option value="1">แบบปกติ</option>
							<option value="2">แปบบกำหนดค่าเลย</option>
						</select>
					</div>
					<table id="tblSelected" class="table table-bordered">
						<thead>
							<tr>
								<th class="text-center">Date</th>
								<th class="text-center">USD Amount</th>
								<th class="text-center">Rate Exchange</th>
								<th class="text-center">THB Value</th>
								<th class="text-center show-a">Start Date/Premium Date</th>
								<th class="text-center show-a">Preminum Date</th>
								<th class="text-center">Contract No.</th>
								<th class="text-center show-a">Preminum Rate</th>
								<th class="text-center show-a">Fx + Premium</th>
								<th class="text-center show-b">Premium</th>
								<th class="text-center">THB+Premium</th>
							</tr>
						</thead>
						<tbody>
							<tr id="selected_tr">
								<input type="hidden" class="form-control form-control-sm text-right" readonly xname="tr_id">

								<td xname="date" class="text-right">-</td>
								<td xname="amount" class="text-right">-</td>
								<td xname="rate_exchange" class="text-right">-</td>
								<td xname="thb_value" class="text-right">-</td>
								<td class="text-center show-a">
									<input type="date" name="usd_date_premium_start[]" class="form-control form-control-sm" xname="date_premium_start" onchange="fn.app.forward_contract.contract.calculate()">
									<input type="date" name="usd_date_premium_end[]" class="form-control form-control-sm" xname="date_premium_end" onchange="fn.app.forward_contract.contract.calculate()">
								</td>
								<td class="text-center show-a">
									<input type="text" class="form-control form-control-sm text-right" readonly xname="premium_day">
								</td>
								<td class="text-center">
									<input type="text" name="usd_fw_contact_no[]" class="form-control form-control-sm" xname="contact_no">
								</td>
								<td class="text-center show-a">
									<input type="text" name="usd_premium_rate[]" class="form-control form-control-sm text-right" value="0" xname="usd_premium_rate" onchange="fn.app.forward_contract.contract.calculate()">
								</td>
								<td class="text-center show-a">
									<input type="text" class="form-control form-control-sm text-right" readonly xname="premiumfx">
								</td>
								<td class="text-center show-b">
									<input type="text" name="usd_premium[]" class="form-control form-control-sm text-right" readonly xname="usd_premium">
								</td>
								<td class="text-center">
									<input type="text" class="form-control form-control-sm text-right" readonly xname="thbpremium">
								</td>
							</tr>
						</tbody>
					</table>



					<table data-nonfixed="<?php echo $transfer['value_usd_nonfixed']; ?>" id="tblUsdLookup" class="table table-striped table-bordered table-hover table-middle" width="100%">
						<thead>
							<tr>
								<th class="text-center">Select</th>
								<th class="text-center">Date</th>
								<th class="text-center">Type</th>
								<th class="text-center">Method</th>
								<th class="text-center">Amount</th>
								<th class="text-center">Fx</th>
								<th class="text-center">Comment</th>
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