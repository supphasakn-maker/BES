fn.app.defer_adjust.deposit.dialog_edit = function (id) {
    $.ajax({
        url: "apps/defer_adjust/view/dialog.deposit.edit.php",
        data: { id: id },
        type: "POST",
        dataType: "html",
        success: function (html) {
            $("body").append(html);
            fn.ui.modal.setup({ dialog_id: "#dialog_edit_deposit" });
        }
    });
};

fn.app.defer_adjust.deposit.edit = function () {
    $.post("apps/defer_adjust/xhr/action-edit-deposit.php", $("form[name=form_editdeposit]").serialize(), function (response) {
        if (response.success) {
            $("#tblDeposit").DataTable().draw();
            $("#dialog_edit_deposit").modal("hide");
        } else {
            fn.notify.warnbox(response.msg, "Oops...");
        }
    }, "json");
    return false;
};
