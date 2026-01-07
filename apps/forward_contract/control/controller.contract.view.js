
$("#tblUSD").data("selected", []);
$("#tblUSD").DataTable({
	responsive: true,
	"bStateSave": true,
	"autoWidth": true,
	"processing": true,
	"serverSide": true,
	"ajax": {
		url: "apps/forward_contract/store/store-usd.php",
		data: function (d) {
			d.bank = $("select[name=bank]").val();
		}
	},
	"aoColumns": [
		{ "bSortable": false, "data": "id", "sClass": "hidden-xs text-center", "sWidth": "20px" },
		{ "bSort": true, "data": "date" },
		{ "bSort": true, "data": "type", class: "unselectable" },
		{ "bSort": true, "data": "method", class: "unselectable" },
		{ "bSort": true, "data": "amount", "class": "text-right  unselectable" },
		{ "bSort": true, "data": "rate_exchange", "class": "text-right" },
		{ "bSort": true, "data": "comment", "class": "text-left" }
	], "order": [[1, "desc"]],
	"createdRow": function (row, data, index) {
		var selected = false, checked = "", s = '';
		if ($.inArray(data.DT_RowId, $("#tblUSD").data("selected")) !== -1) {
			$(row).addClass("selected");
			selected = true;
		}
		s = '<input type="hidden" xname="amount" value="' + data.amount_value + '">';
		s += '<input type="hidden" xname="rate_exchange" value="' + data.rate_exchange + '">';
		$("td", row).eq(0).html(fn.ui.checkbox("chk_usd", data[0], selected) + s);

	}
});
fn.ui.datatable.selectable("#tblUSD", "chk_usd", true, function (tr) {
	fn.app.forward_contract.contract.append(tr);
	//fn.app.forward_contract.contract.calculate();
});

$("select[name=bank]").change(function () {
	$("#tblUSD").DataTable().draw();
});


$("#tblContract").data("selected", []);
$("#tblContract").DataTable({
	responsive: true,
	"bStateSave": true,
	"autoWidth": true,
	"processing": true,
	"serverSide": true,
	"ajax": "apps/forward_contract/store/store-contract.php",
	"aoColumns": [
		{ "bSortable": false, "data": "id", "sClass": "hidden-xs text-center", "sWidth": "20px" },
		{ "bSortable": false, "data": "id", "sClass": "text-center", "sWidth": "120px" },
		{ "bSort": true, "data": "date" },
		{ "bSort": true, "data": "bank" },
		{ "bSort": true, "data": "supplier" },
		{ "bSort": true, "data": "id", "class": "text-center" },
		{ "bSort": true, "data": "value_usd_goods" },
		{ "bSort": true, "data": "value_usd_deposit" },
		{ "bSort": true, "data": "value_usd_paid" },
		{ "bSort": true, "data": "value_usd_adjusted" },
		{ "bSort": true, "data": "value_usd_total" },
		{ "bSort": true, "data": "value_thb_net" },
		{ "bSort": true, "data": "rate_counter" },
		{ "bSort": true, "data": "amount" },
		{ "bSort": true, "data": "value_adjust_trade" },
		{ "bSort": true, "data": "value_edit_trade" },
		{ "bSort": true, "data": "interest_match" },
		{ "bSort": true, "data": "name" },
	], "order": [[2, "desc"]],
	"createdRow": function (row, data, index) {
		var selected = false, checked = "", s = '';
		if ($.inArray(data.DT_RowId, $("#tblContract").data("selected")) !== -1) {
			$(row).addClass("selected");
			selected = true;
		}
		$("td", row).eq(0).html(fn.ui.checkbox("chk_contract", data[0], selected));
		s = '';
		s += fn.ui.button("btn btn-xs btn-outline-dark mr-1", "far fa-eye", "fn.dialog.open('apps/forward_contract/view/dialog.contract.lookup.php','#dialog_lookup',{id:" + data[0] + "})");
		s += fn.ui.button("btn btn-xs btn-outline-dark mr-1", "far fa-link", "fn.app.forward_contract.import.dialog_lookup(" + data.id + ")");
		s += fn.ui.button("btn btn-xs btn-outline-dark", "far fa-pen", "fn.app.forward_contract.contract.dialog_edit(" + data.id + ")");
		s += fn.ui.button("btn btn-xs btn-outline-dark", "fas fa-wrench", "fn.app.forward_contract.contract.dialog_edit_amount(" + data.id + ")");
		s += fn.ui.button("btn btn-xs btn-outline-dark", "fas fa-dollar-sign", "fn.app.forward_contract.contract.dialog_edit_adjust(" + data.id + ")");

		s += fn.ui.button("btn btn-xs btn-outline-dark", "fas fa-pen", "fn.app.forward_contract.contract.dialog_edit_product(" + data.id + ")");
		s += fn.ui.button("btn btn-xs btn-outline-dark", "far fa-money-bill-alt", "fn.app.forward_contract.contract.dialog_edit_trade(" + data.id + ")");
		s += fn.ui.button("btn btn-xs btn-outline-dark", "fas fa-info", "fn.app.forward_contract.contract.dialog_edit_interest(" + data.id + ")");
		$("td", row).eq(1).html(s);
	}
});
fn.ui.datatable.selectable("#tblContract", "chk_contract");
