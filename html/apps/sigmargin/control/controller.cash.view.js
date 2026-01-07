$("#tblCash").data("selected", []);
$("#tblCash").DataTable({
	responsive: true,
	"bStateSave": true,
	"autoWidth": true,
	"processing": true,
	"serverSide": true,
	"ajax": "apps/sigmargin/store/store-cash.php",
	"aoColumns": [
		{ "bSortable": false, "data": "id", "sClass": "hidden-xs text-center", "sWidth": "20px" },
		{ "bSort": true, "data": "amount" },
		{ "bSort": true, "data": "date" },
		{ "bSortable": false, "data": "id", "sClass": "text-center", "sWidth": "80px" }
	], "order": [[2, "desc"]],
	"createdRow": function (row, data, index) {
		var selected = false, checked = "", s = '';
		if ($.inArray(data.DT_RowId, $("#tblCash").data("selected")) !== -1) {
			$(row).addClass("selected");
			selected = true;
		}
		$("td", row).eq(0).html(fn.ui.checkbox("chk_cash", data[0], selected));
		s = '';
		s += fn.ui.button("btn btn-xs btn-outline-dark", "far fa-pen", "fn.app.sigmargin.cash.dialog_edit(" + data[0] + ")");
		$("td", row).eq(3).html(s);
	}
});
fn.ui.datatable.selectable("#tblCash", "chk_cash");
