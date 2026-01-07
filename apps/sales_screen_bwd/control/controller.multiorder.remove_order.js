fn.app.sales_screen_bwd.multiorder.dialog_remove_order = function (id) {
    $.ajax({
        url: "apps/sales_screen_bwd/view/dialog.orders.remove_order.php",
        data: { id: id },
        type: "POST",
        dataType: "html",
        success: function (html) {
            var trimmed = (html || "").trim();

            // ถ้าเป็น JSON (permission denied / error) ให้ parse
            if (trimmed.startsWith("{") || trimmed.startsWith("[")) {
                try {
                    var jsonResponse = JSON.parse(trimmed);
                    if (jsonResponse.success === false) {
                        if (fn.notify?.warnbox) fn.notify.warnbox(jsonResponse.error, "ไม่สามารถดำเนินการได้");
                        else alert('Error: ' + jsonResponse.error);
                        return;
                    }
                } catch (e) {
                    // ถ้า parse ไม่ได้ ก็ถือว่าเป็น HTML ต่อ
                }
            }

            // กัน modal ซ้ำ
            $("#dialog_remove_order_orders").remove();

            $("body").append(html);
            fn.ui.modal.setup({ dialog_id: "#dialog_remove_order_orders" });
        },
        error: function (xhr, status, error) {
            if (fn.notify?.warnbox) fn.notify.warnbox('Failed to load delete dialog: ' + error, "Error");
            else alert('Failed to load delete dialog: ' + error);
        }
    });
};

fn.app.sales_screen_bwd.multiorder.remove_order = function () {
    var $dlg = $("#dialog_remove_order_orders");
    var $form = $dlg.find("form[name=form_remove_order_orders]");

    if ($form.length === 0) {
        alert("Error: form not found in modal");
        return false;
    }

    var idValue = $form.find("input[name=id]").val();
    if (!idValue) {
        alert("Error: No order ID found");
        return false;
    }

    var reasonValue = $form.find("textarea[name=remove_reason]").val();
    if (!reasonValue || reasonValue.trim() === "") {
        alert("กรุณาระบุเหตุผลในการยกเลิก");
        return false;
    }

    $.post("apps/sales_screen_bwd/xhr/action-remove_order-orders.php", $form.serialize(), function (response) {
        if (response && response.success) {
            if ($.fn.DataTable.isDataTable("#tblOrder")) $("#tblOrder").DataTable().draw(false);
            if ($.fn.DataTable.isDataTable("#tblQuickOrder")) $("#tblQuickOrder").DataTable().draw(false);

            $dlg.modal("hide");

            if (fn.notify?.successbox) fn.notify.successbox(response.msg || "ยกเลิกออเดอร์สำเร็จ", "สำเร็จ");
            else alert(response.msg || "ยกเลิกออเดอร์สำเร็จ");
        } else {
            if (fn.notify?.warnbox) fn.notify.warnbox(response?.msg || "ไม่สามารถยกเลิกได้", "เกิดข้อผิดพลาด");
            else alert("Error: " + (response?.msg || "ไม่สามารถยกเลิกได้"));
        }
    }, "json").fail(function (xhr, status, error) {
        alert("AJAX Error: " + error + "\n" + xhr.responseText);
    });

    return false;
};
