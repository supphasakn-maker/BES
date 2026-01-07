fn.app.sales.order.dialog_remove = function () {
	var item_selected = $("#tblOrder").data("selected");
	$.ajax({
		url: "apps/sales/view/dialog.order.remove.php",
		data: { items: item_selected },
		type: "POST",
		dataType: "html",
		success: function (html) {

			if (html.indexOf('alert-danger') !== -1 && html.indexOf('ไม่มีสิทธิ์') !== -1) {

				if (typeof fn.notify !== 'undefined' && fn.notify.warnbox) {
					fn.notify.warnbox('คุณไม่มีสิทธิ์ในการลบ Order กรุณาติดต่อผู้ดูแลระบบ', 'ไม่มีสิทธิ์');
				} else {
					alert('คุณไม่มีสิทธิ์ในการลบ Order');
				}
				return;
			}

			$("body").append(html);
			$("#dialog_remove_order").on("hidden.bs.modal", function () {
				$(this).remove();
			});
			$("#dialog_remove_order").modal("show");
			$("#dialog_remove_order .btnConfirm").click(function () {
				fn.app.sales.order.remove();
			});
		},
		error: function (xhr, status, error) {
			console.error('Failed to load dialog:', error);
			if (typeof fn.notify !== 'undefined' && fn.notify.warnbox) {
				fn.notify.warnbox('ไม่สามารถโหลดหน้าต่างได้: ' + error, 'Error');
			} else {
				alert('Failed to load dialog: ' + error);
			}
		}
	});
};

fn.app.sales.order.remove = function (id) {

	if (typeof id != "undefined") {
		fn.dialog.confirmbox("Confirmation", "Are you sure to remove this item?", function () {
			$.post("apps/sales/xhr/action-remove-order.php", { item: id }, function (response) {
				if (response.success) {
					$("#tblOrder").DataTable().draw();
					fn.notify.successbox(response.msg || "Remove Success", "Success");
				} else {
					fn.notify.warnbox(response.msg || "Failed to remove order", "Error");
				}
			}, "json").fail(function (xhr, status, error) {
				fn.notify.warnbox('เกิดข้อผิดพลาด: ' + error, "Error");
			});
		});
	} else {
		var item_selected = $("#tblOrder").data("selected");
		$.post("apps/sales/xhr/action-remove-order.php", { items: item_selected }, function (response) {
			if (response.success) {
				$("#tblOrder").data("selected", []);
				$("#tblOrder").DataTable().draw();
				$("#dialog_remove_order").modal("hide");
				fn.notify.successbox(response.msg || "Remove Success", "Success");
			} else {
				fn.notify.warnbox(response.msg || "Failed to remove order", "Error");
			}
		}, "json").fail(function (xhr, status, error) {
			fn.notify.warnbox('เกิดข้อผิดพลาด: ' + error, "Error");
		});
	}
};

fn.app.sales.order.dialog_soft_remove = function () {
	var item_selected = $("#tblOrder").data("selected");
	$.ajax({
		url: "apps/sales/view/dialog.order.remove.php",
		data: { item: item_selected },
		type: "POST",
		dataType: "html",
		success: function (html) {

			if (html.indexOf('alert-danger') !== -1 && html.indexOf('ไม่มีสิทธิ์') !== -1) {

				if (typeof fn.notify !== 'undefined' && fn.notify.warnbox) {
					fn.notify.warnbox('คุณไม่มีสิทธิ์ในการลบ Order', 'ไม่มีสิทธิ์');
				} else {
					alert('คุณไม่มีสิทธิ์ในการลบ Order');
				}
				return;
			}

			$("body").append(html);
			$("#dialog_remove_order").on("hidden.bs.modal", function () {
				$(this).remove();
			});
			$("#dialog_remove_order").modal("show");
			$("#dialog_remove_order .btnConfirm").click(function () {
				fn.app.sales.order.remove();
			});
		},
		error: function (xhr, status, error) {
			if (typeof fn.notify !== 'undefined' && fn.notify.warnbox) {
				fn.notify.warnbox('ไม่สามารถโหลดหน้าต่างได้: ' + error, 'Error');
			} else {
				alert('Failed to load dialog: ' + error);
			}
		}
	});
};

fn.app.sales.order.remove_soft = function (id) {

	if (typeof id != "undefined") {
		fn.dialog.confirmbox("Confirmation", "Are you sure to remove this item?", function () {
			$.post("apps/sales/xhr/action-remove-order.php", { item: id }, function (response) {
				if (response.success) {
					$("#tblOrder").DataTable().draw();
					fn.notify.successbox(response.msg || "Remove Success", "Success");
				} else {
					fn.notify.warnbox(response.msg || "Failed to remove order", "Error");
				}
			}, "json").fail(function (xhr, status, error) {
				fn.notify.warnbox('เกิดข้อผิดพลาด: ' + error, "Error");
			});
		});
	} else {
		var item_selected = $("#tblOrder").data("selected");
		$.post("apps/sales/xhr/action-remove-order.php", { items: item_selected }, function (response) {
			if (response.success) {
				$("#tblOrder").data("selected", []);
				$("#tblOrder").DataTable().draw();
				$("#dialog_remove_order").modal("hide");
				fn.notify.successbox(response.msg || "Remove Success", "Success");
			} else {
				fn.notify.warnbox(response.msg || "Failed to remove order", "Error");
			}
		}, "json").fail(function (xhr, status, error) {
			fn.notify.warnbox('เกิดข้อผิดพลาด: ' + error, "Error");
		});
	}
};

$(".btn-area").append(fn.ui.button({
	class_name: "btn btn-light has-icon",
	icon_type: "material",
	icon: "delete",
	onclick: "fn.app.sales.order.dialog_remove()",
	caption: "Remove"
}));