
fn.app.profit_loss_bwd.profitloss.unmatchusd = function (id) {
    bootbox.confirm({
        message: "Are sure to unmatch USD this record?",
        buttons: {
            confirm: { label: 'Remove', className: 'btn-danger' },
            cancel: { label: 'No', className: 'btn-secondary' }
        },
        callback: function (result) {
            if (result) {
                $.post("apps/profit_loss_bwd/xhr/action-unmatch-usd.php", { id: id }, function (response) {
                    if (response.success) {
                        $("#tblSales").data("selected", []);
                        $("#tblSales").DataTable().draw();
                        $("#tblLoss").DataTable().draw();
                    } else {
                        fn.notify.warnbox(response.msg, "Oops...");
                    }
                }, "json");
            }
        }
    });



};
