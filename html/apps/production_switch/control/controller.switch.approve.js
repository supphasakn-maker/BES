fn.app.production_switch.switch.dialog_approve = function (id) {
    $.ajax({
        url: "apps/production_switch/view/dialog.switch.approve.php",
        data: { id: id },
        type: "POST",
        dataType: "html",
        success: function (html) {
            $("body").append(html);
            fn.ui.modal.setup({ dialog_id: "#dialog_approve_switch" });
        }
    });
};

fn.app.production_switch.switch.approve = function () {
    $.post("apps/production_switch/xhr/action-approve-switch.php", $("form[name=form_approveswitch]").serialize(), function (response) {
        if (response.success) {
            $("#tblSwitch").DataTable().draw();
            $("#dialog_approve_switch").modal("hide");
        } else {
            fn.notify.warnbox(response.msg, "Oops...");
        }
    }, "json");
    return false;
};
