fn.app.holiday_announce.holiday.dialog_edit = function (id) {
    $.ajax({
        url: "apps/holiday_announce/view/dialog.holiday_announce.edit.php",
        data: { id: id },
        type: "POST",
        dataType: "html",
        success: function (html) {
            $("body").append(html);
            fn.ui.modal.setup({ dialog_id: "#dialog_edit_announce_silver" });
        }
    });
};

fn.app.holiday_announce.holiday.edit = function () {
    $.post("apps/holiday_announce/xhr/action-edit-holiday_announce.php", $("form[name=form_edit_holiday]").serialize(), function (response) {
        if (response.success) {
            $("#tblSilver").DataTable().draw();
            $("#dialog_edit_holiday_announce").modal("hide");
        } else {
            fn.notify.warnbox(response.msg, "Oops...");
        }
    }, "json");
    return false;
};
