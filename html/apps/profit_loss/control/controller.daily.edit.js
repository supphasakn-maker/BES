fn.app.profit_loss.daily.dialog_edit = function (id) {
    $.ajax({
        url: "apps/profit_loss/view/dialog.edit_daily.php",
        data: { id: id },
        type: "POST",
        dataType: "html",
        success: function (html) {
            $("body").append(html);
            fn.ui.modal.setup({ dialog_id: "#dialog_edit_daily" });
        }
    });
};

fn.app.profit_loss.daily.edit = function () {
    $.post("apps/profit_loss/xhr/action-edit-daily.php", $("form[name=form_editdialy]").serialize(), function (response) {
        if (response.success) {
            $("#tblNoted").DataTable().draw();
            $("#dialog_edit_daily").modal("hide");
            fn.reload();
        } else {
            fn.notify.warnbox(response.msg, "Oops...");
        }
    }, "json");
    return false;
};
