fn.app.profit_loss_bwd.profitloss.removeusd = function (id) {
    bootbox.confirm({
        message: "Are sure to Remove USD this record?",
        buttons: {
            confirm: { label: 'Remove', className: 'btn-danger' },
            cancel: { label: 'No', className: 'btn-secondary' }
        },
        callback: function (result) {
            if (result) {
                $.post("apps/profit_loss_bwd/xhr/action-remove-usd.php", { id: id }, function (response) {
                    if (response.success) {
                        $("#tblPurchaseUSDtrue").data("selected", []);
                        $("#tblPurchaseUSDtrue").DataTable().draw();
                    } else {
                        fn.notify.warnbox(response.msg, "Oops...");
                    }
                }, "json");
            }
        }
    });



};
