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
$delivery = $dbc->GetRecord("bs_deliveries_bwd", "*", "id=" . $_POST['id']);
$order = $dbc->GetRecord("bs_orders_bwd", "*", "delivery_id=" . $delivery['id']);
if ($delivery['payment_note'] == null) {
	$payment_note = array(
		"bank" => $delivery['default_bank'],
		"payment" => $delivery['default_payment'],
		"remark" => ""
	);
} else {
	$payment_note = json_decode($delivery['payment_note'], true);
}

$modal = new imodal($dbc, $os->auth);

$modal->setModel("dialog_payment_delivery", "Payment");
$modal->initiForm("form_paymentdelivery");
$modal->setExtraClass("modal-lg");
$modal->setButton(array(
	array("close", "btn-secondary", "Dismiss"),
	array("action", "btn-outline-dark", "Save Change", "fn.app.sales_bwd.delivery.payment()")
));
$modal->SetVariable(array(
	array("id", $delivery['id'])
));

$blueprint = array(
	array(
		array(
			"type" => "comboboxdatabank",
			"source" => "db_bank_bwd",
			"name" => "bank",
			"caption" => "ธนาคาร",
			"default" => array(
				"value" => "none",
				"name" => "ไม่ระบุ"
			),
			"flex" => 4,
			"flex-label" => 1,
			"placeholder" => "Bank Detail",
			"value" => $payment_note['bank']
		),
		array(
			"type" => "comboboxdatabank",
			"source" => "db_payment_bwd",
			"name" => "payment",
			"caption" => "เงือนไขการชำระเงิน",
			"default" => array(
				"value" => "none",
				"name" => "ไม่ระบุ"
			),
			"flex-label" => 3,
			"flex" => 4,
			"value" => $payment_note['payment']
		)
	),
	array(
		array(
			"type" => "textarea",
			"name" => "remark",
			"caption" => "ข้อมูลเพิ่มเติม",
			"value" => $payment_note['remark']
		)

	)
);

$modal->SetBlueprint($blueprint);
$modal->EchoInterface();
?>
<style>
	/* แก้ไข select dropdown ใน modal */
	#dialog_payment_delivery .modal-body .form-control,
	#dialog_payment_delivery .modal-body .form-select,
	#dialog_payment_delivery .modal-body select,
	#dialog_payment_delivery .modal-body input[type="text"],
	#dialog_payment_delivery .modal-body input[type="date"],
	#dialog_payment_delivery .modal-body input[type="number"],
	#dialog_payment_delivery .modal-body textarea {
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
	#dialog_payment_delivery .modal-body textarea {
		height: 80px !important;
		line-height: 1.4 !important;
		resize: vertical !important;
	}

	/* แก้ไข select โดยเฉพาะ */
	#dialog_payment_delivery .modal-body select {
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
	#dialog_payment_delivery .modal-body select option {
		padding: 10px 15px !important;
		font-size: 1rem !important;
		line-height: 2 !important;
		background: white !important;
		color: #333 !important;
		height: 45px !important;
		box-sizing: border-box !important;
	}

	#dialog_payment_delivery .modal-body select option:hover {
		background: #f8f9fa !important;
	}

	/* Focus states */
	#dialog_payment_delivery .modal-body .form-control:focus,
	#dialog_payment_delivery .modal-body .form-select:focus,
	#dialog_payment_delivery .modal-body select:focus,
	#dialog_payment_delivery .modal-body input:focus,
	#dialog_payment_delivery .modal-body textarea:focus {
		border-color: #0056b3 !important;
		box-shadow: 0 0 0 3px rgba(0, 86, 179, 0.15) !important;
		outline: none !important;
	}

	/* แก้ไข label */
	#dialog_payment_delivery .modal-body label {
		font-weight: 600 !important;
		color: #343a40 !important;
		font-size: 0.9rem !important;
		margin-bottom: 6px !important;
	}

	/* ปรับ modal header */
	#dialog_payment_delivery .modal-header {
		background: linear-gradient(135deg, #2c3e50 0%, #0056b3 100%) !important;
		color: white !important;
		border-bottom: none !important;
		padding: 1rem 1.5rem !important;
	}

	#dialog_payment_delivery .modal-header .modal-title {
		color: white !important;
		font-weight: 600 !important;
		font-size: 1.2rem !important;
	}

	#dialog_payment_delivery .modal-header .btn-close,
	#dialog_payment_delivery .modal-header .close {
		color: white !important;
		opacity: 0.8 !important;
	}

	/* ปรับ modal body */
	#dialog_payment_delivery .modal-body {
		padding: 1.5rem !important;
		background: white !important;
	}

	/* ปรับ modal footer */
	#dialog_payment_delivery .modal-footer {
		padding: 1rem 1.5rem !important;
		background: white !important;
		border-top: 1px solid #e9ecef !important;
	}

	#dialog_payment_delivery .modal-footer .btn {
		padding: 0.6rem 1.5rem !important;
		font-weight: 600 !important;
		border-radius: 20px !important;
		font-size: 0.95rem !important;
	}

	/* ปรับขนาด modal dialog */
	#dialog_payment_delivery .modal-lg {
		max-width: 900px !important;
	}

	/* Responsive */
	@media (max-width: 768px) {

		#dialog_payment_delivery .modal-body .form-control,
		#dialog_payment_delivery .modal-body .form-select,
		#dialog_payment_delivery .modal-body select,
		#dialog_payment_delivery .modal-body input,
		#dialog_payment_delivery .modal-body textarea {
			font-size: 16px !important;
			/* ป้องกัน zoom ใน iOS */
			height: 50px !important;
			padding: 10px 12px !important;
		}

		#dialog_payment_delivery .modal-body textarea {
			height: 70px !important;
		}

		#dialog_payment_delivery .modal-body {
			padding: 1rem !important;
		}

		#dialog_payment_delivery .modal-header {
			padding: 0.8rem 1rem !important;
		}

		#dialog_payment_delivery .modal-footer {
			padding: 0.8rem 1rem !important;
		}
	}
</style>

<script>
	$(document).ready(function() {
		// รอให้ modal แสดงขึ้นมาก่อน
		$('#dialog_payment_delivery').on('shown.bs.modal', function() {
			fixModalSelectHeight();
		});

		// รอให้ modal เริ่มแสดง
		$('#dialog_payment_delivery').on('show.bs.modal', function() {
			setTimeout(fixModalSelectHeight, 100);
		});

		// Force style แก้ปัญหา select dropdown ขนาดเล็กใน modal
		function fixModalSelectHeight() {
			$('#dialog_payment_delivery .modal-body select, #dialog_payment_delivery .modal-body input, #dialog_payment_delivery .modal-body .form-control, #dialog_payment_delivery .modal-body .form-select, #dialog_payment_delivery .modal-body textarea').each(function() {
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
			$('#dialog_payment_delivery .modal-body select').css({
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
		if ($('#dialog_payment_delivery').length > 0) {
			fixModalSelectHeight();
		}

		// รันซ้ำทุก 1 วินาทีเพื่อให้แน่ใจ (สำหรับ modal)
		setInterval(function() {
			if ($('#dialog_payment_delivery').is(':visible')) {
				fixModalSelectHeight();
			}
		}, 1000);

		// เมื่อมีการเปลี่ยนแปลงใน form
		$(document).on('change', '#dialog_payment_delivery select', function() {
			setTimeout(fixModalSelectHeight, 100);
		});

		// เมื่อ modal ปิด
		$('#dialog_payment_delivery').on('hidden.bs.modal', function() {
			console.log('Modal closed');
		});
	});
</script>

<?php
$dbc->Close();
?>