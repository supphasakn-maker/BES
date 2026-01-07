fn.app.defer_adjust.deposit.remove = function (id) {
    if (typeof id != "undefined") {
        fn.dialog.confirmbox("Confirmation", "Are you sure to remove this item?", function () {
            $.post("apps/defer_adjust/xhr/action-remove-deposit.php", { item: id }, function (response) {
                $("#tblDeposit").DataTable().draw();
                fn.notify.successbox("", "Remove Success");
            });
        });
    }
};