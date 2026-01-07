$("#tblSpot").DataTable({
	responsive: true,
	"bStateSave": true,
	"autoWidth": true,
	"processing": true,
	"pageLength": 100,
	"serverSide": true,
	"ajax": "apps/sales/store/store-spot.php",
	"aoColumns": [
		{ "bSortable": true, "data": "created", class: "text-center" },
		{ "bSortable": true, "data": "date", class: "text-center" },
		{ "bSortable": true, "data": "type", class: "text-center" },
		{ "bSortable": true, "data": "supplier_name", class: "text-center" },
		{ "bSortable": true, "data": "amount", class: "text-center" },
		{ "bSortable": true, "data": "rate_spot", class: "text-center" },
		{ "bSortable": true, "data": "user", class: "text-center" },
		{ "bSortable": true, "data": "name_sup", class: "text-center" },
		{ "bSortable": true, "data": "maturity", class: "text-center" },
		{ "bSortable": true, "data": "comment", class: "text-center" },
		{ "bSortable": true, "data": "name", class: "text-center" },
		{ "bSortable": false, "data": "id", class: "text-center", "sWidth": "80px" }
	], "order": [[1, "desc"]],
	"createdRow": function (row, data, index) {
		var selected = false, checked = "", s = '';

		s = '';
		s += fn.ui.button("btn btn-xs btn-outline-danger mr-1", "far fa-trash", "fn.app.sales.spot.remove(" + data[0] + ")");
		s += fn.ui.button("btn btn-xs btn-outline-dark", "far fa-pen", "fn.app.sales.spot.dialog_edit(" + data[0] + ")");
		$("td", row).eq(11).html(s);
	}
});
