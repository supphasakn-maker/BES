fn.app.production_prepare.prepare.dialog_add_scrap = function (id) {
    $.ajax({
        url: "apps/production_prepare/view/prepare/dialog.pack.scrap.php",
        data: { id: id },
        type: "POST",
        dataType: "html",
        success: function (html) {
            $("body").append(html);
            fn.ui.modal.setup({ dialog_id: "#dialog_add_scrap" });
        }
    });
};
fn.app.production_prepare.prepare.add_scrap = function () {
    // ตรวจสอบว่าฟอร์มถูกต้องหรือไม่
    var form = $("form[name=form_add_scrap]");
    if (!form.length) {
        fn.notify.errorbox("ไม่พบฟอร์มที่ต้องการ");
        return false;
    }

    // ป้องกันการ submit ซ้ำ
    if (form.data('submitting')) {
        return false;
    }

    // Basic validation (ปรับแต่งตามความต้องการ)
    var requiredFields = form.find('input[required], select[required]');
    var hasError = false;
    requiredFields.each(function () {
        if (!$(this).val().trim()) {
            $(this).addClass('is-invalid');
            hasError = true;
        } else {
            $(this).removeClass('is-invalid');
        }
    });

    if (hasError) {
        fn.notify.warnbox("กรุณากรอกข้อมูลให้ครบถ้วน", "ข้อมูลไม่ครบ");
        return false;
    }

    // ตั้งสถานะ loading
    form.data('submitting', true);
    var submitBtn = form.find('button[type=submit]');
    var originalText = submitBtn.html();
    submitBtn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> กำลังบันทึก...');

    // ส่งข้อมูล
    $.post("apps/production_prepare/xhr/action-add-add_scrap.php",
        form.serialize(),
        function (response) {
            // Reset loading state
            form.data('submitting', false);
            submitBtn.prop('disabled', false).html(originalText);

            if (response.success) {
                // อัปเดต DataTable
                var table = $("#tblScrap").DataTable();
                if (table) {
                    table.draw();
                }

                // ปิด dialog
                $("#dialog_add_scrap").modal("hide");

                // Trigger weight calculation
                $("input[name=weight_out_safe]").trigger('change');

                // แสดงข้อความสำเร็จ
                fn.notify.successbox("บันทึกข้อมูลสำเร็จ", "สำเร็จ");

                // รีเซ็ตฟอร์ม
                form[0].reset();

            } else {
                fn.notify.warnbox(response.msg || "เกิดข้อผิดพลาดในการบันทึกข้อมูล", "Oops...");
            }
        }, "json")
        .fail(function (xhr, status, error) {
            // Reset loading state on error
            form.data('submitting', false);
            submitBtn.prop('disabled', false).html(originalText);

            // แสดงข้อผิดพลาด
            var errorMsg = "เกิดข้อผิดพลาดในการเชื่อมต่อ";
            if (xhr.status === 404) {
                errorMsg = "ไม่พบไฟล์ที่ต้องการ";
            } else if (xhr.status === 500) {
                errorMsg = "เกิดข้อผิดพลาดในเซิร์ฟเวอร์";
            } else if (status === 'timeout') {
                errorMsg = "การเชื่อมต่อหมดเวลา";
            }

            fn.notify.errorbox(errorMsg + " (" + xhr.status + ")");
            console.error("AJAX Error:", xhr, status, error);
        });

    return false;
};
$("#tblScrap").DataTable({
    responsive: true,
    "bStateSave": true,
    "autoWidth": true,
    "processing": true,
    "serverSide": true,
    "ajax": {
        "data": function (d) {
            d.production_id = $("#tblScrap").attr("data-id");
        },
        "url": "apps/production_prepare/store/store-scrap.php"
    },
    "aoColumns": [
        { "bSort": true, "data": "round", "class": "text-center" },
        { "bSort": true, "data": "code", "class": "text-center" },
        { "bSort": true, "data": "status", "class": "text-center unselectable" },
        { "bSort": true, "data": "created", "class": "text-center" },
        { "bSort": true, "data": "pack_name", "class": "text-center" },
        { "bSort": true, "data": "weight_expected", "class": "text-right" },
        { "bSort": true, "data": "name", "class": "text-right" },
        { "bSortable": true, "data": "id", "sClass": "text-center", "sWidth": "80px" }
    ], "order": [[3, "desc"]],
    "createdRow": function (row, data, index) {

        var s = '';

        if (data.status == "0") {
            s += fn.ui.button("btn btn-xs btn-outline-danger mr-1", "far fa-trash", "fn.app.production_prepare.scrap.remove(" + data[0] + ")");
        } else {
            s += '<span class="badge badge-warning">-</span>';
        }

        if (data.status != 0) {
            $("td", row).eq(2).html('<a class="badge badge-warning">Combined</a>');
        } else {
            $("td", row).eq(2).html('<a>ยังไม่ได้ใช้</a>');
        }
        $("td", row).eq(7).html(s);

    }
});
