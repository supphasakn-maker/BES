fn.app.defer_adjust.usd.remove = function (id) {
    if (typeof id != "undefined") {
        fn.dialog.confirmbox("Confirmation", "Are you sure to remove this item?", function () {
            $.post("apps/defer_adjust/xhr/action-remove-usd.php", { item: id }, function (response) {
                $("#tblUSD").DataTable().draw();
                fn.notify.successbox("", "Remove Success");
            });
        });
    }
};