$("#tblPurchase").data("selected", []);
$("#tblPurchase").DataTable({
	responsive: false,
	"paging": false,
	"bStateSave": true,
	"autoWidth": true,
	"processing": true,
	"serverSide": true,
	"ajax": "apps/adjust_cost/store/store-purchase.php",
	"aoColumns": [
		{ "bSortable": false, "data": "id", "sClass": "hidden-xs text-center", "sWidth": "20px" },
		{ "bSort": true, "data": "date" },
		{ "bSort": true, "data": "supplier", "class": "text-center" },
		{ "bSort": true, "data": "rate_spot", "class": "text-right pr-2" },
		{ "bSort": true, "data": "rate_pmdc", "class": "text-right pr-2" },
		{ "bSort": true, "data": "amount", "class": "text-right pr-2" },
		{ "bSort": true, "data": "spot_value", "class": "text-right pr-2" },
		{ "bSort": true, "data": "spot_discount", "class": "text-right pr-2" },
		{ "bSort": true, "data": "spot_net", "class": "text-right pr-2" }
	], "order": [[1, "desc"]],
	"createdRow": function (row, data, index) {
		var selected = false, checked = "", s = '';
		if ($.inArray(data.DT_RowId, $("#tblPurchase").data("selected")) !== -1) {
			$(row).addClass("selected");
			selected = true;
		}
		$("td", row).eq(0).html(fn.ui.checkbox("chk_purchase", data[0], selected));
	}
});
fn.ui.datatable.selectable("#tblPurchase", "chk_purchase");

$("#tblPurchaseNew").data("selected", []);
$("#tblPurchaseNew").DataTable({
	responsive: false,
	"paging": false,
	"bStateSave": true,
	"autoWidth": true,
	"processing": true,
	"serverSide": true,
	"ajax": "apps/adjust_cost/store/store-purchase-new.php",
	"aoColumns": [
		{ "bSortable": false, "data": "id", "sClass": "hidden-xs text-center", "sWidth": "20px" },
		{ "bSort": true, "data": "date" },
		{ "bSort": true, "data": "supplier", "class": "text-center" },
		{ "bSort": true, "data": "rate_spot", "class": "text-right pr-2" },
		{ "bSort": true, "data": "rate_pmdc", "class": "text-right pr-2" },
		{ "bSort": true, "data": "amount", "class": "text-right pr-2" },
		{ "bSort": true, "data": "spot_value", "class": "text-right pr-2" },
		{ "bSort": true, "data": "spot_discount", "class": "text-right pr-2" },
		{ "bSort": true, "data": "spot_net", "class": "text-right pr-2" }
	], "order": [[1, "desc"]],
	"createdRow": function (row, data, index) {
		var selected = false, checked = "", s = '';
		if ($.inArray(data.DT_RowId, $("#tblPurchaseNew").data("selected")) !== -1) {
			$(row).addClass("selected");
			selected = true;
		}
		$("td", row).eq(0).html(fn.ui.checkbox("chk_new", data[0], selected));
	}
});
fn.ui.datatable.selectable("#tblPurchaseNew", "chk_new");



$("#tblSales").data("selected", []);
$("#tblSales").DataTable({
	responsive: false,
	"paging": false,
	"autoWidth": true,
	"processing": true,
	"serverSide": true,
	"ajax": "apps/adjust_cost/store/store-sales.php",
	"aoColumns": [
		{ "bSortable": false, "data": "id", class: "hidden-xs text-center", "sWidth": "20px" },
		{ "bSortable": true, "data": "value_date", class: "text-center" },
		{ "bSortable": true, "data": "supplier", class: "text-center" },
		{ "bSort": true, "data": "rate_spot", "class": "text-right pr-2" },
		{ "bSort": true, "data": "rate_pmdc", "class": "text-right pr-2" },
		{ "bSort": true, "data": "amount", "class": "text-right pr-2" },
		{ "bSort": true, "data": "spot_value", "class": "text-right pr-2" },
		{ "bSort": true, "data": "spot_discount", "class": "text-right pr-2" },
		{ "bSort": true, "data": "spot_net", "class": "text-right pr-2" }
	], "order": [[1, "desc"]],

	"createdRow": function (row, data, index) {
		var selected = false, checked = "", s = '';
		if ($.inArray(data.DT_RowId, $("#tblSales").data("selected")) !== -1) {
			$(row).addClass("selected");
			selected = true;
		}
		$("td", row).eq(0).html(fn.ui.checkbox("chk_sales", data[0], selected));


	}
});
fn.ui.datatable.selectable("#tblSales", "chk_sales");

$("#tblAdjusted").data("selected", []);
$("#tblAdjusted").DataTable({
	responsive: false,
	"bStateSave": true,
	"autoWidth": true,
	"processing": true,
	"serverSide": true,
	"ajax": {
		"url": "apps/adjust_cost/store/store-adjust.php",
		"data": function (d) {
			//d.where = "bs_purchase_spot.status = 1";
		}
	},
	"aoColumns": [
		{ "bSortable": true, "data": "date_adjust", class: "text-center" },
		{ "bSortable": true, "data": "value_amount", class: "text-center" },
		{ "bSortable": true, "data": "value_buy", class: "text-center" },
		{ "bSortable": true, "data": "value_sell", class: "text-center" },
		{ "bSortable": true, "data": "value_new", class: "text-center" },
		{ "bSortable": true, "data": "value_profit", class: "text-center" },
		{ "bSortable": true, "data": "value_adjust_cost", class: "text-center" },
		{ "bSortable": true, "data": "value_adjust_discount", class: "text-center" },
		{ "bSortable": true, "data": "value_net", class: "text-center" },
		{ "bSortable": true, "data": "supplier", class: "text-center" },
		{ "bSortable": true, "data": "products", class: "text-center" },
		{ "bSort": true, "data": "id", "class": "text-center" }
	], "order": [[0, "desc"]],
	"createdRow": function (row, data, index) {
		var selected = false, checked = "", s = '';

		s += fn.ui.button("btn btn-xs btn-outline-danger mr-1", "fas fa-trash", "fn.app.adjust_cost.adjust.remove(" + data[0] + ")");
		s += fn.ui.button("btn btn-xs btn-outline-dark mr-1", "far fa-eye", "fn.dialog.open('apps/adjust_cost/view/dialog.adjust.lookup.php','#dialog_lookup',{id:" + data[0] + "})");

		$("td", row).eq(11).html(s);
	}
});
fn.ui.datatable.selectable("#tblAdjusted", "chk_adjust");



