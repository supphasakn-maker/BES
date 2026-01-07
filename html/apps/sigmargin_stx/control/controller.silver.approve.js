fn.app.sigmargin_stx.silver.dialog_approve = function (id) {
    $.ajax({
        url: "apps/sigmargin_stx/view/dialog.silver.approve.php",
        data: { id: id },
        type: "POST",
        dataType: "html",
        success: function (html) {
            $("body").append(html);
            fn.ui.modal.setup({ dialog_id: "#dialog_approve_silver" });
        }
    });
};

fn.app.sigmargin_stx.silver.approve = function () {
    $.post("apps/sigmargin_stx/xhr/action-approve-silver.php", $("form[name=form_approvesilver]").serialize(), function (response) {
        if (response.success) {
            $("#tblSilver").DataTable().draw();
            $("#dialog_approve_silver").modal("hide");
        } else {
            fn.notify.warnbox(response.msg, "Oops...");
        }
    }, "json");
    return false;
};
