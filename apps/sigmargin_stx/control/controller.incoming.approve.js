fn.app.sigmargin_stx.incoming.dialog_approve = function (id) {
    $.ajax({
        url: "apps/sigmargin_stx/view/dialog.incoming.approve.php",
        data: { id: id },
        type: "POST",
        dataType: "html",
        success: function (html) {
            $("body").append(html);
            fn.ui.modal.setup({ dialog_id: "#dialog_approve_incoming" });
        }
    });
};

fn.app.sigmargin_stx.incoming.approve = function () {
    $.post("apps/sigmargin_stx/xhr/action-approve-incoming.php", $("form[name=form_approveincoming]").serialize(), function (response) {
        if (response.success) {
            $("#tblIncoming").DataTable().draw();
            $("#dialog_approve_incoming").modal("hide");
        } else {
            fn.notify.warnbox(response.msg, "Oops...");
        }
    }, "json");
    return false;
};
