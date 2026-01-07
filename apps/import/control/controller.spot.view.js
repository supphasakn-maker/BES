$("#tblSpot").data("selected", []);
$("#tblSpot").DataTable({
	"paging": false,
	responsive: true,
	"bStateSave": true,
	"autoWidth": true,
	"processing": true,
	"serverSide": true,
	"ajax": {
		"url": "apps/import/store/store-spot.php",
		"data": function (d) {
			d.where = "bs_purchase_spot.status > -1";
		}
	},
	"aoColumns": [
		{ "bSortable": false, "data": "id", "sClass": "hidden-xs text-center", "sWidth": "20px" },
		{ "bSortable": false, "data": "date", "class": "text-center" },
		{ "bSortable": false, "data": "type", "class": "text-center" },
		{ "bSortable": false, "data": "supplier", "class": "text-center" },
		{ "bSortable": false, "data": "amount", "class": "text-right", "sWidth": "150px" },
		{ "bSortable": false, "data": "rate_spot", "class": "text-right pr-2" },
		{ "bSortable": false, "data": "rate_pmdc", "class": "text-right" },
		{ "bSortable": false, "data": "user", "class": "text-center" },
		{ "bSortable": false, "data": "ref", "class": "text-center" },
		{ "bSortable": false, "data": "id", "sClass": "text-center", "sWidth": "80px" }
	], "order": [[1, "desc"]],
	"createdRow": function (row, data, index) {
		var selected = false, checked = "", s = '';
		if ($.inArray(data.DT_RowId, $("#tblSpot").data("selected")) !== -1) {
			$(row).addClass("selected");
			selected = true;
		}
		$("td", row).eq(0).html(fn.ui.checkbox("chk_spot", data[0], selected));

		s = '';
		s += '<input type="hidden" class="form-control form-control-sm" name="spot[]" data-name="spot" value="' + parseFloat(data.amount.replace(/,/g, '')) + '">';
		s += data.amount;
		s += '';
		$("td", row).eq(4).html(s);

		s = '';
		if (data.rate_pmdc != null) {
			s += '<input type="hidden" class="form-control form-control-sm" name="rate_pmdc" data-name="rate_pmdc" value="' +
				parseFloat(data.rate_pmdc.replace(/,/g, '')) + '">';
			s += data.rate_pmdc;
		} else {
			s += '<input type="hidden" class="form-control form-control-sm" name="rate_pmdc" data-name="rate_pmdc" value="0">';
			s += '0';
		}
		s += '';

		s = '';
		s += fn.ui.button("btn btn-xs btn-outline-warning mr-1", "far fa-cut", "fn.app.purchase.spot.dialog_split(" + data[0] + ")");
		s += fn.ui.button("btn btn-xs btn-outline-dark", "far fa-pen", "fn.app.purchase.spot.dialog_edit(" + data[0] + ")");
		$("td", row).eq(9).html(s);
	}
});
fn.ui.datatable.selectable("#tblSpot", "chk_spot");

