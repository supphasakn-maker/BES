fn = fn || {};
fn.app = fn.app || {};
fn.app.sales_screen_bwd = fn.app.sales_screen_bwd || {};
fn.app.sales_screen_bwd.multiorder = fn.app.sales_screen_bwd.multiorder || {};

fn.app.sales_screen_bwd.multiorder.isDiscountPlatform = function (platform) {
  const discountPlatforms = ['Shopee', 'Lazada', 'TikTok'];
  return discountPlatforms.includes(platform);
};


fn.app.sales_screen_bwd.multiorder.remotePostalCodes = [
  '20120',
  '23170',
  '57170', '57180', '57260',
  '58000', '58110', '58120', '58130', '58140', '58150',
  '63150', '63170',
  '71180', '71240',
  '81150', '81210',
  '82160',
  '83000', '83001', '83002', '83100', '83110', '83111', '83120', '83130', '83150', '83151',
  '84140', '84280', '84310', '84320', '84330', '84360',
  '94000', '94001', '94110', '94120', '94130', '94140', '94150', '94160', '94170', '94180', '94190', '94220', '94230',
  '95000', '95001', '95110', '95120', '95130', '95140', '95150', '95160', '95170',
  '96000', '96110', '96120', '96130', '96140', '96150', '96160', '96170', '96180', '96190', '96210', '96220'
];

fn.app.sales_screen_bwd.multiorder.extractPostalCode = function (address) {
  if (!address) return null;

  const matches = address.match(/\b\d{5}\b/g);

  if (matches && matches.length > 0) {
    return matches[matches.length - 1];
  }

  return null;
};


fn.app.sales_screen_bwd.multiorder.updateRemoteCheckbox = function () {
  const $modal = $('#dialog_edit_order');
  const orderableType = $modal.find('select[name="orderable_type"]').val() || '';
  const $checkbox = $modal.find('input[name="is_remote"]');

  if (orderableType !== 'post_office') {
    $checkbox.prop('checked', false);
    fn.app.sales_screen_bwd.multiorder.calculateOrderSummary();
    return;
  }

  const shippingAddress = $modal.find('textarea[name="shipping_address"]').val() || '';
  const postalCode = fn.app.sales_screen_bwd.multiorder.extractPostalCode(shippingAddress);
  const isRemote = fn.app.sales_screen_bwd.multiorder.isRemoteArea(postalCode);

  $checkbox.prop('checked', isRemote);

  fn.app.sales_screen_bwd.multiorder.calculateOrderSummary();

};

fn.app.sales_screen_bwd.multiorder.isRemoteArea = function (postalCode) {
  if (!postalCode) return false;
  return fn.app.sales_screen_bwd.multiorder.remotePostalCodes.includes(postalCode);
};

fn.app.sales_screen_bwd.multiorder.calculateShippingPerBox = function (boxItems, boxTotal, isRemote, orderableType, shippingMethod) {
  if (shippingMethod === '4' || shippingMethod === 4) {
    return {
      base: 0,
      box_fee: 0,
      remote_fee: 0,
      total: 0,
      wooden_count: 0,
      premium_count: 0
    };
  }

  if (!orderableType || orderableType !== 'post_office') {
    return {
      base: 0,
      box_fee: 0,
      remote_fee: 0,
      total: 0,
      wooden_count: 0,
      premium_count: 0
    };
  }

  let baseShipping = 0;
  if (boxTotal > 0 && boxTotal <= 14999) {
    baseShipping = 50;
  } else if (boxTotal >= 15000 && boxTotal <= 50000) {
    baseShipping = 100;
  } else if (boxTotal > 50000) {
    baseShipping = 100;
  }

  let woodenBoxCount = 0;
  let premiumBoxCount = 0;

  const woodenBoxTypes = [17, 18, 19, 20];
  const premiumBoxTypes = [13, 14, 15, 16, 21, 22, 23, 24, 25];

  boxItems.forEach(item => {
    const productTypeId = parseInt(item.product_type) || 0;
    const amount = parseFloat(item.amount) || 0;

    if (woodenBoxTypes.includes(productTypeId)) {
      woodenBoxCount += amount;
    }

    if (premiumBoxTypes.includes(productTypeId)) {
      premiumBoxCount += amount;
    }
  });

  const boxFee = (woodenBoxCount * 100) + (premiumBoxCount * 25);

  let remoteFee = 0;
  if (isRemote) {
    remoteFee = 50;
  }

  return {
    base: baseShipping,
    box_fee: boxFee,
    remote_fee: remoteFee,
    total: baseShipping + boxFee + remoteFee,
    wooden_count: woodenBoxCount,
    premium_count: premiumBoxCount
  };
};

fn.app.sales_screen_bwd.multiorder.dialog_edit = function (id) {
  if (!id) {
    alert('ไม่พบ ID ของออเดอร์');
    return false;
  }

  fn.app.sales_screen_bwd.multiorder.createLoadingModal(id);

  $.ajax({
    url: "apps/sales_screen_bwd/xhr/action-get-order.php",
    data: { id: id },
    type: "POST",
    dataType: "json",
    timeout: 30000,
    success: function (response) {
      if (response.success) {
        fn.app.sales_screen_bwd.multiorder.createEditForm(response.data);
      } else {
        fn.app.sales_screen_bwd.multiorder.showError(response.msg || 'ไม่สามารถโหลดข้อมูลได้');
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
      fn.app.sales_screen_bwd.multiorder.showError(errorMsg + ': ' + error);
    }
  });
};

fn.app.sales_screen_bwd.multiorder.createLoadingModal = function (orderId) {
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

fn.app.sales_screen_bwd.multiorder.showError = function (message) {
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

fn.app.sales_screen_bwd.multiorder.createOrderableTypeOptions = function (selected) {
  const opts = [
    { value: '', text: 'Select Delivery' },
    { value: 'delivered_by_company', text: 'จัดส่งโดยรถบริษัท' },
    { value: 'post_office', text: 'จัดส่งโดยไปรษณีย์ไทย' },
    { value: 'receive_at_company', text: 'รับสินค้าที่บริษัท' },
    { value: 'receive_at_luckgems', text: 'รับสินค้าที่ Luck Gems' },
  ];
  const sel = (v) => (String(selected || '') === String(v) ? 'selected' : '');
  return opts.map(o => `<option value="${o.value}" ${sel(o.value)}>${o.text}</option>`).join('');
};

fn.app.sales_screen_bwd.multiorder.createEditForm = function (data) {
  const mainOrder = data.main_order;
  const subOrders = data.sub_orders || [];
  const allOrders = [mainOrder, ...subOrders];

  const feeVal = (typeof mainOrder.fee !== 'undefined' && mainOrder.fee !== null) ? mainOrder.fee : 0;

  const shippingRemoteFee = parseFloat(mainOrder.shipping_remote_fee) || 0;
  const isRemote = shippingRemoteFee > 0;
  const boxNumberSet = new Set();
  let totalShipping = 0;

  allOrders.forEach(o => {
    if (!o) return;
    if (o.box_number !== undefined && o.box_number !== null && o.box_number !== '') {
      boxNumberSet.add(String(o.box_number));
    }
    if (o.shipping_total !== undefined && o.shipping_total !== null) {
      const shipVal = parseFloat(o.shipping_total);
      if (!isNaN(shipVal)) totalShipping += shipVal;
    }
  });

  const boxCount = boxNumberSet.size || 1;
  const shippingTotalNum = totalShipping || 0;
  const shippingTotalText = shippingTotalNum.toLocaleString('th-TH', {
    minimumFractionDigits: 2,
    maximumFractionDigits: 2
  });

  let formHtml = `
    <form name="form_editorder" id="form_editorder">
      <input type="hidden" name="main_order_id" value="${mainOrder.id}">
      <input type="hidden" name="box_count" id="box_count" value="${boxCount}">
      
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
                     value="${fn.app.sales_screen_bwd.multiorder.escapeHtml(mainOrder.customer_name || '')}" required>
            </div>
          </div>
          <div class="col-md-3">
            <div class="form-group">
              <label>Platform <span class="text-danger">*</span></label>
              <select name="platform" class="form-control custom-select" required>
                <option value="">เลือก Platform</option>
                ${fn.app.sales_screen_bwd.multiorder.createPlatformOptions(mainOrder.platform)}
              </select>
            </div>
          </div>
          <div class="col-md-3">
            <div class="form-group">
              <label>เบอร์โทร</label>
              <input type="text" class="form-control custom-input" name="phone" 
                     value="${fn.app.sales_screen_bwd.multiorder.escapeHtml(mainOrder.phone || '')}" 
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
        <div class="row mt-2">
          <div class="col-md-4">
            <div class="form-group">
              <label class="form-label">เลข Order จาก Platform อื่นๆ</label>
              <input type="text"
                     class="form-control custom-input"
                     name="order_platform"
                     placeholder="เช่น เลขออเดอร์ Shopee / Lazada / TikTok"
                     value="${fn.app.sales_screen_bwd.multiorder.escapeHtml(mainOrder.order_platform || '')}">
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
    const selectedType = (order.product_type_id ?? order.product_type ?? '');
    const boxNumber = order.box_number || 0;

    formHtml += `
      <div class="order-item card mb-3" data-order-index="${index}" data-selected-type="${fn.app.sales_screen_bwd.multiorder.escapeHtml(selectedType)}">
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
          <input type="hidden" name="orders[${index}][box_number]" value="${order.box_number || 0}">
          
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
                  ${fn.app.sales_screen_bwd.multiorder.createDiscountOptions(order.discount_type)}
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
                  ${fn.app.sales_screen_bwd.multiorder.createProductOptions(data.products, order.product_id)}
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
                    ${fn.app.sales_screen_bwd.multiorder.createFontOptions(data.fonts, order.font)}
                  </select>
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <label class="engrave-cost-label">ข้อความสลัก (คิดเพิ่ม 300 บาท/แท่ง)</label>
                  <input type="text" class="form-control custom-input carving-input" name="orders[${index}][carving]" 
                         value="${fn.app.sales_screen_bwd.multiorder.escapeHtml(order.carving || '')}"
                         ${order.engrave !== 'สลักข้อความบนแท่งเงิน' ? 'readonly' : ''}>
                </div>
              </div>
              <div class="col-md-3">
                <div class="form-group">
                  <label class="ai-cost-label">รูปภาพ AI (คิดเพิ่ม 400 บาท/แท่ง)</label>
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
              <input type="text" name="date" class="form-control custom-input" value="${mainOrder.date || ''}">
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
                <option value="" ${!mainOrder.shipping || mainOrder.shipping == '' ? 'selected' : ''}>คำนวนอัตโนมัติ</option>
                <option value="4" ${mainOrder.shipping == '4' ? 'selected' : ''}>ไม่เสียค่าจัดส่ง</option>
              </select>
            </div>
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
              <textarea name="shipping_address" class="form-control custom-input" rows="3">${fn.app.sales_screen_bwd.multiorder.escapeHtml(mainOrder.shipping_address || '')}</textarea>
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-group">
              <label>ที่อยู่ออกใบเสร็จ</label>
              <textarea name="billing_address" class="form-control custom-input" rows="3">${fn.app.sales_screen_bwd.multiorder.escapeHtml(mainOrder.billing_address || '')}</textarea>
            </div>
          </div>
        </div>

        <!-- รูปแบบการจัดส่ง และพื้นที่ห่างไกล -->
        <div class="row">
          <div class="col-md-6">
            <div class="form-group">
              <label>รูปแบบการจัดส่ง</label>
              <select name="orderable_type" class="form-control custom-select">
                ${fn.app.sales_screen_bwd.multiorder.createOrderableTypeOptions(mainOrder.orderable_type)}
              </select>
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-group">
              <div class="custom-control custom-checkbox mt-4">
                <input type="checkbox" class="custom-control-input" id="is_remote" name="is_remote" 
                       ${isRemote ? 'checked' : ''}>
                <label class="custom-control-label" for="is_remote">
                  <i class="fas fa-map-marker-alt mr-1"></i>พื้นที่ห่างไกล (+50 บาท)
                </label>
              </div>
            </div>
          </div>
        </div>
        
        <div class="row">
          <div class="col-md-8">
            <div class="form-group">
              <label>หมายเหตุ</label>
              <textarea name="comment" class="form-control custom-input" rows="2">${fn.app.sales_screen_bwd.multiorder.escapeHtml(mainOrder.comment || '')}</textarea>
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
                  <td class="text-right">
                    <span id="summary-items">${allOrders.length}</span> รายการ
                  </td>
                </tr>
                <tr>
                  <td>จำนวนกล่อง:</td>
                  <td class="text-right">
                    <span id="summary-boxes" data-box-count="${boxCount}">${boxCount}</span> กล่อง
                  </td>
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
                  <td class="pl-4">• ค่าส่งไปรษณีย์:</td>
                  <td class="text-right">+<span id="summary-shipping-base">0.00</span> บาท</td>
                </tr>
                <tr>
                  <td class="pl-4">• ค่ากล่องพิเศษ:</td>
                  <td class="text-right">
                    +<span id="summary-shipping-box">0.00</span> บาท
                    <small id="shipping-box-details" class="text-muted d-block"></small>
                  </td>
                </tr>
                <tr>
                  <td class="pl-4">• ค่าพื้นที่ห่างไกล:</td>
                  <td class="text-right">+<span id="summary-shipping-remote">0.00</span> บาท</td>
                </tr>
                <tr class="font-weight-bold">
                  <td class="pl-4">รวมค่าจัดส่ง:</td>
                  <td class="text-right">+<span id="summary-shipping-total">0.00</span> บาท</td>
                </tr>
                  <td>ค่าธรรมเนียม:</td>
                  <td class="text-right">-<span id="summary-fee">0.00</span> บาท</td>
                </tr>
                <tr class="table-primary font-weight-bold">
                  <td><strong>ยอดรวมสุทธิ:</strong></td>
                  <td class="text-right">
                    <strong><span id="summary-total">0.00</span> บาท</strong>
                  </td>
                </tr>
              </table>

              <!-- ข้อความเตือนเรื่องเพดาน 50,000 ต่อกล่อง -->
              <div id="summary-box-warning" class="mt-2 small"></div>
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
    <button type="button" class="btn btn-primary" onclick="fn.app.sales_screen_bwd.multiorder.edit()">
      <i class="fas fa-save mr-2"></i>บันทึกการแก้ไข
    </button>
  `;
  if ($('#dialog_edit_order .modal-footer').length === 0) {
    $('#dialog_edit_order .modal-content').append(`<div class="modal-footer">${footerHtml}</div>`);
  } else {
    $('#dialog_edit_order .modal-footer').html(footerHtml);
  }

  $('#dialog_edit_order .order-item').each(function () {
    const $item = $(this);
    const productId = $item.find('.product-select').val();
    const $productTypeSelect = $item.find('.product-type-select');
    const selectedTypeId = ($item.data('selected-type') || '').toString();

    fn.app.sales_screen_bwd.multiorder.loadProductTypes($productTypeSelect, productId, selectedTypeId);
  });

  fn.app.sales_screen_bwd.multiorder.bindEditEvents();

  setTimeout(function () {
    fn.app.sales_screen_bwd.multiorder.calculateOrderSummary();
  }, 200);
};

fn.app.sales_screen_bwd.multiorder.escapeHtml = function (text) {
  if (!text) return '';
  return String(text)
    .replace(/&/g, '&amp;')
    .replace(/</g, '&lt;')
    .replace(/>/g, '&gt;')
    .replace(/"/g, '&quot;')
    .replace(/'/g, '&#039;');
};

fn.app.sales_screen_bwd.multiorder.createPlatformOptions = function (selectedPlatform) {
  const current = (selectedPlatform || '').toString();
  const curNorm = current.toLowerCase();

  const platforms = [
    { value: 'Shopee', label: 'Shopee' },
    { value: 'Lazada', label: 'Lazada' },
    { value: 'TikTok', label: 'TikTok' },
    { value: 'Facebook', label: 'Facebook' },
    { value: 'LINE', label: 'LINE' },
    { value: 'IG', label: 'IG' },
    { value: 'Website', label: 'Website' },
    { value: 'SilverNow', label: 'Silver Now' },
    { value: 'Exhibition', label: 'Exhibition' },
    { value: 'WalkIn', label: 'Walk-in' },
    { value: 'LuckGems', label: 'Luck Gems' }
  ];

  return platforms.map(p => {
    const isSelected = curNorm === p.value.toLowerCase();
    return `<option value="${p.value}" ${isSelected ? 'selected' : ''}>${p.label}</option>`;
  }).join('');
};

fn.app.sales_screen_bwd.multiorder.createDiscountOptions = function (selectedDiscount) {
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

fn.app.sales_screen_bwd.multiorder.createProductOptions = function (products, selectedProductId) {
  if (!products || !Array.isArray(products)) return '';
  return products.map(product =>
    `<option ${selectedProductId == product.id ? 'selected' : ''} value="${product.id}">${fn.app.sales_screen_bwd.multiorder.escapeHtml(product.name)}</option>`
  ).join('');
};

fn.app.sales_screen_bwd.multiorder.createFontOptions = function (fonts, selectedFont) {
  if (!fonts || !Array.isArray(fonts)) return '';
  return fonts.map(font =>
    `<option ${selectedFont === font.name ? 'selected' : ''} value="${fn.app.sales_screen_bwd.multiorder.escapeHtml(font.name)}">${fn.app.sales_screen_bwd.multiorder.escapeHtml(font.name)}</option>`
  ).join('');
};

fn.app.sales_screen_bwd.multiorder.createShippingOptions = function (shippingMethods, selectedShipping) {
  if (!shippingMethods || !Array.isArray(shippingMethods)) return '';
  return shippingMethods.map(shipping =>
    `<option ${selectedShipping == shipping.id ? 'selected' : ''} value="${shipping.id}">${fn.app.sales_screen_bwd.multiorder.escapeHtml(shipping.name)}</option>`
  ).join('');
};

fn.app.sales_screen_bwd.multiorder.updateEngraveLabels = function (platform) {
  const $modal = $('#dialog_edit_order');
  const isDiscount = fn.app.sales_screen_bwd.multiorder.isDiscountPlatform(platform);
  $modal.find('.order-item').each(function () {
    const $item = $(this);
    const $carvingLabel = $item.find('.engrave-cost-label');
    const $aiLabel = $item.find('.ai-cost-label');

    if (isDiscount) {
      $carvingLabel.text('ข้อความสลัก (ไม่คิดค่า)');
      $aiLabel.text('รูปภาพ AI (ไม่คิดค่า)');
    } else {
      $carvingLabel.text('ข้อความสลัก (คิดเพิ่ม 300 บาท/แท่ง)');
      $aiLabel.text('รูปภาพ AI (คิดเพิ่ม 400 บาท/แท่ง)');
    }
  });
};

fn.app.sales_screen_bwd.multiorder.calculateOrderSummary = function () {
  const $modal = $('#dialog_edit_order');

  const getNum = (v) => {
    const s = (v ?? '').toString().replace(/,/g, '').trim();
    const n = parseFloat(s);
    return isNaN(n) ? 0 : n;
  };

  let grandSubtotal = 0,
    grandDiscount = 0,
    grandEngrave = 0,
    grandAI = 0;

  const currentPlatform = ($modal.find('select[name="platform"]').val() || '').toString();
  const isDiscount = currentPlatform
    ? fn.app.sales_screen_bwd.multiorder.isDiscountPlatform(currentPlatform)
    : false;

  const shippingMethod = $modal.find('select[name="shipping"]').val() || '';
  const isFreeShipping = (shippingMethod === '4');

  const boxItemsGrouped = {};
  const boxTotalsNoShipping = {};

  $modal.find('.order-item').each(function () {
    const $item = $(this);

    const amount = getNum($item.find('.amount-input').val());
    const price = getNum($item.find('.price-input').val());
    const discountPercent = getNum($item.find('.discount-select').val());
    const productType = $item.find('.product-type-select').val();

    const hasEngrave = $item.find('.engrave-radio:checked').val() === 'สลักข้อความบนแท่งเงิน';
    const hasAI = ($item.find('.ai-select').val() || '0') == '1';

    const subtotal = amount * price;
    const discount = subtotal * (discountPercent / 100);
    const engraveCost = hasEngrave ? (isDiscount ? 0 : amount * 300) : 0;
    const aiCost = hasAI ? (isDiscount ? 0 : amount * 400) : 0;

    const itemTotal = subtotal - discount + engraveCost + aiCost;

    $item.find('.total-display').val(itemTotal.toLocaleString('th-TH', {
      minimumFractionDigits: 2,
      maximumFractionDigits: 2
    }));

    grandSubtotal += subtotal;
    grandDiscount += discount;
    grandEngrave += engraveCost;
    grandAI += aiCost;

    let boxNumber = 0;
    const boxNumberInput = $item.find('input[name*="[box_number]"]');
    if (boxNumberInput.length > 0) {
      boxNumber = parseInt(boxNumberInput.val()) || 0;
    } else {
      const orderIndex = $item.data('order-index');
      if (orderIndex !== undefined) {
        boxNumber = orderIndex;
      }
    }

    if (!boxItemsGrouped[boxNumber]) {
      boxItemsGrouped[boxNumber] = [];
      boxTotalsNoShipping[boxNumber] = 0;
    }

    boxItemsGrouped[boxNumber].push({
      amount: amount,
      product_type: productType,
      subtotal: subtotal
    });

    boxTotalsNoShipping[boxNumber] += subtotal;
  });

  const orderableType = $modal.find('select[name="orderable_type"]').val() || '';
  const isRemote = $modal.find('input[name="is_remote"]').is(':checked') || false;

  let totalShippingBase = 0;
  let totalShippingBoxFee = 0;
  let totalShippingRemoteFee = 0;
  let totalShippingCost = 0;
  let totalWoodenCount = 0;
  let totalPremiumCount = 0;

  if (!isFreeShipping) {
    Object.keys(boxItemsGrouped).forEach(boxNumber => {
      const boxItems = boxItemsGrouped[boxNumber];
      const boxTotal = boxTotalsNoShipping[boxNumber];

      const shippingCalc = fn.app.sales_screen_bwd.multiorder.calculateShippingPerBox(
        boxItems,
        boxTotal,
        isRemote,
        orderableType,
        shippingMethod
      );

      totalShippingBase += shippingCalc.base;
      totalShippingBoxFee += shippingCalc.box_fee;
      totalShippingRemoteFee += shippingCalc.remote_fee;
      totalShippingCost += shippingCalc.total;
      totalWoodenCount += shippingCalc.wooden_count;
      totalPremiumCount += shippingCalc.premium_count;
    });
  }


  const feeVal = getNum($modal.find('input[name="fee"]').val());

  const grandTotal = grandSubtotal - grandDiscount + grandEngrave + grandAI + totalShippingCost - feeVal;

  $modal.find('#summary-subtotal').text(
    grandSubtotal.toLocaleString('th-TH', { minimumFractionDigits: 2, maximumFractionDigits: 2 })
  );
  $modal.find('#summary-discount').text(
    grandDiscount.toLocaleString('th-TH', { minimumFractionDigits: 2, maximumFractionDigits: 2 })
  );
  $modal.find('#engrave-cost-sign').text(grandEngrave >= 0 ? '+' : '');
  $modal.find('#summary-engrave').text(
    Math.abs(grandEngrave).toLocaleString('th-TH', { minimumFractionDigits: 2, maximumFractionDigits: 2 })
  );
  $modal.find('#ai-cost-sign').text(Math.abs(grandAI) >= 0 ? '+' : '');
  $modal.find('#summary-ai').text(
    Math.abs(grandAI).toLocaleString('th-TH', { minimumFractionDigits: 2, maximumFractionDigits: 2 })
  );

  $modal.find('#summary-shipping-base').text(
    totalShippingBase.toLocaleString('th-TH', { minimumFractionDigits: 2, maximumFractionDigits: 2 })
  );
  $modal.find('#summary-shipping-box').text(
    totalShippingBoxFee.toLocaleString('th-TH', { minimumFractionDigits: 2, maximumFractionDigits: 2 })
  );
  $modal.find('#summary-shipping-remote').text(
    totalShippingRemoteFee.toLocaleString('th-TH', { minimumFractionDigits: 2, maximumFractionDigits: 2 })
  );
  $modal.find('#summary-shipping-total').text(
    totalShippingCost.toLocaleString('th-TH', { minimumFractionDigits: 2, maximumFractionDigits: 2 })
  );

  let boxDetailsText = '';
  if (totalWoodenCount > 0 || totalPremiumCount > 0) {
    const details = [];
    if (totalWoodenCount > 0) {
      details.push(`กล่องไม้ ${totalWoodenCount} ชิ้น`);
    }
    if (totalPremiumCount > 0) {
      details.push(`กล่องพรีเมียม ${totalPremiumCount} ชิ้น`);
    }
    boxDetailsText = `(${details.join(', ')})`;
  }
  $modal.find('#shipping-box-details').text(boxDetailsText);

  $modal.find('#summary-fee').text(
    Math.abs(feeVal).toLocaleString('th-TH', { minimumFractionDigits: 2, maximumFractionDigits: 2 })
  );
  $modal.find('#summary-total').text(
    grandTotal.toLocaleString('th-TH', { minimumFractionDigits: 2, maximumFractionDigits: 2 })
  );

  const boxCount = Object.keys(boxItemsGrouped).length;
  const maxPerBox = 50000;
  const maxAllowed = boxCount * maxPerBox;

  const $warning = $modal.find('#summary-box-warning');
  const $totalSpan = $modal.find('#summary-total');

  if (grandSubtotal > maxAllowed + 0.0001) {
    $warning
      .text(
        'ยอดมูลค่าสินค้ารวมตอนนี้ ' +
        grandSubtotal.toLocaleString('th-TH', { minimumFractionDigits: 2 }) +
        ' บาท ซึ่งเกินวงเงินสูงสุด ' +
        maxAllowed.toLocaleString('th-TH', { minimumFractionDigits: 2 }) +
        ' บาท (' + maxPerBox.toLocaleString('th-TH') +
        ' บาทต่อกล่อง × ' + boxCount + ' กล่อง) – ต้องแยกกล่องเพิ่ม'
      )
      .addClass('text-danger');

    $totalSpan.addClass('text-danger');
  } else {
    $warning.text('').removeClass('text-danger');
    $totalSpan.removeClass('text-danger');
  }

  fn.app.sales_screen_bwd.multiorder.updateEngraveLabels(currentPlatform);
};

fn.app.sales_screen_bwd.multiorder.loadProductTypes = function ($productTypeSelect, productId, selectedId) {
  if (!productId) {
    $productTypeSelect.html('<option value="">เลือกประเภทสินค้า</option>').prop('disabled', false);
    return;
  }
  $productTypeSelect.html('<option value="">กำลังโหลด...</option>').prop('disabled', true);

  $.ajax({
    type: 'POST',
    url: 'apps/sales_screen_bwd/xhr/action-load-Type.php',
    data: { id: productId },
    timeout: 10000,
    success: function (html) {
      $productTypeSelect.html(html).prop('disabled', false);
      if (selectedId !== undefined && selectedId !== null && selectedId !== '') {
        $productTypeSelect.val(String(selectedId));
      }
      if (!$productTypeSelect.val()) {
        $productTypeSelect.prop('selectedIndex', 0);
      }
      fn.app.sales_screen_bwd.multiorder.calculateOrderSummary();
    },
    error: function () {
      $productTypeSelect.html('<option value="">เกิดข้อผิดพลาด</option>').prop('disabled', false);
    }
  });
};

fn.app.sales_screen_bwd.multiorder.bindEditEvents = function () {
  const $modal = $('#dialog_edit_order');
  $modal.off('.editOrder');

  $modal.on('change.editOrder', 'select[name="platform"]', function () {
    fn.app.sales_screen_bwd.multiorder.calculateOrderSummary();
  });

  $modal.on('change.editOrder', 'select[name="shipping"]', function () {
    fn.app.sales_screen_bwd.multiorder.calculateOrderSummary();
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
    fn.app.sales_screen_bwd.multiorder.calculateOrderSummary();
  });

  $modal.on('change.editOrder', '.product-select', function () {
    const productId = $(this).val();
    const $item = $(this).closest('.order-item');
    const $productTypeSelect = $item.find('.product-type-select');
    fn.app.sales_screen_bwd.multiorder.loadProductTypes($productTypeSelect, productId, null);
  });

  $modal.on('change.editOrder', 'select[name="orderable_type"]', function () {
    fn.app.sales_screen_bwd.multiorder.updateRemoteCheckbox();
  });

  $modal.on('change.editOrder', 'input[name="is_remote"]', function () {
    fn.app.sales_screen_bwd.multiorder.calculateOrderSummary();
  });

  $modal.on('change.editOrder', '.product-type-select', function () {
    fn.app.sales_screen_bwd.multiorder.calculateOrderSummary();
  });

  $modal.on(
    'input.editOrder change.editOrder',
    '.amount-input, .price-input, .discount-select, .ai-select, input[name="fee"]',
    function () {
      fn.app.sales_screen_bwd.multiorder.calculateOrderSummary();
    }
  );

  $modal.on('blur.editOrder', 'input[required], select[required]', function () {
    const $t = $(this);
    if (($t.val() || '').toString().trim() === '') $t.addClass('is-invalid');
    else $t.removeClass('is-invalid');
  });
};

fn.app.sales_screen_bwd.multiorder.edit = function () {
  const $modal = $('#dialog_edit_order');
  const $form = $modal.find('#form_editorder');
  if ($form.length === 0) {
    alert('ไม่พบฟอร์มแก้ไข');
    return false;
  }

  const mainOrderId = $form.find('input[name="main_order_id"]').val();
  const customerName = ($form.find('input[name="customer_name"]').val() || '').trim();
  const platform = $form.find('select[name="platform"]').val();
  const vat_type = $form.find('select[name="vat_type"]').val();
  const orderable_type = ($form.find('select[name="orderable_type"]').val() || '').trim();

  const orderPlatform = ($form.find('input[name="order_platform"]').val() || '').trim();
  const marketplacePlatforms = ['Shopee', 'Lazada', 'TikTok', 'SilverNow'];

  if (!mainOrderId) {
    alert('ไม่พบ ID ของออเดอร์หลัก');
    return false;
  }
  if (!customerName) {
    alert('กรุณากรอกชื่อลูกค้า');
    $form.find('input[name="customer_name"]').focus().addClass('is-invalid');
    return false;
  }
  if (!platform) {
    alert('กรุณาเลือก Platform');
    $form.find('select[name="platform"]').focus().addClass('is-invalid');
    return false;
  }
  if (marketplacePlatforms.includes(platform) && !orderPlatform) {
    alert('กรุณากรอกเลข Order จาก Platform (Shopee/Lazada/TikTok/SilverNow)');
    $form.find('input[name="order_platform"]').focus().addClass('is-invalid');
    return false;
  } else {
    $form.find('input[name="order_platform"]').removeClass('is-invalid');
  }

  if (!vat_type) {
    alert('กรุณาเลือก Vats');
    $form.find('select[name="vat_type"]').focus().addClass('is-invalid');
    return false;
  }
  if (!orderable_type) {
    alert('กรุณาเลือกรูปแบบการจัดส่ง');
    $form.find('select[name="orderable_type"]').focus().addClass('is-invalid');
    return false;
  }

  if (orderable_type === 'post_office') {
    const MAX_PER_BOX = 50000;
    const currentPlatform = (platform || '').toString();
    const isDiscountPlatform = currentPlatform
      ? fn.app.sales_screen_bwd.multiorder.isDiscountPlatform(currentPlatform)
      : false;

    let hasOverMaxItem = false;
    let overMaxIndex = -1;
    let overMaxAmount = 0;

    $modal.find('.order-item').each(function (index) {
      const $item = $(this);
      const amount = parseFloat(($item.find('input[name*="[amount]"]').val() || '0').toString().replace(/,/g, '').trim()) || 0;
      const price = parseFloat(($item.find('input[name*="[price]"]').val() || '0').toString().replace(/,/g, '').trim()) || 0;
      const discountPercent = parseFloat($item.find('.discount-select').val() || '0') || 0;

      const hasEngrave = $item.find('.engrave-radio:checked').val() === 'สลักข้อความบนแท่งเงิน';
      const hasAI = ($item.find('.ai-select').val() || '0') == '1';

      const subtotal = amount * price;
      const discount = subtotal * (discountPercent / 100);
      const engraveCost = hasEngrave ? (isDiscountPlatform ? 0 : amount * 300) : 0;
      const aiCost = hasAI ? (isDiscountPlatform ? 0 : amount * 400) : 0;
      const itemTotal = subtotal - discount + engraveCost + aiCost;

      if (itemTotal > MAX_PER_BOX) {
        hasOverMaxItem = true;
        overMaxIndex = index + 1;
        overMaxAmount = itemTotal;
        return false; // break
      }
    });

    if (hasOverMaxItem) {
      alert(
        'ไม่สามารถเปลี่ยนเป็น "จัดส่งโดยไปรษณีย์ไทย" ได้\n\n' +
        'เนื่องจากรายการที่ ' + overMaxIndex + ' มียอดรวม ' +
        overMaxAmount.toLocaleString('th-TH', { minimumFractionDigits: 2 }) + ' บาท\n' +
        'ซึ่งเกิน 50,000 บาทต่อกล่อง\n\n' +
        'วิธีแก้ไข:\n' +
        '1. เปลี่ยนกลับเป็น "รับสินค้าที่บริษัท" หรือ "รับสินค้าที่ Luck Gems"\n' +
        '2. หรือลดจำนวน/ราคาให้ไม่เกิน 50,000 บาทต่อรายการ\n' +
        '3. หรือสร้างออเดอร์ใหม่แยกกล่อง'
      );
      $form.find('select[name="orderable_type"]').focus().addClass('is-invalid');
      return false;
    }
  }

  const currentPlatform = (platform || '').toString();
  const isDiscountPlatform = currentPlatform
    ? fn.app.sales_screen_bwd.multiorder.isDiscountPlatform(currentPlatform)
    : false;

  const MAX_PER_BOX = 50000;
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
      hasError = true;
      return false;
    }
    if (isNaN(price) || price < 0) {
      errorMessage = `กรุณากรอกราคาที่ถูกต้องสำหรับรายการที่ ${index + 1}`;
      $item.find('input[name*="[price]"]').focus().addClass('is-invalid');
      hasError = true;
      return false;
    }
    if (!productId) {
      errorMessage = `กรุณาเลือกสินค้าสำหรับรายการที่ ${index + 1}`;
      $item.find('select[name*="[product_id]"]').focus().addClass('is-invalid');
      hasError = true;
      return false;
    }
    if (!productType) {
      errorMessage = `กรุณาเลือกประเภทสินค้าสำหรับรายการที่ ${index + 1}`;
      $item.find('select[name*="[product_type]"]').focus().addClass('is-invalid');
      hasError = true;
      return false;
    }

    const discountPercentRaw = ($item.find('.discount-select').val() || '0').toString();
    const discountPercent = parseFloat(discountPercentRaw) || 0;

    const hasEngrave = $item.find('.engrave-radio:checked').val() === 'สลักข้อความบนแท่งเงิน';
    const hasAI = ($item.find('.ai-select').val() || '0') == '1';

    const subtotal = amount * price;
    const discount = subtotal * (discountPercent / 100);
    const engraveCost = hasEngrave ? (isDiscountPlatform ? 0 : amount * 300) : 0;
    const aiCost = hasAI ? (isDiscountPlatform ? 0 : amount * 400) : 0;
    const itemTotal = subtotal - discount + engraveCost + aiCost;

    const needsBoxSplit = (orderable_type === 'post_office' || orderable_type === 'delivered_by_company');

    if (needsBoxSplit && itemTotal > MAX_PER_BOX + 0.0001) {
      errorMessage =
        `ยอดรวมของรายการที่ ${index + 1} เท่ากับ ` +
        itemTotal.toLocaleString('th-TH', { minimumFractionDigits: 2 }) +
        ' บาท ซึ่งเกิน 50,000 บาท/กล่อง\n' +
        'กรุณาแยกเป็นกล่องใหม่ หรือปรับจำนวน/ราคาให้ไม่เกิน 50,000 บาทต่อกล่อง';
      $item.find('input[name*="[amount]"]').focus().addClass('is-invalid');
      hasError = true;
      return false;
    }

    $item.find('.is-invalid').removeClass('is-invalid');
  });
  if (hasError) {
    alert(errorMessage);
    return false;
  }

  const getNum = (v) => {
    const s = (v ?? '').toString().replace(/,/g, '').trim();
    const n = parseFloat(s);
    return isNaN(n) ? 0 : n;
  };
  const needsBoxSplit = (orderable_type === 'post_office' || orderable_type === 'delivered_by_company');

  if (needsBoxSplit) {
    const subtotalText = $modal.find('#summary-subtotal').text() || '0';
    const grandSubtotal = getNum(subtotalText);
    const boxCount = parseInt($modal.find('#box_count').val(), 10) || 1;
    const maxAllowed = boxCount * MAX_PER_BOX;

    if (grandSubtotal > maxAllowed + 0.0001) {
      alert(
        'ไม่สามารถบันทึกได้ เนื่องจากมูลค่าสินค้ารวมตอนนี้ ' +
        grandSubtotal.toLocaleString('th-TH', { minimumFractionDigits: 2 }) + ' บาท\n' +
        'เกินวงเงินสูงสุด ' +
        maxAllowed.toLocaleString('th-TH', { minimumFractionDigits: 2 }) +
        ' บาท (ไม่เกิน 50,000 บาทต่อกล่อง × ' + boxCount + ' กล่อง)\n\n' +
        'กรุณาแยกกล่องเพิ่มก่อนทำการบันทึก'
      );
      return false;
    }
  }

  const dataArr = $form.serializeArray();
  const payload = {};
  dataArr.forEach(({ name, value }) => {
    const v = (value ?? '').toString().trim();
    if (name === 'delivery_date' || name === 'date') {
      payload[name] = v;
      return;
    }
    if (name === 'is_remote') {
      payload[name] = v ? '1' : '0';
      return;
    }
    payload[name] = v;
  });

  payload.is_remote = $modal.find('input[name="is_remote"]').is(':checked') ? '1' : '0';
  payload.orderable_type = orderable_type;

  $.ajax({
    url: "apps/sales_screen_bwd/xhr/action-edit-multiorder.php",
    type: "POST",
    data: payload,
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
        fn.app.sales_screen_bwd.multiorder.refreshTables();
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

fn.app.sales_screen_bwd.multiorder.refreshTables = function () {
  try { if (typeof $('#tblOrder').DataTable === 'function') $('#tblOrder').DataTable().draw(false); } catch (e) { }
  try { if (typeof $('#tblQuickOrder').DataTable === 'function') $('#tblQuickOrder').DataTable().draw(false); } catch (e) { }
  try { if (typeof $('#tblOrdersList').DataTable === 'function') $('#tblOrdersList').DataTable().draw(false); } catch (e) { }
};

fn.app.sales_screen_bwd.multiorder.debugEdit = function () {
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
  window.debugEditOrder = fn.app.sales_screen_bwd.multiorder.debugEdit;
  window.calculateOrderSummary = fn.app.sales_screen_bwd.multiorder.calculateOrderSummary;
  window.refreshTables = fn.app.sales_screen_bwd.multiorder.refreshTables;
  window.editOrder = fn.app.sales_screen_bwd.multiorder.edit;
}

if (typeof module !== 'undefined' && module.exports) {
  module.exports = fn.app.sales_screen_bwd.multiorder;
}