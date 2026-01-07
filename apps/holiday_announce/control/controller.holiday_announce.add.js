fn.app.holiday_announce.holiday.add = function () {
    var PublicHoliday = $("input[name=PublicHoliday]").val();
    var Descripiton = $("input[name=Descripiton]").val();

    var FisYear = '';
    if (PublicHoliday) {
        var date = new Date(PublicHoliday);
        FisYear = date.getFullYear();
    }

    var dateFormatted = '';
    if (PublicHoliday) {
        var date = new Date(PublicHoliday);
        dateFormatted = date.toLocaleDateString('th-TH', {
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        });
    }

    function showMainConfirmation() {
        var confirmMessage = "ยืนยันการเพิ่มวันหยุด?\n\n" +
            "ปี: " + FisYear + "\n" +
            "วันที่: " + (dateFormatted || PublicHoliday || '-') + "\n" +
            "รายละเอียด: " + (Descripiton || '-');

        fn.dialog.confirmbox("Confirmation", confirmMessage, function () {
            var formData = $("form[name=holiday]").serialize();
            formData += "&FisYear=" + FisYear;

            $.post("apps/holiday_announce/xhr/action-add-holiday_announce.php", formData, function (response) {
                if (response.success) {
                    $("#tblSilver").DataTable().draw();
                    fn.notify.successbox("เพิ่มข้อมูลวันหยุดสำเร็จ", "สำเร็จ!");
                    fn.reload();
                } else {
                    fn.notify.warnbox(response.msg, "Oops...");
                }
            }, "json");
        });
    }

    if (!PublicHoliday) {
        fn.notify.warnbox("กรุณาระบุวันที่", "ข้อมูลไม่ครบถ้วน");
        return false;
    }

    if (!Descripiton) {
        fn.notify.warnbox("กรุณาระบุรายละเอียด", "ข้อมูลไม่ครบถ้วน");
        return false;
    }

    showMainConfirmation();

    return false;
};