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
$order = $dbc->GetRecord("bs_orders", "*", "id=" . $_POST['id']);


$readonly = false;
if (strtotime($order['created']) > strtotime(date("Y-m-d"))) {
	$readonly = true;
}

if ($os->allow("sales", "special")) $readonly = "false";

$modal = new imodal($dbc, $os->auth);

$modal->setModel("dialog_edit_order", "Edit Order");
$modal->initiForm("form_editorder");
$modal->setExtraClass("modal-lg");
$modal->setButton(array(
	array("close", "btn-secondary", "Dismiss", "data-dismiss='modal'"),
	array("action", "btn-outline-dark", "Save Change", "fn.app.sales.order.edit()")
));
$modal->SetVariable(array(
	array("id", $order['id'])
));

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
			),
			"value" => $order['customer_id']
		)
	),
	array(
		array(
			"type" => "datetime",
			"name" => "date",
			"caption" => "Date",
			"flex" => 4,
			"value" => $order['date']
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
			"flex" => 4,
			"value" => $order['sales']
		)
	),
	array(
		array(
			"type" => "number",
			"name" => "amount",
			"caption" => "Amount",
			"placeholder" => "Amount",
			"flex" => 4,
			"readonly" => $readonly,
			"value" => $order['amount']
		),
		array(
			"type" => "number",
			"name" => "price",
			"placeholder" => "Price",
			"flex" => 4,
			"readonly" => $readonly,
			"value" => $order['price']
		),
		array(
			"name" => "currency",
			"type" => "comboboxdb",
			"source" => array(
				"table" => "bs_currencies",
				"value" => "code",
				"name" => "code"
			),
			"flex" => 2,
			"value" => $order['currency']
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
			),
			"value" => $order['vat_type']
		)
	),
	array(
		array(
			"type" => "number",
			"name" => "price_usd",
			"caption" => "Price in USD",
			"placeholder" => "Price in USD",
			"flex" => 4,
			"value" => $order['usd']
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
			"value" => is_null($order['delivery_date']) ? true : false
		),
		array(
			"type" => "date",
			"name" => "delivery_date",
			"flex" => 4,
			"value" => date("Y-m-d"),
			"value" => $order['delivery_date']
		),
		array(
			"type" => "comboboxdatabank",
			"source" => "db_time",
			"name" => "delivery_time",
			"default" => array(
				"value" => "none",
				"name" => "ไม่ระบุ"
			),
			"flex" => 4,
			"value" => $order['delivery_time']
		)
	),
	array(
		array(
			"name" => "contact",
			"caption" => "Contact",
			"value" => $order['info_contact']
		)
	),
	array(
		array(
			"type" => "textarea",
			"name" => "comment",
			"caption" => "Comment",
			"value" => $order['comment']
		)
	),
	array(
		array(
			"type" => "textarea",
			"name" => "shipping_address",
			"caption" => "Shipping",
			"value" => $order['shipping_address']
		)
	),
	array(
		array(
			"type" => "textarea",
			"name" => "billing_address",
			"caption" => "Billing",
			"value" => $order['billing_address']
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
			),
			"value" => $order['info_payment']
		),
		array(
			"name" => "rate_spot",
			"caption" => "Rate",
			"placeholder" => "Spot Rate",
			"flex-label" => 1,
			"flex" => 3,
			"help" => "Spot Rate",
			"value" => $order['rate_spot']
		),
		array(
			"name" => "rate_exchange",
			"placeholder" => "Exchange Rate",
			"flex" => 3,
			"help" => "Exchange Reate",
			"value" => $order['rate_exchange']
		)
	),
	array(
		array(
			"name" => "product_id",
			"caption" => "Product Item",
			"type" => "comboboxdb",
			"source" => array(
				"table" => "bs_products",
				"value" => "id",
				"name" => "name",
				"where" => "id != 9"
			),
			"value" => $order['product_id']
		)
	),
	array(
		array(
			"type" => "combobox",
			"name" => "store",
			"caption" => "Store",
			"source" => array(
				array("BWS", "BWS"),
				array("SILVERNOW", "SILVER NOW"),
				array("LG", "LUCK GEMS"),
				array("EXHIBITION", "EXHIBITION")
			),
			"value" => $order['store']
		)
	),
	array(
		array(
			"type" => "combobox",
			"name" => "orderable_type",
			"caption" => "Delivery Type",
			"source" => array(
				array("delivered_by_company", "จัดส่งโดยรถบริษัท"),
				array("post_office", "จัดส่งโดยไปรษณีย์ไทย"),
				array("receive_at_company", "รับสินค้าที่บริษัท"),
				array("receive_at_luckgems", "รับสินค้าที่ Luck Gems")
			),
			"value" => $order['orderable_type']
		)
	)
);

$modal->SetBlueprint($blueprint);
$modal->EchoInterface();
?>

<style>
	.modal#dialog_edit_order .modal-dialog {
		max-width: 900px;
		pointer-events: auto;
	}

	.modal#dialog_edit_order .modal-content {
		border: none;
		border-radius: 12px;
		box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
	}

	#dialog_edit_order .modal-body .form-control,
	#dialog_edit_order .modal-body .form-select,
	#dialog_edit_order .modal-body select,
	#dialog_edit_order .modal-body input[type="text"],
	#dialog_edit_order .modal-body input[type="date"],
	#dialog_edit_order .modal-body input[type="datetime-local"],
	#dialog_edit_order .modal-body input[type="number"],
	#dialog_edit_order .modal-body textarea {
		border: 2px solid #e9ecef;
		border-radius: 6px;
		padding: 12px 15px;
		font-size: 1rem;
		transition: all 0.3s ease;
		background: white;
		width: 100%;
		height: 55px;
		line-height: 30px;
		vertical-align: middle;
		box-sizing: border-box;
	}

	#dialog_edit_order .modal-body textarea {
		height: 80px;
		line-height: 1.4;
		resize: vertical;
	}

	#dialog_edit_order .modal-body select {
		appearance: none;
		background-image: url("data:image/svg+xml;charset=US-ASCII,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 4 5'><path fill='%23666' d='M2 0L0 2h4zm0 5L0 3h4z'/></svg>");
		background-repeat: no-repeat;
		background-position: right 12px center;
		background-size: 0.7rem auto;
		padding-right: 40px;
		cursor: pointer;
		font-weight: 500;
	}

	#dialog_edit_order .modal-body select option {
		padding: 10px 15px;
		font-size: 1rem;
		line-height: 2;
		background: white;
		color: #333;
		height: 45px;
		box-sizing: border-box;
	}

	#dialog_edit_order .modal-body select option:hover {
		background: #f8f9fa;
	}

	#dialog_edit_order .modal-body .form-control:focus,
	#dialog_edit_order .modal-body .form-select:focus,
	#dialog_edit_order .modal-body select:focus,
	#dialog_edit_order .modal-body input:focus,
	#dialog_edit_order .modal-body textarea:focus {
		border-color: #0056b3;
		box-shadow: 0 0 0 3px rgba(0, 86, 179, 0.15);
		outline: none;
	}

	#dialog_edit_order .modal-body label {
		font-weight: 600;
		color: #343a40;
		font-size: 0.9rem;
		margin-bottom: 6px;
		display: block;
	}

	#dialog_edit_order .modal-header {
		background: linear-gradient(135deg, #2c3e50 0%, #0056b3 100%);
		color: white;
		border-bottom: none;
		padding: 1rem 1.5rem;
		border-radius: 12px 12px 0 0;
	}

	#dialog_edit_order .modal-header .modal-title {
		color: white;
		font-weight: 600;
		font-size: 1.2rem;
	}

	#dialog_edit_order .modal-header .btn-close,
	#dialog_edit_order .modal-header .close {
		color: white;
		opacity: 0.8;
		cursor: pointer;
	}

	#dialog_edit_order .modal-header .btn-close:hover,
	#dialog_edit_order .modal-header .close:hover {
		opacity: 1;
	}

	#dialog_edit_order .modal-body {
		padding: 1.5rem;
		background: white;
		max-height: 70vh;
		overflow-y: auto;
	}

	#dialog_edit_order .modal-footer {
		padding: 1rem 1.5rem;
		background: white;
		border-top: 1px solid #e9ecef;
		border-radius: 0 0 12px 12px;
	}

	#dialog_edit_order .modal-footer .btn {
		padding: 0.6rem 1.5rem;
		font-weight: 600;
		border-radius: 20px;
		font-size: 0.95rem;
	}

	#dialog_edit_order .row {
		margin-left: -5px;
		margin-right: -5px;
	}

	#dialog_edit_order .row>div {
		padding-left: 5px;
		padding-right: 5px;
		margin-bottom: 1rem;
	}

	#dialog_edit_order .form-check {
		padding-top: 0.5rem;
	}

	#dialog_edit_order .form-check-input {
		width: 18px;
		height: 18px;
		margin-top: 0.25rem;
		accent-color: #0056b3;
	}

	#dialog_edit_order .form-check-label {
		font-size: 0.95rem;
		color: #495057;
		margin-left: 0.5rem;
	}

	/* Modal backdrop และ modal state fixes */
	.modal-backdrop {
		position: fixed;
		top: 0;
		left: 0;
		z-index: 1040;
		width: 100vw;
		height: 100vh;
		background-color: #000;
	}

	.modal {
		position: fixed;
		top: 0;
		left: 0;
		z-index: 1050;
		width: 100%;
		height: 100%;
		overflow-x: hidden;
		overflow-y: auto;
		outline: 0;
	}

	/* ป้องกันปัญหา modal ปิดไม่ได้ */
	.modal.show .modal-dialog {
		transform: none;
		pointer-events: auto;
	}

	/* Responsive */
	@media (max-width: 768px) {

		#dialog_edit_order .modal-body .form-control,
		#dialog_edit_order .modal-body .form-select,
		#dialog_edit_order .modal-body select,
		#dialog_edit_order .modal-body input,
		#dialog_edit_order .modal-body textarea {
			font-size: 16px;
			/* ป้องกัน zoom ใน iOS */
			height: 50px;
			padding: 10px 12px;
		}

		#dialog_edit_order .modal-body textarea {
			height: 70px;
		}

		#dialog_edit_order .modal-body {
			padding: 1rem;
		}

		#dialog_edit_order .modal-header {
			padding: 0.8rem 1rem;
		}

		#dialog_edit_order .modal-footer {
			padding: 0.8rem 1rem;
		}

		.modal-dialog {
			margin: 0.25rem;
		}
	}
</style>

<script>
	$(document).ready(function() {
		let modalInitialized = false;

		$('#dialog_edit_order').attr({
			'data-backdrop': 'true',
			'data-keyboard': 'true',
			'tabindex': '-1',
			'role': 'dialog',
			'aria-labelledby': 'dialog_edit_order_title',
			'aria-hidden': 'true'
		});

		function fixModalSelectHeight() {
			try {
				$('#dialog_edit_order .modal-body select, #dialog_edit_order .modal-body input, #dialog_edit_order .modal-body .form-control, #dialog_edit_order .modal-body .form-select, #dialog_edit_order .modal-body textarea').each(function() {
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

				$('#dialog_edit_order .modal-body select').css({
					'padding-right': '40px',
					'background-position': 'right 12px center',
					'appearance': 'none',
					'background-image': 'url("data:image/svg+xml;charset=US-ASCII,<svg xmlns=\'http://www.w3.org/2000/svg\' viewBox=\'0 0 4 5\'><path fill=\'%23666\' d=\'M2 0L0 2h4zm0 5L0 3h4z\'/></svg>")',
					'background-repeat': 'no-repeat',
					'background-size': '0.7rem auto',
					'cursor': 'pointer',
					'font-weight': '500'
				});

				console.log('Modal Blueprint styles applied successfully');
			} catch (error) {
				console.error('Error applying modal styles:', error);
			}
		}

		$('#dialog_edit_order').on('show.bs.modal', function(e) {
			if (modalInitialized) return;
			modalInitialized = true;
			console.log('Edit Order Blueprint Modal is showing');

			setTimeout(function() {
				fixModalSelectHeight();
			}, 100);
		});

		$('#dialog_edit_order').on('shown.bs.modal', function() {
			fixModalSelectHeight();
			$(this).find('input:first, select:first').focus();
			console.log('Edit Order Blueprint Modal shown');
		});

		$('#dialog_edit_order').on('hidden.bs.modal', function() {
			console.log('Edit Order Blueprint Modal hidden');
			modalInitialized = false;

			$('.modal-backdrop').remove();
			$('body').removeClass('modal-open');
			$('body').css('padding-right', '');
		});

		$(document).on('submit', 'form[name="form_editorder"]', function(e) {
			e.preventDefault();
			console.log('Form submission prevented');
			return false;
		});

		$(document).on('change', '#dialog_edit_order select, #dialog_edit_order input', function() {
			setTimeout(fixModalSelectHeight, 100);
		});

		// เพิ่มฟังก์ชันปิด modal แบบ manual
		window.closeEditOrderBlueprintModal = function() {
			$('#dialog_edit_order').modal('hide');
			$('.modal-backdrop').remove();
			$('body').removeClass('modal-open');
			$('body').css('padding-right', '');
		};

		$(document).on('keydown', function(e) {
			if (e.key === 'Escape' && $('#dialog_edit_order').hasClass('show')) {
				window.closeEditOrderBlueprintModal();
			}
		});

		$(document).on('click', '.modal-backdrop', function() {
			if ($('#dialog_edit_order').hasClass('show')) {
				window.closeEditOrderBlueprintModal();
			}
		});

		$(document).on('click', '#dialog_edit_order .modal-header .close, #dialog_edit_order .modal-footer .btn-secondary', function() {
			window.closeEditOrderBlueprintModal();
		});

		$(document).on('click', '#dialog_edit_order .modal-footer .btn', function(e) {
			if ($(this).hasClass('btn-secondary') || $(this).text().toLowerCase().includes('dismiss')) {
				e.preventDefault();
				window.closeEditOrderBlueprintModal();
			}
		});

		if ($('#dialog_edit_order').length > 0) {
			setTimeout(fixModalSelectHeight, 500);
		}
	});

	function debugEditOrderBlueprintModal() {
		console.log('=== Edit Order Blueprint Modal Debug ===');
		console.log('Modal exists:', $('#dialog_edit_order').length > 0);
		console.log('Modal is visible:', $('#dialog_edit_order').is(':visible'));
		console.log('Modal has show class:', $('#dialog_edit_order').hasClass('show'));
		console.log('Backdrop exists:', $('.modal-backdrop').length);
		console.log('Body has modal-open:', $('body').hasClass('modal-open'));
		console.log('Form controls count:', $('#dialog_edit_order .form-control').length);
	}

	window.forceCloseEditOrderBlueprintModal = function() {
		console.log('Force closing Edit Order Blueprint modal...');

		$('#dialog_edit_order').modal('hide');

		setTimeout(function() {
			$('#dialog_edit_order').removeClass('show');
			$('.modal-backdrop').remove();
			$('body').removeClass('modal-open');
			$('body').css('padding-right', '');
			$('#dialog_edit_order').hide();

			console.log('Edit Order Blueprint Modal force closed');
		}, 300);
	};

	console.log('=== Edit Order Blueprint Modal Functions ===');
	console.log('Available functions:');
	console.log('- debugEditOrderBlueprintModal() - Debug modal state');
	console.log('- forceCloseEditOrderBlueprintModal() - Force close modal');
	console.log('- closeEditOrderBlueprintModal() - Normal close modal');
</script>

<?php
$dbc->Close();
?>