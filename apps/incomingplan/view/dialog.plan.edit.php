<?php
session_start();
include_once "../../../config/define.php";
@ini_set('display_errors', DEBUG_MODE ? 1 : 0);
date_default_timezone_set(DEFAULT_TIMEZONE);

include_once "../../../include/db.php";
include_once "../../../include/oceanos.php";
include_once "../../../include/iface.php";
include_once "../../../include/session.php";

$dbc = new dbc;
$dbc->Connect();

$os = new oceanos($dbc);
$plan = $dbc->GetRecord("bs_incoming_plans", "*", "id=" . $_POST['id']);

$modal = new imodal($dbc, $os->auth);

$modal->setModel("dialog_edit_plan", "Edit Plan");
$modal->initiForm("form_editplan");
$modal->setExtraClass("modal-lg");
$modal->setButton(array(
	array("close", "btn-secondary", "Dismiss"),
	array("action", "btn-outline-dark", "Save Change", "fn.app.incomingplan.plan.edit()")
));
$modal->SetVariable(array(
	array("id", $plan['id'])
));
$supplierOptions = array();

$supplierOptions[] = array("", "กรุณาเลือกรายการ");

$sqlSup = "SELECT id, name FROM bs_suppliers WHERE status = 1 ORDER BY name";
$rsSup  = $dbc->Query($sqlSup);

while ($row = $dbc->Fetch($rsSup)) {
	$supplierOptions[] = array($row['id'], $row['name']);
}

$blueprint = array(
	array(
		array(
			"type" => "comboboxdb",
			"name" => "import_id", //Reservie ID
			"caption" => "Reserve",
			"source" => array(
				"table" => "bs_reserve_silver",
				"name" => "CONCAT(id,'|',lock_date,'|',weight_lock,'|',bar,'|',brand)",
				"value" => "id",
			),
			"value" => $plan['import_id']
		)
	),
	array(
		array(
			"type" => "date",
			"name" => "import_date",
			"caption" => "Date",
			"placeholder" => "Import Date",
			"value" => $plan['import_date']
		)
	),
	array(
		array(
			"name" => "import_brand",
			"caption" => "Brand",
			"placeholder" => "Brand",
			"value" => $plan['import_brand']
		)
	),
	array(
		array(
			"name" => "import_lot",
			"caption" => "Lot",
			"placeholder" => "Lot Number",
			"value" => $plan['import_lot']
		)
	),
	array(
		array(
			"name" => "amount",
			"caption" => "Amount",
			"placeholder" => "Amount",
			"value" => $plan['amount']
		)
	),
	array(
		array(
			"name" => "rate_pmdc",
			"caption" => "PM/DC",
			"placeholder" => "PM/DC",
			"value" => $plan['rate_pmdc']
		)
	),
	array(
		array(
			"name" => "factory",
			"caption" => "Factory",
			"placeholder" => "Factory",
			"value" => $plan['factory']
		)
	),
	array(
		array(
			"type" => "comboboxdb",
			"name" => "product_type_id",
			"caption" => "Product",
			"source" => array(
				"table" => "bs_products",
				"name" => "name",
				"value" => "id",
				"where" => "id != 9"
			),
			"value" => $plan['product_type_id']
		)
	),
	array(
		array(
			"name" => "coa",
			"caption" => "INVOICE",
			"placeholder" => "INVOICE",
			"value" => $plan['coa']
		)
	),
	array(
		array(
			"name" => "country",
			"caption" => "COUNTRY",
			"placeholder" => "COUNTRY",
			"value" => $plan['country']
		)
	),
	array(
		array(
			"name" => "coc",
			"caption" => "Pre CoC",
			"placeholder" => "Pre CoC",
			"value" => $plan['coc']
		)
	),
	array(
		array(
			"name" => "brand",
			"caption" => "เลขที่ใบขน",
			"placeholder" => "เลขที่ใบขน",
			"value" => $plan['brand']
		)
	),
	array(
		array(
			"type" => "comboboxdb",
			"name" => "remark",
			"caption" => "Type",
			"source" => array(
				"table" => "bs_products_import",
				"name" => "name",
				"value" => "code"
			),
			"value" => $plan['remark']
		)
	),
	array(
		array(
			"type" => "date",
			"name" => "bank_date",
			"caption" => "วันที่จ่ายเงิน",
			"placeholder" => "วันที่จ่ายเงิน",
			"value" => $plan['bank_date']
		)
	),
	array(
		array(
			"name" => "supplier_id",
			"caption" => "Supplier",
			"type" => "combobox",
			"source" => $supplierOptions,
			"value" => $plan['supplier_id']
		)
	),
	array(
		array(
			"name" => "usd",
			"caption" => "USD",
			"placeholder" => "USD",
			"value" => $plan['usd']
		)
	)
);

$modal->SetBlueprint($blueprint);
$modal->EchoInterface();
?>

<style>
	/* แก้ไข select dropdown ใน modal Edit Plan */
	#dialog_edit_plan .modal-body .form-control,
	#dialog_edit_plan .modal-body .form-select,
	#dialog_edit_plan .modal-body select,
	#dialog_edit_plan .modal-body input[type="text"],
	#dialog_edit_plan .modal-body input[type="date"],
	#dialog_edit_plan .modal-body input[type="number"],
	#dialog_edit_plan .modal-body textarea {
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

	/* เฉพาะ textarea ให้สูงกว่า */
	#dialog_edit_plan .modal-body textarea {
		height: 80px !important;
		line-height: 1.4 !important;
		resize: vertical !important;
	}

	/* แก้ไข select โดยเฉพาะ */
	#dialog_edit_plan .modal-body select {
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
	#dialog_edit_plan .modal-body select option {
		padding: 10px 15px !important;
		font-size: 1rem !important;
		line-height: 2 !important;
		background: white !important;
		color: #333 !important;
		height: 45px !important;
		box-sizing: border-box !important;
	}

	#dialog_edit_plan .modal-body select option:hover {
		background: #f8f9fa !important;
	}

	/* Focus states */
	#dialog_edit_plan .modal-body .form-control:focus,
	#dialog_edit_plan .modal-body .form-select:focus,
	#dialog_edit_plan .modal-body select:focus,
	#dialog_edit_plan .modal-body input:focus,
	#dialog_edit_plan .modal-body textarea:focus {
		border-color: #0056b3 !important;
		box-shadow: 0 0 0 3px rgba(0, 86, 179, 0.15) !important;
		outline: none !important;
	}

	/* แก้ไข label */
	#dialog_edit_plan .modal-body label {
		font-weight: 600 !important;
		color: #343a40 !important;
		font-size: 0.9rem !important;
		margin-bottom: 6px !important;
	}

	/* ปรับ modal header */
	#dialog_edit_plan .modal-header {
		background: linear-gradient(135deg, #2c3e50 0%, #0056b3 100%) !important;
		color: white !important;
		border-bottom: none !important;
		padding: 1rem 1.5rem !important;
	}

	#dialog_edit_plan .modal-header .modal-title {
		color: white !important;
		font-weight: 600 !important;
		font-size: 1.2rem !important;
	}

	#dialog_edit_plan .modal-header .btn-close,
	#dialog_edit_plan .modal-header .close {
		color: white !important;
		opacity: 0.8 !important;
	}

	/* ปรับ modal body */
	#dialog_edit_plan .modal-body {
		padding: 1.5rem !important;
		background: white !important;
	}

	/* ปรับ modal footer */
	#dialog_edit_plan .modal-footer {
		padding: 1rem 1.5rem !important;
		background: white !important;
		border-top: 1px solid #e9ecef !important;
	}

	#dialog_edit_plan .modal-footer .btn {
		padding: 0.6rem 1.5rem !important;
		font-weight: 600 !important;
		border-radius: 20px !important;
		font-size: 0.95rem !important;
	}

	/* ปรับขนาด modal dialog */
	#dialog_edit_plan .modal-lg {
		max-width: 900px !important;
	}

	/* เพิ่มสีสันให้ขั้น form */
	#dialog_edit_plan .modal-body .row {
		margin-bottom: 1rem !important;
	}

	#dialog_edit_plan .modal-body .col-sm-3 {
		display: flex !important;
		align-items: center !important;
		padding-right: 1rem !important;
	}

	#dialog_edit_plan .modal-body .col-sm-9 {
		padding-left: 0 !important;
	}

	/* Responsive */
	@media (max-width: 768px) {

		#dialog_edit_plan .modal-body .form-control,
		#dialog_edit_plan .modal-body .form-select,
		#dialog_edit_plan .modal-body select,
		#dialog_edit_plan .modal-body input,
		#dialog_edit_plan .modal-body textarea {
			font-size: 16px !important;
			/* ป้องกัน zoom ใน iOS */
			height: 50px !important;
			padding: 10px 12px !important;
		}

		#dialog_edit_plan .modal-body textarea {
			height: 70px !important;
		}

		#dialog_edit_plan .modal-body {
			padding: 1rem !important;
		}

		#dialog_edit_plan .modal-header {
			padding: 0.8rem 1rem !important;
		}

		#dialog_edit_plan .modal-footer {
			padding: 0.8rem 1rem !important;
		}
	}
</style>

<script>
	$(document).ready(function() {
		// รอให้ modal แสดงขึ้นมาก่อน
		$('#dialog_edit_plan').on('shown.bs.modal', function() {
			fixModalSelectHeight();
		});

		// รอให้ modal เริ่มแสดง
		$('#dialog_edit_plan').on('show.bs.modal', function() {
			setTimeout(fixModalSelectHeight, 100);
		});

		// Force style แก้ปัญหา select dropdown ขนาดเล็กใน modal
		function fixModalSelectHeight() {
			$('#dialog_edit_plan .modal-body select, #dialog_edit_plan .modal-body input, #dialog_edit_plan .modal-body .form-control, #dialog_edit_plan .modal-body .form-select, #dialog_edit_plan .modal-body textarea').each(function() {
				const isTextarea = $(this).is('textarea');

				$(this).css({
					'height': isTextarea ? '80px' : '55px',
					'line-height': isTextarea ? '1.4' : '30px',
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
			$('#dialog_edit_plan .modal-body select').css({
				'padding-right': '40px',
				'background-position': 'right 12px center',
				'appearance': 'none',
				'background-image': 'url("data:image/svg+xml;charset=US-ASCII,<svg xmlns=\'http://www.w3.org/2000/svg\' viewBox=\'0 0 4 5\'><path fill=\'%23666\' d=\'M2 0L0 2h4zm0 5L0 3h4z\'/></svg>")',
				'background-repeat': 'no-repeat',
				'background-size': '0.7rem auto',
				'cursor': 'pointer',
				'font-weight': '500'
			});

			console.log('Fixed modal plan select heights');
		}

		// รันทันทีถ้า modal มีอยู่แล้ว
		if ($('#dialog_edit_plan').length > 0) {
			fixModalSelectHeight();
		}

		// รันซ้ำทุก 1 วินาทีเพื่อให้แน่ใจ (สำหรับ modal)
		setInterval(function() {
			if ($('#dialog_edit_plan').is(':visible')) {
				fixModalSelectHeight();
			}
		}, 1000);

		// เมื่อมีการเปลี่ยนแปลงใน form
		$(document).on('change', '#dialog_edit_plan select', function() {
			setTimeout(fixModalSelectHeight, 100);
		});

		// เมื่อ modal ปิด
		$('#dialog_edit_plan').on('hidden.bs.modal', function() {
			console.log('Modal plan closed');
		});
	});
</script>

<?php
$dbc->Close();
?>