fn.app.profit_loss.profitloss.dialog_split = function (order_id) {
    $.ajax({
        url: "apps/profit_loss/view/dialog.split.php",
        data: { order_id: order_id },
        type: "POST",
        dataType: "html",
        success: function (html) {
            $("body").append(html);
            if (fn.ui && fn.ui.modal && fn.ui.modal.setup) {
                fn.ui.modal.setup({ dialog_id: "#dialog_split_order" });
            } else {
                $("#dialog_split_order").modal("show");
            }
        },
        error: function (xhr, status, error) {
            console.error("Error loading split dialog:", error);
            fn.notify.warnbox("เกิดข้อผิดพลาดในการเปิด Modal", "Oops...");
        }
    });
};

fn.app.profit_loss.profitloss.split = function () {
    // ตรวจสอบความถูกต้องก่อน
    var originalAmount = parseFloat($("#original_amount").text()) || 0;
    var splitTotal = parseFloat($("#split_total").text()) || 0;

    if (Math.abs(originalAmount - splitTotal) > 0.0001) {
        fn.notify.warnbox("ยอดรวมการแบ่งไม่ตรงกับยอดเดิม", "Warning");
        return false;
    }

    $.post("apps/profit_loss/xhr/action-split-order.php",
        $("form[name=form_split]").serialize())
        .done(function (response) {
            console.log("Split response:", response);

            if (response.success) {
                $("#tblSales").data("selected", []);
                $("#tblSales").DataTable().draw();
                $("#dialog_split_order").modal("hide");

                // ใช้ fn.notify แทน fn.ui.alert
                if (fn.notify && fn.notify.successbox) {
                    fn.notify.successbox(response.msg || "Split สำเร็จ!", "Success");
                } else {
                    // Fallback
                    alert(response.msg || "Split สำเร็จ!");
                }
            } else {
                fn.notify.warnbox(response.msg || "เกิดข้อผิดพลาด", "Oops...");
            }
        })
        .fail(function (xhr, status, error) {
            console.error("Split AJAX error:", xhr.responseText);
            fn.notify.warnbox("เกิดข้อผิดพลาดในการเชื่อมต่อ: " + error, "Error");
        });

    return false;
};

fn.app.profit_loss.profitloss.unsplit = function (parent_order_id) {
    // ใช้ bootbox.confirm แทน fn.ui.confirm
    bootbox.confirm({
        message: "ต้องการรวม Split Orders กลับมาเป็น 1 รายการหรือไม่?",
        buttons: {
            confirm: {
                label: 'Unsplit',
                className: 'btn-warning'
            },
            cancel: {
                label: 'Cancel',
                className: 'btn-secondary'
            }
        },
        callback: function (result) {
            if (result) {
                $.post("apps/profit_loss/xhr/action-unsplit-order.php", {
                    parent: parent_order_id
                })
                    .done(function (response) {
                        console.log("Unsplit response:", response);

                        if (response.success) {
                            $("#tblSales").data("selected", []);
                            $("#tblSales").DataTable().draw();

                            if (fn.notify && fn.notify.successbox) {
                                fn.notify.successbox(response.msg || "Unsplit สำเร็จ!", "Success");
                            } else {
                                alert(response.msg || "Unsplit สำเร็จ!");
                            }
                        } else {
                            fn.notify.warnbox(response.msg || "เกิดข้อผิดพลาด", "Oops...");
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

fn.app.profit_loss.profitloss.calculateSplitTotal = function () {
    var total = 0;
    var hasValidInput = false;

    $("input[name^='split_amount']").each(function () {
        var amount = parseFloat($(this).val()) || 0;
        total += amount;
        if (amount > 0) hasValidInput = true;
    });

    $("#split_total").text(total.toFixed(4));

    var original = parseFloat($("#original_amount").text()) || 0;
    var remaining = original - total;
    $("#split_remaining").text(remaining.toFixed(4));

    // เปลี่ยนสีและเปิด/ปิดปุ่ม
    if (Math.abs(remaining) < 0.0001 && hasValidInput && total > 0) {
        $("#split_remaining").removeClass("text-danger").addClass("text-success");
        $("#btn_split").prop("disabled", false);
    } else {
        $("#split_remaining").removeClass("text-success").addClass("text-danger");
        $("#btn_split").prop("disabled", true);
    }
};

fn.app.profit_loss.profitloss.addSplitRow = function () {
    var newRow = `
        <div class="split-row row mb-2">
            <div class="col-8">
                <input type="number" step="0.0001" class="form-control" 
                       name="split_amount[]" placeholder="Amount" 
                       onchange="fn.app.profit_loss.profitloss.calculateSplitTotal()">
            </div>
            <div class="col-4">
                <button type="button" class="btn btn-danger btn-sm" 
                        onclick="$(this).closest('.split-row').remove(); fn.app.profit_loss.profitloss.calculateSplitTotal();">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        </div>
    `;
    $("#split_amounts_container").append(newRow);

    // Focus ใน input ใหม่
    $("#split_amounts_container .split-row:last input").focus();
};