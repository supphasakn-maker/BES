window.App = window.App || {};
if (typeof App.checkAll !== "function") {
    App.checkAll = function () {
        return false;
    };
}

(function () {
    const TABLE_SEL = '#tblOrder';
    const EXPORT_URL = 'apps/schedule_bwd/print/thaipost-bulk.php';
    const CHECK_NAME = 'chk_order';
    const HEADER_BTN_ID = 'btnPrintThaiPost';

    const SELECT_KEY = 'thaipost:selectedIds';

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

    // ฟังก์ชันแปลง orderable_type เป็นข้อความภาษาไทย
    function getOrderableTypeText(type) {
        const types = {
            'delivered_by_company': 'จัดส่งโดยรถบริษัท',
            'post_office': 'จัดส่งโดยไปรษณีย์ไทย',
            'receive_at_company': 'รับสินค้าที่บริษัท',
            'receive_at_luckgems': 'รับสินค้าที่ Luck Gems'
        };
        return types[type] || '<span class="text-muted">-</span>';
    }

    // ฟังก์ชันสร้าง badge สำหรับ orderable_type
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

    function loadSelectedFromStorage() {
        try {
            const raw = localStorage.getItem(SELECT_KEY);
            const arr = raw ? JSON.parse(raw) : [];
            return Array.isArray(arr) ? arr.map(v => String(v)) : [];
        } catch (e) {
            console.warn('Cannot parse selection storage', e);
            return [];
        }
    }
    function saveSelectedToStorage(selectedSet) {
        try {
            localStorage.setItem(SELECT_KEY, JSON.stringify(Array.from(selectedSet)));
        } catch (e) {
            console.warn('Cannot save selection storage', e);
        }
    }

    const selected = new Set(loadSelectedFromStorage());

    if ($.fn.DataTable.isDataTable(TABLE_SEL)) {
        $(TABLE_SEL).DataTable().destroy();
    }

    function buildTabsHtml() {
        let html = `
        <div class="order-tab-bar mb-2">
            <ul class="nav nav-tabs">
                <!-- แท็บแรก: ทั้งหมด -->
                <li class="nav-item">
                    <a href="#" class="nav-link ${currentTabMode === 'all' ? 'active' : ''}"
                       data-mode="all">
                        ทั้งหมด
                    </a>
                </li>

                <!-- แท็บสอง: ไม่ระบุวันส่ง -->
                <li class="nav-item">
                    <a href="#" class="nav-link ${currentTabMode === 'no_delivery' ? 'active' : ''}"
                       data-mode="no_delivery">
                        ไม่ระบุวันส่ง
                    </a>
                </li>
    `;

        PLATFORMS.forEach(function (pf) {
            const isActive = (currentTabMode === 'platform' && currentTabPlatform === pf)
                ? 'active'
                : '';
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

        html += `
            </ul>
        </div>
    `;
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
            url: "apps/schedule_bwd/store/store-order.php",
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
                    const i = data.indexOf('{'); if (i > 0) data = data.slice(i);
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
                console.error("Ajax Error", { status: xhr.status, statusText: xhr.statusText, error, thrown, responseText: xhr.responseText });
                alert(
                    'โหลดข้อมูลล้มเหลว: ' + xhr.status + ' ' + xhr.statusText + '\n' +
                    (xhr.responseText ? xhr.responseText.slice(0, 800) : '')
                );
            }
        },

        aoColumns: [
            { bSortable: false, data: "id", sClass: "hidden-xs text-center", sWidth: "20px" },
            { bSortable: false, data: null, sWidth: "40px", class: "text-center" },
            { bSortable: false, data: null, sWidth: "40px", class: "text-center" },
            { bSortable: true, data: "code", class: "text-center" },
            { bSort: true, data: "delivery_code", class: "text-center" },
            { bSort: true, data: "customer_name", class: "text-center" },
            { bSort: true, data: "username", class: "text-center" },
            { bSort: true, data: "amount", class: "text-right pr-2" },
            { bSort: true, data: "price", class: "text-right pr-2" },
            { bSort: true, data: "platform", class: "text-center" },
            { bSort: true, data: "net", class: "text-right pr-2" },
            { bSort: true, data: "box_count", class: "text-center" },
            { bSort: true, data: "date", class: "text-center" },
            { bSort: true, data: "delivery_date", class: "text-center" },
            { bSort: true, data: "orderable_type", class: "text-center" },
            { bSortable: false, data: null, class: "text-center" },
            { bSort: true, data: "delivery_pack", class: "text-center" },
            { bSort: true, data: "sales", class: "text-center" },
            { bSort: true, data: "Tracking", class: "text-center" }
        ],
        order: [[11, "asc"]],

        createdRow: function (row, data) {
            try {
                if (!data) return;
                const idStr = String(data.id);
                const isChecked = selected.has(idStr);

                // Checkbox column
                $('td', row).eq(0).html(
                    '<input type="checkbox" class="row-check" name="' + CHECK_NAME + '" value="' + idStr + '" ' +
                    'data-id="' + idStr + '" ' + (isChecked ? 'checked' : '') + '>'
                );

                // Cancel button - ใช้ class และ data-attribute แทน onclick
                const cancelBtn = `
                <button type="button"
                    class="btn btn-xs btn-outline-danger btn-cancel-order"
                    data-order-id="${data.id}"
                    title="ยกเลิกรายการ">
                    <i class="far fa-times-circle"></i>
                </button>
                `;

                $('td', row).eq(1).html(`
                    <div class="d-flex justify-content-center gap-1">
                        ${cancelBtn}
                    </div>
                `);

                // Box count column
                const boxCount = data.box_count || 1;
                const boxHtml = boxCount > 1
                    ? `<span class="badge badge-primary box-tracking-btn" 
                      style="font-size:14px; cursor:pointer;" 
                      data-order-id="${data.id}"
                      title="คลิกดู Tracking แต่ละกล่อง">
                  <i class="fas fa-boxes mr-1"></i>${boxCount}
               </span>`
                    : `<span class="text-muted box-tracking-btn" 
                      style="font-size:14px; cursor:pointer;"
                      data-order-id="${data.id}"
                      title="คลิกดู Tracking">
                  <i class="fas fa-box mr-1"></i>1
               </span>`;
                $('td', row).eq(11).html(boxHtml);

                // Edit button - ใช้ class และ data-attribute แทน onclick
                $('td', row).eq(2).html(
                    `<button type="button" 
                        class="btn btn-xs btn-outline-secondary btn-edit-order" 
                        data-order-id="${data.id}"
                        title="แก้ไข">
                        <i class="far fa-pen"></i>
                    </button>`
                );

                // Order code link
                $('td', row).eq(3).html(
                    '<a href="#apps/schedule_bwd/index.php?view=printablemulti&order_id=' + data.id + '">' + data.code + '</a>'
                );

                // Orderable type badge
                const orderableType = data.orderable_type || '';
                $('td', row).eq(14).html(getOrderableTypeBadge(orderableType));

                // Action buttons (Add Delivery / Lock) - ใช้ class และ data-attribute แทน onclick
                const $act = $('td', row).eq(15).empty();
                const deliveryIdEmpty = (data.delivery_id == null || data.delivery_id === '' || data.delivery_id === 'null');
                const pack = String(data.delivery_pack);

                if (deliveryIdEmpty || pack === "0") {
                    $act.append(`
                        <button type="button" 
                            class="btn btn-xs btn-outline-danger mr-1 btn-add-delivery" 
                            data-order-id="${data.id}"
                            title="เพิ่มข้อมูลจัดส่ง">
                            <i class="far fa-truck"></i>
                        </button>
                    `);
                    $act.append(`
                        <button type="button" 
                            class="btn btn-xs btn-outline-danger btn-lock-order" 
                            data-order-id="${data.id}"
                            title="ล็อครายการ">
                            <i class="far fa-lock"></i>
                        </button>
                    `);
                }

                // Delivery pack status
                if (pack === "1") {
                    $('td', row).eq(16).html('<span class="badge badge-success" style="font-size:16px;cursor:pointer;"><i class="fa fa-thumbs-up mr-1"></i></span>');
                } else {
                    $('td', row).eq(16).html('<span class="badge badge-danger" style="font-size:16px;cursor:pointer;"><i class="fa fa-thumbs-down mr-1"></i></span>');
                }

                // Tracking status
                const trackingCount = data.tracking_count || 0;
                const trackingComplete = trackingCount >= boxCount;

                const trackingHtml = trackingComplete
                    ? `<span class="badge badge-success" style="font-size:13px; padding: 6px 12px;" 
              title="ใส่ tracking ครบแล้ว ${trackingCount}/${boxCount} กล่อง">
          <i class="fas fa-check-circle mr-1"></i>ครบ (${trackingCount}/${boxCount})
       </span>`
                    : `<span class="badge badge-warning" style="font-size:13px; padding: 6px 12px;" 
              title="ยังใส่ tracking ไม่ครบ ${trackingCount}/${boxCount} กล่อง">
          <i class="fas fa-exclamation-triangle mr-1"></i>ไม่ครบ (${trackingCount}/${boxCount})
       </span>`;

                $('td', row).eq(18).html(trackingHtml);

            } catch (err) {
                console.error(err);
            }
        },

        drawCallback: function () {
            ensureTabs();
            ensureHeaderControls();
            restoreChecksOnCurrentPage();
            updateThaiPostButtonState();
        },
        initComplete: function () {
            ensureTabs();
            ensureHeaderControls();
            restoreChecksOnCurrentPage();
            updateThaiPostButtonState();
        }
    });

    if (window.fn && fn.ui && fn.ui.datatable && typeof fn.ui.datatable.selectable === 'function') {
        fn.ui.datatable.selectable(TABLE_SEL, CHECK_NAME);
    }

    function ensureHeaderControls() {
        const $firstTh = $(`${TABLE_SEL} thead th`).first();
        if (!$firstTh.length) return;

        if (!$firstTh.find('.check-all-btn').length) {
            $firstTh.append(
                '<span type="checkall" control="' + CHECK_NAME + '" class="far fa-lg fa-square check-all-btn" style="cursor:pointer; margin-right:6px;"></span>'
            );
        }

        if (!$firstTh.find('#' + HEADER_BTN_ID).length) {
            $firstTh.append(
                '<button type="button" id="' + HEADER_BTN_ID + '" class="btn btn-sm btn-outline-primary rounded-pill px-2 py-1 ml-1" title="พิมพ์ THAIPOST จากรายการที่เลือก" disabled><i class="fas fa-print"></i> THAIPOST</button>'
            );
        }

        if (!$firstTh.find('#btnClearSel').length) {
            $firstTh.append(
                '<button type="button" id="btnClearSel" class="btn btn-sm btn-outline-danger rounded-pill px-2 py-1 ml-1"><i class="fas fa-ban"></i> CLEAR</button>'
            );
        }
    }

    function restoreChecksOnCurrentPage() {
        $(`${TABLE_SEL} tbody input[name="${CHECK_NAME}"]`).each(function () {
            const idStr = String(this.value);
            this.checked = selected.has(idStr);
        });
    }

    function getSelectedIds() {
        return Array.from(selected);
    }

    function updateThaiPostButtonState() {
        $('#' + HEADER_BTN_ID).prop('disabled', selected.size === 0);
    }

    // Event: Row checkbox change
    $(document)
        .off('change.thaipost', `${TABLE_SEL} tbody input[name="${CHECK_NAME}"]`)
        .on('change.thaipost', `${TABLE_SEL} tbody input[name="${CHECK_NAME}"]`, function () {
            const idStr = String(this.value);
            if (this.checked) selected.add(idStr);
            else selected.delete(idStr);
            saveSelectedToStorage(selected);
            updateThaiPostButtonState();
        });

    // Event: Check all button
    $(document)
        .off('click.thaipost', '.check-all-btn')
        .on('click.thaipost', '.check-all-btn', function (e) {
            e.stopPropagation();
            const $icon = $(this);
            const control = $icon.attr('control') || CHECK_NAME;

            const $rows = $(`${TABLE_SEL} tbody input[name="${control}"]`);
            const anyUnchecked = $rows.toArray().some(el => !el.checked);
            const willCheck = anyUnchecked;

            $rows.each(function () {
                const idStr = String(this.value);
                this.checked = willCheck;
                if (willCheck) selected.add(idStr);
                else selected.delete(idStr);
            });

            $icon.removeClass('fa-square fa-check-square').addClass(willCheck ? 'fa-check-square' : 'fa-square');

            saveSelectedToStorage(selected);
            updateThaiPostButtonState();
        });

    // Event: Print ThaiPost button
    $(document)
        .off('click.thaipost', '#' + HEADER_BTN_ID)
        .on('click.thaipost', '#' + HEADER_BTN_ID, function (e) {
            e.stopPropagation();
            const ids = getSelectedIds();
            if (!ids.length) {
                alert('กรุณาเลือกออเดอร์อย่างน้อย 1 รายการ');
                return;
            }
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = EXPORT_URL;
            form.target = '_blank';
            ids.forEach(id => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'ids[]';
                input.value = id;
                form.appendChild(input);
            });
            document.body.appendChild(form);
            form.submit();
            setTimeout(() => document.body.removeChild(form), 0);
        });

    // Event: Clear selection button
    $(document)
        .off('click.clear', '#btnClearSel')
        .on('click.clear', '#btnClearSel', function () {
            if (!confirm('ต้องการล้างการเลือกทั้งหมดหรือไม่?')) return;

            selected.clear();
            localStorage.removeItem(SELECT_KEY);
            $(`${TABLE_SEL} tbody input[name="${CHECK_NAME}"]`).prop('checked', false);
            updateThaiPostButtonState();
        });

    // Event: Box tracking button
    $(document)
        .off('click.boxtracking', '.box-tracking-btn')
        .on('click.boxtracking', '.box-tracking-btn', function (e) {
            e.stopPropagation();
            const orderId = $(this).data('order-id');
            showBoxTrackingModal(orderId);
        });

    // Event: Cancel order button
    $(document)
        .off('click.cancelorder', '.btn-cancel-order')
        .on('click.cancelorder', '.btn-cancel-order', function (e) {
            e.stopPropagation();
            const orderId = $(this).data('order-id');

            if (window.fn &&
                fn.app &&
                fn.app.sales_screen_bwd &&
                fn.app.sales_screen_bwd.multiorder &&
                typeof fn.app.sales_screen_bwd.multiorder.dialog_remove_order === 'function') {
                fn.app.sales_screen_bwd.multiorder.dialog_remove_order(orderId);
            } else {
                console.error('Function not found: fn.app.sales_screen_bwd.multiorder.dialog_remove_order');
                alert('ไม่สามารถเรียกใช้ฟังก์ชันได้ กรุณาติดต่อผู้ดูแลระบบ');
            }
        });

    // Event: Edit order button
    $(document)
        .off('click.editorder', '.btn-edit-order')
        .on('click.editorder', '.btn-edit-order', function (e) {
            e.stopPropagation();
            const orderId = $(this).data('order-id');

            if (window.fn &&
                fn.app &&
                fn.app.sales_screen_bwd &&
                fn.app.sales_screen_bwd.multiorder &&
                typeof fn.app.sales_screen_bwd.multiorder.dialog_edit === 'function') {
                fn.app.sales_screen_bwd.multiorder.dialog_edit(orderId);
            } else {
                console.error('Function not found: fn.app.sales_screen_bwd.multiorder.dialog_edit');
                alert('ไม่สามารถเรียกใช้ฟังก์ชันได้ กรุณาติดต่อผู้ดูแลระบบ');
            }
        });

    // Event: Add delivery button
    $(document)
        .off('click.adddelivery', '.btn-add-delivery')
        .on('click.adddelivery', '.btn-add-delivery', function (e) {
            e.stopPropagation();
            const orderId = $(this).data('order-id');

            if (window.fn &&
                fn.app &&
                fn.app.sales_screen_bwd &&
                fn.app.sales_screen_bwd.multiorder &&
                typeof fn.app.sales_screen_bwd.multiorder.dialog_add_delivery === 'function') {
                fn.app.sales_screen_bwd.multiorder.dialog_add_delivery(orderId);
            } else {
                console.error('Function not found: fn.app.sales_screen_bwd.multiorder.dialog_add_delivery');
                alert('ไม่สามารถเรียกใช้ฟังก์ชันได้ กรุณาติดต่อผู้ดูแลระบบ');
            }
        });

    // Event: Lock order button
    $(document)
        .off('click.lockorder', '.btn-lock-order')
        .on('click.lockorder', '.btn-lock-order', function (e) {
            e.stopPropagation();
            const orderId = $(this).data('order-id');

            if (window.fn &&
                fn.app &&
                fn.app.sales_screen_bwd &&
                fn.app.sales_screen_bwd.multiorder &&
                typeof fn.app.sales_screen_bwd.multiorder.dialog_lock === 'function') {
                fn.app.sales_screen_bwd.multiorder.dialog_lock(orderId);
            } else {
                console.error('Function not found: fn.app.sales_screen_bwd.multiorder.dialog_lock');
                alert('ไม่สามารถเรียกใช้ฟังก์ชันได้ กรุณาติดต่อผู้ดูแลระบบ');
            }
        });

    // Event: Tracking status click (alternative)
    $(document)
        .off('click.trackingstatus', '.tracking-status')
        .on('click.trackingstatus', '.tracking-status', function (e) {
            e.stopPropagation();
            const orderId = $(this).data('order-id');
            showBoxTrackingModal(orderId);
        });

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

        // แสดงข้อมูลแต่ละกล่อง
        data.boxes.forEach((box, index) => {
            const trackingHtml = box.tracking
                ? `<span class="badge badge-success tracking-number" style="font-size: 14px; cursor: pointer;" 
                      onclick="copyToClipboard('${box.tracking}')" 
                      title="คลิกเพื่อคัดลอก">
                  <i class="fas fa-barcode mr-1"></i>${box.tracking}
                  <i class="fas fa-copy ml-2"></i>
               </span>`
                : `<span class="badge badge-secondary"><i class="fas fa-times mr-1"></i>ยังไม่มี Tracking</span>`;

            const statusHtml = box.delivery_pack === 1
                ? `<span class="badge badge-success px-3 py-2"><i class="fa fa-check-circle mr-1"></i>จัดส่งแล้ว</span>`
                : `<span class="badge badge-warning px-3 py-2"><i class="fa fa-clock mr-1"></i>รอจัดส่ง</span>`;

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
                    <td>
                        <strong>${item.type_name}</strong>
                    </td>
                    <td class="text-center">
                        <span class="badge badge-light" style="font-size: 13px;">
                            ${item.amount} ชิ้น
                        </span>
                    </td>
                    <td class="text-center">${item.price.toLocaleString()} บาท</td>
                    <td class="text-center">
                        <strong>${item.total.toLocaleString()}</strong> บาท
                    </td>
                    <td>${extrasHtml}</td>
                </tr>
            `;
            });

            // แสดงสรุปของแต่ละกล่อง
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

        // สรุปรวมทั้งหมด
        const totalBoxes = data.boxes.length;
        const totalItems = data.boxes.reduce((sum, box) => sum + box.item_count, 0);
        const totalAmount = data.boxes.reduce((sum, box) => sum + box.total_amount, 0);
        const totalShipping = data.boxes.reduce((sum, box) => sum + box.shipping_total, 0);
        const grandTotal = totalAmount + totalShipping;
        const deliveredBoxes = data.boxes.filter(box => box.delivery_pack === 1).length;

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
                                            <td class="text-right"><strong>${totalBoxes} กล่อง</strong> (จัดส่งแล้ว ${deliveredBoxes})</td>
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

    function copyToClipboard(text) {
        if (navigator.clipboard) {
            navigator.clipboard.writeText(text).then(() => {
                const toast = $(`
                <div class="alert alert-success" 
                     style="position: fixed; top: 20px; right: 20px; z-index: 9999; min-width: 250px;">
                    <i class="fas fa-check-circle mr-2"></i>คัดลอก Tracking แล้ว!
                </div>
            `);
                $('body').append(toast);
                setTimeout(() => toast.fadeOut(() => toast.remove()), 2000);
            });
        } else {
            alert('Tracking: ' + text);
        }
    }

})();