fn.app.buy_fixed.buy.add = function () {

    var type = $("select[name=type]").val();
    var amount = $("input[name=amount]").val();
    var date = $("input[name=date]").val();
    var method = $("select[name=method]").val();
    var supplier = $("select[name=supplier_id]").val();
    var product = $("select[name=product_id]").val();

    var supplier_name = $("select[name=supplier_id] option:selected").text();
    var product_name = $("select[name=product_id] option:selected").text();
    var dateFormatted = '';
    if (date) {
        var dateObj = new Date(date);
        dateFormatted = dateObj.toLocaleDateString('th-TH');
    }

    function showMainConfirmation() {
        var confirmMessage = "ยืนยันการเพิ่มข้อมูล?\n\n" +
            "AMOUNT : " + (amount || '0') + "\n" +
            "วันที่: " + dateFormatted + "\n" +
            "Type: " + type + "\n" +
            "Maturity : " + method + "\n" +
            "Supplier : " + supplier_name + "\n" +
            "Product : " + product_name + "\n";

        fn.dialog.confirmbox("Confirmation", confirmMessage, function () {
            $.post("apps/buy_fixed/xhr/action-add-buy-fixed.php", $("form[name=form_addusd]").serialize(), function (response) {

                if (response.success) {
                    $("form[name=form_addusd]")[0].reset();
                    fn.notify.successbox("เพิ่มข้อมูลสำเร็จ!");
                    setTimeout(function () {
                        $("#tblPurchase").DataTable().ajax.reload(null, false);
                    }, 300);
                } else {

                    fn.notify.warnbox(response.msg, "Oops...");
                }

            }, "json").fail(function () {

                fn.notify.warnbox("เกิดข้อผิดพลาดในการเชื่อมต่อ", "Error");
            });
        });
    }

    showMainConfirmation();
    return false;
};