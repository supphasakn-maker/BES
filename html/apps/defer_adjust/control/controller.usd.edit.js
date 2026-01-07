fn.app.defer_adjust.usd.dialog_edit = function (id) {
    $.ajax({
        url: "apps/defer_adjust/view/dialog.usd.edit.php",
        data: { id: id },
        type: "POST",
        dataType: "html",
        success: function (html) {
            $("body").append(html);
            fn.ui.modal.setup({ dialog_id: "#dialog_edit_usd" });
        }
    });
};

fn.app.defer_adjust.usd.edit = function () {
    $.post("apps/defer_adjust/xhr/action-edit-usd.php", $("form[name=form_editusd]").serialize(), function (response) {
        if (response.success) {
            $("#tblUSD").DataTable().draw();
            $("#dialog_edit_usd").modal("hide");
        } else {
            fn.notify.warnbox(response.msg, "Oops...");
        }
    }, "json");
    return false;
};
