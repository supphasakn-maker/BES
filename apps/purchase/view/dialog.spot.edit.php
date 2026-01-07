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
$spot = $dbc->GetRecord("bs_purchase_spot", "*", "id=" . $_POST['id']);

$modal = new imodal($dbc, $os->auth);

$modal->setModel("dialog_edit_spot", "Edit Spot");
$modal->initiForm("form_editspot");
$modal->setExtraClass("modal-lg");
$modal->setButton(array(
	array("close", "btn-secondary", "Dismiss"),
	array("action", "btn-outline-dark", "Save Change", "fn.app.purchase.spot.edit()")
));
$modal->SetVariable(array(
	array("id", $spot['id'])
));

$readonly = false;
if (strtotime($spot['created']) < strtotime(date("Y-m-d"))) {
	$readonly = true;
}

if ($readonly && isset($_SESSION['auth']['user_id'])) {
	$user_id = intval($_SESSION['auth']['user_id']);

	$user_data = $dbc->GetRecord("os_users", "*", "id=" . $user_id);

	if ($user_data && isset($user_data['gid'])) {
		$user_gid = intval($user_data['gid']);

		if (in_array($user_gid, array(1, 17, 6))) {
			$readonly = false;
		}
	}
}

$amount_field = array(
	"name" => "amount",
	"caption" => "Amount",
	"placeholder" => "Amount To Purchase",
	"value" => $spot['amount'],
	"flex" => 6
);
if ($readonly) {
	$amount_field["readonly"] = "readonly";
}

$rate_spot_field = array(
	"name" => "rate_spot",
	"caption" => "Spot",
	"placeholder" => "Spot Name",
	"flex" => 4,
	"value" => $spot['rate_spot']
);
if ($readonly) {
	$rate_spot_field["readonly"] = "readonly";
}

$blueprint = array(
	array(
		array(
			"name" => "supplier_id",
			"caption" => "Supplier",
			"type" => "comboboxdb",
			"source" => array(
				"table" => "bs_suppliers",
				"value" => "id",
				"name" => "name",
				"where" => "status = 1"
			),
			"value" => $spot['supplier_id']
		)
	),
	array(
		array(
			"name" => "type",
			"type" => "combobox",
			"caption" => "Type",
			"source" => array(
				array("physical", "physical"),
				array("stock", "stock"),
				array("trade", "trade"),
				array("defer", "defer"),
				array("physical-adjust", "physical-Adjust")
			),
			"value" => $spot['type']
		)
	),
	array(
		$amount_field,
		array(
			"name" => "currency",
			"caption" => "Currency",
			"type" => "comboboxdb",
			"source" => array(
				"table" => "bs_currencies",
				"value" => "code",
				"name" => "code"
			),
			"value" => $spot['currency'],
			"flex" => 2
		)
	),
	array(
		array(
			"name" => "THBValue",
			"caption" => "THBValue",
			"placeholder" => "Total Purchase in THB",
			"value" => $spot['THBValue']
		)
	),
	array(
		$rate_spot_field,
		array(
			"name" => "rate_pmdc",
			"caption" => "Pm/Dc",
			"placeholder" => "premium/discount",
			"flex" => 4,
			"value" => $spot['rate_pmdc']
		)
	),
	array(
		array(
			"type" => "date",
			"name" => "date",
			"caption" => "Date",
			"placeholder" => "Purchase Date",
			"value" => $spot['date']
		)
	),
	array(
		array(
			"type" => "date",
			"name" => "value_date",
			"caption" => "Value Date",
			"placeholder" => "Value Date",
			"value" => $spot['value_date']
		)
	),
	array(
		array(
			"name" => "method",
			"type" => "combobox",
			"caption" => "Method",
			"flex" => 2,
			"source" => array(
				"Call To Buy",
				"Deal ID",
				"Via Message"
			),
			"value" => $spot['method']
		),
		array(
			"name" => "ref",
			"caption" => "Reference",
			"flex" => 6,
			"placeholder" => "Reference",
			"value" => $spot['ref']
		)
	),
	array(
		array(
			"name" => "noted",
			"type" => "combobox",
			"caption" => "NOTE",
			"source" => array(
				array("Normal", "Normal"),
				array("Close-Adjust", "Close-Adjust"),
				array("Open-Adjust", "Open-Adjust")
			),
			"value" => $spot['noted']
		)
	),
	array(
		array(
			"type" => "textarea",
			"name" => "comment",
			"caption" => "Comment",
			"value" => $spot['comment']
		)
	),
	array(
		array(
			"name" => "adj_supplier",
			"caption" => "ADJ Supplier",
			"type" => "comboboxdb",
			"source" => array(
				"table" => "bs_suppliers",
				"value" => "id",
				"name" => "name",
				"where" => "status = 1"
			),
			"value" => $spot['adj_supplier']
		)
	),
	array(
		array(
			"name" => "product_id",
			"caption" => "Product",
			"type" => "comboboxdb",
			"source" => array(
				"table" => "bs_products",
				"value" => "id",
				"name" => "name",
				"where" => "id != 9"
			),
			"value" => $spot['product_id']
		)
	)
);

$modal->SetBlueprint($blueprint);
$modal->EchoInterface();
?>

<style>
	#dialog_edit_spot .modal-body .form-control,
	#dialog_edit_spot .modal-body .form-select,
	#dialog_edit_spot .modal-body select,
	#dialog_edit_spot .modal-body input[type="text"],
	#dialog_edit_spot .modal-body input[type="date"],
	#dialog_edit_spot .modal-body input[type="number"],
	#dialog_edit_spot .modal-body textarea {
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

	#dialog_edit_spot .modal-body textarea {
		height: 80px !important;
		line-height: 1.4 !important;
		resize: vertical !important;
	}

	#dialog_edit_spot .modal-body select {
		appearance: none !important;
		background-image: url("data:image/svg+xml;charset=US-ASCII,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 4 5'><path fill='%23666' d='M2 0L0 2h4zm0 5L0 3h4z'/></svg>") !important;
		background-repeat: no-repeat !important;
		background-position: right 12px center !important;
		background-size: 0.7rem auto !important;
		padding-right: 40px !important;
		cursor: pointer !important;
		font-weight: 500 !important;
	}

	#dialog_edit_spot .modal-body select option {
		padding: 10px 15px !important;
		font-size: 1rem !important;
		line-height: 2 !important;
		background: white !important;
		color: #333 !important;
		height: 45px !important;
		box-sizing: border-box !important;
	}

	#dialog_edit_spot .modal-body select option:hover {
		background: #f8f9fa !important;
	}

	#dialog_edit_spot .modal-body .form-control:focus,
	#dialog_edit_spot .modal-body .form-select:focus,
	#dialog_edit_spot .modal-body select:focus,
	#dialog_edit_spot .modal-body input:focus,
	#dialog_edit_spot .modal-body textarea:focus {
		border-color: #0056b3 !important;
		box-shadow: 0 0 0 3px rgba(0, 86, 179, 0.15) !important;
		outline: none !important;
	}

	#dialog_edit_spot .modal-body label {
		font-weight: 600 !important;
		color: #343a40 !important;
		font-size: 0.9rem !important;
		margin-bottom: 6px !important;
	}

	#dialog_edit_spot .modal-header {
		background: linear-gradient(135deg, #2c3e50 0%, #0056b3 100%) !important;
		color: white !important;
		border-bottom: none !important;
		padding: 1rem 1.5rem !important;
	}

	#dialog_edit_spot .modal-header .modal-title {
		color: white !important;
		font-weight: 600 !important;
		font-size: 1.2rem !important;
	}

	#dialog_edit_spot .modal-header .btn-close,
	#dialog_edit_spot .modal-header .close {
		color: white !important;
		opacity: 0.8 !important;
	}

	/* ปรับ modal body */
	#dialog_edit_spot .modal-body {
		padding: 1.5rem !important;
		background: white !important;
	}

	/* ปรับ modal footer */
	#dialog_edit_spot .modal-footer {
		padding: 1rem 1.5rem !important;
		background: white !important;
		border-top: 1px solid #e9ecef !important;
	}

	#dialog_edit_spot .modal-footer .btn {
		padding: 0.6rem 1.5rem !important;
		font-weight: 600 !important;
		border-radius: 20px !important;
		font-size: 0.95rem !important;
	}

	#dialog_edit_spot .modal-lg {
		max-width: 900px !important;
	}

	@media (max-width: 768px) {

		#dialog_edit_spot .modal-body .form-control,
		#dialog_edit_spot .modal-body .form-select,
		#dialog_edit_spot .modal-body select,
		#dialog_edit_spot .modal-body input,
		#dialog_edit_spot .modal-body textarea {
			font-size: 16px !important;
			height: 50px !important;
			padding: 10px 12px !important;
		}

		#dialog_edit_spot .modal-body textarea {
			height: 70px !important;
		}

		#dialog_edit_spot .modal-body {
			padding: 1rem !important;
		}

		#dialog_edit_spot .modal-header {
			padding: 0.8rem 1rem !important;
		}

		#dialog_edit_spot .modal-footer {
			padding: 0.8rem 1rem !important;
		}
	}
</style>

<script>
	$(document).ready(function() {
		$('#dialog_edit_spot').on('shown.bs.modal', function() {
			fixModalSelectHeight();
		});

		$('#dialog_edit_spot').on('show.bs.modal', function() {
			setTimeout(fixModalSelectHeight, 100);
		});

		function fixModalSelectHeight() {
			$('#dialog_edit_spot .modal-body select, #dialog_edit_spot .modal-body input, #dialog_edit_spot .modal-body .form-control, #dialog_edit_spot .modal-body .form-select, #dialog_edit_spot .modal-body textarea').each(function() {
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

			$('#dialog_edit_spot .modal-body select').css({
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

		if ($('#dialog_edit_spot').length > 0) {
			fixModalSelectHeight();
		}

		setInterval(function() {
			if ($('#dialog_edit_spot').is(':visible')) {
				fixModalSelectHeight();
			}
		}, 1000);

		$(document).on('change', '#dialog_edit_spot select', function() {
			setTimeout(fixModalSelectHeight, 100);
		});

		$('#dialog_edit_spot').on('hidden.bs.modal', function() {
			console.log('Modal closed');
		});
	});
</script>

<?php
$dbc->Close();
?>