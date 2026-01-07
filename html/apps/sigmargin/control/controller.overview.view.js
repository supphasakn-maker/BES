
fn.app.sigmargin.overview.calculate = function () {
	var s = '';

	var value_usd = parseFloat($("input[name=value_usd]").val());
	var value_xag = parseFloat($("input[name=value_xag]").val());
	var value_initial = parseFloat($("input[name=value_initial]").val()) / 100;
	var value_cut_loss = parseFloat($("input[name=value_cut_loss]").val()) / 100;
	var value_variation = parseFloat($("input[name=value_variation]").val()) / 100;

	var numberic = function (number, decimal_digit) {
		return number.toLocaleString('en-US', { minimumFractionDigits: decimal_digit, maximumFractionDigits: decimal_digit });
	}

	let data = [
		['USD', value_usd, 1],
		['', 0, 1],
		['USD/ACL', 0, 1],
		['SIGHT L/C', 730000.00, 1],
		['NAG', 0, 49.0000],
		['XAG', value_xag, 49.0000]
	];

	let total = [0, 0, 0, 0];

	for (i in data) {
		let e_principle = data[i][1];
		let exchange_rate = 1;

		// Exchange Rate
		exchange_rate = data[i][2];
		let usd_eqv = e_principle * exchange_rate;
		let e_initial = e_principle < 0 ? (usd_eqv + usd_eqv * value_initial) : usd_eqv;

		let e_variation = e_principle < 0 ? (usd_eqv + usd_eqv * value_variation) : usd_eqv;
		let e_cut_loss = e_principle < 0 ? (usd_eqv + usd_eqv * value_cut_loss) : usd_eqv;

		let decimal_digit = 2;
		if (data[i][0] == 'XAG') { decimal_digit = 4 }
		s += '<tr>';
		// ชื่อหน้า
		s += '<td class="text-center">' + data[i][0] + '</td>';

		// Balance
		s += '<td class="text-right">';
		s += numberic(e_principle, decimal_digit);
		s += '</td>';

		if (data[i][0] == 'NAG') {
			s += '<td class="text-center"><input type="text" class="form-control form-control-sm text-center" value="49.0000"></td>';
		} else if (data[i][0] == 'XAG') {
			exchange_rate = data[i][2];
			s += '<td class="text-center"><input type="text" class="form-control form-control-sm text-center" value="49.0000"></td>';
		} else {
			s += '<td class="text-center">1.0000</td>';
		}

		// In USD
		s += '<td class="text-right">';
		s += usd_eqv.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
		s += '</td>';

		// In KG
		s += '<td class="text-center">';
		if (data[i][0] == 'XAG' || data[i][0] == 'NAG') {
			let xag_oz = e_principle / 32.1507;
			s += xag_oz.toLocaleString('en-US', { minimumFractionDigits: 3, maximumFractionDigits: 3 });
			s += ' kg';
		}
		s += '</td>';


		s += '<td class="text-right">' + numberic(e_initial, 2) + '</td>';

		if (data[i][0] == '' || data[i][0] == 'USD/ACL' || data[i][0] == 'SIGHT L/C') {
			if (usd_eqv < 0) {
				s += '<td class="text-right">' + numberic(Math.abs(usd_eqv), 2) + '</td>';
			} else {
				s += '<td class="text-right">0.00</td>';
			}
		} else {
			s += '<td class="text-center"></td>';
		}


		s += numberic(e_principle, decimal_digit);
		s += '<td class="text-right">' + numberic(e_variation, 2) + '</td>';
		s += '<td class="text-center"></td>';
		s += '<td class="text-right">' + numberic(e_cut_loss, 2) + '</td>';
		s += '</tr>';

		total[0] += usd_eqv;
		total[1] += e_initial;
		total[2] += e_variation;
		total[3] += e_cut_loss;
	}

	s += '<tr class="text-primary">';
	s += '<td class="text-center">Net Avail.</td>';
	s += '<td class="text-center"></td>';
	s += '<td class="text-center"></td>';
	s += '<td class="text-right">' + numberic(total[0], 2) + '</td>';
	s += '<td class="text-center"></td>';
	s += '<td class="text-right">' + numberic(total[1], 2) + '</td>';
	s += '<td class="text-center"></td>';
	s += '<td class="text-right">' + numberic(total[2], 2) + '</td>';
	s += '<td class="text-center"></td>';
	s += '<td class="text-right">' + numberic(total[3], 2) + '</td>';
	s += '</tr>';

	s += '<tr class="text-primary">';
	s += '<td class="text-center">Rate XAU</td>';
	s += '<td class="text-center"></td>';
	s += '<td class="text-center"></td>';
	s += '<td class="text-center"></td>';
	s += '<td class="text-center"></td>';
	s += '<td class="text-center"></td>';
	s += '<td class="text-center"></td>';
	s += '<td class="text-center"></td>';
	s += '</tr>';


	var aXag = [
		value_xag < 0 ? Math.floor(total[0] / -value_xag * 100) / 100 : Math.ceil(-total[0] / value_xag * 100) / 100,
		value_xag < 0 ? Math.floor(total[1] / -value_xag * 100) / 100 : Math.ceil(-total[1] / value_xag * 100) / 100,
		value_xag < 0 ? Math.floor(total[2] / -value_xag * 100) / 100 : Math.ceil(-total[2] / value_xag * 100) / 100,
		value_xag < 0 ? Math.floor(total[3] / -value_xag * 100) / 100 : Math.ceil(-total[3] / value_xag * 100) / 100
	];

	s += '<tr class="text-primary">';
	s += '<td class="text-center">Rate XAG</td>';
	s += '<td class="text-center"></td>';
	s += '<td class="text-center"></td>';
	s += '<td class="text-right">' + numberic(aXag[0], 2) + '</td>';
	s += '<td class="text-center"></td>';
	s += '<td class="text-right">' + numberic(aXag[1], 2) + '</td>';
	s += '<td class="text-center"></td>';
	s += '<td class="text-right">' + numberic(aXag[2], 2) + '</td>';
	s += '<td class="text-center"></td>';
	s += '<td class="text-right">' + numberic(aXag[3], 2) + '</td>';
	s += '</tr>';

	$("#tblOverview tbody").html(s);


};

$('input[name=date]').change(function () {
	var date = $(this).val();
	console.log(date);
	$.post("apps/sigmargin/xhr/overview/action-load-table.php",
		{ date: date },
		function (html) {
			$("#output").html(html);
			fn.app.sigmargin.overview.calculate();
		}, "html");
});

$('input[name=date]').change();
