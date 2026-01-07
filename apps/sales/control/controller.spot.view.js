$("#tblSpot").data("selected", []);

$("#tblSpot").DataTable({
	"bStateSave": true,
	"autoWidth": true,
	"processing": true,
	"serverSide": true,
	"pageLength": 100,
	"ajax": "apps/sales/store/store-spot.php",
	"aoColumns": [
		{ "bSortable": false, "data": "id", class: "hidden-xs text-center", "sWidth": "20px" },
		{ "bSortable": true, "data": "created", class: "text-center" },
		{ "bSortable": true, "data": "date", class: "text-center" },
		{ "bSortable": true, "data": "type", class: "text-center" },
		{ "bSortable": true, "data": "supplier_name", class: "text-center" },
		{ "bSortable": true, "data": "amount", class: "text-center" },
		{ "bSortable": true, "data": "rate_spot", class: "text-center" },
		{ "bSortable": true, "data": "user", class: "text-center" },
		{ "bSortable": true, "data": "name_sup", class: "text-center" },
		{ "bSortable": true, "data": "value_date", class: "text-center" },
		{ "bSortable": true, "data": "comment", class: "text-center" },
		{ "bSortable": false, "data": "id", class: "text-center", "sWidth": "80px" }

	], "order": [[1, "desc"]],

	"createdRow": function (row, data, index) {
		var selected = false, checked = "", s = '';
		if ($.inArray(data.DT_RowId, $("#tblSpot").data("selected")) !== -1) {
			$(row).addClass("selected");
			selected = true;
		}
		$("td", row).eq(0).html(fn.ui.checkbox("chk_spot", data[0], selected));
		s = '';
		s += fn.ui.button("btn btn-xs btn-outline-dark", "far fa-pen", "fn.app.sales.spot.dialog_edit(" + data[0] + ")");
		$("td", row).eq(11).html(s);

	}
});

fn.ui.datatable.selectable("#tblSpot", "chk_spot");
