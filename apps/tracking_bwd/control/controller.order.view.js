window.App = window.App || {};

(function () {
    const TABLE_SEL = '#tblOrder';

    const PLATFORMS = [
        'Facebook',
        'LINE',
        'IG',
        'Shopee',
        'Lazada',
        'Website',
        'LuckGems',
        'TikTok',
        'SilverNow',
        'WalkIN',
        'Exhibition'
    ];

    function getOrderableTypeBadge(type) {
        const config = {
            'delivered_by_company': { class: 'badge-primary', icon: 'fa-truck', text: 'รถบริษัท' },
            'post_office': { class: 'badge-info', icon: 'fa-mail-bulk', text: 'ไปรษณีย์' },
            'receive_at_company': { class: 'badge-success', icon: 'fa-building', text: 'รับที่บริษัท' },
            'receive_at_luckgems': { class: 'badge-warning', icon: 'fa-gem', text: 'รับที่ Luck Gems' }
        };

        const cfg = config[type];
        if (!cfg) {
            return '<span class="badge badge-secondary"><i class="fas fa-question mr-1"></i>ไม่ระบุ</span>';
        }

        return `<span class="badge ${cfg.class}" style="font-size: 12px; padding: 5px 10px;">
            <i class="fas ${cfg.icon} mr-1"></i>${cfg.text}
        </span>`;
    }

    let currentTabMode = 'all';
    let currentTabPlatform = null;

    if ($.fn.DataTable.isDataTable(TABLE_SEL)) {
        $(TABLE_SEL).DataTable().destroy();
    }

    function buildTabsHtml() {
        let html = `
        <div class="order-tab-bar mb-2">
            <ul class="nav nav-tabs">
                <li class="nav-item">
                    <a href="#" class="nav-link ${currentTabMode === 'all' ? 'active' : ''}"
                       data-mode="all">
                        ทั้งหมด
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#" class="nav-link ${currentTabMode === 'no_delivery' ? 'active' : ''}"
                       data-mode="no_delivery">
                        ไม่ระบุวันส่ง
                    </a>
                </li>
        `;

        PLATFORMS.forEach(function (pf) {
            const isActive = (currentTabMode === 'platform' && currentTabPlatform === pf) ? 'active' : '';
            html += `
                <li class="nav-item">
                    <a href="#" class="nav-link ${isActive}"
                       data-mode="platform"
                       data-platform="${pf}">
                        ${pf}
                    </a>
                </li>
            `;
        });

        html += `</ul></div>`;
        return html;
    }

    function setActiveTab(mode, platform) {
        $('.order-tab-bar .nav-link').each(function () {
            const $a = $(this);
            const m = $a.data('mode');
            const p = $a.data('platform') || null;

            if (m === mode && (m !== 'platform' || p === platform)) {
                $a.addClass('active');
            } else {
                $a.removeClass('active');
            }
        });
    }

    function ensureTabs() {
        const $wrapper = $(TABLE_SEL).closest('.dataTables_wrapper');
        if (!$wrapper.length) return;

        if (!$wrapper.prev('.order-tab-bar').length) {
            $wrapper.before(buildTabsHtml());
        }

        $(document)
            .off('click.orderTabs', '.order-tab-bar .nav-link')
            .on('click.orderTabs', '.order-tab-bar .nav-link', function (e) {
                e.preventDefault();
                const $a = $(this);
                const mode = $a.data('mode');
                const platform = $a.data('platform') || null;

                currentTabMode = mode;
                currentTabPlatform = (mode === 'platform') ? platform : null;

                setActiveTab(mode, currentTabPlatform);
                dt.ajax.reload();
            });

        setActiveTab(currentTabMode, currentTabPlatform);
    }

    const dt = $(TABLE_SEL).DataTable({
        responsive: true,
        pageLength: 100,
        bStateSave: false,
        autoWidth: true,
        processing: true,
        serverSide: true,
        deferRender: true,
        searchDelay: 400,
        orderMulti: false,
        stateDuration: 0,

        ajax: {
            url: "apps/tracking_bwd/store/store-order-tracking.php",
            method: "POST",
            type: "POST",
            contentType: "application/json",
            data: function (d) {
                d.date_from = $("form[name=filter] input[name=from]").val();
                d.date_to = $("form[name=filter] input[name=to]").val();
                d.tab_type = currentTabMode;
                d.tab_platform = currentTabPlatform;
                return JSON.stringify(d);
            },
            dataFilter: function (data) {
                try {
                    const i = data.indexOf('{');
                    if (i > 0) data = data.slice(i);
                    JSON.parse(data);
                    return data;
                } catch (e) {
                    console.error("Response not JSON:", data);
                    return '{"error":"Invalid JSON","message":"' + (e.message || 'parse error') + '","data":[]}';
                }
            },
            dataSrc: function (json) {
                if (json.error) {
                    console.error('Server error:', json);
                    alert('เกิดข้อผิดพลาด: ' + (json.message || json.error));
                    return [];
                }
                return json.data || [];
            },
            error: function (xhr, error, thrown) {
                console.error("Ajax Error", {
                    status: xhr.status,
                    statusText: xhr.statusText,
                    error,
                    thrown,
                    responseText: xhr.responseText
                });
                alert(
                    'โหลดข้อมูลล้มเหลว: ' + xhr.status + ' ' + xhr.statusText + '\n' +
                    (xhr.responseText ? xhr.responseText.slice(0, 800) : '')
                );
            }
        },

        aoColumns: [
            { bSortable: true, data: "code", class: "text-center" },                    // 0 - หมายเลขสั่งซื้อ
            { bSort: true, data: "delivery_code", class: "text-center" },               // 1 - หมายเลขส่งของ
            { bSort: true, data: "customer_name", class: "text-center" },               // 2 - ชื่อลูกค้า
            { bSort: true, data: "username", class: "text-center" },                    // 3 - User
            { bSort: true, data: "amount", class: "text-right pr-2" },                  // 4 - จำนวน
            { bSort: true, data: "price", class: "text-right pr-2" },                   // 5 - ราคา
            { bSort: true, data: "platform", class: "text-center" },                    // 6 - Platform
            { bSort: true, data: "net", class: "text-right pr-2" },                     // 7 - ยอดรวม
            { bSort: true, data: "box_count", class: "text-center" },                   // 8 - กล่อง
            { bSort: true, data: "date", class: "text-center" },                        // 9 - สั่งซื้อ
            { bSort: true, data: "delivery_date", class: "text-center" },               // 10 - ส่งของ
            { bSort: true, data: "orderable_type", class: "text-center" },              // 11 - วิธีจัดส่ง
            { bSort: true, data: "delivery_pack", class: "text-center" },               // 12 - จัดเตรียม
            { bSortable: false, data: null, class: "text-center" }                      // 13 - Tracking
        ],
        order: [[10, "asc"]],

        createdRow: function (row, data) {
            try {
                if (!data) return;

                // 0: Order Code
                $('td', row).eq(0).html(
                    '<a href="#apps/schedule_bwd/index.php?view=printablemulti&order_id=' + data.id + '">' + data.code + '</a>'
                );

                // 8: Box Count
                const boxCount = data.box_count || 1;
                const boxHtml = boxCount > 1
                    ? `<span class="badge badge-primary box-detail-btn" 
                              style="font-size:14px; cursor:pointer;" 
                              data-order-id="${data.id}"
                              title="คลิกดูรายละเอียดกล่อง">
                          <i class="fas fa-boxes mr-1"></i>${boxCount}
                       </span>`
                    : `<span class="text-muted box-detail-btn" 
                              style="font-size:14px; cursor:pointer;"
                              data-order-id="${data.id}"
                              title="คลิกดูรายละเอียดกล่อง">
                          <i class="fas fa-box mr-1"></i>1
                       </span>`;
                $('td', row).eq(8).html(boxHtml);

                const orderableType = data.orderable_type || '';
                $('td', row).eq(11).html(getOrderableTypeBadge(orderableType));

                const pack = String(data.delivery_pack);
                if (pack === "1") {
                    $('td', row).eq(12).html('<span class="badge badge-success" style="font-size:16px;"><i class="fa fa-thumbs-up mr-1"></i></span>');
                } else {
                    $('td', row).eq(12).html('<span class="badge badge-danger" style="font-size:16px;"><i class="fa fa-thumbs-down mr-1"></i></span>');
                }

                const trackingCount = data.tracking_count || 0;
                const trackingComplete = trackingCount >= boxCount;

                const trackingHtml = trackingComplete
                    ? `<span class="badge badge-success tracking-status-btn" 
                              style="font-size:13px; padding: 6px 12px; cursor:pointer;" 
                              data-order-id="${data.id}"
                              title="คลิกเพื่อดู/แก้ไข Tracking">
                          <i class="fas fa-check-circle mr-1"></i>ครบ (${trackingCount}/${boxCount})
                       </span>`
                    : `<span class="badge badge-warning tracking-status-btn" 
                              style="font-size:13px; padding: 6px 12px; cursor:pointer; animation: pulse 2s infinite;" 
                              data-order-id="${data.id}"
                              title="คลิกเพื่อใส่ Tracking">
                          <i class="fas fa-exclamation-triangle mr-1"></i>ไม่ครบ (${trackingCount}/${boxCount})
                       </span>`;

                $('td', row).eq(13).html(trackingHtml);

            } catch (err) {
                console.error(err);
            }
        },

        drawCallback: function () {
            ensureTabs();
        },
        initComplete: function () {
            ensureTabs();
        }
    });

    $(document)
        .off('click.boxdetail', '.box-detail-btn')
        .on('click.boxdetail', '.box-detail-btn', function (e) {
            e.stopPropagation();
            const orderId = $(this).data('order-id');
            showBoxTrackingModal(orderId);
        });

    $(document)
        .off('click.trackingstatus', '.tracking-status-btn')
        .on('click.trackingstatus', '.tracking-status-btn', function (e) {
            e.stopPropagation();
            const orderId = $(this).data('order-id');
            console.log('🎯 Clicked order_id from table:', orderId);
            showTrackingInputModal(orderId);
        });

    function showTrackingInputModal(orderId) {
        $.ajax({
            url: 'apps/schedule_bwd/store/get-box-tracking.php',
            method: 'POST',
            data: JSON.stringify({ order_id: orderId }),
            contentType: 'application/json',
            success: function (response) {
                if (response.success) {
                    response.data.parent_order_id = orderId;

                    displayTrackingInputModal(response.data);
                } else {
                    alert('ไม่สามารถโหลดข้อมูลได้: ' + response.msg);
                }
            },
            error: function () {
                alert('เกิดข้อผิดพลาดในการโหลดข้อมูล');
            }
        });
    }

    function displayTrackingInputModal(data) {
        const parentOrderId = data.parent_order_id || data.id || data.order_id;

        let html = `
        <div class="modal fade" id="modalTrackingInput" tabindex="-1" role="dialog">
            <div class="modal-dialog modal-xl" role="document">
                <div class="modal-content">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title">
                            <i class="fas fa-barcode mr-2"></i>
                            ใส่เลข Tracking - ${data.order_code}
                        </h5>
                        <button type="button" class="close text-white" data-dismiss="modal">
                            <span>&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle mr-2"></i>
                            <strong>ลูกค้า:</strong> ${data.customer_name} | 
                            <strong>เบอร์:</strong> ${data.phone || '-'} |
                            <strong>Parent Order ID:</strong> ${parentOrderId}
                        </div>
        `;

        data.boxes.forEach((box, index) => {
            const boxId = box.id;
            const boxNumber = box.box_number !== undefined ? box.box_number : index;
            const currentTracking = box.tracking || '';

            console.log(`📦 Box ${index}:`, {
                boxId,
                boxNumber,
                parentOrderId,
                hasBoxId: !!boxId,
                box
            });

            html += `
            <div class="card mb-3">
                <div class="card-header bg-light">
                    <div class="d-flex justify-content-between align-items-center">
                        <h6 class="mb-0">
                            <i class="fas fa-box mr-2"></i>กล่องที่ ${index + 1}
                            <small class="text-muted">(${box.item_count} รายการ, box_number=${boxNumber}, box_id=${boxId})</small>
                        </h6>
                        ${box.tracking ?
                    '<span class="badge badge-success"><i class="fas fa-check mr-1"></i>มี Tracking แล้ว</span>' :
                    '<span class="badge badge-secondary">ยังไม่มี Tracking</span>'
                }
                    </div>
                </div>
                <div class="card-body">
                    <div class="form-group mb-3">
                        <label for="tracking_${index}">
                            <i class="fas fa-barcode mr-1"></i>เลข Tracking:
                        </label>
                        <div class="input-group">
                            <input type="text" 
                                   class="form-control tracking-input" 
                                   id="tracking_${index}" 
                                   data-box-id="${boxId || ''}"
                                   data-box-number="${boxNumber}"
                                   data-parent-order="${parentOrderId}"
                                   value="${currentTracking}" 
                                   placeholder="ใส่เลข Tracking">
                            ${currentTracking ?
                    `<div class="input-group-append">
                                    <button class="btn btn-outline-danger btn-clear-tracking" 
                                            type="button" 
                                            data-target-input="tracking_${index}"
                                            title="ลบ Tracking">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>` : ''
                }
                        </div>
                    </div>
                    
                    <!-- แสดงรายละเอียดสินค้าในกล่อง -->
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered">
                            <thead class="thead-light">
                                <tr>
                                    <th class="text-center" style="width: 50px;">#</th>
                                    <th>รายการสินค้า</th>
                                    <th class="text-center" style="width: 100px;">จำนวน</th>
                                    <th class="text-center" style="width: 120px;">ราคา/ชิ้น</th>
                                    <th class="text-center" style="width: 120px;">รวม</th>
                                    <th>รายละเอียดพิเศษ</th>
                                </tr>
                            </thead>
                            <tbody>
            `;

            box.items.forEach((item, itemIndex) => {
                const extrasHtml = item.extras.length > 0
                    ? item.extras.map(e => `<span class="badge badge-info mr-1"><i class="fas fa-star mr-1"></i>${e}</span>`).join('')
                    : '<span class="text-muted">-</span>';

                html += `
                <tr>
                    <td class="text-center">${itemIndex + 1}</td>
                    <td><strong>${item.type_name}</strong></td>
                    <td class="text-center">
                        <span class="badge badge-light" style="font-size: 13px;">
                            ${item.amount} ชิ้น
                        </span>
                    </td>
                    <td class="text-center">${item.price.toLocaleString()} บาท</td>
                    <td class="text-center"><strong>${item.total.toLocaleString()}</strong> บาท</td>
                    <td>${extrasHtml}</td>
                </tr>
                `;
            });

            // สรุปของกล่อง
            html += `
                            </tbody>
                            <tfoot class="thead-light">
                                <tr>
                                    <td colspan="4" class="text-right"><strong>ยอดรวมสินค้า:</strong></td>
                                    <td class="text-center"><strong>${box.total_amount.toLocaleString()} บาท</strong></td>
                                    <td></td>
                                </tr>
                                <tr class="table-info">
                                    <td colspan="4" class="text-right">
                                        <strong>ค่าส่ง:</strong>
                                        <small class="text-muted ml-2">
                                            (ฐาน: ${box.shipping_base.toLocaleString()}฿ + 
                                            กล่อง: ${box.shipping_box_fee.toLocaleString()}฿ + 
                                            ห่างไกล: ${box.shipping_remote_fee.toLocaleString()}฿)
                                        </small>
                                    </td>
                                    <td class="text-center"><strong class="text-info">${box.shipping_total.toLocaleString()} บาท</strong></td>
                                    <td></td>
                                </tr>
                                <tr class="table-success">
                                    <td colspan="4" class="text-right"><strong>รวมทั้งสิ้น (กล่องที่ ${index + 1}):</strong></td>
                                    <td class="text-center">
                                        <strong class="text-success" style="font-size: 16px;">
                                            ${(box.total_amount + box.shipping_total).toLocaleString()} บาท
                                        </strong>
                                    </td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
            `;
        });

        html += `
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">
                            <i class="fas fa-times mr-2"></i>ปิด
                        </button>
                        <button type="button" class="btn btn-primary" id="btnSaveTracking">
                            <i class="fas fa-save mr-2"></i>บันทึก Tracking
                        </button>
                    </div>
                </div>
            </div>
        </div>
        `;

        $('#modalTrackingInput').remove();
        $('body').append(html);
        $('#modalTrackingInput').modal('show');
    }

    $(document).on('click', '.btn-clear-tracking', function () {
        const targetInput = $(this).data('target-input');
        $(`#${targetInput}`).val('');
    });

    $(document).on('click', '#btnSaveTracking', function () {
        const trackingData = [];

        $('.tracking-input').each(function () {
            const boxId = $(this).data('box-id');
            const boxNumber = parseInt($(this).data('box-number'));
            const parentOrder = parseInt($(this).data('parent-order'));
            const tracking = $(this).val().trim();


            if (!isNaN(parentOrder) && !isNaN(boxNumber)) {
                trackingData.push({
                    box_id: boxId && boxId !== '' ? parseInt(boxId) : null,
                    box_number: boxNumber,
                    parent_order: parentOrder,
                    tracking: tracking
                });
            } else {
            }
        });


        if (trackingData.length === 0) {
            alert('ไม่พบข้อมูลที่จะบันทึก');
            return;
        }

        saveTrackingNumbers(trackingData);
    });

    function saveTrackingNumbers(trackingData) {
        $.ajax({
            url: 'apps/tracking_bwd/xhr/save-tracking.php',
            method: 'POST',
            dataType: 'json',
            contentType: 'application/json',
            data: JSON.stringify({ tracking_data: trackingData }),
            success: function (resp) {

                if (resp && resp.success) {
                    if (window.fn && fn.notify && fn.notify.successbox) {
                        fn.notify.successbox(resp.msg || 'บันทึก Tracking เรียบร้อยแล้ว', 'Success');
                    } else {
                        alert(resp.msg || 'บันทึก Tracking เรียบร้อยแล้ว');
                    }

                    $('#modalTrackingInput').modal('hide');
                    dt.ajax.reload(null, false);
                } else {
                    if (window.fn && fn.notify && fn.notify.warnbox) {
                        fn.notify.warnbox((resp && resp.msg) ? resp.msg : 'ไม่สามารถบันทึกได้', 'Error');
                    } else {
                        alert((resp && resp.msg) ? resp.msg : 'ไม่สามารถบันทึกได้');
                    }
                }
            },
            error: function (xhr, status, error) {
                if (window.fn && fn.notify && fn.notify.warnbox) {
                    fn.notify.warnbox("Connection error occurred: " + error, "Error");
                } else {
                    alert("Connection error occurred: " + error);
                }
            }
        });
    }

    function showBoxTrackingModal(orderId) {
        $.ajax({
            url: 'apps/schedule_bwd/store/get-box-tracking.php',
            method: 'POST',
            data: JSON.stringify({ order_id: orderId }),
            contentType: 'application/json',
            success: function (response) {
                if (response.success) {
                    displayTrackingModal(response.data);
                } else {
                    alert('ไม่สามารถโหลดข้อมูลได้: ' + response.msg);
                }
            },
            error: function () {
                alert('เกิดข้อผิดพลาดในการโหลดข้อมูล');
            }
        });
    }

    function displayTrackingModal(data) {
        let html = `
        <div class="modal fade" id="modalBoxTracking" tabindex="-1" role="dialog">
            <div class="modal-dialog modal-xl" role="document">
                <div class="modal-content">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title">
                            <i class="fas fa-shipping-fast mr-2"></i>
                            รายละเอียดการจัดส่ง - ${data.order_code}
                        </h5>
                        <button type="button" class="close text-white" data-dismiss="modal">
                            <span>&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
        `;

        data.boxes.forEach((box, index) => {
            const trackingHtml = box.tracking
                ? `<span class="badge badge-success tracking-number" style="font-size: 14px; cursor: pointer;" 
                          onclick='copyToClipboard(${JSON.stringify(box.tracking || "")})'
                          title="คลิกเพื่อคัดลอก">
                      <i class="fas fa-barcode mr-1"></i>${box.tracking}
                      <i class="fas fa-copy ml-2"></i>
                   </span>`
                : `<span class="badge badge-secondary"><i class="fas fa-times mr-1"></i>ยังไม่มี Tracking</span>`;

            const statusHtml = box.delivery_pack === 1
                ? `<span class="badge badge-success px-3 py-2"><i class="fa fa-check-circle mr-1"></i>Pack แล้ว</span>`
                : `<span class="badge badge-warning px-3 py-2"><i class="fa fa-clock mr-1"></i>ยังไม่ Pack</span>`;

            html += `
            <div class="box-detail-card mb-4">
                <div class="box-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="mb-0">
                                <span class="badge badge-primary mr-2" style="font-size: 18px; padding: 10px 15px;">
                                    <i class="fas fa-box mr-2"></i>กล่องที่ ${index + 1}
                                </span>
                                <span class="text-muted" style="font-size: 14px;">
                                    ${box.item_count} รายการ
                                </span>
                            </h5>
                        </div>
                        <div class="text-right">
                            ${statusHtml}
                        </div>
                    </div>
                    <div class="mt-2 d-flex justify-content-between align-items-center">
                        <div>
                            <strong>Tracking:</strong> ${trackingHtml}
                        </div>
                        <div class="shipping-info">
                            <span class="badge badge-info" style="font-size: 13px; padding: 8px 12px;">
                                <i class="fas fa-shipping-fast mr-1"></i>
                                ค่าส่ง: ${box.shipping_total.toLocaleString()} บาท
                            </span>
                        </div>
                    </div>
                </div>
                
                <div class="box-items mt-3">
                    <table class="table table-bordered table-hover table-sm">
                        <thead class="thead-light">
                            <tr>
                                <th class="text-center" style="width: 50px;">#</th>
                                <th>รายการสินค้า</th>
                                <th class="text-center" style="width: 100px;">จำนวน</th>
                                <th class="text-center" style="width: 120px;">ราคา/ชิ้น</th>
                                <th class="text-center" style="width: 120px;">รวม</th>
                                <th>รายละเอียดพิเศษ</th>
                            </tr>
                        </thead>
                        <tbody>
            `;

            box.items.forEach((item, itemIndex) => {
                const extrasHtml = item.extras.length > 0
                    ? item.extras.map(e => `<span class="badge badge-info mr-1"><i class="fas fa-star mr-1"></i>${e}</span>`).join('')
                    : '<span class="text-muted">-</span>';

                html += `
                <tr>
                    <td class="text-center">${itemIndex + 1}</td>
                    <td><strong>${item.type_name}</strong></td>
                    <td class="text-center">
                        <span class="badge badge-light" style="font-size: 13px;">
                            ${item.amount} ชิ้น
                        </span>
                    </td>
                    <td class="text-center">${item.price.toLocaleString()} บาท</td>
                    <td class="text-center"><strong>${item.total.toLocaleString()}</strong> บาท</td>
                    <td>${extrasHtml}</td>
                </tr>
                `;
            });

            html += `
                        </tbody>
                        <tfoot class="thead-light">
                            <tr>
                                <td colspan="4" class="text-right"><strong>ยอดรวมสินค้า:</strong></td>
                                <td class="text-center"><strong>${box.total_amount.toLocaleString()} บาท</strong></td>
                                <td></td>
                            </tr>
                            <tr class="table-info">
                                <td colspan="4" class="text-right">
                                    <strong>ค่าส่ง:</strong>
                                    <small class="text-muted ml-2">
                                        (ฐาน: ${box.shipping_base.toLocaleString()}฿ + 
                                        กล่อง: ${box.shipping_box_fee.toLocaleString()}฿ + 
                                        ห่างไกล: ${box.shipping_remote_fee.toLocaleString()}฿)
                                    </small>
                                </td>
                                <td class="text-center"><strong class="text-info">${box.shipping_total.toLocaleString()} บาท</strong></td>
                                <td></td>
                            </tr>
                            <tr class="table-success">
                                <td colspan="4" class="text-right"><strong>รวมทั้งสิ้น (กล่องที่ ${index + 1}):</strong></td>
                                <td class="text-center">
                                    <strong class="text-success" style="font-size: 16px;">
                                        ${(box.total_amount + box.shipping_total).toLocaleString()} บาท
                                    </strong>
                                </td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
            `;
        });

        const totalBoxes = data.boxes.length;
        const totalItems = data.boxes.reduce((sum, box) => sum + box.item_count, 0);
        const totalAmount = data.boxes.reduce((sum, box) => sum + box.total_amount, 0);
        const totalShipping = data.boxes.reduce((sum, box) => sum + box.shipping_total, 0);
        const grandTotal = totalAmount + totalShipping;
        const packedBoxes = data.boxes.filter(box => box.delivery_pack === 1).length;

        html += `
                        <div class="alert alert-primary mt-3">
                            <div class="row">
                                <div class="col-md-6">
                                    <p class="mb-1"><i class="fas fa-user mr-2"></i><strong>ลูกค้า:</strong> ${data.customer_name}</p>
                                    <p class="mb-1"><i class="fas fa-phone mr-2"></i><strong>เบอร์โทร:</strong> ${data.phone || '-'}</p>
                                    <p class="mb-0"><i class="fas fa-map-marker-alt mr-2"></i><strong>ที่อยู่จัดส่ง:</strong> ${data.shipping_address || '-'}</p>
                                </div>
                                <div class="col-md-6">
                                    <h5 class="mb-3 text-primary"><i class="fas fa-calculator mr-2"></i>สรุปรวมทั้งหมด</h5>
                                    <table class="table table-sm table-bordered bg-white mb-0">
                                        <tr>
                                            <td><i class="fas fa-boxes mr-2"></i>จำนวนกล่อง:</td>
                                            <td class="text-right"><strong>${totalBoxes} กล่อง</strong> (Pack แล้ว ${packedBoxes})</td>
                                        </tr>
                                        <tr>
                                            <td><i class="fas fa-list mr-2"></i>รายการทั้งหมด:</td>
                                            <td class="text-right"><strong>${totalItems} รายการ</strong></td>
                                        </tr>
                                        <tr>
                                            <td><i class="fas fa-box-open mr-2"></i>ยอดรวมสินค้า:</td>
                                            <td class="text-right"><strong>${totalAmount.toLocaleString()} บาท</strong></td>
                                        </tr>
                                        <tr class="table-info">
                                            <td><i class="fas fa-shipping-fast mr-2"></i>ค่าส่งรวม:</td>
                                            <td class="text-right"><strong class="text-info">${totalShipping.toLocaleString()} บาท</strong></td>
                                        </tr>
                                        <tr class="table-success">
                                            <td><strong><i class="fas fa-money-bill-wave mr-2"></i>ยอดรวมทั้งสิ้น:</strong></td>
                                            <td class="text-right">
                                                <strong class="text-success" style="font-size: 18px;">
                                                    ${grandTotal.toLocaleString()} บาท
                                                </strong>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">
                            <i class="fas fa-times mr-2"></i>ปิด
                        </button>
                    </div>
                </div>
            </div>
        </div>
        `;

        $('#modalBoxTracking').remove();
        $('body').append(html);
        $('#modalBoxTracking').modal('show');
    }

    window.copyToClipboard = async function (text) {
        const s = String(text ?? '');

        try {
            // Clipboard API ใช้ได้จริงต้องเป็น HTTPS/localhost
            if (navigator.clipboard && window.isSecureContext) {
                await navigator.clipboard.writeText(s);
                showCopyToast('คัดลอก Tracking แล้ว!');
                return;
            }

            // Fallback: textarea + execCommand
            const el = document.createElement('textarea');
            el.value = s;
            el.setAttribute('readonly', '');
            el.style.position = 'fixed';
            el.style.left = '-9999px';
            el.style.top = '0';
            document.body.appendChild(el);
            el.focus();
            el.select();
            el.setSelectionRange(0, el.value.length);

            const ok = document.execCommand('copy');
            document.body.removeChild(el);

            if (!ok) throw new Error('execCommand(copy) failed');

            showCopyToast('คัดลอก Tracking แล้ว!');
        } catch (err) {
            console.error('Copy failed:', err, {
                isSecureContext: window.isSecureContext,
                hasClipboard: !!navigator.clipboard
            });
            alert('คัดลอกไม่สำเร็จ: ' + s);
        }
    };

    function showCopyToast(msg) {
        const toast = $(`
        <div class="alert alert-success"
             style="position: fixed; top: 20px; right: 20px; z-index: 9999; min-width: 250px;">
            <i class="fas fa-check-circle mr-2"></i>${msg}
        </div>
    `);
        $('body').append(toast);
        setTimeout(() => toast.fadeOut(() => toast.remove()), 2000);
    }


})();