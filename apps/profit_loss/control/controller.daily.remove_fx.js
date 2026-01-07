fn.app.profit_loss.daily.remove_fx = function (id) {
    bootbox.confirm({
        message: "Are sure to Remove FX this record?",
        buttons: {
            confirm: { label: 'Remove', className: 'btn-danger' },
            cancel: { label: 'No', className: 'btn-secondary' }
        },
        callback: function (result) {
            if (result) {
                $.post("apps/profit_loss/xhr/action-remove-fx-daily.php", { id: id }, function (response) {
                    if (response.success) {
                        $("#tblFX").DataTable().draw();
                        fn.reload();
                    } else {
                        fn.notify.warnbox(response.msg, "Oops...");
                    }
                }, "json");
            }
        }
    });



};
