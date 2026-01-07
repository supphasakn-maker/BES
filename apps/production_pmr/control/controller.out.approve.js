fn.app.production_pmr.out.dialog_approve = function (id) {
    $.ajax({
        url: "apps/production_pmr/view/dialog.out.approve.php",
        data: { id: id },
        type: "POST",
        dataType: "html",
        success: function (html) {
            $("body").append(html);
            fn.ui.modal.setup({ dialog_id: "#dialog_approve_out" });
        }
    });
};

fn.app.production_pmr.out.approve = function () {
    $.post("apps/production_pmr/xhr/action-approve-out.php", $("form[name=form_approveout]").serialize(), function (response) {
        if (response.success) {
            $("#tblOut").DataTable().draw();
            $("#dialog_approve_out").modal("hide");
        } else {
            fn.notify.warnbox(response.msg, "Oops...");
        }
    }, "json");
    return false;
};
