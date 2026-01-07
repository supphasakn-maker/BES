var date_base = new Date();
var weekday = ['อา', 'จ', 'อ', 'พ', 'พฤ', 'ศ', 'ส'];

if (typeof fn === 'undefined') window.fn = {};
if (!fn.app) fn.app = {};
if (!fn.app.schedule) fn.app.schedule = {};
if (!fn.app.tracking_bwd_2) fn.app.tracking_bwd_2 = {};
if (!fn.app.tracking_bwd_2.order_bar) fn.app.tracking_bwd_2.order_bar = {};

function initializeOrderTable() {
    $("#tblOrderBar").data("selected", []);

    $("#tblOrderBar").DataTable({
        responsive: true,
        pageLength: 25,
        stateSave: true,
        autoWidth: true,
        processing: true,
        serverSide: true,
        ajax: {
            url: "apps/tracking_bwd_2/store/store-order_bar.php",
            data: function (d) {
                d.from = $("form[name=filter] input[name=from]").val();
                d.to = $("form[name=filter] input[name=to]").val();
            },
            error: function (xhr, error, thrown) {
                console.error('DataTable AJAX Error:', error);
                if (xhr.status === 0) {
                    alert('ไม่สามารถเชื่อมต่อกับเซิร์ฟเวอร์ได้ กรุณาตรวจสอบการเชื่อมต่ออินเทอร์เน็ต');
                } else {
                    alert('เกิดข้อผิดพลาดในการโหลดข้อมูล: ' + thrown);
                }
            }
        },
        columns: [
            { sortable: false, data: "id", className: "hidden-xs text-center", width: "20px" },
            { sortable: true, data: "orderable_type", width: "40px", className: "text-center" },
            { sortable: true, data: "code", className: "text-center" },
            { sortable: true, data: "delivery_code", className: "text-center" },
            { sortable: true, data: "customer_name", className: "text-center" },
            { sortable: true, data: "amount", className: "text-right pr-2" },
            { sortable: true, data: "price", className: "text-right pr-2" },
            { sortable: true, data: "vat", className: "text-right pr-2" },
            { sortable: true, data: "net", className: "text-right pr-2" },
            { sortable: true, data: "date", className: "text-center" },
            { sortable: true, data: "delivery_date", className: "text-center" },
            { sortable: true, data: "Tracking", className: "text-center" }, // ช่องที่แก้เป็น input
            { sortable: true, data: "sales" },
            { sortable: false, data: "id", className: "text-center" } // ตาราง schedule 7 วัน
        ],
        order: [[3, "desc"]],

        // วาดคอนเทนต์ของแต่ละแถวทุกครั้งที่ redraw
        rowCallback: function (row, data) {
            var $row = $(row);
            var cells = $('td', row);

            var isSelected = $.inArray(data.DT_RowId, $("#tblOrderBar").data("selected")) !== -1;
            $row.toggleClass("selected", isSelected);
            cells.eq(0).html(fn.ui.checkbox("chk_order", data.id, isSelected));

            cells.eq(1).addClass('text-nowrap').html(renderOrderableType(data.orderable_type));


            var linkUrl = getOrderLinkUrl(data);
            cells.eq(2).html('<a href="' + linkUrl + '" class="order-link">' + data.code + '</a>');

            // delivery button (col 10) ถ้ายังไม่มี delivery
            if (data.delivery_id == null) {
                cells.eq(10).html(createActionButton("delivery", data.id));
            }

            const trackingVal = (data.Tracking ?? '').toString();
            const safeVal = $('<div>').text(trackingVal).html();
            cells.eq(11).html(`
        <div class="d-flex align-items-center gap-2">
          <input 
            type="text" 
            class="form-control form-control-sm track-input" 
            placeholder="พิมพ์เลขพัสดุ…" 
            value="${safeVal}" 
            data-id="${data.id}" 
            data-old="${safeVal}" 
            style="min-width:180px"
          />
          <span class="save-state text-muted" style="display:none;font-size:.8rem">กำลังบันทึก…</span>
          <i class="fa fa-check text-success ms-2 save-ok" style="display:none" title="บันทึกแล้ว"></i>
        </div>
      `);

            cells.eq(13).attr("date", data.delivery_date).addClass("show_date").html('');
        },

        drawCallback: function () {
            fn.app.tracking_bwd_2.order_bar.date_update();
        },

        language: {
            processing: "กำลังประมวลผล...",
            search: "ค้นหา:",
            lengthMenu: "แสดง _MENU_ รายการ",
            info: "แสดง _START_ ถึง _END_ จาก _TOTAL_ รายการ",
            infoEmpty: "แสดง 0 ถึง 0 จาก 0 รายการ",
            infoFiltered: "(กรองข้อมูล _MAX_ ทุกรายการ)",
            loadingRecords: "กำลังโหลดข้อมูล...",
            zeroRecords: "ไม่พบข้อมูล",
            emptyTable: "ไม่พบข้อมูลในตาราง",
            paginate: {
                first: "หน้าแรก",
                previous: "ก่อนหน้า",
                next: "ถัดไป",
                last: "หน้าสุดท้าย"
            }
        }
    });

    fn.ui.datatable.selectable("#tblOrderBar", "chk_order");
}

function createActionButton(type, id) {
    var buttonConfig = {
        split: { class: "btn btn-xs btn-outline-dark", icon: "far fa-cut", onclick: "fn.app.sales.order.dialog_split(" + id + ")" },
        edit: { class: "btn btn-xs btn-outline-danger mr-1", icon: "far fa-pen", onclick: "fn.app.sales.order.dialog_edit(" + id + ")" },
        delete: { class: "btn btn-xs btn-danger mr-1", icon: "far fa-trash", onclick: "fn.app.sales.order.dialog_remove_each(" + id + ")" },
        delivery: { class: "btn btn-xs btn-outline-danger", icon: "far fa-truck", onclick: "fn.app.sales.order.dialog_add_delivery(" + id + ")" },
        postpone: { class: "btn btn-xs btn-outline-warning mr-1", icon: "far fa-truck", onclick: "fn.app.sales.order.dialog_postpone(" + id + ")" },
        lock: { class: "btn btn-xs btn-outline-danger mr-1", icon: "far fa-lock", onclick: "fn.app.sales.order.dialog_lock(" + id + ")" }
    };
    var config = buttonConfig[type];
    if (!config) { console.warn('Unknown button type:', type); return ''; }
    return fn.ui.button(config.class, config.icon, config.onclick);
}

function getOrderableTypeName(type) {
    const map = {
        delivered_by_company: "จัดส่งโดยรถบริษัท",
        post_office: "จัดส่งโดยไปรษณีย์ไทย",
        receive_at_company: "รับสินค้าที่บริษัท",
        receive_at_luckgems: "รับสินค้าที่ Luck Gems"
    };
    return map[type] || "-";
}

function renderOrderableType(type) {
    const name = getOrderableTypeName(type || "");
    let icon = "fa-question-circle";
    if (type === "delivered_by_company") icon = "fa-truck-moving";
    else if (type === "post_office") icon = "fa-mail-bulk";
    else if (type === "receive_at_company" || type === "receive_at_luckgems") icon = "fa-store";

    return `<span class="badge badge-secondary text-wrap text-left" style="font-size:0.8rem;padding:6px 10px;>
              <i class="fa ${icon} mr-1"></i>${name}
            </span>`;
}

function getOrderLinkUrl(data) {
    var view = (data.product_id == 2) ? 'printablesalebf' : 'printable';
    return '#apps/schedule/index.php?view=' + view + '&order_id=' + data.id;
}

fn.app.tracking_bwd_2.order_bar.date_update = function () {
    updateScheduleHeader();
    updateDeliveryIndicators();
};

function updateScheduleHeader() {
    var s = '';
    s += '<table class="table table-xs mb-0"><tbody><tr>';

    s += '<th width="20" class="p-0 m-0">';
    s += '<button onclick="fn.app.tracking_bwd_2.order_bar.date_previous()" class="btn btn-xs btn-dark m-0" title="วันก่อนหน้า">';
    s += '<i data-feather="chevron-left"></i></button></th>';

    for (var i = 0; i < 7; i++) {
        var current_date = new Date(date_base.getTime());
        current_date.setDate(date_base.getDate() + i);
        var dayName = weekday[current_date.getDay()];
        var dayNumber = current_date.getDate();
        var isToday = isDateToday(current_date);
        s += '<th width="35" class="text-center text-white font-weight-bold' + (isToday ? ' bg-warning text-white' : '') + '">';
        s += dayName + '.' + dayNumber + '</th>';
    }

    s += '<th width="20" class="p-0">';
    s += '<button onclick="fn.app.tracking_bwd_2.order_bar.date_next()" class="btn btn-xs btn-dark m-0" title="วันถัดไป">';
    s += '<i data-feather="chevron-right"></i></button></th>';

    s += '</tr></tbody></table>';
    $('#schedule_header').html(s);
}

function updateDeliveryIndicators() {
    $(".show_date").each(function () {
        var deliveryDate = $(this).attr('date');
        if (!deliveryDate) return;

        var s = '';
        s += '<table class="table table-xs mb-0"><tbody><tr>';
        s += '<td width="20"></td>';

        for (var i = 0; i < 7; i++) {
            var current_date = new Date(date_base.getTime());
            current_date.setDate(date_base.getDate() + i);
            var dateStr = moment(current_date).format('YYYY-MM-DD');
            var hasDelivery = (dateStr === deliveryDate);

            s += '<td width="35" class="text-center">';
            s += hasDelivery ? '<i class="fa fa-sm fa-truck text-danger" title="วันส่งของ"></i>'
                : '<i class="fa fa-sm fa-minus text-muted"></i>';
            s += '</td>';
        }

        s += '<td width="20"></td></tr></tbody></table>';
        $(this).html(s);
    });
}

fn.app.tracking_bwd_2.order_bar.date_next = function () {
    var nextDate = new Date(date_base.getTime());
    nextDate.setDate(date_base.getDate() + 1);
    date_base = nextDate;
    fn.app.tracking_bwd_2.order_bar.date_update();
};

fn.app.tracking_bwd_2.order_bar.date_previous = function () {
    var prevDate = new Date(date_base.getTime());
    prevDate.setDate(date_base.getDate() - 1);
    date_base = prevDate;
    fn.app.tracking_bwd_2.order_bar.date_update();
};

function isDateToday(date) {
    var today = new Date();
    return date.getDate() === today.getDate() &&
        date.getMonth() === today.getMonth() &&
        date.getFullYear() === today.getFullYear();
}

fn.app.tracking_bwd_2.order_bar.jump_to_date = function (targetDate) {
    if (targetDate instanceof Date) {
        date_base = new Date(targetDate.getTime());
        fn.app.tracking_bwd_2.order_bar.date_update();
    }
};

fn.app.tracking_bwd_2.order_bar.jump_to_today = function () {
    date_base = new Date();
    fn.app.tracking_bwd_2.order_bar.date_update();
};

fn.app.tracking_bwd_2.order_bar.get_date_range = function () {
    var dates = [];
    for (var i = 0; i < 7; i++) {
        var current_date = new Date(date_base.getTime());
        current_date.setDate(date_base.getDate() + i);
        dates.push(current_date);
    }
    return dates;
};

function setupKeyboardShortcuts() {
    $(document).on('keydown', function (e) {
        if ($(e.target).is('input, textarea, select')) return;
        switch (e.keyCode) {
            case 37: if (e.ctrlKey) { e.preventDefault(); fn.app.tracking_bwd_2.order_bar.date_previous(); } break;
            case 39: if (e.ctrlKey) { e.preventDefault(); fn.app.tracking_bwd_2.order_bar.date_next(); } break;
            case 84: if (e.ctrlKey) { e.preventDefault(); fn.app.tracking_bwd_2.order_bar.jump_to_today(); } break;
        }
    });
}

let trackingTimers = {};

function saveTracking(orderId, tracking, $input) {
    const $wrap = $input.closest('td');
    const $state = $wrap.find('.save-state');
    const $ok = $wrap.find('.save-ok');

    tracking = (tracking || '').trim();

    $input.prop('disabled', true);
    $state.show();
    $ok.hide();

    $.ajax({
        url: 'apps/tracking_bwd_2/xhr/update-tracking.php',
        method: 'POST',
        dataType: 'json',
        data: { order_id: orderId, tracking: tracking },
    })
        .done(function (resp) {
            if (resp && resp.success) {
                $input.attr('data-old', tracking);
                const dt = $('#tblOrderBar').DataTable();
                const row = dt.row($input.closest('tr'));
                row.invalidate().draw(false);
                $ok.show();
            } else {
                fn.notify ? fn.notify.warnbox(resp && resp.msg ? resp.msg : 'บันทึกไม่สำเร็จ', 'Oops...') : alert(resp?.msg || 'บันทึกไม่สำเร็จ');
                // ย้อนค่ากลับเป็นของเดิม
                $input.val($input.attr('data-old') || '');
            }
        })
        .fail(function (xhr) {
            fn.notify ? fn.notify.warnbox('เชื่อมต่อเซิร์ฟเวอร์ผิดพลาด (' + xhr.status + ')', 'Network') : alert('เชื่อมต่อเซิร์ฟเวอร์ผิดพลาด (' + xhr.status + ')');
            $input.val($input.attr('data-old') || '');
        })
        .always(function () {
            $input.prop('disabled', false);
            $state.hide();
            setTimeout(() => $ok.hide(), 2000);
        });
}

$(document).on('keydown', '.track-input', function (e) {
    if (e.key === 'Enter') {
        e.preventDefault();
        const $input = $(this);
        const id = $input.data('id');
        const val = $input.val().trim();
        const oldVal = ($input.data('old') || '').trim();
        if (val === oldVal) return;

        const confirmMessage =
            'คุณต้องการเปลี่ยน Tracking จาก "' + (oldVal || '-') +
            '" เป็น "' + (val || '-') + '" ใช่หรือไม่?';

        fn.dialog.confirmbox("Confirmation", confirmMessage, function () {
            saveTracking(id, val, $input);
        });
    }
});

$(document).on('blur', '.track-input', function () {
    const $input = $(this);
    const id = $input.data('id');
    const val = $input.val().trim();
    const oldVal = ($input.data('old') || '').trim();
    if (val === oldVal) return;

    const confirmMessage =
        'คุณต้องการเปลี่ยน Tracking จาก "' + (oldVal || '-') +
        '" เป็น "' + (val || '-') + '" ใช่หรือไม่?';

    fn.dialog.confirmbox("Confirmation", confirmMessage, function () {
        saveTracking(id, val, $input);
    });
});


$(document).on('input', '.track-input', function () {
    this.value = this.value.replace(/[^A-Za-z0-9\-_]/g, '');
});


$(document).ready(function () {
    try {
        initializeOrderTable();
        setupKeyboardShortcuts();
        date_base = new Date();
        console.log('Order schedule system initialized successfully');
    } catch (error) {
        console.error('Failed to initialize order schedule system:', error);
        alert('เกิดข้อผิดพลาดในการเริ่มต้นระบบ กรุณาโหลดหน้าใหม่');
    }
});

if (typeof window.ScheduleDebug === 'undefined') {
    window.ScheduleDebug = {
        getCurrentDate: function () { return date_base; },
        getDateRange: fn.app.tracking_bwd_2.order_bar.get_date_range,
        jumpToDate: fn.app.tracking_bwd_2.order_bar.jump_to_date,
        refreshTable: function () { $("#tblOrderBar").DataTable().draw(); }
    };
}
