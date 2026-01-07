
(function () {
    'use strict';

    if (typeof fn === 'undefined') window.fn = {};
    if (typeof fn.app === 'undefined') fn.app = {};
    if (typeof fn.app.profit_loss_bwd === 'undefined') fn.app.profit_loss_bwd = {};
    if (typeof fn.app.profit_loss_bwd.profitloss === 'undefined') fn.app.profit_loss_bwd.profitloss = {};

    fn.app.profit_loss_bwd.profitloss.dialog_split = function (order_id, max_amount) {
        if (typeof order_id === 'string' && order_id.indexOf('SPLIT_') === 0) {
            fn.notify.warnbox('ไม่สามารถ Split รายการที่ถูก Split แล้ว', 'Warning');
            return;
        }

        $.ajax({
            url: "apps/profit_loss_bwd/view/dialog.split.php",
            data: {
                order_id: order_id,
                max_amount: max_amount
            },
            type: "POST",
            dataType: "html",
            success: function (html) {
                $("body").append(html);
                if (fn.ui && fn.ui.modal && fn.ui.modal.setup) {
                    fn.ui.modal.setup({ dialog_id: "#dialog_split_order_bwd" });
                } else {
                    $("#dialog_split_order_bwd").modal("show");
                }
            },
            error: function (xhr, status, error) {
                console.error("Error loading split dialog:", error);
                fn.notify.warnbox("เกิดข้อผิดพลาดในการเปิด Modal", "Oops...");
            }
        });
    };

    fn.app.profit_loss_bwd.profitloss.addSplitRow = function () {
        var newRow = `
            <div class="split-row row mb-2">
                <div class="col-10">
                    <input type="number" step="0.0001" class="form-control" 
                           name="split_amount[]" placeholder="Amount" 
                           onchange="fn.app.profit_loss_bwd.profitloss.calculateSplitTotal()"
                           oninput="fn.app.profit_loss_bwd.profitloss.calculateSplitTotal()">
                </div>
                <div class="col-2">
                    <button type="button" class="btn btn-danger btn-sm btn-block" 
                            onclick="$(this).closest('.split-row').remove(); fn.app.profit_loss_bwd.profitloss.calculateSplitTotal();">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
        `;
        $("#split_amounts_container").append(newRow);

        $("#split_amounts_container .split-row:last input").focus();
    };

    fn.app.profit_loss_bwd.profitloss.calculateSplitTotal = function () {
        var total = 0;
        var hasValidInput = false;

        $("input[name^='split_amount']").each(function () {
            var amount = parseFloat($(this).val()) || 0;
            total += amount;
            if (amount > 0) hasValidInput = true;
        });

        $("#split_total").text(total.toFixed(4));

        var original = parseFloat($("#original_amount").val()) || 0;
        var remaining = original - total;
        $("#split_remaining").text(remaining.toFixed(4));

        if (Math.abs(remaining) < 0.0001 && hasValidInput && total > 0) {
            $("#split_remaining").removeClass("text-danger").addClass("text-success");
            $("#btn_split").prop("disabled", false);
        } else {
            $("#split_remaining").removeClass("text-success").addClass("text-danger");
            $("#btn_split").prop("disabled", true);
        }
    };

    fn.app.profit_loss_bwd.profitloss.split = function () {
        var originalAmount = parseFloat($("#original_amount").val()) || 0;
        var splitTotal = parseFloat($("#split_total").text()) || 0;

        if (Math.abs(originalAmount - splitTotal) > 0.0001) {
            fn.notify.warnbox("ยอดรวมการแบ่งไม่ตรงกับยอดเดิม", "Warning");
            return false;
        }

        $.post("apps/profit_loss_bwd/xhr/split-order.php",
            $("form[name=form_split_bwd]").serialize())
            .done(function (response) {
                console.log("Split response:", response);

                if (response.success) {
                    $("#tblSales").DataTable().ajax.reload(null, false);
                    $("#dialog_split_order_bwd").modal("hide");

                    if (fn.notify && fn.notify.successbox) {
                        fn.notify.successbox(response.message || "Split สำเร็จ!", "Success");
                    } else {
                        alert(response.message || "Split สำเร็จ!");
                    }
                } else {
                    fn.notify.warnbox(response.message || "เกิดข้อผิดพลาด", "Oops...");
                }
            })
            .fail(function (xhr, status, error) {
                console.error("Split AJAX error:", xhr.responseText);
                fn.notify.warnbox("เกิดข้อผิดพลาดในการเชื่อมต่อ: " + error, "Error");
            });

        return false;
    };



    console.log('✓ Split Order Controller BWD loaded');

})();