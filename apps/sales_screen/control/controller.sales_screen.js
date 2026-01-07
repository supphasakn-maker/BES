$("form[name=customer] select[name=customer_select]").change(function () {
	console.log('=== Customer Select Changed ===');
	let customer_id = $(this).val();
	console.log('Selected customer_id:', customer_id);


	$("form[name=form_addquick_order] select[name=customer_id]").val(customer_id);


	if ($("form[name=form_addquick_order] select[name=customer_id]").hasClass('select2')) {
		$("form[name=form_addquick_order] select[name=customer_id]").trigger('change');
	}


	if (customer_id != "") {
		$.post('apps/sales_screen/xhr/action-load-customer.php', { id: customer_id }, function (customer) {


			for (i in customer) {
				$("form[name=customer] input[name=" + i + "]").val(customer[i]);
				$("form[name=customer] textarea[name=" + i + "]").val(customer[i]);
				$("form[name=customer] select[name=" + i + "]").val(customer[i]);
			};
			$("form[name=order] input[name=contact]").val(customer['contact']);
			$("form[name=order] select[name=vat_type]").val(customer['default_vat_type']);

			if (customer['new_cus'] == "0") {
				$("form[name=order] input[name=new_cus]").val(customer['new_cus']);
			} else if (customer['new_cus'] == "1") {
				$("form[name=order] input[name=new_cus]").val(customer['new_cus']);
			}

			if (customer['default_vat_type'] != null) {
				$("form[name=order] select[name=vat_type]").val(customer['default_vat_type']);
			} else {
				$("form[name=order] select[name=vat_type]").val(0);
			}

			if (customer['remark'] != "") {
				$("#info_memo").html('<div class="alert alert-danger" role="alert"><strong>คำเตือน</strong> ' + customer['remark'] + '</div>');
			} else {
				$("#info_memo").html("");
			}


			if (customer['product_id']) {
				$("form[name=form_addquick_order] select[name=product_id]").val(customer['product_id']);
			}
			if (customer['default_vat_type'] != null) {
				$("form[name=form_addquick_order] select[name=vat_type]").val(customer['default_vat_type']);
			} else {
				$("form[name=form_addquick_order] select[name=vat_type]").val(0);
			}



			setTimeout(function () {
				try {

					if ($.fn.DataTable.isDataTable('#tblDailyTable')) {
						$("#tblDailyTable").DataTable().draw();
						console.log('tblDailyTable refreshed');
					}

					if ($.fn.DataTable.isDataTable('#tblDailyRemain')) {
						$("#tblDailyRemain").DataTable().draw();
						console.log('tblDailyRemain refreshed');
					}


					if ($.fn.DataTable.isDataTable('#tblQuickOrder')) {
						console.log('Reloading tblQuickOrder (server-side)...');
						$('#tblQuickOrder').DataTable().ajax.reload(function () {
							console.log('tblQuickOrder reloaded successfully');
						});
					} else {
						console.warn('tblQuickOrder DataTable not initialized');
					}

				} catch (error) {
					console.error('Error refreshing DataTables:', error);
				}
			}, 100);

		}, "json").fail(function (xhr, status, error) {
			console.error('AJAX Error loading customer:', status, error);
		});
	} else {


		$("form[name=customer] input").val("-");


		$("form[name=form_addquick_order] select[name=customer_id]").val("");
		if ($("form[name=form_addquick_order] select[name=customer_id]").hasClass('select2')) {
			$("form[name=form_addquick_order] select[name=customer_id]").trigger('change');
		}


		$("#info_memo").html("");


		setTimeout(function () {
			try {
				if ($.fn.DataTable.isDataTable('#tblDailyTable')) {
					$("#tblDailyTable").DataTable().draw();
				}
				if ($.fn.DataTable.isDataTable('#tblDailyRemain')) {
					$("#tblDailyRemain").DataTable().draw();
				}
				if ($.fn.DataTable.isDataTable('#tblQuickOrder')) {
					$('#tblQuickOrder').DataTable().ajax.reload();
					console.log('tblQuickOrder reloaded (no customer)');
				}
			} catch (error) {
				console.error('Error refreshing DataTables (no customer):', error);
			}
		}, 100);
	}
});



$("form[name=order] select[name=customer_id]").change(function () {

	$("#tblDailyTable").DataTable().draw();
	let customer_id = $(this).val();
	if (customer_id != "") {
		$.post('apps/sales_screen/xhr/action-load-customer.php', { id: customer_id }, function (customer) {

			$("form[name=order] input[name=contact]").val(customer['contact']);
			$("form[name=order] select[name=vat_type]").val(customer['default_vat_type']);

			if (customer['default_vat_type'] != null) {
				$("form[name=order] select[name=vat_type]").val(customer['default_vat_type']);

			} else {
				$("form[name=order] select[name=vat_type]").val(0);
			}

			if (customer['remark'] != "") {
				$("#info_memo").html('<div class="alert alert-danger" role="alert"><strong>ตำเตือน</strong> ' + customer['remark'] + '</div>');
			} else {
				$("#info_memo").html("");
			}

		}, "json");
	} else {
		$("form[name=customer] input").val("-");
	}
});
$("form[name=form_addquick_order] select[name=customer_id]").change(function () {
	let customer_id = $(this).val();

	if (customer_id != "") {
		$.post('apps/sales_screen/xhr/action-load-customer.php', { id: customer_id }, function (customer) {


			if (customer['product_id']) {
				$("form[name=form_addquick_order] select[name=product_id]").val(customer['product_id']);
			}
			if (customer['default_vat_type'] != null) {
				$("form[name=form_addquick_order] select[name=vat_type]").val(customer['default_vat_type']);
			} else {
				$("form[name=form_addquick_order] select[name=vat_type]").val(0);
			}

		}, "json").fail(function (xhr, status, error) {
			console.error('Quick order AJAX Error:', status, error);
		});
	}
});

$("input[name=delivery_lock]").change(function () {
	$("input[name=delivery_date]").prop('readOnly', $(this).prop('checked'));
});



fn.app.sales_screen.reset = function () {
	$('form[name=quick_order]')[0].reset();
	$('form[name=order]')[0].reset();
	$("#info_memo").html("");
	$('.select2').val("").trigger('change');
	return false;
};


fn.app.sales_screen.recalcuate = function () {
	var spot = $('form[name=rate] input[name=rate_spot]').val();
	var exchange = $('form[name=rate] input[name=rate_exchange]').val();
	var discount = $('form[name=rate] input[name=discount]').val();
	var margin = $('form[name=rate] input[name=margin]').val();
	var discount_recycle = $('form[name=rate] input[name=discount_recycle]').val();
	var recycle1 = $('form[name=rate] input[name=rate_recycle1]').val();
	var recycle2 = $('form[name=rate] input[name=rate_recycle2]').val();

	var recycle_plus1 = parseFloat(recycle1);
	var recycle_plus2 = parseFloat(recycle2);
	var total = ((parseFloat(spot) + parseFloat(discount)) * 32.1507) * parseFloat(exchange);
	var total_recycle1 = ((parseFloat(spot) + parseFloat(discount_recycle)) * 32.1507) * parseFloat(exchange) + (recycle_plus1);
	var total_recycle2 = ((parseFloat(spot) + parseFloat(discount_recycle)) * 32.1507) * parseFloat(exchange) + (recycle_plus2);
	var price_extra = total + parseFloat(margin);

	$('form[name=rate] input[name=price]').val(price_extra.toFixed(2));

	var price1 = total;
	var price2 = total + 20;
	var price3 = total + 40;
	var price4 = total_recycle1;
	var price5 = total_recycle2;

	$('form[name=rate] input[name=price1]').val(fn.ui.numberic.format(price1, 2));
	$('form[name=rate] input[name=price2]').val(fn.ui.numberic.format(price2, 2));
	$('form[name=rate] input[name=price3]').val(fn.ui.numberic.format(price3, 2));
	$('form[name=rate] input[name=price4]').val(fn.ui.numberic.format(price4, 2));
	$('form[name=rate] input[name=price5]').val(fn.ui.numberic.format(price5, 2));

};

fn.app.sales_screen.recalcuate();

$("form[name=form_addquick_order] select[name=customer_id]").change(function () {
	let customer_id = $(this).val();
	if (customer_id != "") {

		$.post('apps/sales_screen/xhr/action-load-customer.php', { id: customer_id }, function (customer) {
			$("form[name=form_addquick_order] select[name=product_id]").val(customer['product_id']);
			if (customer['default_vat_type'] != null) {
				$("form[name=form_addquick_order] select[name=vat_type]").val(customer['default_vat_type']);
			} else {
				$("form[name=form_addquick_order] select[name=vat_type]").val(0);
			}

		}, "json");
	}
});
