fn.app.sigmargin_stx.int_rate.dialog_edit = function (id) {
    $.ajax({
        url: "apps/sigmargin_stx/view/dialog.int_rate.edit.php",
        data: { id: id },
        type: "POST",
        dataType: "html",
        success: function (html) {
            $("body").append(html);
            fn.ui.modal.setup({ dialog_id: "#dialog_edit_int_rate" });
        }
    });
};

fn.app.sigmargin_stx.int_rate.edit = function () {
    $.post("apps/sigmargin_stx/xhr/action-edit-int_rate.php", $("form[name=form_editint_rate]").serialize(), function (response) {
        if (response.success) {
            $("#tblInt_rate").DataTable().draw();
            $("#dialog_edit_int_rate").modal("hide");
        } else {
            fn.notify.warnbox(response.msg, "Oops...");
        }
    }, "json");
    return false;
};
