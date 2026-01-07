fn.app.sales_back_bwd.sale_back.remove = function (id) {
    if (typeof id != "undefined") {
        fn.dialog.confirmbox("Confirmation", "Are you sure to remove this item?", function () {
            $.post("apps/sales_back_bwd/xhr/action-remove-quick_buyorder.php", { item: id }, function (response) {
                $("#tblsaleback").DataTable().draw();
                fn.notify.successbox("", "Remove Success");
                fn.reload()
            });
        });
    }
};