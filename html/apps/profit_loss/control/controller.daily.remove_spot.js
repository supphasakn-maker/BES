fn.app.profit_loss.daily.remove_spot = function (id) {
    bootbox.confirm({
        message: "Are sure to Remove SPOT this record?",
        buttons: {
            confirm: { label: 'Remove', className: 'btn-danger' },
            cancel: { label: 'No', className: 'btn-secondary' }
        },
        callback: function (result) {
            if (result) {
                $.post("apps/profit_loss/xhr/action-remove-spot-daily.php", { id: id }, function (response) {
                    if (response.success) {
                        $("#tblSPOT").DataTable().draw();
                        fn.reload();
                    } else {
                        fn.notify.warnbox(response.msg, "Oops...");
                    }
                }, "json");
            }
        }
    });



};
