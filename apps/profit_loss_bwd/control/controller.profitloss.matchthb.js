fn.app.profit_loss_bwd.profitloss.dialog_matchthb = function () {
    var sales_selected = $("#tblSales").data("selected");
    var date_filter = $('#tblSales_length input[name=date_filter]').val();

    $.ajax({
        url: "apps/profit_loss_bwd/view/dialog.silver.matchthb.php",
        data: {
            sales: sales_selected,
            date_filter: date_filter
        },
        type: "POST",
        dataType: "html",
        success: function (html) {
            $("body").append(html);
            fn.ui.modal.setup({ dialog_id: "#dialog_match_silver" });
            fn.app.profit_loss_bwd.profitloss.dialog_matchthb_calculation();
            fn.app.profit_loss_bwd.profitloss.dialog_matchthb_calculationtotal();
        }
    });
};
fn.app.profit_loss_bwd.profitloss.dialog_matchthb_calculation = function () {
    let total_order_amount = 0;
    $("[xname=order_amount]").each(function () {
        total_order_amount += parseFloat($(this).val());
    });
    $("#silver_total_match_order").html(fn.ui.numberic.format(total_order_amount, 4));

}

fn.app.profit_loss_bwd.profitloss.dialog_matchthb_calculationtotal = function () {
    let total_order_total = 0;
    $("[xname=order_total]").each(function () {
        total_order_total += parseFloat($(this).val());
    });
    $("#silver_total_match_order_total").html(fn.ui.numberic.format(total_order_total, 4));

}

fn.app.profit_loss_bwd.profitloss.matchthb = function () {
    $.post("apps/profit_loss_bwd/xhr/action-match-sumorders.php", $("form[name=form_matchsilver]").serialize(), function (response) {
        if (response.success) {
            $("#tblSales").data("selected", []);
            $("#tblSales").DataTable().draw();
            $("#tblLoss").DataTable().draw();
            $("#dialog_match_silver").modal("hide");
        } else {
            fn.notify.warnbox(response.msg, "Oops...");
        }
    }, "json");
    return false;
};