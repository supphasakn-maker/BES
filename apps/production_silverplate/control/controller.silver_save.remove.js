fn.app.production_silverplate.silver_save.remove = function (id) {
    bootbox.confirm("Are you sure to remove?", function (result) {
        if (result) {
            $.post("apps/production_silverplate/xhr/action-remove_silver_save.php", { id: id }, function (response) {
                $("#tblSilver").DataTable().draw();
            });
        }
    });

};
