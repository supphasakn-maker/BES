// Safe namespace
if (typeof fn === 'undefined') window.fn = {};
if (typeof fn.app === 'undefined') fn.app = {};
if (typeof fn.app.sales_screen_bwd_2 === 'undefined') fn.app.sales_screen_bwd_2 = {};

fn.app.sales_screen_bwd_2.multiorder = {
    __isSubmitting: false, // กันกดย้ำ

    add: function () {
        if (!fn.app.sales_screen_bwd_2.multiorder.validateForm()) return false;

        if (typeof fn.dialog !== 'undefined' && typeof fn.dialog.confirmbox === 'function') {
            fn.dialog.confirmbox("Confirmation", "Are you sure to Add Multi-Order", function () {
                fn.app.sales_screen_bwd_2.multiorder.submitOrder();
            });
        } else {
            if (confirm("Are you sure to Add Multi-Order?")) {
                fn.app.sales_screen_bwd_2.multiorder.submitOrder();
            }
        }
        return false;
    },

    submitOrder: function () {
        if (fn.app.sales_screen_bwd_2.multiorder.__isSubmitting) return;
        fn.app.sales_screen_bwd_2.multiorder.__isSubmitting = true;

        const formData = fn.app.sales_screen_bwd_2.multiorder.collectFormData();

        const $btn = $('#btn-submit, .btn-submit').first();
        const oldHtml = $btn.length ? $btn.html() : null;
        if ($btn.length) $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-2"></i>กำลังทำรายการ...');

        $.post(
            "apps/sales_screen_bwd_2/xhr/action-add-multi-order.php",
            { data: JSON.stringify(formData) },
            function (response) {
                if (response && response.success) {
                    if (typeof $("#tblQuickOrder").DataTable === 'function') {
                        $("#tblQuickOrder").DataTable().draw();
                    }

                    fn.app.sales_screen_bwd_2.multiorder.resetForm();

                    if (typeof fn.reload === 'function') {
                        fn.reload();
                    }

                    if (typeof fn.notify !== 'undefined' && typeof fn.notify.successbox === 'function') {
                        const msg = response.msg
                            + (response.subtotal_before_fee ? `<br>ก่อนหักค่าธรรมเนียม: ${response.subtotal_before_fee}` : '')
                            + (response.fee ? `<br>ค่าธรรมเนียม: ${response.fee}` : '')
                            + (response.total_amount ? `<br><b>ยอดสุทธิ:</b> ${response.total_amount}` : '');
                        fn.notify.successbox(msg, "Success");
                    } else {
                        let msg = "Success: " + response.msg;
                        if (response.subtotal_before_fee) msg += "\nBefore fee: " + response.subtotal_before_fee;
                        if (response.fee) msg += "\nFee: " + response.fee;
                        if (response.total_amount) msg += "\nGrand total: " + response.total_amount;
                        alert(msg);
                    }
                } else {
                    const err = (response && response.msg) ? response.msg : "Unknown error";
                    if (typeof fn.notify !== 'undefined' && typeof fn.notify.warnbox === 'function') {
                        fn.notify.warnbox(err, "Oops...");
                    } else {
                        alert("Error: " + err);
                    }
                }
            },
            "json"
        ).fail(function (xhr, status, error) {
            if (typeof fn.notify !== 'undefined' && typeof fn.notify.warnbox === 'function') {
                fn.notify.warnbox("Connection error occurred: " + error, "Error");
            } else {
                alert("Connection error occurred: " + error + "\nResponse: " + xhr.responseText);
            }
        }).always(function () {
            fn.app.sales_screen_bwd_2.multiorder.__isSubmitting = false;
            if ($btn.length) $btn.prop('disabled', false).html(oldHtml);
        });
    },

    // แทนที่ฟังก์ชัน validateForm เดิม
    validateForm: function () {
        const customerName = ($('[name="customer_name"]').val() || '').trim();
        const phone = ($('[name="phone"]').val() || '').trim();
        const username = ($('[name="username"]').val() || '').trim();
        const platform = $('[name="platform"]').val();
        const vat_type = $('[name="vat_type"]').val();

        if (!customerName) { (fn.notify?.warnbox)?.("กรุณากรอกชื่อลูกค้า", "Validation Error") ?? alert("กรุณากรอกชื่อลูกค้า"); $('[name="customer_name"]').focus(); return false; }
        if (!phone && !username) { (fn.notify?.warnbox)?.("กรุณากรอกเบอร์โทรศัพท์หรือ Username", "Validation Error") ?? alert("กรุณากรอกเบอร์โทรศัพท์หรือ Username"); $('[name="phone"]').focus(); return false; }
        if (!platform) { (fn.notify?.warnbox)?.("กรุณาเลือก Platform", "Validation Error") ?? alert("กรุณาเลือก Platform"); $('[name="platform"]').focus(); return false; }
        if (!vat_type) { (fn.notify?.warnbox)?.("กรุณาเลือก การเสีย Vats", "Validation Error") ?? alert("กรุณาเลือก Vats"); $('[name="vat_type"]').focus(); return false; }

        const feeStr = ($('[name="fee"]').val() || '').trim();
        if (feeStr !== '') {
            const feeVal = parseFloat(feeStr.replace(/,/g, ''));
            if (isNaN(feeVal) || feeVal < 0) {
                (fn.notify?.warnbox)?.("กรุณากรอกค่าธรรมเนียมเป็นตัวเลขที่ไม่ติดลบ", "Validation Error") ?? alert("กรุณากรอกค่าธรรมเนียมเป็นตัวเลขที่ไม่ติดลบ");
                $('[name="fee"]').focus(); return false;
            }
        }

        let hasValidItem = false;

        $('.item-row').each(function () {
            const $row = $(this);
            const productId = $row.find('[name*="[product_id]"]').val();
            const productType = $row.find('[name*="[product_type]"]').val();

            const amountRaw = ($row.find('[name*="[amount]"]').val() || '').toString().replace(/,/g, '').trim();
            const priceRaw = ($row.find('[name*="[price]"]').val() || '').toString().replace(/,/g, '').trim();

            const amount = parseFloat(amountRaw);
            const price = parseFloat(priceRaw); // อนุญาตให้เป็น 0

            if (productId && productType && !isNaN(amount) && amount > 0 && !isNaN(price) && price >= 0) {
                hasValidItem = true;
            }
        });

        if (!hasValidItem) {
            (fn.notify?.warnbox)?.("กรุณาเพิ่มรายการสินค้าที่ถูกต้องอย่างน้อย 1 รายการ", "Validation Error") ?? alert("กรุณาเพิ่มรายการสินค้าที่ถูกต้องอย่างน้อย 1 รายการ");
            return false;
        }
        return true;
    },
    collectFormData: function () {
        const feeVal = parseFloat((($('[name="fee"]').val() || '0').toString().replace(/,/g, '').trim())) || 0;

        const formData = {
            customer_name: ($('[name="customer_name"]').val() || '').trim(),
            phone: ($('[name="phone"]').val() || '').trim(),
            username: ($('[name="username"]').val() || '').trim(),
            platform: $('[name="platform"]').val(),
            vat_type: $('[name="vat_type"]').val(),
            date: $('[name="date"]').val(),
            delivery_date: $('[name="delivery_date"]').val(),
            shipping: $('[name="shipping"]').val(),
            fee: feeVal,
            shipping_address: ($('[name="shipping_address"]').val() || '').trim(),
            billing_address: ($('[name="billing_address"]').val() || '').trim(),
            comment: ($('[name="comment"]').val() || '').trim(),
            items: []
        };

        $('.item-row').each(function () {
            const $row = $(this);
            const productId = $row.find('[name*="[product_id]"]').val();
            const productType = $row.find('[name*="[product_type]"]').val();

            const amount = parseFloat((($row.find('[name*="[amount]"]').val() || '').toString().replace(/,/g, '').trim()));
            const price = parseFloat((($row.find('[name*="[price]"]').val() || '').toString().replace(/,/g, '').trim()));

            if (productId && productType && !isNaN(amount) && amount > 0 && !isNaN(price) && price >= 0) {
                formData.items.push({
                    product_id: productId,
                    product_type: productType,
                    amount: amount,
                    price: price,
                    discount: $row.find('[name*="[discount]"]').val() || "0",
                    engrave: $row.find('[name*="[engrave]"]:checked').val() || 'ไม่สลักข้อความบนแท่งเงิน',
                    font: $row.find('[name*="[font]"]').val() || '',
                    carving: (($row.find('[name*="[carving]"]').val() || '').trim()),
                    ai: $row.find('[name*="[ai]"]').val() || "0"
                });
            }
        });

        return formData;
    },


    resetForm: function () {
        const $form = $("form[name=form_multi_order]");
        if ($form.length) $form[0].reset();

        if (window.SalesScreenApp?.customerManager?.clearCustomerForm) {
            window.SalesScreenApp.customerManager.clearCustomerForm();
        }

        if (window.SalesScreenApp && window.SalesScreenApp.multiOrderManager) {
            const firstItem = $('#items-container .item-row:first');
            $('#items-container .item-row').not(':first').remove();

            firstItem.find('select').prop('selectedIndex', 0);
            firstItem.find('input[type="text"], input[type="number"]').val('');
            firstItem.find('input[name*="[amount]"]').val('1');
            firstItem.find('input[type="radio"][value="ไม่สลักข้อความบนแท่งเงิน"]').prop('checked', true);
            firstItem.find('.carving-input').attr('readonly', true).val('');
            firstItem.find('.font-select').prop('disabled', true);

            $('[name="fee"]').val('0');
            if ($('#fee-amount').length) $('#fee-amount').text('0.00');

            window.SalesScreenApp.itemCounter = 1;
            window.SalesScreenApp.multiOrderManager.updateRemoveButtons();
            window.SalesScreenApp.multiOrderManager.calculateTotal();
        }

        const today = new Date().toISOString().split('T')[0];
        const deliveryDate = new Date(Date.now() + 3 * 24 * 60 * 60 * 1000).toISOString().split('T')[0];
        $('[name="date"]').val(today);
        $('[name="delivery_date"]').val(deliveryDate);
    }
};

// Bind events
$(document).ready(function () {
    $('form[name="form_multi_order"]').off('submit.formhandler').on('submit.formhandler', function (e) {
        e.preventDefault();
        e.stopPropagation();
        fn.app.sales_screen_bwd_2.multiorder.add();
        return false;
    });

    // ปุ่มอยู่นอกฟอร์ม → ให้ trigger ฟอร์มเอง
    $('.btn-submit, #btn-submit').off('click.formhandler').on('click.formhandler', function (e) {
        e.preventDefault();
        $('form[name="form_multi_order"]').trigger('submit');
    });
});
