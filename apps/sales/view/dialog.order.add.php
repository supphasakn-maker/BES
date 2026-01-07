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

$modal = new imodal($dbc, $os->auth);
$modal->setModel("dialog_add_order", "Add Order");
$modal->initiForm("form_addorder");
$modal->setExtraClass("modal-lg");
$modal->setButton(array(
	array("close", "btn-secondary", "Dismiss"),
	array("action", "btn-primary", "Save Change", "fn.app.sales.order.add()")
));


$rate_exchange = $os->load_variable("rate_exchange");
$rate_spot = $os->load_variable("rate_spot");
$rate_pmdc = $os->load_variable("rate_pmdc");

$blueprint = array(
	array(
		array(
			"name" => "customer_id",
			"caption" => "Customer",
			"type" => "comboboxdb",
			"source" => array(
				"table" => "bs_customers",
				"value" => "id",
				"name" => "name"
			)
		)
	),
	array(
		array(
			"type" => "date",
			"name" => "date",
			"caption" => "Date",
			"value" => date("Y-m-d"),
			"flex" => 4
		),
		array(
			"name" => "sales",
			"caption" => "Sales",
			"type" => "comboboxdb",
			"source" => array(
				"table" => "bs_employees",
				"value" => "id",
				"name" => "fullname"
			),
			"flex" => 4
		)
	),
	array(
		array(
			"type" => "number",
			"name" => "amount",
			"caption" => "Amount",
			"placeholder" => "Amount",
			"flex" => 4
		),
		array(
			"type" => "number",
			"name" => "price",
			"placeholder" => "Price",
			"flex" => 4
		),
		array(
			"name" => "currency",
			"type" => "comboboxdb",
			"source" => array(
				"table" => "bs_currencies",
				"value" => "code",
				"name" => "code"
			),
			"flex" => 2
		)
	),
	array(
		array(
			"type" => "combobox",
			"name" => "vat_type",
			"caption" => "VAT",
			"source" => array(
				array(0, "No VAT"),
				array(2, "7% VAT")
			)
		)
	),
	array(
		array(
			"type" => "number",
			"name" => "price_usd",
			"caption" => "Price in USD",
			"placeholder" => "Price in USD",
		)
	),
	array(
		array(
			"caption" => "Delivery",
			"type" => "checkbox",
			"name" => "delivery_lock",
			"text" => "Lock",
			"class" => "pt-2",
			"flex" => 2,
		),
		array(
			"type" => "date",
			"name" => "delivery_date",
			"flex" => 4,
			"value" => date("Y-m-d")
		),
		array(
			"type" => "comboboxdatabank",
			"source" => "db_time",
			"name" => "delivery_time",
			"default" => array(
				"value" => "none",
				"name" => "ไม่ระบุ"
			),
			"flex" => 4
		)
	),
	array(
		array(
			"name" => "contact",
			"caption" => "Contact"
		)
	),
	array(
		array(
			"type" => "textarea",
			"name" => "comment",
			"caption" => "Comment"
		)
	),
	array(
		array(
			"type" => "textarea",
			"name" => "shipping_address",
			"caption" => "Shipping"
		)
	),
	array(
		array(
			"type" => "textarea",
			"name" => "billing_address",
			"caption" => "Billing"
		)
	),
	array(
		array(
			"type" => "comboboxdatabank",
			"source" => "db_payment",
			"name" => "payment",
			"caption" => "การจ่ายเงิน",
			"flex" => 3,
			"default" => array(
				"value" => "none",
				"name" => "ไม่ระบุ"
			)
		),
		array(
			"name" => "rate_spot",
			"caption" => "Rate",
			"placeholder" => "Spot Rate",
			"flex-label" => 1,
			"flex" => 3,
			"value" => $rate_spot,
			"help" => "Spot Rate"
		),
		array(
			"name" => "rate_exchange",
			"placeholder" => "Exchange Rate",
			"flex" => 3,
			"value" => $rate_exchange,
			"help" => "Exchange Reate"
		)
	),
	array(
		array(
			"type" => "combobox",
			"name" => "store",
			"caption" => "Store",
			"source" => array(
				array("BWS", "BWS"),
				array("LG", "LUCK GEMS")
			),
		)
	)
);

$modal->SetBlueprint($blueprint);
$modal->EchoInterface();
?>
<style>
	/* แก้ไข select dropdown ใน modal */
	#dialog_add_order .modal-body .form-control,
	#dialog_add_order .modal-body .form-select,
	#dialog_add_order .modal-body select,
	#dialog_add_order .modal-body input[type="text"],
	#dialog_add_order .modal-body input[type="date"],
	#dialog_add_order .modal-body input[type="number"],
	#dialog_add_order .modal-body textarea {
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
	#dialog_add_order .modal-body textarea {
		height: 80px !important;
		line-height: 1.4 !important;
		resize: vertical !important;
	}

	/* แก้ไข select โดยเฉพาะ */
	#dialog_add_order .modal-body select {
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
	#dialog_add_order .modal-body select option {
		padding: 10px 15px !important;
		font-size: 1rem !important;
		line-height: 2 !important;
		background: white !important;
		color: #333 !important;
		height: 45px !important;
		box-sizing: border-box !important;
	}

	#dialog_add_order .modal-body select option:hover {
		background: #f8f9fa !important;
	}

	/* Focus states */
	#dialog_add_order .modal-body .form-control:focus,
	#dialog_add_order .modal-body .form-select:focus,
	#dialog_add_order .modal-body select:focus,
	#dialog_add_order .modal-body input:focus,
	#dialog_add_order .modal-body textarea:focus {
		border-color: #0056b3 !important;
		box-shadow: 0 0 0 3px rgba(0, 86, 179, 0.15) !important;
		outline: none !important;
	}

	/* แก้ไข label */
	#dialog_add_order .modal-body label {
		font-weight: 600 !important;
		color: #343a40 !important;
		font-size: 0.9rem !important;
		margin-bottom: 6px !important;
	}

	/* ปรับ modal header */
	#dialog_add_order .modal-header {
		background: linear-gradient(135deg, #2c3e50 0%, #0056b3 100%) !important;
		color: white !important;
		border-bottom: none !important;
		padding: 1rem 1.5rem !important;
	}

	#dialog_add_order .modal-header .modal-title {
		color: white !important;
		font-weight: 600 !important;
		font-size: 1.2rem !important;
	}

	#dialog_add_order .modal-header .btn-close,
	#dialog_add_order .modal-header .close {
		color: white !important;
		opacity: 0.8 !important;
	}

	/* ปรับ modal body */
	#dialog_add_order .modal-body {
		padding: 1.5rem !important;
		background: white !important;
	}

	/* ปรับ modal footer */
	#dialog_add_order .modal-footer {
		padding: 1rem 1.5rem !important;
		background: white !important;
		border-top: 1px solid #e9ecef !important;
	}

	#dialog_add_order .modal-footer .btn {
		padding: 0.6rem 1.5rem !important;
		font-weight: 600 !important;
		border-radius: 20px !important;
		font-size: 0.95rem !important;
	}

	/* ปรับขนาด modal dialog */
	#dialog_add_order .modal-lg {
		max-width: 900px !important;
	}

	/* Responsive */
	@media (max-width: 768px) {

		#dialog_add_order .modal-body .form-control,
		#dialog_add_order .modal-body .form-select,
		#dialog_add_order .modal-body select,
		#dialog_add_order .modal-body input,
		#dialog_add_order .modal-body textarea {
			font-size: 16px !important;
			/* ป้องกัน zoom ใน iOS */
			height: 50px !important;
			padding: 10px 12px !important;
		}

		#dialog_add_order .modal-body textarea {
			height: 70px !important;
		}

		#dialog_add_order .modal-body {
			padding: 1rem !important;
		}

		#dialog_add_order .modal-header {
			padding: 0.8rem 1rem !important;
		}

		#dialog_add_order .modal-footer {
			padding: 0.8rem 1rem !important;
		}
	}
</style>

<script>
	$(document).ready(function() {
		// รอให้ modal แสดงขึ้นมาก่อน
		$('#dialog_add_order').on('shown.bs.modal', function() {
			fixModalSelectHeight();
		});

		// รอให้ modal เริ่มแสดง
		$('#dialog_add_order').on('show.bs.modal', function() {
			setTimeout(fixModalSelectHeight, 100);
		});

		// Force style แก้ปัญหา select dropdown ขนาดเล็กใน modal
		function fixModalSelectHeight() {
			$('#dialog_add_order .modal-body select, #dialog_add_order .modal-body input, #dialog_add_order .modal-body .form-control, #dialog_add_order .modal-body .form-select, #dialog_add_order .modal-body textarea').each(function() {
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
			$('#dialog_add_order .modal-body select').css({
				'padding-right': '40px',
				'background-position': 'right 12px center',
				'appearance': 'none',
				'background-image': 'url("data:image/svg+xml;charset=US-ASCII,<svg xmlns=\'http://www.w3.org/2000/svg\' viewBox=\'0 0 4 5\'><path fill=\'%23666\' d=\'M2 0L0 2h4zm0 5L0 3h4z\'/></svg>")',
				'background-repeat': 'no-repeat',
				'background-size': '0.7rem auto',
				'cursor': 'pointer',
				'font-weight': '500'
			});

			console.log('Fixed modal select heights');
		}

		// รันทันทีถ้า modal มีอยู่แล้ว
		if ($('#dialog_add_order').length > 0) {
			fixModalSelectHeight();
		}

		// รันซ้ำทุก 1 วินาทีเพื่อให้แน่ใจ (สำหรับ modal)
		setInterval(function() {
			if ($('#dialog_add_order').is(':visible')) {
				fixModalSelectHeight();
			}
		}, 1000);

		// เมื่อมีการเปลี่ยนแปลงใน form
		$(document).on('change', '#dialog_add_order select', function() {
			setTimeout(fixModalSelectHeight, 100);
		});

		// เมื่อ modal ปิด
		$('#dialog_add_order').on('hidden.bs.modal', function() {
			console.log('Modal closed');
		});
	});
</script>

<?php
$dbc->Close();
?>