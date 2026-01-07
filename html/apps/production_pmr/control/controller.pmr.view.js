$("#tblPmr").DataTable({
	responsive: true,
	pageLength: 50,
	"bStateSave": true,
	"autoWidth": true,
	"processing": true,
	"serverSide": true,
	"ajax": {
		"url": "apps/production_pmr/store/store_pmr.php",
		"data": function (d) {
			d.date_from = $("form[name=filter] input[name=from]").val();
			d.date_to = $("form[name=filter] input[name=to]").val();
		}
	},
	"aoColumns": [
		{ "bSort": true, "data": "created", class: "text-center" },
		{ "bSort": true, "data": "submited", class: "text-center" },

		{ "bSort": true, "data": "round", class: "text-center" },
		{ "bSort": true, "data": "weight_out_total", class: "text-center" },
		{ "bSort": true, "data": "weight_out_packing", class: "text-center" },
		{ "bSort": true, "data": "product_name", class: "text-left" },
		{ "bSort": true, "data": "remark", class: "text-center" },
		{ "bSortable": false, "data": "id", "sClass": "text-center", "sWidth": "120px" }
	], "order": [[1, "desc"]],
	"createdRow": function (row, data, index) {

		var s = '';
		if (data.status == "0") {
			s += fn.ui.button("btn btn-xs btn-outline-danger mr-1", "far fa-trash", "fn.app.production_pmr.pmr.remove(" + data[0] + ")");
			s += fn.ui.button("btn btn-xs btn-outline-warning mr-1", "far fa-thumbs-up", "fn.app.production_pmr.pmr.dialog_approve(" + data[0] + ")");
			s += fn.ui.button("btn btn-xs btn-outline-dark", "far fa-pen", "fn.navigate('production_pmr','section=prepare&id=" + data[0] + "')");
		} else {
			s += fn.ui.button("btn btn-xs btn-outline-dark mr-1", "far fa-eye", "fn.navigate('production_pmr','section=prepare&id=" + data[0] + "')");

			s += '<span class="badge badge-warning">Submited</span>';
		}
		$("td", row).eq(7).html(s);
	}
});

fn.app.production_pmr.pmr.load_data = function () {
	$.post("apps/production_pmr/xhr/action-load-data.php", { id: $("#tblPmrDetail").attr("data-id") }, function (json) {
		$("#amount_total").html(json.total);
		$("#amount_remain").html(json.remain);
		if (json.remain > 0) {
			$("#amount_remain").addClass("text-danger");
		} else {
			$("#amount_remain").removeClass("text-danger");
		}
	}, "json");

};
fn.app.production_pmr.pmr.load_data();