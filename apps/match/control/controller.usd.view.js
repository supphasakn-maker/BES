$("#tblUSD").data("selected", []);
$("#tblUSD").DataTable({
	responsive: true,
	"bStateSave": true,
	"autoWidth": true,
	"processing": true,
	"serverSide": true,
	"ajax": "apps/match/store/store-usd.php",
	"aoColumns": [
		{ "bSortable": false, "data": "id", "sClass": "hidden-xs text-center", "sWidth": "20px" },
		{ "bSort": true, "data": "mapped", class: "text-center", "sWidth": "200px" },
		{ "bSort": true, "data": "usd_amount", class: "text-right" },
		{ "bSort": true, "data": "remark" },
		{ "bSortable": false, "data": "id", "sClass": "text-center", "sWidth": "80px" }
	], "order": [[1, "desc"]],
	"createdRow": function (row, data, index) {
		var selected = false, checked = "", s = '';
		if ($.inArray(data.DT_RowId, $("#tblUSD").data("selected")) !== -1) {
			$(row).addClass("selected");
			selected = true;
		}
		$("td", row).eq(0).html(fn.ui.checkbox("chk_usd", data[0], selected));
		s = '';
		s += fn.ui.button("btn btn-xs btn-danger mr-1", "far fa-trash", "fn.app.match.usd.unmatch(" + data.id + ")");
		s += fn.ui.button("btn btn-xs btn-outline-dark mr-1", "far fa-pen", "fn.app.match.usd.dialog_remark(" + data.id + ")");
		s += fn.ui.button("btn btn-xs btn-primary", "far fa-eye", "fn.dialog.open('apps/match/view/dialog.usd.lookup.php','#dialog_lookup_usd',{id:" + data.id + "})");

		$("td", row).eq(4).html(s);
	}
});
fn.ui.datatable.selectable("#tblUSD", "chk_usd");

$("#tblPurchaseSpot").data("selected", []);
$("#tblPurchaseSpot").DataTable({
	responsive: true,
	"bStateSave": true,
	"autoWidth": true,
	"processing": true,
	"serverSide": true,
	"ajax": {
		"url": "apps/match/store/store-purchase-silver.php",
		"data": function (d) {
			let date_filter = $("#tblPurchaseSpot_length input[name=date_filter]").val();
			if (date_filter != "") {
				d.date_filter = date_filter;
			}
		}
	},
	"aoColumns": [
		{ "bSortable": false, "data": "id", "sClass": "hidden-xs text-center", "sWidth": "20px" },
		{ "bSort": true, "data": "date", "class": "text-center" },
		{ "bSort": true, "data": "supplier", "class": "text-center" },
		{ "bSort": true, "data": "amount", "class": "text-right" },
		{ "bSort": true, "data": "rate_spot", "class": "text-right" },
		{ "bSort": true, "data": "rate_pmdc", "class": "text-right" },
		{ "bSort": true, "data": "total_usd", "class": "text-right" },
		{ "bSort": true, "data": "ref", "class": "text-center" },
		{ "bSortable": false, "data": "id", "sClass": "text-center", "sWidth": "80px" }
	], "order": [[1, "asc"]],
	"createdRow": function (row, data, index) {
		var selected = false, checked = "", s = '';
		if ($.inArray(data.DT_RowId, $("#tblPurchaseSpot").data("selected")) !== -1) {
			$(row).addClass("selected");
			selected = true;
		}
		if (data.mapping_item_id != null) {
			$("td", row).eq(6).html('<span class="text-warning">' + data.remain + "</span>/" + data.total_usd);
			$(row).addClass("bg-secondary text-white");

			s = '';
			s += fn.ui.button("btn btn-xs btn-danger mr-1", "far fa-trash", "fn.app.match.usd.spot_item_remove(" + data.mapping_item_id + ")");
			$("td", row).eq(8).html(s);
		}


		$("td", row).eq(0).html(fn.ui.checkbox("chk_purchase_spot", data[0], selected));


	}
}).on('xhr.dt', function (e, settings, json, xhr) {
	$("#tblPurchaseSpot_filter input[name=total_amount]").val(fn.ui.numberic.format(json.total.remian_unmatch, 2));
	$(this).find(".total_amount").html(fn.ui.numberic.format(json.total.remain_total, 2));
});
fn.ui.datatable.selectable("#tblPurchaseSpot", "chk_purchase_spot");



$("#tblPurchaseUSD").data("selected", []);
$("#tblPurchaseUSD").DataTable({
	responsive: true,
	"bStateSave": true,
	"autoWidth": true,
	"processing": true,
	"serverSide": true,
	"ajax": {
		"url": "apps/match/store/store-purchase-usd.php",
		"data": function (d) {
			let date_filter = $("#tblPurchaseUSD_length input[name=date_filter]").val();
			if (date_filter != "") {
				d.date_filter = date_filter;
			}
		}
	},
	"aoColumns": [
		{ "bSortable": false, "data": "id", "sClass": "hidden-xs text-center", "sWidth": "20px" },
		{ "bSort": true, "data": "date", "class": "text-center" },
		{ "bSort": true, "data": "bank", "class": "text-center" },
		{ "bSort": true, "data": "amount", "class": "text-right" },
		{ "bSort": true, "data": "rate_exchange", "class": "text-right" },
		{ "bSort": true, "data": "rate_finance", "class": "text-right" },
		{ "bSort": true, "data": "type", "class": "text-center" },
		{ "bSort": true, "data": "value", "class": "text-right" }
	], "order": [[1, "asc"]],
	"createdRow": function (row, data, index) {
		var selected = false, checked = "", s = '';
		if ($.inArray(data.DT_RowId, $("#tblPurchaseUSD").data("selected")) !== -1) {
			$(row).addClass("selected");
			selected = true;
		}

		$("td", row).eq(1).html('<span title="' + data.id + '">' + data.date + "</span>");
		if (data.mapping_item_id != null) {
			$("td", row).eq(3).html('<span class="text-warning">' + data.remain + "</span>/" + data.amount);
			$(row).addClass("bg-secondary text-white");

			s = '<span class="badge badge-danger mr-1">' + data.id + "</span>";
			s += '<a href="javascript:;" onclick="fn.app.match.usd.dialog_lookup_usd_map(' + data.mapping_item_id + ')" class="badge badge-info">' + data.mapping_item_id + "</a>";


			$("td", row).eq(6).html(s);
		}



		$("td", row).eq(0).html(fn.ui.checkbox("chk_purchase_usd", data[0], selected));
	}
}).on('xhr.dt', function (e, settings, json, xhr) {
	$("#tblPurchaseUSD_filter input[name=total_amount]").val(fn.ui.numberic.format(json.total.remian_unmatch, 2));
	$(this).find(".total_amount").html(fn.ui.numberic.format(json.total.remain_total, 2));
});

fn.ui.datatable.selectable("#tblPurchaseUSD", "chk_purchase_usd");

/*
$("#tblPurchaseSpot_length").addClass('form-inline');
let s = '';
s += '<select id="spot_changer" class="form-control">';
	s += '<option value="1">แบบปกติ</option>';
	s += '<option value="2">การสั่งซื้อที่ผูกแล้ว</option>';
s += '</select>';
$("#tblPurchaseSpot_length").append(s);
*/


$("#tblPurchaseSpot_length").append('<input onchange=\'$("#tblPurchaseSpot").DataTable().draw();\' type="date" class="form-control form-control-sm" name="date_filter">');
$("#tblPurchaseSpot_filter").append('<label>รวม:<input readonly name="total_amount" class="form-control form-control-sm"></label>');
$("#tblPurchaseUSD_length").append('<input onchange=\'$("#tblPurchaseUSD").DataTable().draw();\' type="date" class="form-control form-control-sm" name="date_filter">');
$("#tblPurchaseUSD_filter").append('<label>รวม:<input readonly name="total_amount" class="form-control form-control-sm"></label>');

$("#spot_changer").change(function () {
	$("#tblPurchaseSpotMatched").toggleClass("d-none");
	$("#tblPurchaseSpot").toggleClass("d-none");

	$("#tblPurchaseSpotMatched").data("selected", []);
	$("#tblPurchaseSpot").data("selected", []);

	$("#tblPurchaseSpotMatched").DataTable().draw();
	$("#tblPurchaseSpot").DataTable().draw();

});

