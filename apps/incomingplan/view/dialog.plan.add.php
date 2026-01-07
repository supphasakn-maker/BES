<?php
session_start();
include_once "../../../config/define.php";
include_once "../../../include/db.php";
include_once "../../../include/oceanos.php";
include_once "../../../include/iface.php";
include_once "../../../include/session.php";

@ini_set('display_errors', DEBUG_MODE ? 1 : 0);
date_default_timezone_set(DEFAULT_TIMEZONE);

$dbc = new dbc;
$dbc->Connect();

$os = new oceanos($dbc);

class myModel extends imodal
{
	function body()
	{
		$dbc = $this->dbc;
?>
		<style>
			/* แก้ไข select dropdown ใน modal Add Plan */
			#dialog_add_plan .modal-body .form-control,
			#dialog_add_plan .modal-body .form-select,
			#dialog_add_plan .modal-body select,
			#dialog_add_plan .modal-body input[type="text"],
			#dialog_add_plan .modal-body input[type="date"],
			#dialog_add_plan .modal-body input[type="number"] {
				border: 2px solid #e9ecef !important;
				border-radius: 6px !important;
				padding: 12px 15px !important;
				font-size: 1rem !important;
				transition: all 0.3s ease !important;
				background: white !important;
				width: 100% !important;
				height: 55px !important;
				line-height: 30px !important;
				vertical-align: middle !important;
				box-sizing: border-box !important;
			}

			/* แก้ไข select โดยเฉพาะ */
			#dialog_add_plan .modal-body select {
				appearance: none !important;
				background-image: url("data:image/svg+xml;charset=US-ASCII,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 4 5'><path fill='%23666' d='M2 0L0 2h4zm0 5L0 3h4z'/></svg>") !important;
				background-repeat: no-repeat !important;
				background-position: right 12px center !important;
				background-size: 0.7rem auto !important;
				padding-right: 40px !important;
				cursor: pointer !important;
				font-weight: 500 !important;
			}

			/* แก้ไข option ใน dropdown */
			#dialog_add_plan .modal-body select option {
				padding: 10px 15px !important;
				font-size: 1rem !important;
				line-height: 2 !important;
				background: white !important;
				color: #333 !important;
				height: 45px !important;
				box-sizing: border-box !important;
			}

			#dialog_add_plan .modal-body select option:hover {
				background: #f8f9fa !important;
			}

			/* Focus states */
			#dialog_add_plan .modal-body .form-control:focus,
			#dialog_add_plan .modal-body .form-select:focus,
			#dialog_add_plan .modal-body select:focus,
			#dialog_add_plan .modal-body input:focus {
				border-color: #0056b3 !important;
				box-shadow: 0 0 0 3px rgba(0, 86, 179, 0.15) !important;
				outline: none !important;
			}

			/* แก้ไข label */
			#dialog_add_plan .modal-body label {
				font-weight: 600 !important;
				color: #343a40 !important;
				font-size: 0.9rem !important;
				margin-bottom: 6px !important;
				display: block !important;
			}

			/* ปรับ modal header */
			#dialog_add_plan .modal-header {
				background: linear-gradient(135deg, #2c3e50 0%, #0056b3 100%) !important;
				color: white !important;
				border-bottom: none !important;
				padding: 1rem 1.5rem !important;
			}

			#dialog_add_plan .modal-header .modal-title {
				color: white !important;
				font-weight: 600 !important;
				font-size: 1.2rem !important;
			}

			#dialog_add_plan .modal-header .btn-close,
			#dialog_add_plan .modal-header .close {
				color: white !important;
				opacity: 0.8 !important;
			}

			/* ปรับ modal body */
			#dialog_add_plan .modal-body {
				padding: 1.5rem !important;
				background: white !important;
			}

			/* ปรับ form layout */
			#dialog_add_plan .modal-body .container {
				padding: 0 !important;
			}

			#dialog_add_plan .modal-body .form-row {
				margin-bottom: 1rem !important;
				display: block !important;
			}

			#dialog_add_plan .modal-body .form-group {
				margin-bottom: 0 !important;
			}

			/* ปรับ modal footer */
			#dialog_add_plan .modal-footer {
				padding: 1rem 1.5rem !important;
				background: white !important;
				border-top: 1px solid #e9ecef !important;
			}

			#dialog_add_plan .modal-footer .btn {
				padding: 0.6rem 1.5rem !important;
				font-weight: 600 !important;
				border-radius: 20px !important;
				font-size: 0.95rem !important;
			}

			/* ปรับขนาด modal dialog */
			#dialog_add_plan .modal-lg {
				max-width: 900px !important;
			}

			/* Responsive */
			@media (max-width: 768px) {

				#dialog_add_plan .modal-body .form-control,
				#dialog_add_plan .modal-body .form-select,
				#dialog_add_plan .modal-body select,
				#dialog_add_plan .modal-body input {
					font-size: 16px !important;
					/* ป้องกัน zoom ใน iOS */
					height: 50px !important;
					padding: 10px 12px !important;
				}

				#dialog_add_plan .modal-body {
					padding: 1rem !important;
				}

				#dialog_add_plan .modal-header {
					padding: 0.8rem 1rem !important;
				}

				#dialog_add_plan .modal-footer {
					padding: 0.8rem 1rem !important;
				}
			}
		</style>

		<div class="container">
			<form name="form_addplan">
				<div class="form-row">
					<div class="form-group col-md-6">
						<label>Reserve</label>
						<select id="import_id" name="import_id" class="form-control">
							<option value="">Select Import</option>
							<?php
							$sql = "SELECT id, CONCAT(id, '|', lock_date, '|', weight_lock, '|', bar, '|', brand) AS concatenated_value FROM bs_reserve_silver WHERE type = 2";
							$result = $dbc->query($sql);
							if ($result->num_rows > 0) {
								while ($row = $result->fetch_assoc()) {
									echo '<option value="' . $row['id'] . '">' . htmlspecialchars($row['concatenated_value']) . '</option>';
								}
							} else {
								echo '<option value="">No records found</option>';
							}
							?>
						</select>
					</div>
				</div>
				<div class="form-row">
					<label>Import Date</label>
					<input min=<?php echo date("Y-m-d"); ?> type="date" name="import_date" class="form-control">
				</div>
				<div class="form-row">
					<label>Brand</label>
					<input type="text" name="import_brand" class="form-control">
				</div>
				<div class="form-row">
					<label>Lot</label>
					<input type="text" name="import_lot" class="form-control">
				</div>
				<div class="form-row">
					<label>Amount</label>
					<input type="text" name="amount" class="form-control">
				</div>
				<div class="form-row">
					<label>PM/DC</label>
					<input type="text" name="rate_pmdc" class="form-control">
				</div>
				<div class="form-row">
					<label>Factory</label>
					<input type="text" name="factory" class="form-control">
				</div>
				<div class="form-row">
					<div class="form-group col-md-6">
						<label>Product</label>
						<select id="product_type_id" name="product_type_id" class="form-control">
							<option value="">Select Product Type</option>
							<?php
							$sql = "SELECT * FROM bs_products WHERE id!= 9";
							$result = $dbc->query($sql);
							if ($result->num_rows > 0) {
								while ($row = $result->fetch_assoc()) {
									echo '<option value="' . $row['id'] . '">' . $row['name'] . '</option>';
								}
							} else {
								echo '<option value="">No records found</option>';
							}
							?>
						</select>
					</div>
				</div>
				<div class="form-row">
					<label>INVOICE</label>
					<input type="text" name="coa" class="form-control" value="-">
				</div>
				<div class="form-row">
					<label>COUNTRY</label>
					<input type="text" name="country" class="form-control" value="-">
				</div>
				<div class="form-row">
					<label>PRE COC</label>
					<input type="text" name="coc" class="form-control" value="-">
				</div>
				<div class="form-row">
					<label>เลขที่ใบขน</label>
					<input type="text" name="brand" class="form-control" value="-">
				</div>
				<div class="form-row">
					<div class="form-group col-md-6">
						<label>Type</label>
						<select id="remark" name="remark" class="form-control">
							<option value="">Select Type</option>
							<?php
							$sql = "SELECT * FROM bs_products_import";
							$result = $dbc->query($sql);
							if ($result->num_rows > 0) {
								while ($row = $result->fetch_assoc()) {
									echo '<option value="' . $row['code'] . '">' . $row['name'] . '</option>';
								}
							} else {
								echo '<option value="">No records found</option>';
							}
							?>
						</select>
					</div>
				</div>
				<div class="form-row">
					<div class="form-group col-md-6">
						<label>Supplier</label>
						<select id="supplier_id" name="supplier_id" class="form-control">
							<option value="">Select Supplier</option>
							<?php
							$sql = "SELECT * FROM bs_suppliers WHERE status =1";
							$result = $dbc->query($sql);
							if ($result->num_rows > 0) {
								while ($row = $result->fetch_assoc()) {
									echo '<option value="' . $row['id'] . '">' . $row['name'] . '</option>';
								}
							} else {
								echo '<option value="">No records found</option>';
							}
							?>
						</select>
					</div>
				</div>
				<div class="form-row">
					<label>USD</label>
					<input type="text" name="usd" class="form-control" value="0">
				</div>
			</form>
		</div>

		<script>
			$(document).ready(function() {
				// รอให้ modal แสดงขึ้นมาก่อน
				$('#dialog_add_plan').on('shown.bs.modal', function() {
					fixModalSelectHeight();
				});

				// รอให้ modal เริ่มแสดง
				$('#dialog_add_plan').on('show.bs.modal', function() {
					setTimeout(fixModalSelectHeight, 100);
				});

				// Force style แก้ปัญหา select dropdown ขนาดเล็กใน modal
				function fixModalSelectHeight() {
					$('#dialog_add_plan .modal-body select, #dialog_add_plan .modal-body input, #dialog_add_plan .modal-body .form-control, #dialog_add_plan .modal-body .form-select').each(function() {
						$(this).css({
							'height': '55px',
							'line-height': '30px',
							'padding': '12px 15px',
							'font-size': '1rem',
							'vertical-align': 'middle',
							'display': 'block',
							'box-sizing': 'border-box',
							'border': '2px solid #e9ecef',
							'border-radius': '6px',
							'background': 'white',
							'width': '100%'
						});
					});

					// เฉพาะ select
					$('#dialog_add_plan .modal-body select').css({
						'padding-right': '40px',
						'background-position': 'right 12px center',
						'appearance': 'none',
						'background-image': 'url("data:image/svg+xml;charset=US-ASCII,<svg xmlns=\'http://www.w3.org/2000/svg\' viewBox=\'0 0 4 5\'><path fill=\'%23666\' d=\'M2 0L0 2h4zm0 5L0 3h4z\'/></svg>")',
						'background-repeat': 'no-repeat',
						'background-size': '0.7rem auto',
						'cursor': 'pointer',
						'font-weight': '500'
					});

					console.log('Fixed modal add plan select heights');
				}

				// รันทันทีถ้า modal มีอยู่แล้ว
				if ($('#dialog_add_plan').length > 0) {
					fixModalSelectHeight();
				}

				// รันซ้ำทุก 1 วินาทีเพื่อให้แน่ใจ (สำหรับ modal)
				setInterval(function() {
					if ($('#dialog_add_plan').is(':visible')) {
						fixModalSelectHeight();
					}
				}, 1000);

				// เมื่อมีการเปลี่ยนแปลงใน form
				$(document).on('change', '#dialog_add_plan select', function() {
					setTimeout(fixModalSelectHeight, 100);
				});

				// เมื่อ modal ปิด
				$('#dialog_add_plan').on('hidden.bs.modal', function() {
					console.log('Modal add plan closed');
				});
			});
		</script>

<?php
		/*
			}
			*/
	}
}

$modal = new myModel($dbc, $os->auth);
$modal->setParam($_POST);
$modal->setModel("dialog_add_plan", "Add Plan");
$modal->setExtraClass("modal-lg");
$modal->setButton(array(
	array("close", "btn-secondary", "Dismiss"),
	array("action", "btn-danger", "Summit", "fn.app.incomingplan.plan.add()")
));
$modal->EchoInterface();

$dbc->Close();
?>