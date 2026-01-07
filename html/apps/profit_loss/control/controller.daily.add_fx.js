fn.app.profit_loss.daily.dialog_add_fx = function () {
    $.ajax({
        url: "apps/profit_loss/view/dialog.usd.add_daily.php",
        type: "POST",
        dataType: "html",
        data: { date_filter: selectedDate },
        success: function (html) {
            $("body").append(html);
            fn.ui.modal.setup({ dialog_id: "#dialog_add_usd" });
        }
    });
};

fn.app.profit_loss.daily.add_fx = function () {
    $.post("apps/profit_loss/xhr/action-add-usd-daily.php", $("form[name=form_addusd]").serialize(), function (response) {
        if (response.success) {
            $("#tblFX").DataTable().draw();
            $("form[name=form_addusd]")[0].reset();
            $("#dialog_add_usd").modal("hide");
             fn.reload();
        } else {
            fn.notify.warnbox(response.msg, "Oops...");
        }
    }, "json");
    return false;
};
