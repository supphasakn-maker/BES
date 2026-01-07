
fn.app.profit_loss.profitloss.unmatchthb = function (id) {
    bootbox.confirm({
        message: "Are sure to unmatch THB this record?",
        buttons: {
            confirm: { label: 'Remove', className: 'btn-danger' },
            cancel: { label: 'No', className: 'btn-secondary' }
        },
        callback: function (result) {
            if (result) {
                $.post("apps/profit_loss/xhr/action-unmatch-thb.php", { id: id }, function (response) {
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
