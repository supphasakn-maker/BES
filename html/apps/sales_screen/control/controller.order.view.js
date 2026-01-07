$('#tblDailyTable').DataTable({
	responsive: true,
	"bStateSave": true,
	"autoWidth": true,
	"processing": true,
	"serverSide": true,
	"ajax": {
		data: function (d) {

			var customer_id = $("form[name=customer] select[name=customer_select]").val();
			var cus_id = $("form[name=form_addquick_order] select[name=customer_id]").val();
			if (customer_id != "") {
				d.customer_id = customer_id
			} else {
				d.customer_id = "NULL"
			}

			d.date_order_from = $("form[name=filter] input[name=from]").val();
			d.date_order_to = $("form[name=filter] input[name=to]").val();
		},
		url: "apps/sales/store/store-order.php",
	},
	"aoColumns": [
		{ "bSortable": true, "data": "code", "sClass": "hidden-xs text-center", "sWidth": "50px" },
		{ "bSortable": true, "data": "created", class: "text-center", "sWidth": "60px" },
		{ "bSortable": true, "data": "delivery_date", "sClass": "hidden-xs text-center", "sWidth": "60px" },
		{ "bSortable": true, "data": "amount", "sClass": "hidden-xs text-right", "sWidth": "50px" },
		{ "bSortable": true, "data": "price", "sClass": "text-right", "sWidth": "50px" },
		{ "bSortable": true, "data": "vat", "sClass": "text-right", "sWidth": "50px" },
		{ "bSortable": true, "data": "net", "sClass": "text-right", "sWidth": "60px" },
		{ "bSortable": true, "data": "usd", "sClass": "text-right", "sWidth": "50px" },
		{ "bSortable": false, "data": "sales", "sClass": "text-center", "sWidth": "50px" }
	], "order": [[1, "desc"]],
	"createdRow": function (row, data, index) {

		$('td', row).eq(1).html(moment(data.created).format("DD/MM/YYYY"));
		if (data.delivery_date != null && data.delivery_date != "0000-00-00") {
			$('td', row).eq(2).html(moment(data.delivery_date).format("DD/MM/YYYY"));
		} else {
			$('td', row).eq(2).html("-");
		}



	},
	"footerCallback": function (row, data, start, end, display) {
		var api = this.api();
		var tAmount = 0, tValue = 0;

		for (i in data) {
			tAmount += parseFloat((data[i].amount + '').replace(/,/g, '')) || 0;
			tValue += parseFloat((data[i].net + '').replace(/,/g, '')) || 0;
		}

		$("#tblDailyTable [xname=tAmount]").html(fn.ui.numberic.format(tAmount));
		$("#tblDailyTable [xname=tValue]").html(fn.ui.numberic.format(tValue));
	}
});

$('#tblDailyRemain').DataTable({
	responsive: true,
	"bStateSave": true,
	"autoWidth": true,
	"processing": true,
	"serverSide": true,
	"ajax": {
		data: function (d) {
			var customer_id = $("form[name=customer] select[name=customer_select]").val();
			var cus_id = $("form[name=form_addquick_order] select[name=customer_id]").val();
			if (customer_id != "") {
				d.customer_id = customer_id
			} else {
				d.customer_id = "NULL"
			}
		},
		url: "apps/sales_screen/store/store-remain.php",
	},
	"aoColumns": [
		{ "bSortable": true, "data": "code", "sClass": "hidden-xs text-center", "sWidth": "20px" },
		{ "bSortable": true, "data": "created", class: "text-center" },
		{ "bSortable": true, "data": "delivery_date", "sClass": "hidden-xs text-center" },
		{ "bSortable": true, "data": "amount", "sClass": "hidden-xs text-right" },
		{ "bSortable": true, "data": "price", "sClass": "text-right" },
		{ "bSortable": false, "data": "sales", "sClass": "text-center", "sWidth": "80px" }
	], "order": [[1, "desc"]],
	"createdRow": function (row, data, index) {

		$('td', row).eq(1).html(moment(data.created).format("DD/MM/YYYY HH:mm:ss"));
		if (data.delivery_date != null && data.delivery_date != "0000-00-00") {
			$('td', row).eq(2).html(moment(data.delivery_date).format("DD/MM/YYYY"));
		} else {
			$('td', row).eq(2).html("-");
		}



	},
	"footerCallback": function (row, data, start, end, display) {
		var api = this.api(), data;

		var tAmount = 0, tValue = 0;
		for (i in data) {
			tAmount += parseFloat(data[i].amount_value);
			tValue += (parseFloat(data[i].amount_value) * parseFloat(data[i].price));
		}

		$("#tblDailyRemain [xname=tAmount]").html(fn.ui.numberic.format(tAmount));

	}
});