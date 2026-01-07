fn.app.production_switch.switch.dialog_edit = function (id) {
    $.ajax({
        url: "apps/production_switch/view/dialog.switch.turn.php",
        data: { id: id },
        type: "POST",
        dataType: "html",
        success: function (html) {
            $("body").append(html);
            fn.ui.modal.setup({ dialog_id: "#dialog_add_switch" });
            $("[name=form_addswitch] [name=amount]").change(function () {
                var amount = parseFloat($("[name=form_addswitch] [name=amount]").val());
                var amount_balance = parseFloat($("[name=form_addswitch] [name=amount_balance]").val());
                var balance = amount_balance - amount;
                $("[name=form_addswitch] [name=balance]").val(balance.toFixed(4));
            });
        }

    });
};

fn.app.production_switch.switch.edit = function () {
    $.post("apps/production_switch/xhr/action-switch-turn.php", $("form[name=form_addswitch]").serialize(), function (response) {
        if (response.success) {
            $("#tblSwitch").DataTable().draw();
            $("#dialog_add_switch").modal("hide");
        } else {
            fn.notify.warnbox(response.msg, "Oops...");
        }
    }, "json");
    return false;
};
