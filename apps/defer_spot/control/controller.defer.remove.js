fn.app.defer_spot.defer.remove = function (id) {
    if (typeof id != "undefined") {
        fn.dialog.confirmbox("Confirmation", "Are you sure to remove this item?", function () {
            $.post("apps/defer_spot/xhr/action-remove-defer.php", { item: id }, function (response) {
                $("#tblDefer").DataTable().draw();
                fn.notify.successbox("", "Remove Success");
            });
        });
    }
};