fn.app.production_pmr.in.dialog_approve = function (id) {
    $.ajax({
        url: "apps/production_pmr/view/dialog.in.approve.php",
        data: { id: id },
        type: "POST",
        dataType: "html",
        success: function (html) {
            $("body").append(html);
            fn.ui.modal.setup({ dialog_id: "#dialog_approve_in" });
        }
    });
};

fn.app.production_pmr.in.approve = function () {
    $.post("apps/production_pmr/xhr/action-approve-in.php", $("form[name=form_approvein]").serialize(), function (response) {
        if (response.success) {
            $("#tblIn").DataTable().draw();
            $("#dialog_approve_in").modal("hide");
        } else {
            fn.notify.warnbox(response.msg, "Oops...");
        }
    }, "json");
    return false;
};
