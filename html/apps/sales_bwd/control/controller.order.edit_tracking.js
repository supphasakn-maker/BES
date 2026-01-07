fn.app.sales_bwd.order.dialog_edit_tracking = function (id) {
    $.ajax({
        url: "apps/sales_bwd/view/dialog.order.edit_tracking.php",
        data: { id: id },
        type: "POST",
        dataType: "html",
        success: function (html) {
            $("body").append(html);
            fn.ui.modal.setup({ dialog_id: "#dialog_edit_order" });

        }
    });
};

fn.app.sales_bwd.order.edit_tracking = function () {
    $.post("apps/sales_bwd/xhr/action-edit-order-tracking.php", $("form[name=form_editorder]").serialize(), function (response) {
        if (response.success) {
            $("#tblOrder").DataTable().draw();
            $("#dialog_edit_order").modal("hide");
        } else {
            fn.notify.warnbox(response.msg, "Oops...");
        }
    }, "json");
    return false;
};
