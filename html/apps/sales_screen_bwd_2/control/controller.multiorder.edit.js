fn = fn || {};
fn.app = fn.app || {};
fn.app.sales_screen_bwd_2 = fn.app.sales_screen_bwd_2 || {};
fn.app.sales_screen_bwd_2.multiorder = fn.app.sales_screen_bwd_2.multiorder || {};

fn.app.sales_screen_bwd_2.multiorder.isDiscountPlatform = function (platform) {
    const discountPlatforms = ['Shopee', 'Lazada', 'TikTok'];
    return discountPlatforms.includes(platform);
};

fn.app.sales_screen_bwd_2.multiorder.dialog_edit = function (id) {
    if (!id) {
        alert('ไม่พบ ID ของออเดอร์');
        return false;
    }

    fn.app.sales_screen_bwd_2.multiorder.createLoadingModal(id);

    $.ajax({
        url: "apps/sales_screen_bwd_2/xhr/action-get-order.php",
        data: { id: id },
        type: "POST",
        dataType: "json",
        timeout: 30000,
        success: function (response) {
            if (response.success) {
                fn.app.sales_screen_bwd_2.multiorder.createEditForm(response.data);
            } else {
                fn.app.sales_screen_bwd_2.multiorder.showError(response.msg || 'ไม่สามารถโหลดข้อมูลได้');
            }
        },
        error: function (xhr, status, error) {
            let errorMsg = 'เกิดข้อผิดพลาดในการโหลดข้อมูล';
            if (status === 'timeout') {
                errorMsg = 'การเชื่อมต่อใช้เวลานานเกินไป กรุณาลองใหม่อีกครั้ง';
            } else if (xhr.status === 404) {
                errorMsg = 'ไม่พบไฟล์ action-get-order.php กรุณาตรวจสอบ path';
            } else if (xhr.status === 500) {
                errorMsg = 'เกิดข้อผิดพลาดภายในเซิร์ฟเวอร์';
            }
            fn.app.sales_screen_bwd_2.multiorder.showError(errorMsg + ': ' + error);
        }
    });
};

fn.app.sales_screen_bwd_2.multiorder.createLoadingModal = function (orderId) {
    const modalHtml = `
        <div class="modal fade" id="dialog_edit_order" tabindex="-1" role="dialog" data-backdrop="static" data-keyboard="false">
            <div class="modal-dialog modal-xl" role="document">
                <div class="modal-content">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title">
                            <i class="fas fa-edit mr-2"></i>
                            แก้ไขออเดอร์ (ID: ${orderId})
                        </h5>
                        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="text-center py-5">
                            <div class="spinner-border text-primary mb-3" role="status" style="width: 3rem; height: 3rem;">
                                <span class="sr-only">Loading...</span>
                            </div>
                            <h5>กำลังโหลดข้อมูล...</h5>
                            <p class="text-muted">กรุณารอสักครู่</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `;

    $('#dialog_edit_order').remove();
    $('.modal-backdrop').remove();
    $('body').removeClass('modal-open').css('padding-right', '');

    $('body').append(modalHtml);

    $('#dialog_edit_order').modal('show');

    $('#dialog_edit_order').on('hidden.bs.modal', function () {
        $(this).remove();
        $('.modal-backdrop').remove();
        $('body').removeClass('modal-open').css('padding-right', '');
        $(document).off('.editOrder');
        $('#dialog_edit_order').off('.editOrder');
    });
};

fn.app.sales_screen_bwd_2.multiorder.showError = function (message) {
    const errorHtml = `
        <div class="alert alert-danger">
            <h5><i class="fas fa-exclamation-circle mr-2"></i>เกิดข้อผิดพลาด</h5>
            <p class="mb-0">${message}</p>
        </div>
        <div class="text-center mt-3">
            <button class="btn btn-secondary mr-2" data-dismiss="modal">
                <i class="fas fa-times mr-2"></i>ปิด
            </button>
            <button class="btn btn-primary" onclick="location.reload()">
                <i class="fas fa-redo mr-2"></i>รีเฟรชหน้า
            </button>
        </div>
    `;
    $('#dialog_edit_order .modal-body').html(errorHtml);
};

fn.app.sales_screen_bwd_2.multiorder.createEditForm = function (data) {
    const mainOrder = data.main_order;
    const subOrders = data.sub_orders || [];
    const allOrders = [mainOrder, ...subOrders];

    const feeVal = (typeof mainOrder.fee !== 'undefined' && mainOrder.fee !== null) ? mainOrder.fee : 0;

    let formHtml = `
        <form name="form_editorder" id="form_editorder">
            <input type="hidden" name="main_order_id" value="${mainOrder.id}">
            
            <!-- Customer Information -->
            <div class="customer-section mb-4 p-3 bg-light rounded">
                <h6 class="text-primary mb-3">
                    <i class="fas fa-user mr-2"></i>ข้อมูลลูกค้า
                </h6>
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>ชื่อลูกค้า <span class="text-danger">*</span></label>
                            <input type="text" class="form-control custom-input" name="customer_name" 
                                   value="${fn.app.sales_screen_bwd_2.multiorder.escapeHtml(mainOrder.customer_name || '')}" required>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Platform <span class="text-danger">*</span></label>
                            <select name="platform" class="form-control custom-select" required>
                                <option value="">เลือก Platform</option>
                                ${fn.app.sales_screen_bwd_2.multiorder.createPlatformOptions(mainOrder.platform)}
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>เบอร์โทร</label>
                            <input type="text" class="form-control custom-input" name="phone" 
                                   value="${fn.app.sales_screen_bwd_2.multiorder.escapeHtml(mainOrder.phone || '')}" 
                                   pattern="^0$|^[0-9]{10}$" title="กรุณากรอก 0 ตัวเดียว หรือเบอร์โทรศัพท์ 10 หลัก" autocomplete="off"
                                   oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="vat_type">Vats <span class="text-danger">*</span></label>
                            <select name="vat_type" id="vat_type" class="form-control custom-select" required>
                                <option value="">กรุณาเลือกรายการ</option>
                                <option value="0" ${mainOrder.vat_type == '0' ? 'selected' : ''}>ไม่เสีย Vats</option>
                                <option value="2" ${mainOrder.vat_type == '2' ? 'selected' : ''}>เสีย Vats</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Orders Section -->
            <div class="orders-section mb-4">
                <h6 class="text-primary mb-3">
                    <i class="fas fa-box mr-2"></i>รายการสินค้า (${allOrders.length} รายการ)
                </h6>
                <div class="orders-container">
    `;

    allOrders.forEach((order, index) => {
        const isMain = index === 0;

        formHtml += `
            <div class="order-item card mb-3" data-order-index="${index}">
                <div class="card-header ${isMain ? 'bg-primary' : 'bg-secondary'} text-white">
                    <h6 class="mb-0">
                        <i class="fas fa-gem mr-2"></i>
                        ${isMain ? 'รายการหลัก' : `รายการที่ ${index}`}
                        <small class="ml-2">(ID: ${order.id})</small>
                    </h6>
                </div>
                <div class="card-body">
                    <input type="hidden" name="orders[${index}][id]" value="${order.id}">
                    <input type="hidden" name="orders[${index}][is_main]" value="${isMain ? '1' : '0'}">
                    
                    <!-- Basic Information -->
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>จำนวนแท่ง <span class="text-danger">*</span></label>
                                <input type="number" class="form-control custom-input amount-input" name="orders[${index}][amount]" 
                                       value="${order.amount || 0}" min="1" step="1" required>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>ราคา <span class="text-danger">*</span></label>
                                <input type="number" class="form-control custom-input price-input"
                                    name="orders[${index}][price]" value="${order.price || 0}"
                                    min="0" step="0.01" required>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>ส่วนลด</label>
                                <select name="orders[${index}][discount_type]" class="form-control custom-select discount-select">
                                    ${fn.app.sales_screen_bwd_2.multiorder.createDiscountOptions(order.discount_type)}
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>ยอดรวม</label>
                                <input type="text" class="form-control custom-input total-display bg-light" readonly value="0.00">
                            </div>
                        </div>
                    </div>
                    
                    <!-- Product Information -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>สินค้า <span class="text-danger">*</span></label>
                                <select name="orders[${index}][product_id]" class="form-control custom-select product-select" data-index="${index}" required>
                                    <option value="">เลือกสินค้า</option>
                                    ${fn.app.sales_screen_bwd_2.multiorder.createProductOptions(data.products, order.product_id)}
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>ประเภทสินค้า <span class="text-danger">*</span></label>
                                <select name="orders[${index}][product_type]" class="form-control custom-select product-type-select" required>
                                    <option value="${order.product_type || ''}">${order.product_type ? 'ประเภทปัจจุบัน' : 'เลือกประเภท'}</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Engraving Section -->
                    <div class="engrave-section border rounded p-3 bg-light">
                        <h6 class="mb-3">
                            <i class="fas fa-edit mr-2"></i>บริการสลักข้อความ
                        </h6>
                        <div class="form-check-inline mb-3">
                            <div class="form-check mr-4">
                                <input class="form-check-input engrave-radio" type="radio" 
                                       name="orders[${index}][engrave]" id="engrave_yes_${index}" 
                                       value="สลักข้อความบนแท่งเงิน" data-index="${index}"
                                       ${order.engrave === 'สลักข้อความบนแท่งเงิน' ? 'checked' : ''}>
                                <label class="form-check-label" for="engrave_yes_${index}">
                                    <i class="fas fa-check-circle text-success mr-1"></i>สลักข้อความ
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input engrave-radio" type="radio" 
                                       name="orders[${index}][engrave]" id="engrave_no_${index}" 
                                       value="ไม่สลักข้อความบนแท่งเงิน" data-index="${index}"
                                       ${order.engrave !== 'สลักข้อความบนแท่งเงิน' ? 'checked' : ''}>
                                <label class="form-check-label" for="engrave_no_${index}">
                                    <i class="fas fa-times-circle text-danger mr-1"></i>ไม่สลักข้อความ
                                </label>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Font</label>
                                    <select name="orders[${index}][font]" class="form-control custom-select font-select" 
                                            ${order.engrave !== 'สลักข้อความบนแท่งเงิน' ? 'disabled' : ''}>
                                        <option value="">เลือก Font</option>
                                        ${fn.app.sales_screen_bwd_2.multiorder.createFontOptions(data.fonts, order.font)}
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="engrave-cost-label">ข้อความสลัก (คิดเพิ่ม 200 บาท/แท่ง)</label>
                                    <input type="text" class="form-control custom-input carving-input" name="orders[${index}][carving]" 
                                           value="${fn.app.sales_screen_bwd_2.multiorder.escapeHtml(order.carving || '')}"
                                           ${order.engrave !== 'สลักข้อความบนแท่งเงิน' ? 'readonly' : ''}>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="ai-cost-label">รูปภาพ AI (คิดเพิ่ม 300 บาท/แท่ง)</label>
                                    <select name="orders[${index}][ai]" class="form-control ai-select custom-select">
                                        <option ${order.ai == '0' ? 'selected' : ''} value="0">ไม่มีภาพ</option>
                                        <option ${order.ai == '1' ? 'selected' : ''} value="1">มีภาพ AI</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;
    });

    formHtml += `
                </div>
            </div>
            
            <!-- Common Information -->
            <div class="common-section p-3 bg-light rounded">
                <h6 class="text-primary mb-3">
                    <i class="fas fa-cog mr-2"></i>ข้อมูลทั่วไป
                </h6>
                
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>วันที่ซื้อ</label>
                            <input type="text" name="date" class="form-control custom-input" value="${mainOrder.date || ''}" >
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>วันที่จัดส่ง</label>
                            <input type="date" name="delivery_date" class="form-control custom-input" value="${mainOrder.delivery_date || ''}">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>วิธีการส่ง</label>
                            <select name="shipping" class="form-control shipping-select custom-select">
                                ${fn.app.sales_screen_bwd_2.multiorder.createShippingOptions(data.shipping_methods, mainOrder.shipping)}
                            </select>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>ค่าธรรมเนียม</label>
                            <input type="number" name="fee" min="0" step="0.01" class="form-control custom-input" value="${feeVal}">
                        </div>
                    </div>
                    <div class="col-md-8"></div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>ที่อยู่จัดส่ง</label>
                            <textarea name="shipping_address" class="form-control custom-input" rows="3">${fn.app.sales_screen_bwd_2.multiorder.escapeHtml(mainOrder.shipping_address || '')}</textarea>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>ที่อยู่ออกใบเสร็จ</label>
                            <textarea name="billing_address" class="form-control custom-input" rows="3">${fn.app.sales_screen_bwd_2.multiorder.escapeHtml(mainOrder.billing_address || '')}</textarea>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-8">
                        <div class="form-group">
                            <label>หมายเหตุ</label>
                            <textarea name="comment" class="form-control custom-input" rows="2">${fn.app.sales_screen_bwd_2.multiorder.escapeHtml(mainOrder.comment || '')}</textarea>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>หมายเลข Tracking</label>
                            <input type="text" name="Tracking" class="form-control custom-input" value="${fn.app.sales_screen_bwd_2.multiorder.escapeHtml(mainOrder.Tracking || '')}">
                        </div>
                    </div>
                </div>
                
                <!-- Order Summary -->
                <div class="order-summary mt-4 p-3 border rounded bg-white">
                    <h6 class="text-success mb-3">
                        <i class="fas fa-calculator mr-2"></i>สรุปการสั่งซื้อ
                    </h6>
                    <div class="row">
                        <div class="col-md-8">
                            <table class="table table-sm table-borderless">
                                <tr>
                                    <td style="width: 60%;">จำนวนรายการ:</td>
                                    <td class="text-right"><span id="summary-items">${allOrders.length}</span> รายการ</td>
                                </tr>
                                <tr>
                                    <td>ยอดรวมก่อนส่วนลด:</td>
                                    <td class="text-right"><span id="summary-subtotal">0.00</span> บาท</td>
                                </tr>
                                <tr>
                                    <td>ส่วนลดรวม:</td>
                                    <td class="text-right text-danger">-<span id="summary-discount">0.00</span> บาท</td>
                                </tr>
                                <tr>
                                    <td>ค่าสลักข้อความ:</td>
                                    <td class="text-right"><span id="engrave-cost-sign">+</span><span id="summary-engrave">0.00</span> บาท</td>
                                </tr>
                                <tr>
                                    <td>ค่ารูปภาพ AI:</td>
                                    <td class="text-right"><span id="ai-cost-sign">+</span><span id="summary-ai">0.00</span> บาท</td>
                                </tr>
                                <tr>
                                    <td>ค่าจัดส่ง:</td>
                                    <td class="text-right">+<span id="summary-shipping">0.00</span> บาท</td>
                                </tr>
                                <tr>
                                    <td>ค่าธรรมเนียม:</td>
                                    <td class="text-right">-<span id="summary-fee">0.00</span> บาท</td>
                                </tr>
                                <tr class="table-primary font-weight-bold">
                                    <td><strong>ยอดรวมสุทธิ:</strong></td>
                                    <td class="text-right"><strong><span id="summary-total">0.00</span> บาท</strong></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    `;

    $('#dialog_edit_order .modal-body').html(formHtml);

    const footerHtml = `
        <button type="button" class="btn btn-secondary" data-dismiss="modal">
            <i class="fas fa-times mr-2"></i>ปิด
        </button>
        <button type="button" class="btn btn-primary" onclick="fn.app.sales_screen_bwd_2.multiorder.edit()">
            <i class="fas fa-save mr-2"></i>บันทึกการแก้ไข
        </button>
    `;
    if ($('#dialog_edit_order .modal-footer').length === 0) {
        $('#dialog_edit_order .modal-content').append(`<div class="modal-footer">${footerHtml}</div>`);
    } else {
        $('#dialog_edit_order .modal-footer').html(footerHtml);
    }

    fn.app.sales_screen_bwd_2.multiorder.bindEditEvents();
    setTimeout(function () {
        fn.app.sales_screen_bwd_2.multiorder.calculateOrderSummary();
    }, 200);
};

/* ===================== Helpers ===================== */
fn.app.sales_screen_bwd_2.multiorder.escapeHtml = function (text) {
    if (!text) return '';
    return String(text)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
};

fn.app.sales_screen_bwd_2.multiorder.createPlatformOptions = function (selectedPlatform) {
    const platforms = ['Facebook', 'LINE', 'IG', 'Shopee', 'Lazada', 'Website', 'LuckGems', 'TikTok', 'SilverNow', 'WalkIN', 'Exhibition'];
    return platforms.map(platform =>
        `<option ${selectedPlatform === platform ? 'selected' : ''} value="${platform}">${platform}</option>`
    ).join('');
};

fn.app.sales_screen_bwd_2.multiorder.createDiscountOptions = function (selectedDiscount) {
    const discounts = [
        { value: '0', text: 'ไม่มีส่วนลด' },
        { value: '5', text: '5%' },
        { value: '10', text: '10%' },
        { value: '15', text: '15%' },
        { value: '20', text: '20%' },
        { value: '25', text: '25%' },
        { value: '30', text: '30%' }
    ];
    return discounts.map(discount =>
        `<option ${selectedDiscount == discount.value ? 'selected' : ''} value="${discount.value}">${discount.text}</option>`
    ).join('');
};

fn.app.sales_screen_bwd_2.multiorder.createProductOptions = function (products, selectedProductId) {
    if (!products || !Array.isArray(products)) return '';
    return products.map(product =>
        `<option ${selectedProductId == product.id ? 'selected' : ''} value="${product.id}">${fn.app.sales_screen_bwd_2.multiorder.escapeHtml(product.name)}</option>`
    ).join('');
};

fn.app.sales_screen_bwd_2.multiorder.createFontOptions = function (fonts, selectedFont) {
    if (!fonts || !Array.isArray(fonts)) return '';
    return fonts.map(font =>
        `<option ${selectedFont === font.name ? 'selected' : ''} value="${fn.app.sales_screen_bwd_2.multiorder.escapeHtml(font.name)}">${fn.app.sales_screen_bwd_2.multiorder.escapeHtml(font.name)}</option>`
    ).join('');
};

fn.app.sales_screen_bwd_2.multiorder.createShippingOptions = function (shippingMethods, selectedShipping) {
    if (!shippingMethods || !Array.isArray(shippingMethods)) return '';
    return shippingMethods.map(shipping =>
        `<option ${selectedShipping == shipping.id ? 'selected' : ''} value="${shipping.id}">${fn.app.sales_screen_bwd_2.multiorder.escapeHtml(shipping.name)}</option>`
    ).join('');
};

fn.app.sales_screen_bwd_2.multiorder.updateEngraveLabels = function (platform) {
    const $modal = $('#dialog_edit_order');
    const isDiscount = fn.app.sales_screen_bwd_2.multiorder.isDiscountPlatform(platform);
    $modal.find('.order-item').each(function () {
        const $item = $(this);
        const $carvingLabel = $item.find('.engrave-cost-label');
        const $aiLabel = $item.find('.ai-cost-label');

        if (isDiscount) {
            $carvingLabel.text('ข้อความสลัก (ไม่คิดค่า)');
            $aiLabel.text('รูปภาพ AI (ไม่คิดค่า)');
        } else {
            $carvingLabel.text('ข้อความสลัก (คิดเพิ่ม 200 บาท/แท่ง)');
            $aiLabel.text('รูปภาพ AI (คิดเพิ่ม 300 บาท/แท่ง)');
        }
    });
};

fn.app.sales_screen_bwd_2.multiorder.calculateOrderSummary = function () {
    const $modal = $('#dialog_edit_order');

    const getNum = (v) => {
        const s = (v ?? '').toString().replace(/,/g, '').trim();
        const n = parseFloat(s);
        return isNaN(n) ? 0 : n;
    };

    let grandSubtotal = 0, grandDiscount = 0, grandEngrave = 0, grandAI = 0, shippingCost = 0;

    const currentPlatform = ($modal.find('select[name="platform"]').val() || '').toString();
    const isDiscount = currentPlatform ? fn.app.sales_screen_bwd_2.multiorder.isDiscountPlatform(currentPlatform) : false;

    $modal.find('.order-item').each(function () {
        const $item = $(this);

        const amount = getNum($item.find('.amount-input').val());
        const price = getNum($item.find('.price-input').val());           // ✅ 0 ได้
        const discountPercent = getNum($item.find('.discount-select').val());

        const hasEngrave = $item.find('.engrave-radio:checked').val() === 'สลักข้อความบนแท่งเงิน';
        const hasAI = ($item.find('.ai-select').val() || '0') == '1';

        const subtotal = amount * price;
        const discount = subtotal * (discountPercent / 100);
        const engraveCost = hasEngrave ? (isDiscount ? 0 : amount * 200) : 0;
        const aiCost = hasAI ? (isDiscount ? 0 : amount * 300) : 0;

        const itemTotal = subtotal - discount + engraveCost + aiCost;

        $item.find('.total-display').val(itemTotal.toLocaleString('th-TH', {
            minimumFractionDigits: 2, maximumFractionDigits: 2
        }));

        grandSubtotal += subtotal;
        grandDiscount += discount;
        grandEngrave += engraveCost;
        grandAI += aiCost;
    });

    const shippingMethod = $modal.find('.shipping-select').val();
    if (shippingMethod == "1") shippingCost = 50;
    else if (shippingMethod == "2") shippingCost = 100;
    else if (shippingMethod == "3") shippingCost = 150;
    else if (shippingMethod == "4") shippingCost = 0;

    const feeVal = getNum($modal.find('input[name="fee"]').val());

    const grandTotal = grandSubtotal - grandDiscount + grandEngrave + grandAI + shippingCost - feeVal;

    $modal.find('#summary-subtotal').text(grandSubtotal.toLocaleString('th-TH', { minimumFractionDigits: 2 }));
    $modal.find('#summary-discount').text(grandDiscount.toLocaleString('th-TH', { minimumFractionDigits: 2 }));
    $modal.find('#engrave-cost-sign').text(grandEngrave >= 0 ? '+' : '');
    $modal.find('#summary-engrave').text(Math.abs(grandEngrave).toLocaleString('th-TH', { minimumFractionDigits: 2 }));
    $modal.find('#ai-cost-sign').text(grandAI >= 0 ? '+' : '');
    $modal.find('#summary-ai').text(Math.abs(grandAI).toLocaleString('th-TH', { minimumFractionDigits: 2 }));
    $modal.find('#summary-shipping').text(shippingCost.toLocaleString('th-TH', { minimumFractionDigits: 2 }));
    $modal.find('#summary-fee').text(Math.abs(feeVal).toLocaleString('th-TH', { minimumFractionDigits: 2 }));
    $modal.find('#summary-total').text(grandTotal.toLocaleString('th-TH', { minimumFractionDigits: 2 }));

    fn.app.sales_screen_bwd_2.multiorder.updateEngraveLabels(currentPlatform);
};


fn.app.sales_screen_bwd_2.multiorder.bindEditEvents = function () {
    const $modal = $('#dialog_edit_order');
    $modal.off('.editOrder');
    $modal.on('change.editOrder', 'select[name="platform"]', function () {
        fn.app.sales_screen_bwd_2.multiorder.calculateOrderSummary();
    });

    $modal.on('change.editOrder', '.engrave-radio', function () {
        const selectedValue = $(this).val();
        const $item = $(this).closest('.order-item');
        const $carvingInput = $item.find('.carving-input');
        const $fontSelect = $item.find('.font-select');

        if (selectedValue === 'สลักข้อความบนแท่งเงิน') {
            $carvingInput.prop('readonly', false).removeClass('bg-light');
            $fontSelect.prop('disabled', false);
        } else {
            $carvingInput.prop('readonly', true).addClass('bg-light').val('');
            $fontSelect.prop('disabled', true).val('');
        }
        fn.app.sales_screen_bwd_2.multiorder.calculateOrderSummary();
    });

    $modal.on('change.editOrder', '.product-select', function () {
        const productId = $(this).val();
        const $item = $(this).closest('.order-item');
        const $productTypeSelect = $item.find('.product-type-select');

        if (productId) {
            $productTypeSelect.html('<option value="">กำลังโหลด...</option>').prop('disabled', true);
            $.ajax({
                type: 'POST',
                url: 'apps/sales_screen_bwd_2/xhr/action-load-Type.php',
                data: 'id=' + productId,
                timeout: 10000,
                success: function (html) { $productTypeSelect.html(html).prop('disabled', false); },
                error: function () { $productTypeSelect.html('<option value="">เกิดข้อผิดพลาด</option>').prop('disabled', false); }
            });
        } else {
            $productTypeSelect.html('<option value="">เลือกประเภทสินค้า</option>');
        }
    });

    $modal.on('input.editOrder change.editOrder',
        '.amount-input, .price-input, .discount-select, .ai-select, .shipping-select, input[name="fee"]',
        function () { fn.app.sales_screen_bwd_2.multiorder.calculateOrderSummary(); }
    );

    $modal.on('blur.editOrder', 'input[required], select[required]', function () {
        const $t = $(this);
        if (($t.val() || '').toString().trim() === '') $t.addClass('is-invalid'); else $t.removeClass('is-invalid');
    });
};

fn.app.sales_screen_bwd_2.multiorder.edit = function () {
    const $modal = $('#dialog_edit_order');
    const form = $modal.find('#form_editorder');
    if (form.length === 0) {
        alert('ไม่พบฟอร์มแก้ไข');
        return false;
    }

    const mainOrderId = form.find('input[name="main_order_id"]').val();
    const customerName = form.find('input[name="customer_name"]').val().trim();
    const platform = form.find('select[name="platform"]').val();
    const vat_type = form.find('select[name="vat_type"]').val();

    if (!mainOrderId) {
        alert('ไม่พบ ID ของออเดอร์หลัก');
        return false;
    }
    if (!customerName) {
        alert('กรุณากรอกชื่อลูกค้า');
        form.find('input[name="customer_name"]').focus().addClass('is-invalid');
        return false;
    }
    if (!platform) {
        alert('กรุณาเลือก Platform');
        form.find('select[name="platform"]').focus().addClass('is-invalid');
        return false;
    }
    if (!vat_type) {
        alert('กรุณาเลือก Vats');
        form.find('select[name="vat_type"]').focus().addClass('is-invalid');
        return false;
    }

    let hasError = false;
    let errorMessage = '';

    $modal.find('.order-item').each(function (index) {
        const $item = $(this);

        const amountRaw = ($item.find('input[name*="[amount]"]').val() || '').toString().replace(/,/g, '').trim();
        const priceRaw = ($item.find('input[name*="[price]"]').val() || '').toString().replace(/,/g, '').trim();

        const amount = parseFloat(amountRaw);
        const price = parseFloat(priceRaw);

        const productId = $item.find('select[name*="[product_id]"]').val();
        const productType = $item.find('select[name*="[product_type]"]').val();

        if (isNaN(amount) || amount <= 0) {
            errorMessage = `กรุณากรอกจำนวนแท่งที่ถูกต้องสำหรับรายการที่ ${index + 1}`;
            $item.find('input[name*="[amount]"]').focus().addClass('is-invalid');
            hasError = true; return false;
        }
        // ✅ ยอมรับราคา = 0 แต่ไม่ยอมติดลบ
        if (isNaN(price) || price < 0) {
            errorMessage = `กรุณากรอกราคาที่ถูกต้องสำหรับรายการที่ ${index + 1}`;
            $item.find('input[name*="[price]"]').focus().addClass('is-invalid');
            hasError = true; return false;
        }
        if (!productId) {
            errorMessage = `กรุณาเลือกสินค้าสำหรับรายการที่ ${index + 1}`;
            $item.find('select[name*="[product_id]"]').focus().addClass('is-invalid');
            hasError = true; return false;
        }
        if (!productType) {
            errorMessage = `กรุณาเลือกประเภทสินค้าสำหรับรายการที่ ${index + 1}`;
            $item.find('select[name*="[product_type]"]').focus().addClass('is-invalid');
            hasError = true; return false;
        }

        $item.find('.is-invalid').removeClass('is-invalid');
    });


    if (hasError) { alert(errorMessage); return false; }

    const formData = form.serialize();

    $.ajax({
        url: "apps/sales_screen_bwd_2/xhr/action-edit-multiorder.php",
        type: "POST",
        data: formData,
        dataType: "json",
        timeout: 30000,
        beforeSend: function () {
            const $btn = $modal.find('.btn-primary');
            $btn.prop('disabled', true)
                .data('original-html', $btn.html())
                .html('<i class="fas fa-spinner fa-spin mr-2"></i>กำลังบันทึก...');
        },
        success: function (response) {
            if (response.success) {
                if (typeof fn.notify !== 'undefined' && fn.notify.successbox) {
                    fn.notify.successbox(response.msg || 'แก้ไขข้อมูลสำเร็จ', 'สำเร็จ');
                } else {
                    alert(response.msg || 'แก้ไขข้อมูลสำเร็จ');
                }
                fn.app.sales_screen_bwd_2.multiorder.refreshTables();
                $modal.modal('hide');
            } else {
                if (typeof fn.notify !== 'undefined' && fn.notify.warnbox) {
                    fn.notify.warnbox(response.msg || 'เกิดข้อผิดพลาดในการแก้ไข', 'ผิดพลาด');
                } else {
                    alert('เกิดข้อผิดพลาด: ' + (response.msg || 'ไม่สามารถแก้ไขได้'));
                }
            }
        },
        error: function (xhr, status, error) {
            let errorMsg = 'เกิดข้อผิดพลาดในการบันทึก';
            if (status === 'timeout') errorMsg = 'การเชื่อมต่อใช้เวลานานเกินไป กรุณาลองใหม่อีกครั้ง';
            else if (xhr.status === 404) errorMsg = 'ไม่พบไฟล์ action-edit-multiorder.php กรุณาตรวจสอบ path';
            else if (xhr.status === 500) errorMsg = 'เกิดข้อผิดพลาดภายในเซิร์ฟเวอร์';

            if (typeof fn.notify !== 'undefined' && fn.notify.warnbox) {
                fn.notify.warnbox(errorMsg + ': ' + error, 'Connection Error');
            } else {
                alert(errorMsg + ': ' + error);
            }
        },
        complete: function () {
            const $btn = $modal.find('.btn-primary');
            const originalHtml = $btn.data('original-html') || '<i class="fas fa-save mr-2"></i>บันทึกการแก้ไข';
            $btn.prop('disabled', false).html(originalHtml);
        }
    });

    return false;
};

fn.app.sales_screen_bwd_2.multiorder.refreshTables = function () {
    try { if (typeof $('#tblOrders').DataTable === 'function') $('#tblOrders').DataTable().draw(false); } catch (e) { }
    try { if (typeof $('#tblQuickOrder').DataTable === 'function') $('#tblQuickOrder').DataTable().draw(false); } catch (e) { }
    try { if (typeof $('#tblOrdersList').DataTable === 'function') $('#tblOrdersList').DataTable().draw(false); } catch (e) { }
};

fn.app.sales_screen_bwd_2.multiorder.debugEdit = function () {
    const $modal = $('#dialog_edit_order');
    $modal.find('.order-item').each(function (index) {
        const item = $(this);
        console.log(`Order ${index + 1}:`, {
            amount: item.find('.amount-input').val(),
            price: item.find('.price-input').val(),
            total: item.find('.total-display').val()
        });
    });
};

$(document).on('hidden.bs.modal', '#dialog_edit_order', function () {
    $('#dialog_edit_order').off('.editOrder');
    $(document).off('.editOrder');
    $('.modal-backdrop').remove();
    $('body').removeClass('modal-open').css('padding-right', '');
    $(this).remove();
});

if (typeof window !== 'undefined') {
    window.debugEditOrder = fn.app.sales_screen_bwd_2.multiorder.debugEdit;
    window.calculateOrderSummary = fn.app.sales_screen_bwd_2.multiorder.calculateOrderSummary;
    window.refreshTables = fn.app.sales_screen_bwd_2.multiorder.refreshTables;
    window.editOrder = fn.app.sales_screen_bwd_2.multiorder.edit;
}

if (typeof module !== 'undefined' && module.exports) {
    module.exports = fn.app.sales_screen_bwd_2.multiorder;
}
