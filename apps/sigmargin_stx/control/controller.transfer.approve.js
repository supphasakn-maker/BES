fn.app.sigmargin_stx.transfer.dialog_approve = function (id) {
    $.ajax({
        url: "apps/sigmargin_stx/view/dialog.transfer.approve.php",
        data: { id: id },
        type: "POST",
        dataType: "html",
        success: function (html) {
            $("body").append(html);
            fn.ui.modal.setup({ dialog_id: "#dialog_approve_transfer" });
        }
    });
};

fn.app.sigmargin_stx.transfer.approve = function () {
    $.post("apps/sigmargin_stx/xhr/action-approve-transfer.php", $("form[name=form_approvetransfer]").serialize(), function (response) {
        if (response.success) {
            $("#tblTransfer").DataTable().draw();
            $("#dialog_approve_transfer").modal("hide");
        } else {
            fn.notify.warnbox(response.msg, "Oops...");
        }
    }, "json");
    return false;
};
