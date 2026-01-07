window.App = window.App || {};

(function () {
    const TABLE_SEL = '#tblOrder';

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

    function nextBusinessDay(d) {
        const day = d.getDay(); // 0=Sun..6=Sat
        const res = new Date(d);
        if (day === 5) { res.setDate(res.getDate() + 3); }      // Fri -> Mon
        else if (day === 6) { res.setDate(res.getDate() + 2); } // Sat -> Mon
        else { res.setDate(res.getDate() + 1); }                // Sun..Thu -> +1
        return res;
    }

    function ensureDeliveryModal() {
        if ($('#deliveryDateModal').length) return;

        const modalHtml = `
      <div class="modal fade" id="deliveryDateModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm">
          <div class="modal-content">
            <div class="modal-header py-2">
              <h6 class="modal-title">ตั้งวันจัดส่ง</h6>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span>&times;</span></button>
            </div>
            <div class="modal-body pt-2">
              <div class="mb-2">ออเดอร์: <b id="ddm-order-code">-</b></div>

              <div class="d-flex flex-column mb-3">
                <button type="button" class="btn btn-success btn-block mb-2" id="btnSetToday" data-date="">
                  <i class="fa fa-check-circle mr-1"></i> ส่งวันนี้ (<span id="ddm-today-label"></span>)
                </button>
                <button type="button" class="btn btn-dark btn-block mb-2" id="btnSetTomorrow" data-date="">
                  <i class="fa fa-calendar-day mr-1"></i> ส่งพรุ่งนี้ (<span id="ddm-tomorrow-label"></span>)
                </button>
              </div>

              <input type="hidden" id="ddm-order-id" value="">
            </div>
          </div>
        </div>
      </div>`;
        $('body').append(modalHtml);
    }

    function submitDeliveryDate(id, delivery_date) {
        $.ajax({
            url: "apps/prepare_pack_bwd/xhr/create-delivery.php",
            method: "POST",
            dataType: "json",
            data: { id: id, delivery_date: delivery_date },
            success: function (resp) {
                if (resp && resp.success) {
                    if (window.fn && fn.notify && fn.notify.successbox) {
                        fn.notify.successbox(resp.msg || 'สร้างรายการส่งสินค้าเรียบร้อยแล้ว', 'Success');
                    } else {
                        alert(resp.msg || 'สร้างรายการส่งสินค้าเรียบร้อยแล้ว');
                    }
                    $('#deliveryDateModal').modal('hide');

                    var dt = $(TABLE_SEL).DataTable();
                    var row = dt.row(function (idx, data) { return +data.id === +id; });
                    if (row && row.data()) {
                        var rd = row.data();
                        rd.delivery_pack = "1";
                        rd.delivery_pack_updated = (new Date()).toISOString().slice(0, 19).replace('T', ' ');
                        rd.delivery_date = delivery_date;
                        row.data(rd).draw(false);
                    } else {
                        setTimeout(function () { dt.ajax.reload(null, false); }, 400);
                    }
                } else {
                    if (window.fn && fn.notify && fn.notify.warnbox) {
                        fn.notify.warnbox((resp && resp.msg) ? resp.msg : 'ไม่สามารถอัปเดตได้', 'Error');
                    } else {
                        alert((resp && resp.msg) ? resp.msg : 'ไม่สามารถอัปเดตได้');
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

    if ($.fn.DataTable.isDataTable(TABLE_SEL)) {
        $(TABLE_SEL).DataTable().destroy();
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
            url: "apps/prepare_pack_bwd/store/store-orders.php",
            method: "POST",
            type: "POST",
            contentType: "application/json",
            data: function (d) {
                d.date_from = $("form[name=filter] input[name=from]").val();
                d.date_to = $("form[name=filter] input[name=to]").val();
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
            { bSortable: true, data: "code", class: "text-center" },                    // 0
            { bSort: true, data: "delivery_code", class: "text-center" },               // 1
            { bSort: true, data: "customer_name", class: "text-center" },               // 2
            { bSort: true, data: "username", class: "text-center" },                    // 3
            { bSort: true, data: "amount", class: "text-right pr-2" },                  // 4
            { bSort: true, data: "price", class: "text-right pr-2" },                   // 5
            { bSort: true, data: "platform", class: "text-center" },                    // 6
            { bSort: true, data: "net", class: "text-right pr-2" },                     // 7
            { bSort: true, data: "box_count", class: "text-center" },                   // 8
            { bSort: true, data: "date", class: "text-center" },                        // 9
            { bSort: true, data: "delivery_date", class: "text-center" },               // 10
            { bSort: true, data: "orderable_type", class: "text-center" },              // 11
            { bSortable: false, data: null, class: "text-center" },                     // 12 - ปุ่มจัดเตรียม
            { bSort: true, data: "delivery_pack_updated", class: "text-center" }        // 13
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

                const packedBoxes = data.packed_boxes || 0;
                const totalBoxesToPack = data.total_boxes_to_pack || 0;
                const totalBoxes = data.box_count || 0;

                let actionHtml = '';
                if (totalBoxesToPack === 0) {
                    actionHtml = '<span class="badge badge-secondary" style="font-size:13px;">-</span>';
                } else if (packedBoxes >= totalBoxesToPack) {
                    actionHtml = `
        <div class="d-flex align-items-center justify-content-center">
            <span class="badge badge-success mr-2" style="font-size:13px; padding: 6px 12px;">
                <i class="fas fa-check-circle mr-1"></i>แพ็คครบแล้ว (${totalBoxes}/${totalBoxes})
            </span>
            <button type="button" 
                    class="btn btn-sm btn-danger btn-cancel-pack" 
                    data-id="${data.id}" 
                    data-code="${data.code}"
                    title="ยกเลิกการแพ็ค">
                <i class="fas fa-undo"></i>
            </button>
        </div>
    `;
                } else {
                    actionHtml = `
        <button type="button" 
                class="btn btn-sm btn-warning btn-set-delivery" 
                data-id="${data.id}" 
                data-code="${data.code}"
                title="ตั้งวันจัดส่ง">
            <i class="fas fa-calendar-check mr-1"></i>จัดเตรียม (${packedBoxes}/${totalBoxes})
        </button>
    `;
                }
                $('td', row).eq(12).html(actionHtml);

                // 13: Pack Updated Date
                const packUpdated = data.delivery_pack_updated || '';
                $('td', row).eq(13).html(packUpdated ? packUpdated : '<span class="text-muted">-</span>');

            } catch (err) {
                console.error(err);
            }
        }
    });

    // Click: ดูรายละเอียดกล่อง
    $(document)
        .off('click.boxdetail', '.box-detail-btn')
        .on('click.boxdetail', '.box-detail-btn', function (e) {
            e.stopPropagation();
            const orderId = $(this).data('order-id');
            showBoxTrackingModal(orderId);
        });

    // Click: ตั้งวันจัดส่ง
    $(document)
        .off('click.setdelivery', '.btn-set-delivery')
        .on('click.setdelivery', '.btn-set-delivery', function (e) {
            e.stopPropagation();
            const orderId = $(this).data('id');
            const orderCode = $(this).data('code');

            ensureDeliveryModal();

            const today = new Date();
            const tomorrow = nextBusinessDay(today);

            const formatDate = (d) => d.toISOString().slice(0, 10);
            const formatLabel = (d) => {
                const days = ['อา', 'จ', 'อ', 'พ', 'พฤ', 'ศ', 'ส'];
                return `${d.getDate()}/${d.getMonth() + 1}/${d.getFullYear()} (${days[d.getDay()]})`;
            };

            $('#ddm-order-id').val(orderId);
            $('#ddm-order-code').text(orderCode);
            $('#btnSetToday').attr('data-date', formatDate(today));
            $('#ddm-today-label').text(formatLabel(today));
            $('#btnSetTomorrow').attr('data-date', formatDate(tomorrow));
            $('#ddm-tomorrow-label').text(formatLabel(tomorrow));

            $('#deliveryDateModal').modal('show');
        });

    // Modal: ส่งวันนี้
    $(document).on('click', '#btnSetToday', function () {
        const id = $('#ddm-order-id').val();
        const date = $(this).attr('data-date');
        if (id && date) submitDeliveryDate(id, date);
    });

    // Modal: ส่งพรุ่งนี้
    $(document).on('click', '#btnSetTomorrow', function () {
        const id = $('#ddm-order-id').val();
        const date = $(this).attr('data-date');
        if (id && date) submitDeliveryDate(id, date);
    });

    // Click: ยกเลิกการแพ็ค
    $(document)
        .off('click.cancelpack', '.btn-cancel-pack')
        .on('click.cancelpack', '.btn-cancel-pack', function (e) {
            e.stopPropagation();
            const orderId = $(this).data('id');
            const orderCode = $(this).data('code');

            fn.dialog.confirmbox(
                `ต้องการยกเลิกการแพ็คของออเดอร์ ${orderCode} ใช่หรือไม่?`,
                'ยืนยันการยกเลิก',
                function () {
                    cancelPackStatus(orderId);
                }
            );
        });

    function cancelPackStatus(id) {
        $.ajax({
            url: "apps/prepare_pack_bwd/xhr/cancel-pack.php",
            method: "POST",
            dataType: "json",
            data: { id: id },
            success: function (resp) {
                if (resp && resp.success) {
                    if (window.fn && fn.notify && fn.notify.successbox) {
                        fn.notify.successbox(resp.msg || 'ยกเลิกการแพ็คเรียบร้อยแล้ว', 'Success');
                    } else {
                        alert(resp.msg || 'ยกเลิกการแพ็คเรียบร้อยแล้ว');
                    }

                    var dt = $(TABLE_SEL).DataTable();
                    var row = dt.row(function (idx, data) { return +data.id === +id; });
                    if (row && row.data()) {
                        var rd = row.data();
                        rd.delivery_pack = "0";
                        rd.delivery_pack_updated = null;
                        rd.delivery_date = null;
                        row.data(rd).draw(false);
                    } else {
                        setTimeout(function () { dt.ajax.reload(null, false); }, 400);
                    }
                } else {
                    if (window.fn && fn.notify && fn.notify.warnbox) {
                        fn.notify.warnbox((resp && resp.msg) ? resp.msg : 'ไม่สามารถยกเลิกได้', 'Error');
                    } else {
                        alert((resp && resp.msg) ? resp.msg : 'ไม่สามารถยกเลิกได้');
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
        // ... (โค้ด displayTrackingModal เหมือนเดิม)
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
                      onclick="copyToClipboard('${box.tracking}')" 
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

    window.copyToClipboard = function (text) {
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
    };

})();