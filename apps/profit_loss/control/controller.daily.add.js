fn.app.profit_loss.daily.dialog_add = function () {
    $.ajax({
        url: "apps/profit_loss/view/dialog.dialy.add.php",
        type: "POST",
        dataType: "html",
        data: { date_filter: selectedDate },
        success: function (html) {
            $("body").append(html);
            fn.ui.modal.setup({ dialog_id: "#dialog_add_daily" });
        }
    });
};

fn.app.profit_loss.daily.add = function () {
    $.post("apps/profit_loss/xhr/action-add-dialy.php", $("form[name=form_adddaily]").serialize(), function (response) {
        if (response.success) {
            $("#tblNoted").DataTable().draw();
            $("form[name=form_adddaily]")[0].reset();
            $("#dialog_add_daily").modal("hide");
            fn.reload();
        } else {
            fn.notify.warnbox(response.msg, "Oops...");
        }
    }, "json");
    return false;
};
