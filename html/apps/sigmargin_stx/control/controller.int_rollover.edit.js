fn.app.sigmargin_stx.int_rollover.dialog_edit = function (id) {
    $.ajax({
        url: "apps/sigmargin_stx/view/dialog.int_rollover.edit.php",
        data: { id: id },
        type: "POST",
        dataType: "html",
        success: function (html) {
            $("body").append(html);
            fn.ui.modal.setup({ dialog_id: "#dialog_edit_int_rollover" });
        }
    });
};

fn.app.sigmargin_stx.int_rollover.edit = function () {
    $.post("apps/sigmargin_stx/xhr/action-edit-int_rollover.php", $("form[name=form_editint_rollover]").serialize(), function (response) {
        if (response.success) {
            $("#tblInt_rollover").DataTable().draw();
            $("#dialog_edit_int_rollover").modal("hide");
        } else {
            fn.notify.warnbox(response.msg, "Oops...");
        }
    }, "json");
    return false;
};
