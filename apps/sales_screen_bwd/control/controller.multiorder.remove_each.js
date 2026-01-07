if (typeof fn.app.sales_screen_bwd.multiorder === 'undefined') {
    fn.app.sales_screen_bwd.multiorder = {};
}
fn.app.sales_screen_bwd.multiorder.dialog_remove_each = function (id) {
    $.ajax({
        url: "apps/sales_screen_bwd/view/dialog.orders.remove_each.php",
        data: { id: id },
        type: "POST",
        dataType: "html",
        success: function (html) {
            console.log('Modal HTML received');
            $("body").append(html);
            fn.ui.modal.setup({ dialog_id: "#dialog_remove_each_orders" });
        },
        error: function (xhr, status, error) {
            console.error('Failed to load modal:', error);
            alert('Failed to load delete dialog: ' + error);
        }
    });
};

fn.app.sales_screen_bwd.multiorder.remove_each = function () {

    var formData = $("form[name=form_remove_eachorder]").serialize();


    var idValue = $("form[name=form_remove_eachorder] input[name=id]").val();

    if (!idValue) {
        alert('Error: No order ID found in form');
        return false;
    }

    var reasonValue = $("form[name=form_remove_eachorder] textarea[name=remove_reason]").val();

    if (!reasonValue || reasonValue.trim() === '') {
        alert('Please provide a reason for deletion');
        return false;
    }

    $.post("apps/sales_screen_bwd/xhr/action-remove_each-orders.php",
        formData,
        function (response) {
            console.log('Delete response:', response);

            if (response.success) {
                console.log('Delete successful, refreshing tables');
                $("#tblOrder").DataTable().draw();
                $("#tblQuickOrder").DataTable().draw();
                $("#dialog_remove_each_orders").modal("hide");


                if (typeof fn.notify !== 'undefined' && fn.notify.successbox) {
                    fn.notify.successbox(response.msg || 'Order deleted successfully', 'Success');
                } else {
                    alert(response.msg || 'Order deleted successfully');
                }
            } else {
                console.error('Delete failed:', response.msg);
                if (typeof fn.notify !== 'undefined' && fn.notify.warnbox) {
                    fn.notify.warnbox(response.msg, "Oops...");
                } else {
                    alert('Error: ' + response.msg);
                }
            }
        }, "json")
        .fail(function (xhr, status, error) {

            alert('AJAX Error: ' + error + '\nResponse: ' + xhr.responseText);
        });

    return false;
};

