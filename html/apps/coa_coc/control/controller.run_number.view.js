$("#tblRun").DataTable({
	responsive: true,
	"pageLength": 100,
	"bStateSave": true,
	"autoWidth": true,
	"processing": true,
	"serverSide": true,
	"ajax": "apps/coa_coc/store/store-run-number.php",
	"aoColumns": [
		{ "bSortable": false, "data": "id", "sClass": "text-center" },
		{ "bSort": true, "data": "number", "class": "text-center" },
		{ "bSort": true, "data": "number_coc", "class": "text-center" },
		{ "bSort": true, "data": "order_id", "class": "text-center" },
		{ "bSort": true, "data": "name", "class": "text-center" },
		{ "bSort": true, "data": "created", "class": "text-center" },
		{ "bSortable": false, "data": "id", "sClass": "text-center", "sWidth": "120px" }
	], "order": [[0, "desc"]],
	"createdRow": function (row, data, index) {
		s = '';
		if (data.status == "2") {
			s += fn.ui.button("btn btn-xs btn-outline-danger mr-1", "far fa-thumbs-down", "fn.app.coa_coc.run_number.deapprove(" + data[0] + ")");
		} else {
			s += fn.ui.button("btn btn-xs btn-outline-warning", "far fa-pen", "fn.app.coa_coc.run_number.dialog_edit(" + data[0] + ")");
		}
		$("td", row).eq(6).html(s);
	}
});


fn.app.coa_coc.run_number.deapprove = function (id) {
	bootbox.confirm("Are yure sure to deapprove!", function (confirmed) {
		if (confirmed)
			$.post("apps/coa_coc/xhr/action-coa_coc-deapprove.php", { id: id }, function () {
				$("#tblRun").DataTable().draw();
			});
	});
};
