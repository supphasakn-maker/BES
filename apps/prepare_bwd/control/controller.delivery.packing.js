$("#tblSilverDetail").DataTable({
	responsive: true,
	"bStateSave": true,
	"autoWidth": true,
	"processing": true,
	"serverSide": true,
	"ajax": {
		"url": "apps/sales_bwd/store/store-item.php",
		"data": function (d) {
			d.where = "bs_bwd_pack_items.delivery_id = " + $("#tblItemDetail").attr("data-id");
		}
	},
	"aoColumns": [
		{ "bSort": true, "data": "code", "class": "text-center" },
		{ "bSort": true, "data": "pack_type", "class": "text-center" },
		{ "bSort": true, "data": "pack_name", "class": "text-center" },
		{ "bSort": true, "data": "weight_expected", "class": "text-center" },
		{ "bSort": true, "data": "amount", "class": "text-center" },
		{ "bSortable": false, "data": "item_id", "sClass": "text-center", "sWidth": "80px" }

	], "order": [[1, "desc"]],
	"createdRow": function (row, data, index) {

		var s = '';
		s += fn.ui.button("btn btn-xs btn-danger", "far fa-trash", "fn.app.prepare_bwd.delivery.remove_mapping(" + data[3] + ")");
		$("td", row).eq(5).html(s);
	}
});


fn.app.prepare_bwd.delivery.packing = function () {
	$.post("apps/prepare_bwd/xhr/action-packing-delivery.php", $("form[name=form_packing]").serialize(), function (response) {
		if (response.success) {
			$("#tblDelivery").DataTable().draw();
		} else {
			fn.notify.warnbox(response.msg, "Oops...");
		}
	}, "json");
	return false;
};

$("[name=code_search]").select2({
	ajax: {
		url: 'apps/prepare_bwd/xhr/action-load-item.php',
		dataType: 'json',
		data: function (d) {
			d.silver = $('select[name=round_filter]').val();
			return d;
		},
		processResults: function (data, params) {
			return {
				results: data.results
			};
		}
	}
});
fn.app.prepare_bwd.delivery.calculate = function () {
	fn.dialog.confirmbox("Confirmation", "Are you sure to submit?", function () {
		$.post("apps/prepare_bwd/xhr/action-save-packing.php", {
			delivery_id: $("#tblItemDetail").attr("data-id"),
			amount_total: $("#amount_total").attr("data-id")

		}, function (response) {

			if (response.success) {
				$("#tblDelivery").DataTable().draw();
				fn.app.prepare_bwd.delivery.load_data();
			} else {
				fn.notify.warnbox(response.msg, "Oops...");
			}
		}, "json");
		return false;
	});
};
fn.app.prepare_bwd.delivery.calcu = function () {
	fn.dialog.confirmbox("Confirmation", "Are you sure to Unsubmit?", function () {
		$.post("apps/prepare_bwd/xhr/action-save-unpacking.php", {
			delivery_id: $("#tblItemDetail").attr("data-id"),
			amount_total: $("#amount_total").attr("data-id")

		}, function (response) {

			if (response.success) {
				$("#tblDelivery").DataTable().draw();
				fn.reload();
				fn.app.prepare_bwd.delivery.load_data();
			} else {
				fn.notify.warnbox(response.msg, "Oops...");
			}
		}, "json");
		return false;
	});
};
fn.app.prepare_bwd.delivery.mapping = function () {
	$.post("apps/prepare_bwd/xhr/action-load-silver.php", {
		packing_id: $("[name=code_search]").val(),
		delivery_id: $("#tblItemDetail").attr("data-id")

	}, function (response) {

		if (response.success) {
			$("#tblSilverDetail").DataTable().draw();
			fn.app.prepare_bwd.delivery.load_data();
		} else {
			fn.notify.warnbox(response.msg, "Oops...");
		}
	}, "json");
	return false;
};

fn.app.prepare_bwd.delivery.remove_mapping = function (id) {
	$.post("apps/prepare_bwd/xhr/action-mapping-remove.php", { id: id }, function (response) {

		if (response.success) {
			$("#tblSilverDetail").DataTable().draw();
			fn.app.prepare_bwd.delivery.load_data();
		} else {
			fn.notify.warnbox(response.msg, "Oops...");
		}
	}, "json");
	return false;
};


fn.app.prepare_bwd.delivery.load_data = function () {
	$.post("apps/prepare_bwd/xhr/action-load-data.php", { id: $("#tblItemDetail").attr("data-id") }, function (json) {
		$("#amount_total").html(json.total);
		$("#amount_remain").html(json.remain);
		if (json.remain > 0) {
			$("#amount_remain").addClass("text-danger");
		} else {
			$("#amount_remain").removeClass("text-danger");
		}
	}, "json");

};
fn.app.prepare_bwd.delivery.load_data();
