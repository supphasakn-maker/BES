fn.app.profit_loss_bwd.profitloss.unsplit = function (split_id) {
    bootbox.confirm({
        message: "คุณต้องการรวม Order นี้กลับเป็นรายการเดิมหรือไม่?",
        buttons: {
            confirm: {
                label: '<i class="fas fa-undo"></i> Unsplit',
                className: 'btn-warning'
            },
            cancel: {
                label: '<i class="fas fa-times"></i> Cancel',
                className: 'btn-secondary'
            }
        },
        callback: function (result) {
            if (result) {
                $.post("apps/profit_loss_bwd/xhr/unsplit-order.php", {
                    split_id: split_id
                })
                    .done(function (response) {
                        console.log("Unsplit response:", response);

                        if (response.success) {
                            $("#tblSales").DataTable().ajax.reload(null, false);

                            if (fn.notify && fn.notify.successbox) {
                                fn.notify.successbox("Unsplit สำเร็จ!", "Success");
                            } else {
                                alert("Unsplit สำเร็จ!");
                            }
                        } else {
                            fn.notify.warnbox(response.message || "เกิดข้อผิดพลาด", "Oops...");
                        }
                    })
                    .fail(function (xhr, status, error) {
                        console.error("Unsplit AJAX error:", xhr.responseText);
                        fn.notify.warnbox("เกิดข้อผิดพลาดในการเชื่อมต่อ: " + error, "Error");
                    });
            }
        }
    });
};