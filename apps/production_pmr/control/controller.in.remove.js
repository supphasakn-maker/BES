fn.app.production_pmr.in.remove = function (id) {
    bootbox.confirm("Are you sure to remove?", function (result) {
        if (result) {
            $.post("apps/production_pmr/xhr/action-remove_in.php", { id: id }, function (response) {
                $("#tblPmr").DataTable().draw();
            });
        }
    });

};
