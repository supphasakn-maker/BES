$("#tblIncoming").data("selected", []);
$("#tblIncoming").DataTable({
	responsive: true,
	"bStateSave": true,
	"autoWidth": true,
	"processing": true,
	"serverSide": true,
	"ajax": {
		"url": "apps/sigmargin_stx/store/store-incoming.php",
		"data": function (d) {
			d.date = $("#selcted_date").val();
		}
	},
	"aoColumns": [
		{ "bSortable": false, "data": "id", "sClass": "hidden-xs text-center", "sWidth": "20px" },
		{ "bSort": true, "data": "amount" },
		{ "bSort": true, "data": "rate_pmdc" },
		{ "bSort": true, "data": "date" },
		{ "bSort": true, "data": "transfer" },
		{ "bSortable": false, "data": "id", "sClass": "text-center", "sWidth": "80px" }
	], "order": [[3, "desc"]],
	"createdRow": function (row, data, index) {
		var selected = false, checked = "", s = '';
		if ($.inArray(data.DT_RowId, $("#tblIncoming").data("selected")) !== -1) {
			$(row).addClass("selected");
			selected = true;
		}
		$("td", row).eq(0).html(fn.ui.checkbox("chk_incoming", data[0], selected));
		s = '';
		s += fn.ui.button("btn btn-xs btn-outline-dark", "far fa-pen", "fn.app.sigmargin_stx.incoming.dialog_edit(" + data[0] + ")");
		$("td", row).eq(5).html(s);
	}
});
fn.ui.datatable.selectable("#tblIncoming", "chk_incoming");
