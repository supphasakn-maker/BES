fn.app.production_pmr.out.remove = function (id) {
    bootbox.confirm("Are you sure to remove?", function (result) {
        if (result) {
            $.post("apps/production_pmr/xhr/action-remove_out.php", { id: id }, function (response) {
                $("#tblOut").DataTable().draw();
            });
        }
    });

};
