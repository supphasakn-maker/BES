
fn.app.production_silverplate.oven.remove = function (id) {
    bootbox.confirm("Are you sure to remove?", function (result) {
        if (result) {
            $.post("apps/production_silverplate/xhr/action-remove_oven.php", { id: id }, function (response) {
                $("#tblOven").DataTable().draw();
            });
        }
    });

};
