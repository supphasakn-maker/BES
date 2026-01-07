fn.app.sigmargin_stx.claim.dialog_edit = function (id) {
    $.ajax({
        url: "apps/sigmargin_stx/view/dialog.claim.edit.php",
        data: { id: id },
        type: "POST",
        dataType: "html",
        success: function (html) {
            $("body").append(html);
            fn.ui.modal.setup({ dialog_id: "#dialog_edit_claim" });
        }
    });
};

fn.app.sigmargin_stx.claim.edit = function () {
    $.post("apps/sigmargin_stx/xhr/action-edit-claim.php", $("form[name=form_editclaim]").serialize(), function (response) {
        if (response.success) {
            $("#tblClaim").DataTable().draw();
            $("#dialog_edit_claim").modal("hide");
        } else {
            fn.notify.warnbox(response.msg, "Oops...");
        }
    }, "json");
    return false;
};
