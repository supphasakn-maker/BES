$("#tblSilver").DataTable({
	responsive: true,
	"pageLength": 50,
	"bStateSave": true,
	"autoWidth": true,
	"processing": true,
	"serverSide": true,
	"ajax": {
		"url": "apps/announce/store/store-silver.php",
		"data": function (d) {
			d.date_from = $("form[name=filter] input[name=from]").val();
			d.date_to = $("form[name=filter] input[name=to]").val();
		}
	},
	"aoColumns": [
		{ "bSortable": true, "data": "created", class: "text-center" },
		{ "bSortable": true, "data": "date", class: "text-center" },
		{ "bSortable": true, "data": "no", class: "text-center" },
		{ "bSortable": true, "data": "timeno", class: "text-center" },
		{ "bSortable": true, "data": "sell", class: "text-center" },
		{ "bSortable": true, "data": "buy", class: "text-center" },
		{ "bSortable": true, "data": "dif", class: "text-center" },
		{ "bSortable": true, "data": "rate_spot", class: "text-center" },
		{ "bSortable": true, "data": "rate_exchange", class: "text-center" },
		{ "bSortable": true, "data": "rate_pmdc", class: "text-center" },
		{ "bSortable": false, "data": "id", class: "text-center", "sWidth": "80px" }
	], "order": [[0, 'desc']],
	"createdRow": function (row, data, index) {
		var selected = false, checked = "", s = '';

		s = '';
		s += fn.ui.button("btn btn-xs btn-outline-danger mr-1", "far fa-trash", "fn.app.announce.announce_silver.remove(" + data[0] + ")");
		s += fn.ui.button("btn btn-xs btn-outline-dark", "far fa-pen", "fn.app.announce.announce_silver.dialog_edit(" + data[0] + ")");
		$("td", row).eq(10).html(s);
	}
});
