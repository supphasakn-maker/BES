fn.app.buy_fixed.buy.remove = function (id) {
    fn.dialog.confirmbox("ยืนยันการลบ", "คุณต้องการลบข้อมูลนี้หรือไม่?", function () {
        $.post("apps/buy_fixed/xhr/action-delete-buy-fixed.php", { item: id }, function (response) {
            $("#tblPurchase").DataTable().draw();
            fn.notify.successbox("", "Remove Success");
        });
    });
};

