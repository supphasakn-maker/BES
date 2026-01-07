fn.app.profit_loss.daily.dialog_add_spot = function () {
    $.ajax({
        url: "apps/profit_loss/view/dialog.spot.add_daily.php",
        type: "POST",
        dataType: "html",
        data: { date_filter: selectedDate },
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

fn.app.profit_loss.daily.add_spot = function () {
    $.post("apps/profit_loss/xhr/action-add-spot-daily.php", $("form[name=form_addspot]").serialize(), function (response) {
        if (response.success) {
            $("#tblSPOT").DataTable().draw();
            $("form[name=form_addspot]")[0].reset();
            $("#dialog_add_spot").modal("hide");
            fn.reload();
        } else {
            fn.notify.warnbox(response.msg, "Oops...");
        }
    }, "json");
    return false;
};

