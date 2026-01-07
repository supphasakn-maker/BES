
$("#tblDefer").DataTable({
	responsive: true,
	"bStateSave": true,
	"autoWidth": true,
	"processing": true,
	"serverSide": true,
	"ajax": {
		"url": "apps/defer_adjust/store/store-defer.php"
	},
	"aoColumns": [
		{ "bSortable": true, "data": "date", "class": "text-center" },
		{ "bSortable": true, "data": "supplier", "class": "text-center" },
		{ "bSortable": true, "data": "value_adjust_type", "class": "text-center" },
		{ "bSortable": true, "data": "name", "class": "text-center" },
		{ "bSortable": false, "data": "id", "class": "text-center", "class": "text-center", "sWidth": "80px" }
	], "order": [[0, "desc"]],
	"createdRow": function (row, data, index) {
		var selected = false, checked = "", s = '';
		$("td", row).eq(0).html(moment(data.date).format("DD/MM/YYYY"));
		s = '';
		s += fn.ui.button("btn btn-xs btn-outline-danger mr-1", "far fa-trash", "fn.app.defer_spot.defer.remove(" + data[0] + ")");
		s += fn.ui.button("btn btn-xs btn-outline-dark mr-1", "far fa-pen", "fn.app.defer_spot.defer.dialog_edit(" + data[0] + ")");
		$("td", row).eq(4).html(s);
	}
});
