fn.app.stock_silver.silver.dialog_add_multiple = function () {
    $.ajax({
        url: "apps/stock_silver/view/dialog.stock_silver.add_multiple.php",
        type: "POST",
        dataType: "html",
        success: function (html) {
            $("body").append(html);
            fn.ui.modal.setup({ dialog_id: "#dialog_add_multiple_silver" });

            setTimeout(function () {
                fn.app.stock_silver.silver.init_preview();

            }, 500);
        }
    });
};

fn.app.stock_silver.silver.generate_preview = function () {
    console.log('Generate preview called');

    var $form = $("form[name=form_add_multiple_silver]");
    var code = $form.find("input[name=code]").val();
    var quantity = parseInt($form.find("input[name=quantity]").val());


    if (!code) {
        code = "";
    }
    code = code.trim();

    if (!code || !quantity || quantity < 1) {
        $("#preview_codes").hide();
        return;
    }

    var pattern = /^([A-Za-z]+)(\d+)$/;
    var matches = code.match(pattern);

    if (!matches) {
        $("#preview_list").html('<span class="text-danger"><i class="fa fa-exclamation-triangle"></i> รูปแบบหมายเลขไม่ถูกต้อง (ต้องเป็นตัวอักษรตามด้วยตัวเลข เช่น A011987)</span>');
        $("#preview_codes").show();
        return;
    }

    var prefix = matches[1];
    var startNumber = parseInt(matches[2]);
    var numberLength = matches[2].length;


    var previewHtml = '<strong style="font-size: 16px;">จะเพิ่มทั้งหมด ' + quantity + ' แท่ง</strong><hr style="margin: 10px 0;">';

    if (quantity <= 20) {
        previewHtml += '<div class="row">';
        for (var i = 0; i < quantity; i++) {
            var currentNumber = startNumber + i;
            var currentCode = prefix + String(currentNumber).padStart(numberLength, '0');
            previewHtml += '<div class="col-md-4 col-sm-6 mb-2"><span class="badge badge-primary" style="font-size: 13px; padding: 8px 12px; width: 100%;">' + (i + 1) + '. ' + currentCode + '</span></div>';
        }
        previewHtml += '</div>';
    } else {
        previewHtml += '<div class="row">';

        for (var i = 0; i < 10; i++) {
            var currentNumber = startNumber + i;
            var currentCode = prefix + String(currentNumber).padStart(numberLength, '0');
            previewHtml += '<div class="col-md-4 col-sm-6 mb-2"><span class="badge badge-primary" style="font-size: 13px; padding: 8px 12px; width: 100%;">' + (i + 1) + '. ' + currentCode + '</span></div>';
        }

        previewHtml += '</div>';
        previewHtml += '<div class="text-center my-3"><strong style="font-size: 14px; color: #666;">... มีอีก ' + (quantity - 20) + ' แท่ง ...</strong></div>';
        previewHtml += '<div class="row">';

        for (var i = quantity - 10; i < quantity; i++) {
            var currentNumber = startNumber + i;
            var currentCode = prefix + String(currentNumber).padStart(numberLength, '0');
            previewHtml += '<div class="col-md-4 col-sm-6 mb-2"><span class="badge badge-primary" style="font-size: 13px; padding: 8px 12px; width: 100%;">' + (i + 1) + '. ' + currentCode + '</span></div>';
        }

        previewHtml += '</div>';
    }

    $("#preview_list").html(previewHtml);
    $("#preview_codes").show();
};

fn.app.stock_silver.silver.init_preview = function () {
    var $form = $("form[name=form_add_multiple_silver]");


    $form.find("input[name=code]").on('input keyup', function () {
        fn.app.stock_silver.silver.generate_preview();
    });

    $form.find("input[name=quantity]").on('input change keyup', function () {
        fn.app.stock_silver.silver.generate_preview();
    });

    fn.app.stock_silver.silver.generate_preview();
};

fn.app.stock_silver.silver.add_multiple = function () {
    var $form = $("form[name=form_add_multiple_silver]");


    var postData = {
        code: $form.find("input[name=code]").val() || '',
        quantity: $form.find("input[name=quantity]").val() || '1',
        customer_po: $form.find("select[name=customer_po]").val() || '',
        stock: $form.find("select[name=stock]").val() || 'BWS',
        date: $form.find("input[name=date]").val() || '',
        pack_name: $form.find("input[name=pack_name]").val() || 'SILVER BAR 1 KG',
        pack_type: $form.find("input[name=pack_type]").val() || 'แท่ง',
        weight_actual: $form.find("input[name=weight_actual]").val() || '1.0000',
        weight_expected: $form.find("input[name=weight_expected]").val() || '1.0000'
    };


    postData.code = postData.code.trim();

    if (!postData.code) {
        fn.notify.warnbox('โปรดใส่หมายเลขแท่ง', "Oops...");
        return false;
    }

    var quantity = parseInt(postData.quantity);

    var pattern = /^([A-Za-z]+)(\d+)$/;
    if (!pattern.test(postData.code)) {
        fn.notify.warnbox('รูปแบบหมายเลขไม่ถูกต้อง (ต้องเป็นตัวอักษรตามด้วยตัวเลข เช่น A011987)', "Oops...");
        return false;
    }

    var confirmMsg = 'คุณต้องการเพิ่มแท่งเงินจำนวน ' + quantity + ' แท่ง\nเริ่มจาก: ' + postData.code;

    if (!confirm(confirmMsg)) {
        return false;
    }

    if ($("#loading_overlay").length === 0) {
        $("#dialog_add_multiple_silver").find(".modal-body").append(
            '<div id="loading_overlay" style="position:absolute; top:0; left:0; width:100%; height:100%; background:rgba(255,255,255,0.9); z-index:9999; display:flex; align-items:center; justify-content:center;">' +
            '<div class="text-center">' +
            '<div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;">' +
            '<span class="sr-only">Loading...</span>' +
            '</div>' +
            '<div style="margin-top: 15px; font-size: 16px; color: #333;">กำลังบันทึก...</div>' +
            '</div></div>'
        );
    }

    console.log('Sending data to server:', postData);

    $.ajax({
        url: "apps/stock_silver/xhr/action-add_multiple-silver.php",
        type: "POST",
        data: postData,
        dataType: "json",
        success: function (response) {
            $("#loading_overlay").remove();

            if (response.success) {
                if (typeof $("#tblStockSilver").DataTable === 'function') {
                    $("#tblStockSilver").DataTable().draw();
                }
                if (typeof $("#tblStockFuture").DataTable === 'function') {
                    $("#tblStockFuture").DataTable().draw();
                }

                $("#dialog_add_multiple_silver").modal("hide");

                var successMsg = 'เพิ่มแท่งเงินสำเร็จ ' + response.count + ' แท่ง';

                if (response.codes && response.codes.length > 0) {
                    if (response.codes.length <= 10) {
                        successMsg += '\n\nหมายเลข: ' + response.codes.join(', ');
                    } else {
                        successMsg += '\n\nหมายเลข: ' + response.codes[0] + ' ถึง ' + response.codes[response.codes.length - 1];
                    }
                }

                fn.notify.successbox(successMsg, 'สำเร็จ');
            } else {
                fn.notify.warnbox(response.msg, "Oops...");
            }
        },
        error: function (xhr, status, error) {
            $("#loading_overlay").remove();
            var errorMsg = 'เกิดข้อผิดพลาดในการเชื่อมต่อ';

            if (xhr.responseText) {
                var preview = xhr.responseText.substring(0, 500);
                console.log('Error Preview:', preview);

                try {
                    var jsonResponse = JSON.parse(xhr.responseText);
                    if (jsonResponse.msg) {
                        errorMsg = jsonResponse.msg;
                    }
                } catch (e) {
                    if (xhr.responseText.indexOf('<!DOCTYPE') !== -1 || xhr.responseText.indexOf('<html') !== -1) {
                        errorMsg = 'เกิด PHP Error กรุณาตรวจสอบ Console (F12)';
                    } else {
                        errorMsg += ': ' + error;
                    }
                }
            }

            fn.notify.warnbox(errorMsg, "Error");
        }
    });

    return false;
};