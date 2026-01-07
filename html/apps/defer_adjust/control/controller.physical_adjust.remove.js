fn.app.defer_adjust.physical.remove = function (id) {
    if (typeof id != "undefined") {
        fn.dialog.confirmbox("Confirmation", "Are you sure to remove this item?", function () {
            $.post("apps/defer_adjust/xhr/action-remove-physical_adjust.php", { item: id }, function (response) {
                $("#tblPhysical").DataTable().draw();
                fn.notify.successbox("", "Remove Success");
            });
        });
    }
};