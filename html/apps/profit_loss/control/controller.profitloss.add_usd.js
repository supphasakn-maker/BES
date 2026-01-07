fn.app.profit_loss.profitloss.dialog_add_usd = function () {
    var date_filter = $('#tblSales_length input[name=date_filter]').val();
    $.ajax({
        url: "apps/profit_loss/view/dialog.usd.add.php",
        type: "POST",
        dataType: "html",
        data: { date_filter: date_filter },
        success: function (html) {
            $("body").append(html);
            fn.ui.modal.setup({ dialog_id: "#dialog_add_usd" });
        }
    });
};

fn.app.profit_loss.profitloss.add_usd = function () {
    $.post("apps/profit_loss/xhr/action-add-usd.php", $("form[name=form_addusd]").serialize(), function (response) {
        if (response.success) {
            $("#tblPurchaseUSDtrue").DataTable().draw();
            $("form[name=form_addusd]")[0].reset();
            $("#dialog_add_usd").modal("hide");
        } else {
            fn.notify.warnbox(response.msg, "Oops...");
        }
    }, "json");
    return false;
};
$(".btn-area-usd").append(fn.ui.button({
    class_name: "btn btn-light has-icon",
    icon_type: "material",
    icon: "add_circle_outline",
    onclick: "fn.app.profit_loss.profitloss.dialog_add_usd()",
    caption: "Add USD"
}));
