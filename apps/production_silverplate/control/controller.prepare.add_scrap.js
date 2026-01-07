fn.app.production_silverplate.prepare.dialog_add_scrap = function (id) {
    $.ajax({
        url: "apps/production_silverplate/view/prepare/dialog.pack.scrap.php",
        data: { id: id },
        type: "POST",
        dataType: "html",
        success: function (html) {
            $("body").append(html);
            fn.ui.modal.setup({ dialog_id: "#dialog_add_scrap" });
        }
    });
};

fn.app.production_silverplate.prepare.add_scrap = function () {
    var form = $("form[name=form_add_scrap]");
    if (!form.length) {
        fn.notify.warnbox("ไม่พบฟอร์มที่ต้องการ", "Error");
        return false;
    }

    if (form.data('submitting')) {
        return false;
    }

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

    form.data('submitting', true);
    var submitBtn = form.find('button[type=submit]');
    var originalText = submitBtn.html();
    submitBtn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> กำลังบันทึก...');

    $.post("apps/production_silverplate/xhr/action-add-add_scrap.php",
        form.serialize(),
        function (response) {
            form.data('submitting', false);
            submitBtn.prop('disabled', false).html(originalText);

            if (response.success) {
                var table = $("#tblScrap").DataTable();
                if (table) {
                    table.draw();
                }

                $("#dialog_add_scrap").modal("hide");

                $("input[name=weight_out_safe]").trigger('change');

                fn.notify.successbox("บันทึกข้อมูลสำเร็จ", "สำเร็จ");

                form[0].reset();

            } else {
                fn.notify.warnbox(response.msg || "เกิดข้อผิดพลาดในการบันทึกข้อมูล", "Oops...");
            }
        }, "json")
        .fail(function (xhr, status, error) {
            form.data('submitting', false);
            submitBtn.prop('disabled', false).html(originalText);

            var errorMsg = "เกิดข้อผิดพลาดในการเชื่อมต่อ";
            if (xhr.status === 404) {
                errorMsg = "ไม่พบไฟล์ที่ต้องการ";
            } else if (xhr.status === 500) {
                errorMsg = "เกิดข้อผิดพลาดในเซิร์ฟเวอร์";
            } else if (status === 'timeout') {
                errorMsg = "การเชื่อมต่อหมดเวลา";
            }

            fn.notify.warnbox(errorMsg + " (" + xhr.status + ")", "Error");
            console.error("AJAX Error:", xhr, status, error);
        });

    return false;
};

fn.app.production_silverplate.scrap.cancel = function (scrap_id) {
    var confirmMessage = "ยืนยันการยกเลิก Combined?\n\n" +
        "การยกเลิกจะคืนค่าน้ำหนักกลับไปยัง Production\n" +
        "";

    fn.dialog.confirmbox("ยืนยันการยกเลิก", confirmMessage, function () {
        $.post("apps/production_silverplate/xhr/action-cancel-scrap.php",
            { scrap_id: scrap_id },
            function (response) {
                if (response.success) {
                    fn.notify.successbox(response.msg, "สำเร็จ");

                    setTimeout(function () {
                        // Reload DataTable
                        var table = $("#tblScrap").DataTable();
                        if (table) {
                            table.ajax.reload(null, false);
                        }

                        $("input[name=weight_out_safe]").trigger('change');
                    }, 300);

                } else {
                    fn.notify.warnbox(response.msg, "เกิดข้อผิดพลาด");
                }
            }, "json")
            .fail(function (xhr, status, error) {
                fn.notify.warnbox("เกิดข้อผิดพลาดในการเชื่อมต่อ", "Error");
            });
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
        "url": "apps/production_silverplate/store/store-scrap.php"
    },
    "aoColumns": [
        { "bSort": true, "data": "round", "class": "text-center" },
        { "bSort": true, "data": "code", "class": "text-center" },
        { "bSort": true, "data": "status", "class": "text-center unselectable" },
        { "bSort": true, "data": "created", "class": "text-center" },
        { "bSort": true, "data": "pack_name", "class": "text-center" },
        { "bSort": true, "data": "weight_expected", "class": "text-right" },
        { "bSort": true, "data": "name", "class": "text-right" },
        { "bSortable": true, "data": "id", "sClass": "text-center", "sWidth": "150px" }
    ],
    "order": [[3, "desc"]],
    "createdRow": function (row, data, index) {

        var s = '';

        if (data.status == "0") {
            s += fn.ui.button("btn btn-xs btn-outline-danger", "far fa-trash", "fn.app.production_silverplate.scrap.remove(" + data[0] + ")");
        } else {
            s += fn.ui.button("btn btn-xs btn-outline-warning", "fas fa-undo", "fn.app.production_silverplate.scrap.cancel(" + data[0] + ")");
        }

        if (data.status != 0) {
            $("td", row).eq(2).html('<a class="badge badge-warning">Combined</a>');
        } else {
            $("td", row).eq(2).html('<a>ยังไม่ได้ใช้</a>');
        }
        $("td", row).eq(7).html(s);

    }
});