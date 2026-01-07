fn.app.sigmargin_stx.daily.dialog_edit = function (id) {
    $.ajax({
        url: "apps/sigmargin_stx/view/dialog.daily.edit.php",
        data: { id: id },
        type: "POST",
        dataType: "html",
        success: function (html) {
            $("body").append(html);
            fn.ui.modal.setup({ dialog_id: "#dialog_edit_daily" });
        }
    });
};

fn.app.sigmargin_stx.daily.edit = function () {
    $.post("apps/sigmargin_stx/xhr/action-edit-daily.php", $("form[name=form_editdaily]").serialize(), function (response) {
        if (response.success) {
            $("#tblDaily").DataTable().draw();
            $("#dialog_edit_daily").modal("hide");
        } else {
            fn.notify.warnbox(response.msg, "Oops...");
        }
    }, "json");
    return false;
};
