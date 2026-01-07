fn.app.profit_loss.profitloss.removespot = function (id) {
    bootbox.confirm({
        message: "Are sure to Remove SPOT this record?",
        buttons: {
            confirm: { label: 'Remove', className: 'btn-danger' },
            cancel: { label: 'No', className: 'btn-secondary' }
        },
        callback: function (result) {
            if (result) {
                $.post("apps/profit_loss/xhr/action-remove-spot.php", { id: id }, function (response) {
                    if (response.success) {
                        $("#tblPurchaseSpot").data("selected", []);
                        $("#tblPurchaseSpot").DataTable().draw();
                    } else {
                        fn.notify.warnbox(response.msg, "Oops...");
                    }
                }, "json");
            }
        }
    });



};
