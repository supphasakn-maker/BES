fn.app.profit_loss.profitloss.dialog_add_spot = function () {
    var date_filter = $('#tblSales_length input[name=date_filter]').val();
    $.ajax({
        url: "apps/profit_loss/view/dialog.spot.add.php",
        type: "POST",
        dataType: "html",
        data: { date_filter: date_filter },
        success: function (html) {
            $("body").append(html);
            fn.ui.modal.setup({ dialog_id: "#dialog_add_spot" });

            $("form[name=form_addspot] select[name=currency]").change(function () {
                if ($(this).val() == "USD") {
                    $("form[name=form_addspot] input[name=rate_spot]").parent().parent().show();
                    $("form[name=form_addspot] input[name=THBValue]").parent().parent().hide();
                } else {
                    $("form[name=form_addspot] input[name=rate_spot]").parent().parent().hide();
                    $("form[name=form_addspot] input[name=THBValue]").parent().parent().show();
                }
            });

            $("form[name=form_addspot] select[name=supplier_id]").change(function () {
                $.post("apps/supplier/xhr/action-load-supplier.php", { id: $(this).val() }, function (supplier) {
                    if (supplier.type == "1") {
                        $("form[name=form_addspot] select[name=currency]").val("USD").change();
                    } else if (supplier.type == "2") {
                        $("form[name=form_addspot] select[name=currency]").val("THB").change();
                    }
                }, "json");
            }).change();
        }
    });
};

fn.app.profit_loss.profitloss.add_spot = function () {
    $.post("apps/profit_loss/xhr/action-add-spot.php", $("form[name=form_addspot]").serialize(), function (response) {
        if (response.success) {
            $("#tblPurchaseSpot").DataTable().draw();
            $("form[name=form_addspot]")[0].reset();
            $("#dialog_add_spot").modal("hide");
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
    onclick: "fn.app.profit_loss.profitloss.dialog_add_spot()",
    caption: "Add Spot"
}));