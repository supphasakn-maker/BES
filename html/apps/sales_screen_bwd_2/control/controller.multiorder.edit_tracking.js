fn.app.sales_screen_bwd_2.multiorder.dialog_edit_tracking = function (id) {
    $.ajax({
        url: "apps/sales_screen_bwd_2/view/dialog.order.edit_tracking.php",
        data: { id: id },
        type: "POST",
        dataType: "html",
        success: function (html) {
            $("body").append(html);
            fn.ui.modal.setup({ dialog_id: "#dialog_edit_order" });

        }
    });
};

fn.app.sales_screen_bwd_2.multiorder.edit_tracking = function () {
    $.post("apps/sales_screen_bwd_2/xhr/action-edit-order-tracking.php", $("form[name=form_editorder]").serialize(), function (response) {
        if (response.success) {
            $("#tblOrder").DataTable().draw();
            $("#dialog_edit_order").modal("hide");
        } else {
            fn.notify.warnbox(response.msg, "Oops...");
        }
    }, "json");
    return false;
};
