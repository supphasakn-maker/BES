fn.app.production_switch.switch.dialog_add = function () {
    $.ajax({
        url: "apps/production_switch/view/dialog.switch.add.php",
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
            $("form[name=form_addswitch] select[name=round_id]").select2();
            $("form[name=form_addswitch] select[name=round_id]").unbind().change(function () {
                $.post("apps/production_switch/xhr/action-load-round.php", { round_id: $(this).val() }, function (response) {
                    $("form[name=form_addswitch] input[name=import_lot]").val(response.round_id.round);
                    $("form[name=form_addswitch] input[name=product_type_id]").val(response.round_id.product_id);
                }, "json");
            })

            $("form[name=form_addswitch] select[name=round_turn]").select2();
            $("form[name=form_addswitch] select[name=round_turn]").unbind().change(function () {
                $.post("apps/production_switch/xhr/action-load-round-turn.php", { round_turn: $(this).val() }, function (response) {
                    $("form[name=form_addswitch] input[name=round_turn_id]").val(response.round_turn.import_lot);
                    $("form[name=form_addswitch] input[name=amount_balance]").val(response.round_turn.amount_balance);
                    $("form[name=form_addswitch] input[name=product_id_turn]").val(response.round_turn.product_type_id);
                }, "json");
            }).change();

        }
    });
};


fn.app.production_switch.switch.add = function (id) {
    $.post("apps/production_switch/xhr/action-add-switch.php", $("form[name=form_addswitch]").serialize(), function (response) {
        if (response.success) {
            $("#tblSwitch").DataTable().draw();
            $("#dialog_add_switch").modal("hide");
        } else {
            fn.notify.warnbox(response.msg, "Oops...");
        }
    }, "json");
    return false;
};

$(".btn-area").append(fn.ui.button({
    class_name: "btn btn-light has-icon",
    icon_type: "material",
    icon: "add_circle_outline",
    onclick: "fn.app.production_switch.switch.dialog_add()",
    caption: "Add"
}));
