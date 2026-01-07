fn.app.defer_spot.defer.dialog_edit = function (id) {
    $.ajax({
        url: "apps/defer_spot/view/dialog.defer.edit.php",
        data: { id: id },
        type: "POST",
        dataType: "html",
        success: function (html) {
            $("body").append(html);
            fn.ui.modal.setup({ dialog_id: "#dialog_edit_defer" });
        }
    });
};

fn.app.defer_spot.defer.edit = function () {
    $.post("apps/defer_spot/xhr/action-edit-defer.php", $("form[name=form_editdefer]").serialize(), function (response) {
        if (response.success) {
            $("#tblDefer").DataTable().draw();
            $("#dialog_edit_defer").modal("hide");
        } else {
            fn.notify.warnbox(response.msg, "Oops...");
        }
    }, "json");
    return false;
};
