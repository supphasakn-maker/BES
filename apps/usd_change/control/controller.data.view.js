$("#tblUsd").data("selected", []);
$("#tblUsd").DataTable({
	responsive: true,
	"bStateSave": true,
	"autoWidth": true,
	"processing": true,
	"serverSide": true,
	"ajax": {
		"url": "apps/purchase/store/store-usd.php",
		"data": function (d) {
			d.where = "bs_purchase_usd.status > -1";
		}
	},

	"aoColumns": [
		{ "bSortable": false, "data": "id", "sClass": "hidden-xs text-center", "sWidth": "20px" },
		{ "bSortable": true, "data": "confirm", "sClass": "text-center" },
		{ "bSortable": true, "data": "parent", "sClass": "text-center" },
		{ "bSortable": true, "data": "date", "sClass": "text-center" },
		{ "bSortable": true, "data": "date", "sClass": "text-center" },
		{ "bSortable": true, "data": "amount", "sClass": "text-center" },
		{ "bSortable": true, "data": "rate_exchange", "sClass": "text-center" },
		{ "bSortable": true, "data": "rate_finance", "sClass": "text-center" },
		{ "bSortable": true, "data": "type", "sClass": "text-center" },
		{ "bSortable": true, "data": "bank", "sClass": "text-center" },
		{ "bSortable": true, "data": "user", "sClass": "text-center" },
		{ "bSortable": true, "data": "ref", "sClass": "text-center" },
		{ "bSortable": true, "data": "comment", "sClass": "text-center" },
		{ "bSortable": false, "data": "id", "sClass": "text-center", "sWidth": "80px" }
	], "order": [[1, "desc"]],
	"createdRow": function (row, data, index) {
		var selected = false, checked = "", s = '';
		if ($.inArray(data.DT_RowId, $("#tblUsd").data("selected")) !== -1) {
			$(row).addClass("selected");
			selected = true;
		}
		$("td", row).eq(0).html(fn.ui.checkbox("chk_usd", data[0], selected));

		if (data.parent != null) {
			//$("td", row).eq(2).html('<span class="badge badge-warning">splited</span>');
			$("td", row).eq(2).html('<span onclick="fn.app.purchase.usd.dialog_split_view(' + data[0] + ')" class="badge badge-warning">splited</span>');


		} else {
			s = '';
			s += fn.ui.button("btn btn-xs btn-outline-warning mr-1", "far fa-cut", "fn.app.purchase.usd.dialog_split(" + data[0] + ")");
			$("td", row).eq(2).html(s);
		}
		if (data.finance == '0') {
			$("td", row).eq(7).html('<span class="badge badge-danger">' + data.rate_finance + '</span>');
		} else {
			$("td", row).eq(7).html('<span class="badge badge-secondary">' + data.rate_finance + '</span>');
		}
		s = '';
		s += fn.ui.button("btn btn-xs btn-outline-dark mr-1", "far fa-cut", "fn.app.purchase.usd.dialog_split(" + data[0] + ")");
		s += fn.ui.button("btn btn-xs btn-outline-dark", "far fa-pen", "fn.app.purchase.usd.dialog_edit_usd(" + data[0] + ")");
		$("td", row).eq(13).html(s);
	}
});
fn.ui.datatable.selectable("#tblUsd", "chk_usd");
