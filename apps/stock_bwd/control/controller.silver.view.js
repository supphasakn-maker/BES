$("#tblStockSilver").DataTable({
	responsive: true,
	"pageLength": 100,
	"bStateSave": true,
	"autoWidth": true,
	"processing": true,
	"serverSide": true,
	"ajax": "apps/stock_bwd/store/store-silver.php",
	"aoColumns": [
		{ "bSortable": true, "data": "code", class: "text-center" },
		{ "bSortable": true, "data": "pack_name", class: "text-center", "sWidth": "80px" },
		{ "bSortable": true, "data": "pack_type", class: "text-center" },
		{ "bSortable": true, "data": "weight_expected", class: "text-center" },
		{ "bSortable": true, "data": "name_product", class: "text-center" },
		{ "bSortable": true, "data": "name", class: "text-center" },
		{ "bSortable": true, "data": "submited", class: "text-center" },
		{ "bSortable": false, "data": "id", "sClass": "hidden-xs text-center", "sWidth": "20px" },

	], "order": [[0, 'desc']],
	"createdRow": function (row, data, index) {
		s = '';
		s += fn.ui.button("btn btn-xs btn-outline-warning mr-1", "far fa-trash", "fn.app.stock_bwd.silver.remove(" + data[0] + ")");
		$("td", row).eq(7).html(s);
	},

	"footerCallback": function (row, data, start, end, display) {
		var api = this.api(), data;

		var tAmount = 0, tValue = 0;
		for (i in data) {
			tAmount += parseFloat(data[i].weight_expected);
		}

		$("#tblStockSilver [xname=tAmount]").html(fn.ui.numberic.format(tAmount, 4));

	}
});
