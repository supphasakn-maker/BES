fn.app.rate_exchange.master.dialog_change_exchange_sigmargin = function (id) {
    $.ajax({
        url: "apps/rate_exchange/view/dialog.master.change_exchange_sigmargin.php",
        data: { id: id },
        type: "POST",
        dataType: "html",
        success: function (html) {
            $("body").append(html);
            fn.ui.modal.setup({ dialog_id: "#dialog_change_exchange_sigmargin" });
        }
    });
};

fn.app.rate_exchange.master.change_exchange_sigmargin = function () {
    $.post("apps/rate_exchange/xhr/action-change_exchange_sigmargin.php", $("form[name=form_change_sigmargin]").serialize(), function (response) {
        if (response.success) {
            $("#tblMaster").DataTable().draw();
            $("#dialog_change_exchange_sigmargin").modal("hide");
            fn.reload();
        } else {
            fn.notify.warnbox(response.msg, "Oops...");
        }
    }, "json");
    return false;
};
