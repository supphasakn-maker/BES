fn.app.profit_loss.daily.remove = function (id) {
    bootbox.confirm({
        message: "Are sure to Remove NOTED this record?",
        buttons: {
            confirm: { label: 'Remove', className: 'btn-danger' },
            cancel: { label: 'No', className: 'btn-secondary' }
        },
        callback: function (result) {
            if (result) {
                $.post("apps/profit_loss/xhr/action-remove-daily.php", { id: id }, function (response) {
                    if (response.success) {
                        $("#tblNoted").DataTable().draw();
                        fn.reload();
                    } else {
                        fn.notify.warnbox(response.msg, "Oops...");
                    }
                }, "json");
            }
        }
    });



};
