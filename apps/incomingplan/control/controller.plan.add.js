fn.app.incomingplan.plan.dialog_add = function () {
	$.ajax({
		url: "apps/incomingplan/view/dialog.plan.add.php",
		type: "POST",
		dataType: "html",
		success: function (html) {
			$("body").append(html);
			fn.ui.modal.setup({ dialog_id: "#dialog_add_plan" });
			$("select[name=import_id]").unbind()
			$("select[name=import_id]").select2();
			$("select[name=import_id]").change(function () {
				$.post("apps/incomingplan/xhr/action-load-reserve.php", { id: $(this).val() }, function (json) {
					$("input[name=amount]").val(json.weight_lock);
					$("input[name=supplier_id]").val(json.supplier_id);
					$("input[name=import_brand]").val(json.brand);
					$("input[name=import_date]").val(json.lock_date);
				}, "json");
			});
		}
	});
};

fn.app.incomingplan.plan.add = function () {
	$.post("apps/incomingplan/xhr/action-add-plan.php", $("form[name=form_addplan]").serialize(), function (response) {
		if (response.success) {
			$("#tblPlan").DataTable().draw();
			$("#dialog_add_plan").modal("hide");
			fn.reload();
		} else {
			fn.notify.warnbox(response.msg, "Oops...");
		}
	}, "json");
	return false;
};
$(".btn-area").append(fn.ui.button({
	class_name: "btn btn-light has-icon",
	icon_type: "material",
	icon: "add_circle_outline",
	onclick: "fn.app.incomingplan.plan.dialog_add()",
	caption: "Add"
}));
