fn.app.sales_screen_bwd_2.multiorder.dialog_remove = function () {
    var validItems = [];

    // วิธีใหม่: ดึงจาก selected rows โดยตรง
    $("#tblOrder tbody tr.selected").each(function () {
        var rowData = $("#tblOrder").DataTable().row(this).data();

        if (rowData && rowData.id) {
            validItems.push(rowData.id);
        }
    });

    // ถ้าไม่มี selected rows ลองดูจาก checkbox
    if (validItems.length === 0) {
        $("#tblOrder input[name='chk_order']:checked").each(function () {
            var value = $(this).val();

            if (value && value !== '' && value !== 'undefined') {
                validItems.push(value);
            }
        });
    }

    // ถ้ายังไม่ได้ ลองจาก data("selected") แต่ต้องกรองดี
    if (validItems.length === 0) {
        var dataSelected = $("#tblOrder").data("selected") || [];

        dataSelected.forEach(function (item) {
            // กรองเฉพาะค่าที่เป็นตัวเลขจริงๆ
            var itemStr = String(item).trim();
            var itemNum = parseInt(itemStr);

            if (itemStr !== '' && !isNaN(itemNum) && itemNum > 0) {
                validItems.push(itemNum);
            }
        });
    }


    if (validItems.length === 0) {
        // ปรับปรุงข้อความให้ชัดเจนขึ้น
        alert("กรุณาเลือกรายการที่ต้องการลบก่อน\n(คลิกที่แถวหรือเลือก checkbox)");
        return;
    }

    // เก็บ validItems ไว้ใน global variable
    window.pendingRemoveItems = validItems;

    $.ajax({
        url: "apps/sales_screen_bwd_2/view/dialog.order.remove.php",
        data: { items: validItems },
        type: "POST",
        dataType: "html",
        success: function (html) {
            $("body").append(html);
            $("#dialog_remove_order").on("hidden.bs.modal", function () {
                $(this).remove();
                delete window.pendingRemoveItems;
            });
            $("#dialog_remove_order").modal("show");

            setTimeout(function () {
                // Override onclick attribute
                $("#dialog_remove_order button").each(function () {
                    var buttonText = $(this).text().toLowerCase();
                    if (buttonText.includes('remove') || $(this).hasClass('btn-danger')) {

                        // ลบ onclick เดิม
                        $(this).removeAttr('onclick');

                        // เพิ่ม event handler ใหม่
                        $(this).off('click').on('click', function (e) {
                            e.preventDefault();
                            e.stopPropagation();


                            // ใช้ validItems ที่เก็บไว้
                            if (window.pendingRemoveItems && window.pendingRemoveItems.length > 0) {
                                console.log('Sending request with pending items');
                                fn.app.sales_screen_bwd_2.multiorder.remove_with_items(window.pendingRemoveItems);
                            } else {
                                alert('ไม่พบรายการที่ต้องการลบ');
                            }
                        });
                    }
                });
            }, 200);
        },
        error: function (xhr, status, error) {
            alert("เกิดข้อผิดพลาดในการโหลด dialog: " + error);
        }
    });
};

// เพิ่ม function ใหม่สำหรับลบด้วย items ที่กำหนด
fn.app.sales_screen_bwd_2.multiorder.remove_with_items = function (items) {

    if (!items || items.length === 0) {
        alert("ไม่พบรายการที่ต้องการลบ");
        return;
    }

    console.log('=== SENDING REQUEST ===');
    $.post("apps/sales_screen_bwd_2/xhr/action-remove-order.php", { items: items }, function (response) {

        if (response.success) {

            // Clear selections
            $("#tblOrder").data("selected", []);
            $("#tblOrder tbody tr.selected").removeClass('selected');
            $("#tblOrder input[name='chk_order']:checked").prop('checked', false);

            // Refresh table
            $("#tblOrder").DataTable().draw();

            // Hide dialog
            $("#dialog_remove_order").modal("hide");

            // Show success message
            if (typeof fn.notify !== 'undefined' && fn.notify.successbox) {
                fn.notify.successbox("", response.message || "Remove Success");
            } else {
                alert(response.message || "Remove Success");
            }

            // Show warnings if any
            if (response.warnings && response.warnings.length > 0) {
            }
        } else {
            alert("Error: " + (response.error || "Unknown error"));
        }
    }, "json").fail(function (xhr, status, error) {
        alert("เกิดข้อผิดพลาดในการลบ: " + error);
    });
};

fn.app.sales_screen_bwd_2.multiorder.remove = function (id) {
    if (typeof id != "undefined" && id !== '' && !isNaN(id)) {
        // กรณีลบรายการเดียว
        fn.dialog.confirmbox("Confirmation", "Are you sure to remove this item?", function () {
            $.post("apps/sales_screen_bwd_2/xhr/action-remove-order.php", { item: id }, function (response) {
                $("#tblOrder").DataTable().draw();
                // ใช้ fn.notify.successbox หรือ alert
                if (typeof fn.notify !== 'undefined' && fn.notify.successbox) {
                    fn.notify.successbox("", "Remove Success");
                } else {
                    alert("Remove Success");
                }
            }).fail(function (xhr, status, error) {
                alert("เกิดข้อผิดพลาดในการลบ");
            });
        });
    } else {
        // กรณีลบหลายรายการ
        var item_selected = $("#tblOrder").data("selected") || [];

        // กรองเฉพาะค่าที่ไม่ว่างและเป็นตัวเลข
        var validItems = item_selected.filter(function (item) {
            var itemStr = String(item).trim();
            var itemNum = parseInt(itemStr);
            return itemStr !== '' && !isNaN(itemNum) && itemNum > 0;
        });

        if (validItems.length === 0) {
            alert("ไม่พบรายการที่ถูกต้องสำหรับการลบ");
            $("#dialog_remove_order").modal("hide");
            return;
        }

        $.post("apps/sales_screen_bwd_2/xhr/action-remove-order.php", { items: validItems }, function (response) {
            $("#tblOrder").data("selected", []);
            $("#tblOrder").DataTable().draw();
            $("#dialog_remove_order").modal("hide");

            if (typeof fn.notify !== 'undefined' && fn.notify.successbox) {
                fn.notify.successbox("", "Remove Success");
            } else {
                alert("Remove Success");
            }
        }).fail(function (xhr, status, error) {
            alert("เกิดข้อผิดพลาดในการลบ");
        });
    }
};



$(".btn-area").append(fn.ui.button({
    class_name: "btn btn-light has-icon",
    icon_type: "material",
    icon: "delete",
    onclick: "fn.app.sales_screen_bwd_2.multiorder.dialog_remove()",
    caption: "Remove"
}));