fn.app.sigmargin_stx.ohter.dialog_edit = function (id) {
    $.ajax({
        url: "apps/sigmargin_stx/view/dialog.ohter.edit.php",
        data: { id: id },
        type: "POST",
        dataType: "html",
        success: function (html) {
            $("body").append(html);
            fn.ui.modal.setup({ dialog_id: "#dialog_edit_ohter" });
        }
    });
};

fn.app.sigmargin_stx.ohter.edit = function () {
    $.post("apps/sigmargin_stx/xhr/action-edit-ohter.php", $("form[name=form_editohter]").serialize(), function (response) {
        if (response.success) {
            $("#tblOhter").DataTable().draw();
            $("#dialog_edit_ohter").modal("hide");
        } else {
            fn.notify.warnbox(response.msg, "Oops...");
        }
    }, "json");
    return false;
};
