fn.app.sales.quick_order.dialog_transform = function (id) {
	$.ajax({
		url: "apps/sales/view/dialog.quick_order.transform.php",
		data: { id: id },
		type: "POST",
		dataType: "html",
		success: function (html) {
			$("body").append(html);
			fn.ui.modal.setup({ dialog_id: "#dialog_transform_quick_order" });
		}
	});
};

fn.app.sales.quick_order.transform = function (id) {
	$.post("apps/sales/xhr/action-transform-quick_order.php", $("form[name=form_transformquick_order]").serialize(), function (response) {
		if (response.success) {
			$("#tblQuick_order").DataTable().draw();
			$("#tblQuickOrder").DataTable().draw();
			$("#tblDailyTable").DataTable().draw();
			$("#dialog_transform_quick_order").modal("hide");
			bootbox.alert("Order Created<br><a href='#apps/schedule/index.php?view=printable&order_id=" + response.order_id + "' target='_blank'>Click to Print</a>");
		} else {
			fn.notify.warnbox(response.msg, "Oops...");
		}
	}, "json");
	return false;
};
