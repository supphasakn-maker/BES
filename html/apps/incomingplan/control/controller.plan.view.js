$("#tblPlan").data("selected", []);
$("#tblPlan").DataTable({
	"bStateSave": true,
	"autoWidth": true,
	"processing": true,
	"serverSide": true,
	"ajax": "apps/incomingplan/store/store-plan.php",
	"aoColumns": [
		{ "bSortable": false, "data": "id", "sClass": "hidden-xs text-center", "sWidth": "20px" },
		{ "bSortable": false, "data": "id", "sClass": "text-center", "sWidth": "80px" },
		{ "bSort": true, "data": "import_id", class: "text-center", "sWidth": "20px" },
		{ "bSort": true, "data": "brand", class: "text-left", "sWidth": "20px" },
		{ "bSort": true, "data": "import_date", class: "text-center" },
		{ "bSort": true, "data": "import_brand" },
		{ "bSort": true, "data": "import_lot" },
		{ "bSort": true, "data": "amount", class: "text-right" },
		{ "bSort": true, "data": "rate_pmdc", class: "text-right" },
		{ "bSort": true, "data": "factory", class: "text-center" },
		{ "bSort": true, "data": "product", class: "text-center", "sWidth": "20px" },
		{ "bSort": true, "data": "coa" },
		{ "bSort": true, "data": "coc", "sWidth": "50px" },
		{ "bSort": true, "data": "country" },
		{ "bSort": true, "data": "supplier_name" },
		{ "bSort": true, "data": "remark" },
		{ "bSort": true, "data": "usd" },
	], "order": [[4, "desc"]],
	"createdRow": function (row, data, index) {
		var selected = false, checked = "", s = '';
		if ($.inArray(data.DT_RowId, $("#tblPlan").data("selected")) !== -1) {
			$(row).addClass("selected");
			selected = true;
		}
		$("td", row).eq(0).html(fn.ui.checkbox("chk_plan", data[0], selected));
		s = '';
		s += fn.ui.button("btn btn-xs btn-outline-warning mr-1", "far fa-cut", "fn.app.incomingplan.plan.dialog_split(" + data[0] + ")");
		s += fn.ui.button("btn btn-xs btn-outline-dark", "far fa-pen", "fn.app.incomingplan.plan.dialog_edit(" + data[0] + ")");
		$("td", row).eq(1).html(s);

		// if(data.parent == null){
		// 	$("td", row).eq(3).html("-");
		// }else{
		// 	$("td", row).eq(3).html('<span class="badge badge-warning">'+data.parent+'</span>');
		// }
		$("td", row).eq(3).html('<span class="badge badge-warning">' + data.brand + '</span>')
		if (data.factory == 'BWS') {
			$("td", row).eq(9).html('<span class="badge badge-primary">' + data.factory + '</span>');
		} else {
			$("td", row).eq(9).html('<span class="badge badge-danger">' + data.factory + '</span>');
		}
	}
});
fn.ui.datatable.selectable("#tblPlan", "chk_plan");

