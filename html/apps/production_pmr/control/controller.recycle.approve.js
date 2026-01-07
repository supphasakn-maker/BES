fn.app.production_pmr.recycle.dialog_approve = function (id) {
    $.ajax({
        url: "apps/production_pmr/view/dialog.recycle.approve.php",
        data: { id: id },
        type: "POST",
        dataType: "html",
        success: function (html) {
            $("body").append(html);
            fn.ui.modal.setup({ dialog_id: "#dialog_approve_recycle" });
        }
    });
};

fn.app.production_pmr.recycle.approve = function () {
    $.post("apps/production_pmr/xhr/action-approve-recycle.php", $("form[name=form_approverecycle]").serialize(), function (response) {
        if (response.success) {
            $("#tblRecycle").DataTable().draw();
            $("#dialog_approve_recycle").modal("hide");
        } else {
            fn.notify.warnbox(response.msg, "Oops...");
        }
    }, "json");
    return false;
};
