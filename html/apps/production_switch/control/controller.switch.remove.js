fn.app.production_switch.switch.remove = function (id) {
    bootbox.confirm("Are you sure to remove?", function (result) {
        if (result) {
            $.post("apps/production_switch/xhr/action-remove_switch.php", { id: id }, function (response) {
                $("#tblSwitch").DataTable().draw();
            });
        }
    });

};
