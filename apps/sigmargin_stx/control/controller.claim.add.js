fn.app.sigmargin_stx.claim.dialog_add = function () {
	$.ajax({
		url: "apps/sigmargin_stx/view/dialog.claim.add.php",
		type: "POST",
		dataType: "html",
		success: function (html) {
			$("body").append(html);
			fn.ui.modal.setup({ dialog_id: "#dialog_add_claim" });
		}
	});
};

fn.app.sigmargin_stx.claim.add = function () {
	$.post("apps/sigmargin_stx/xhr/action-add-claim.php", $("form[name=form_addclaim]").serialize(), function (response) {
		if (response.success) {
			$("#tblClaim").DataTable().draw();
			$("#dialog_add_claim").modal("hide");
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
	onclick: "fn.app.sigmargin_stx.claim.dialog_add()",
	caption: "Add"
}));
