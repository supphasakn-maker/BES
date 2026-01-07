fn.app.profit_loss.daily.dialog_edit_fx = function (id) {
    $.ajax({
        url: "apps/profit_loss/view/dialog.usd.edit_daily.php",
        data: { id: id },
        type: "POST",
        dataType: "html",
        success: function (html) {
            $("body").append(html);
            fn.ui.modal.setup({ dialog_id: "#dialog_edit_usd" });
        }
    });
};

fn.app.profit_loss.daily.edit_fx = function () {
    $.post("apps/profit_loss/xhr/action-edit-usd-daily.php", $("form[name=form_editusd]").serialize(), function (response) {
        if (response.success) {
            $("#tblFX").DataTable().draw();
            $("#dialog_edit_usd").modal("hide");
            fn.reload();
        } else {
            fn.notify.warnbox(response.msg, "Oops...");
        }
    }, "json");
    return false;
};
