fn.app.defer_adjust.physical.dialog_edit = function (id) {
    $.ajax({
        url: "apps/defer_adjust/view/dialog.physical_adjust.edit.php",
        data: { id: id },
        type: "POST",
        dataType: "html",
        success: function (html) {
            $("body").append(html);
            fn.ui.modal.setup({ dialog_id: "#dialog_edit_physical_adjust" });
        }
    });
};

fn.app.defer_adjust.physical.edit = function () {
    $.post("apps/defer_adjust/xhr/action-edit-physical_adjust.php", $("form[name=form_editphysical_adjust]").serialize(), function (response) {
        if (response.success) {
            $("#tblPhysical").DataTable().draw();
            $("#dialog_edit_physical_adjust").modal("hide");
        } else {
            fn.notify.warnbox(response.msg, "Oops...");
        }
    }, "json");
    return false;
};
