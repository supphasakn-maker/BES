fn.app.purchase.usd.dialog_edit_usd = function (id) {
    $.ajax({
        url: "apps/purchase/view/dialog.usd.edit_usd.php",
        data: { id: id },
        type: "POST",
        dataType: "html",
        success: function (html) {
            $("body").append(html);
            fn.ui.modal.setup({ dialog_id: "#dialog_edit_usd" });
        }
    });
};

fn.app.purchase.usd.edit_usd = function () {
    $.post("apps/purchase/xhr/action-edit-usd_finance.php", $("form[name=form_editusd]").serialize(), function (response) {
        if (response.success) {
            $("#tblUsd").DataTable().draw();
            $("#tblPurchase").DataTable().draw();
            $("#tblPending").DataTable().draw();
            $("#dialog_edit_usd").modal("hide");
        } else {
            fn.notify.warnbox(response.msg, "Oops...");
        }
    }, "json");
    return false;
};
