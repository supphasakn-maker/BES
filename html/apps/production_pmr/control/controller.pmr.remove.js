fn.app.production_pmr.pmr.remove = function (id) {
    bootbox.confirm("Are you sure to remove?", function (result) {
        if (result) {
            $.post("apps/production_pmr/xhr/action-remove_pmr.php", { id: id }, function (response) {
                $("#tblIn").DataTable().draw();
            });
        }
    });

};
