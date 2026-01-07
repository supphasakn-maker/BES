fn.app.forward_contract.contract.dialog_edit_trade = function (id) {
    $.ajax({
        url: "apps/forward_contract/view/dialog.contract.edit_trade.php",
        data: { id: id },
        type: "POST",
        dataType: "html",
        success: function (html) {
            $("body").append(html);
            fn.ui.modal.setup({ dialog_id: "#dialog_edit_trade" });
        }
    });
};

fn.app.forward_contract.contract.edit_trade = function () {
    $.post("apps/forward_contract/xhr/action-edit_trade.php", $("form[name=form_edit_trade]").serialize(), function (response) {
        if (response.success) {
            $("#tblContract").DataTable().draw();
            $("#dialog_edit_trade").modal("hide");
        } else {
            fn.notify.warnbox(response.msg, "Oops...");
        }
    }, "json");
    return false;
};
