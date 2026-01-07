fn.app.sigmargin_stx.silver.dialog_edit = function (id) {
    $.ajax({
        url: "apps/sigmargin_stx/view/dialog.silver.edit.php",
        data: { id: id },
        type: "POST",
        dataType: "html",
        success: function (html) {
            $("body").append(html);
            fn.ui.modal.setup({ dialog_id: "#dialog_edit_silver" });
        }
    });
};

fn.app.sigmargin_stx.silver.edit = function () {
    $.post("apps/sigmargin_stx/xhr/action-edit-silver.php", $("form[name=form_editsilver]").serialize(), function (response) {
        if (response.success) {
            $("#tblSilver").DataTable().draw();
            $("#dialog_edit_silver").modal("hide");
        } else {
            fn.notify.warnbox(response.msg, "Oops...");
        }
    }, "json");
    return false;
};
