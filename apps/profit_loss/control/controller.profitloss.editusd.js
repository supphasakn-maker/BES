fn.app.profit_loss.profitloss.dialog_editusd = function (id) {
    $.ajax({
        url: "apps/profit_loss/view/dialog.usd.edit.php",
        data: { id: id },
        type: "POST",
        dataType: "html",
        success: function (html) {
            $("body").append(html);
            fn.ui.modal.setup({ dialog_id: "#dialog_edit_usd" });
        }
    });
};

fn.app.profit_loss.profitloss.editusd = function () {
    $.post("apps/profit_loss/xhr/action-edit-usd.php", $("form[name=form_editusd]").serialize(), function (response) {
        if (response.success) {
            $("#tblPurchaseUSDtrue").DataTable().draw();
            $("#dialog_edit_usd").modal("hide");
        } else {
            fn.notify.warnbox(response.msg, "Oops...");
        }
    }, "json");
    return false;
};
