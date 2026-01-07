fn.app.production_silverplate.prepare.dialog_add = function () {
	$.ajax({
		url: "apps/production_silverplate/view/dialog.prepare.add.php",
		type: "POST",
		dataType: "html",
		success: function (html) {
			$("body").append(html);
			fn.ui.modal.setup({ dialog_id: "#dialog_add_prepare" });
			$("[name=form_addprepare] [name=amount]").change(function () {
				var amount = parseFloat($("[name=form_addprepare] [name=amount]").val());
				var amount_balance = parseFloat($("[name=form_addprepare] [name=amount_balance]").val());
				var balance = amount_balance - amount;
				$("[name=form_addprepare] [name=balance]").val(balance.toFixed(4));
			});

			$("form[name=form_addprepare] select[name=round_id]").select2();
			$("form[name=form_addprepare] select[name=round_id]").unbind().change(function () {
				$.post("apps/production_silverplate/xhr/action-load-round.php", { round_id: $(this).val() }, function (response) {
					$("form[name=form_addprepare] input[name=import_lot]").val(response.round_id.import_lot);
					$("form[name=form_addprepare] input[name=amount_balance]").val(response.round_id.amount_balance);
					$("form[name=form_addprepare] input[name=product_type_id]").val(response.round_id.product_type_id);
					$("form[name=form_addprepare] input[name=PMR]").val(response.round_id.factory);
				}, "json");
			}).change();
		}
	});
};

fn.app.production_silverplate.prepare.add = function () {
	$.post("apps/production_silverplate/xhr/action-add-prepare.php", $("form[name=form_addprepare]").serialize(), function (response) {
		if (response.success) {
			$("#tblPrepare").DataTable().draw();
			$("#dialog_add_prepare").modal("hide");


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
	onclick: "fn.app.production_silverplate.prepare.dialog_add()",
	caption: "Add"
}));
