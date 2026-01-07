fn.app.production_pmr.recycle.remove = function (id) {
    bootbox.confirm("Are you sure to remove?", function (result) {
        if (result) {
            $.post("apps/production_pmr/xhr/action-remove_recycle.php", { id: id }, function (response) {
                $("#tblRecycle").DataTable().draw();
            });
        }
    });

};
