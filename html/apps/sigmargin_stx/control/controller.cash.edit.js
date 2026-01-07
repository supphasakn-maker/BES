fn.app.sigmargin_stx.cash.dialog_edit = function (id) {
    $.ajax({
        url: "apps/sigmargin_stx/view/dialog.cash.edit.php",
        data: { id: id },
        type: "POST",
        dataType: "html",
        success: function (html) {
            $("body").append(html);
            fn.ui.modal.setup({ dialog_id: "#dialog_edit_cash" });
        }
    });
};

fn.app.sigmargin_stx.cash.edit = function () {
    $.post("apps/sigmargin_stx/xhr/action-edit-cash.php", $("form[name=form_editcash]").serialize(), function (response) {
        if (response.success) {
            $("#tblCash").DataTable().draw();
            $("#dialog_edit_cash").modal("hide");
        } else {
            fn.notify.warnbox(response.msg, "Oops...");
        }
    }, "json");
    return false;
};
