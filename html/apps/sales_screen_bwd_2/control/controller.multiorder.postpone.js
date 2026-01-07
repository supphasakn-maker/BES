fn.app.sales_screen_bwd_2.multiorder.dialog_postpone = function (id) {
    $.ajax({
        url: "apps/sales_screen_bwd_2/view/dialog.order.postpone.php",
        data: { id: id },
        type: "POST",
        dataType: "html",
        success: function (html) {
            $("body").append(html);
            fn.ui.modal.setup({ dialog_id: "#dialog_postpone_order" });
        }
    });
};

fn.app.sales_screen_bwd_2.multiorder.postpone = function () {
    $.post("apps/sales_screen_bwd_2/xhr/action-postpone-order.php", $("form[name=form_postponeorder]").serialize(), function (response) {
        if (response.success) {
            $("#tblOrder").DataTable().draw();
            $("#dialog_postpone_order").modal("hide");
        } else {
            fn.notify.warnbox(response.msg, "Oops...");
        }
    }, "json");
    return false;
};
