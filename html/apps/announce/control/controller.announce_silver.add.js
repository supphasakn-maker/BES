fn.app.announce.announce_silver.add = function () {
    var rate_spot = $("input[name=rate_spot]").val();
    var rate_exchange = $("input[name=rate_exchange]").val();
    var sell = $("input[name=sell]").val();
    var buy = $("input[name=buy]").val();
    var date = $("input[name=date]").val();
    var no = $("input[name=no]").val();
    var dif = $("input[name=dif]").val();

    var sellPrice = parseFloat(sell.replace(/,/g, '')) || 0;
    var difPrice = parseFloat(dif.replace(/,/g, '')) || 0;

    var priceDifference = Math.abs(sellPrice - difPrice);

    var sellFormatted = sellPrice.toLocaleString('th-TH', { minimumFractionDigits: 2 });
    var buyFormatted = buy ? parseFloat(buy.replace(/,/g, '')).toLocaleString('th-TH', { minimumFractionDigits: 2 }) : '0.00';
    var difFormatted = difPrice.toLocaleString('th-TH', { minimumFractionDigits: 2 });

    var dateFormatted = '';
    if (date) {
        var dateObj = new Date(date);
        dateFormatted = dateObj.toLocaleDateString('th-TH');
    }

    if (priceDifference >= 500) {
        var warningMessage = "⚠️ คำเตือน!\n\n" +
            "ราคาขายออกปัจจุบัน: ฿" + sellFormatted + "\n" +
            "ราคาขายออกครั้งก่อน: ฿" + difFormatted + "\n" +
            "ส่วนต่าง: ฿" + priceDifference.toLocaleString('th-TH', { minimumFractionDigits: 2 }) + "\n\n" +
            "ราคาต่างกันมากกว่า ฿500\nต้องการดำเนินการต่อหรือไม่?";

        fn.dialog.confirmbox("Price Warning", warningMessage, function () {
            showMainConfirmation();
        });
    } else {
        showMainConfirmation();
    }

    function showMainConfirmation() {
        var confirmMessage = "ยืนยันการเพิ่มข้อมูลราคาแท่งเงิน?\n\n" +
            "SPOT: " + (rate_spot || '0') + "\n" +
            "EXCHANGE: " + (rate_exchange || '0') + " THB/USD\n" +
            "วันที่ประกาศ: " + dateFormatted + "\n" +
            "เวลา: " + new Date().toLocaleTimeString('th-TH', { hour: '2-digit', minute: '2-digit' }) + "\n" +
            "ครั้งที่: " + (no || '') + "\n" +
            "ราคารับซื้อ: ฿" + buyFormatted + "\n" +
            "ราคาขายออก: ฿" + sellFormatted;

        fn.dialog.confirmbox("Confirmation", confirmMessage, function () {
            $.post("apps/announce/xhr/action-add-announce_silver.php", $("form[name=rate]").serialize(), function (response) {
                if (response.success) {
                    $("#tblSilver").DataTable().draw();
                    fn.reload();
                } else {
                    fn.notify.warnbox(response.msg, "Oops...");
                }
            }, "json");
        });
    }

    return false;
};