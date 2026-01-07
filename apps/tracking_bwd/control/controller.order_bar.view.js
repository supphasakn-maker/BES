var date_base = new Date();
var weekday = ['อา', 'จ', 'อ', 'พ', 'พฤ', 'ศ', 'ส'];

if (typeof fn === 'undefined') window.fn = {};
if (!fn.app) fn.app = {};
if (!fn.app.schedule) fn.app.schedule = {};
if (!fn.app.tracking_bwd) fn.app.tracking_bwd = {};
if (!fn.app.tracking_bwd.order_bar) fn.app.tracking_bwd.order_bar = {};

const CHECK_NAME_BAR = 'chk_order';
const EXPORT_URL_BAR = 'apps/tracking_bwd/print/export-excel.php';

const EXPORT_BTN_ID = 'btnExportExcelBar';
const CLEAR_BTN_ID = 'btnClearSelBar';

const SELECT_KEY_BAR = 'orderbar:selectedIds';

(function injectSafetyCSS() {
    const css = `
    #tblOrderBar thead th { overflow: hidden; position: relative; }
    #tblOrderBar .export-clear-wrap { position: relative; z-index: 1; }
    #tblOrderBar tbody td { position: relative; z-index: 2; }
    #tblOrderBar input[type="checkbox"][name="${CHECK_NAME_BAR}"] { cursor: pointer; }

    #${EXPORT_BTN_ID}.btn-white-active {
      background-color: #ffffff !important;
      color: #0d6efd !important;       
      border-color: #0d6efd !important;
    }
    #${EXPORT_BTN_ID}.btn-white-active:hover,
    #${EXPORT_BTN_ID}.btn-white-active:focus {
      background-color: #ffffff !important;
      color: #0a58ca !important;      
      border-color: #0a58ca !important;
    }
    #${EXPORT_BTN_ID}:disabled {
      opacity: .65;
      cursor: not-allowed;
    }
  `;
    const style = document.createElement('style');
    style.type = 'text/css';
    style.appendChild(document.createTextNode(css));
    document.head.appendChild(style);
})();

function ensureTableHeader13() {
    var $tbl = $('#tblOrderBar');
    var need = 13;
    var titles = [
        '', 'รูปแบบการจัดส่ง', 'หมายเลขสั่งซื้อ', 'หมายเลขส่งของ', 'ชื่อลูกค้า',
        'จำนวน', 'ราคา/กิโลกรัม', 'ภาษีมูลค่าเพิ่ม', 'ยอดรวม',
        'วันที่สั่งซื้อ', 'วันที่ส่งของ', 'Tracking', 'ผู้ขาย'
    ];
    var $thead = $tbl.find('thead');
    if ($thead.length === 0 || $thead.find('th').length !== need) {
        if ($thead.length) $thead.remove();
        var html = '<thead><tr>';
        for (var i = 0; i < need; i++) html += '<th>' + (titles[i] || '') + '</th>';
        html += '</tr></thead>';
        $tbl.prepend(html);
    }
}

function loadSelectedOrderBar() {
    try {
        const raw = localStorage.getItem(SELECT_KEY_BAR);
        const arr = raw ? JSON.parse(raw) : [];
        return Array.isArray(arr) ? arr.map(String) : [];
    } catch { return []; }
}
function saveSelectedOrderBar(selectedSet) {
    try { localStorage.setItem(SELECT_KEY_BAR, JSON.stringify(Array.from(selectedSet))); }
    catch (e) { console.warn('Cannot save storage', e); }
}
const selectedOrderBar = new Set(loadSelectedOrderBar());

function ensureHeaderControlsBar() {
    const $ths = $('#tblOrderBar thead th');
    if (!$ths.length) return;

    const $th0 = $ths.eq(0);
    if (!$th0.find('.export-clear-wrap').length) {
        $th0.append(`
      <div class="export-clear-wrap d-flex flex-column align-items-start" style="gap:4px; margin-top:4px; white-space:nowrap;">
        <button type="button" id="${EXPORT_BTN_ID}" class="btn btn-sm btn-outline-primary" disabled>
          <i class="fas fa-file-excel"></i> Export Excel
        </button>
        <button type="button" id="${CLEAR_BTN_ID}" class="btn btn-sm btn-outline-danger">
          <i class="fas fa-broom"></i> Clear Order
        </button>
      </div>
    `);
    }
}

function initializeOrderTable() {
    var $tbl = $("#tblOrderBar");
    if ($.fn.DataTable.isDataTable($tbl)) $tbl.DataTable().destroy();
    ensureTableHeader13();

    $tbl.DataTable({
        responsive: true,
        pageLength: 25,
        stateSave: true,
        autoWidth: true,
        processing: true,
        serverSide: true,
        ajax: {
            url: "apps/tracking_bwd/store/store-order_bar.php",
            data: function (d) {
                d.from = $("form[name=filter] input[name=from]").val();
                d.to = $("form[name=filter] input[name=to]").val();
            },
            error: function (xhr, error, thrown) {
                alert('โหลดข้อมูลล้มเหลว: ' + (thrown || xhr.statusText));
            }
        },
        columns: [
            { sortable: false, data: "id", className: "text-center", width: "28px" },
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
            { sortable: true, data: "Tracking", className: "text-center" },
            { sortable: true, data: "sales" }
        ],
        order: [[3, "desc"]],
        rowCallback: function (row, data) {
            var cells = $('td', row);
            const idStr = String(data.id);
            const isSelected = selectedOrderBar.has(idStr);

            cells.eq(0).html(
                `<input type="checkbox" class="row-check" name="${CHECK_NAME_BAR}" value="${idStr}" ${isSelected ? 'checked' : ''}>`
            );

            cells.eq(1).addClass('text-nowrap').html(renderOrderableType(data.orderable_type));

            var linkUrl = getOrderLinkUrl(data);
            cells.eq(2).html('<a href="' + linkUrl + '" class="order-link">' + data.code + '</a>');

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
        },
        drawCallback: function () {
            ensureHeaderControlsBar();
            restoreChecksOnCurrentPageBar();
            updateExportButtonStateBar();
        },
        initComplete: function () {
            ensureHeaderControlsBar();
            restoreChecksOnCurrentPageBar();
            updateExportButtonStateBar();
        }
    });
}

function getOrderableTypeName(type) {
    const map = {
        delivered_by_company: "จัดส่งโดยรถบริษัท",
        post_office: "จัดส่งโดยไปรษณีย์ไทย",
        receive_at_company: "รับสินค้าที่บริษัท",
        receive_at_luckgems: "รับสินค้าที่ Luck Gems",
        delivered_by_transport: "จัดส่งโดยขนส่ง"
    };
    return map[type] || "-";
}
function renderOrderableType(type) {
    const name = getOrderableTypeName(type || "");
    let icon = "fa-question-circle";
    if (type === "delivered_by_company") icon = "fa-truck-moving";
    else if (type === "post_office") icon = "fa-mail-bulk";
    else if (type === "receive_at_company" || type === "receive_at_luckgems") icon = "fa-store";
    return `<span class="badge badge-secondary text-wrap text-left" style="font-size:0.8rem;padding:6px 10px;">
            <i class="fa ${icon} mr-1"></i>${name}
          </span>`;
}
function getOrderLinkUrl(data) {
    var view = (data.product_id == 2) ? 'printablesalebf' : 'printable';
    return '#apps/schedule/index.php?view=' + view + '&order_id=' + data.id;
}

function restoreChecksOnCurrentPageBar() {
    $('#tblOrderBar tbody input[name="' + CHECK_NAME_BAR + '"]').each(function () {
        this.checked = selectedOrderBar.has(String(this.value));
    });
}

function updateExportButtonStateBar() {
    const active = selectedOrderBar.size > 0;
    const $btn = $('#' + EXPORT_BTN_ID);
    $btn.prop('disabled', !active);
    $btn.toggleClass('btn-white-active', active);
}

$(document)
    .off('change.orderbar', '#tblOrderBar tbody input[name="' + CHECK_NAME_BAR + '"]')
    .on('change.orderbar', '#tblOrderBar tbody input[name="' + CHECK_NAME_BAR + '"]', function () {
        const idStr = String(this.value);
        if (this.checked) selectedOrderBar.add(idStr);
        else selectedOrderBar.delete(idStr);
        saveSelectedOrderBar(selectedOrderBar);
        updateExportButtonStateBar();
    });

$(document)
    .off('click.orderbar-td0', '#tblOrderBar tbody td:first-child')
    .on('click.orderbar-td0', '#tblOrderBar tbody td:first-child', function (e) {
        if ($(e.target).is('input[type="checkbox"]')) return;
        const $cb = $(this).find('input[type="checkbox"][name="' + CHECK_NAME_BAR + '"]');
        if ($cb.length) {
            $cb.prop('checked', !$cb.prop('checked')).trigger('change');
        }
    });

$(document)
    .off('click.orderbar-checkall', 'span[type="checkall"][control="' + CHECK_NAME_BAR + '"]')
    .on('click.orderbar-checkall', 'span[type="checkall"][control="' + CHECK_NAME_BAR + '"]', function () {
        const $this = $(this);
        const $rows = $('#tblOrderBar tbody input[name="' + CHECK_NAME_BAR + '"]');
        const anyUnchecked = $rows.toArray().some(el => !el.checked);
        const willCheck = anyUnchecked;

        $rows.each(function () {
            const idStr = String(this.value);
            this.checked = willCheck;
            if (willCheck) selectedOrderBar.add(idStr);
            else selectedOrderBar.delete(idStr);
        });

        $this.removeClass('fa-square fa-check-square')
            .addClass(willCheck ? 'fa-check-square' : 'fa-square');

        saveSelectedOrderBar(selectedOrderBar);
        updateExportButtonStateBar();
    });

$(document)
    .off('click.orderbar-export', '#' + EXPORT_BTN_ID)
    .on('click.orderbar-export', '#' + EXPORT_BTN_ID, function () {
        const ids = Array.from(selectedOrderBar);
        if (!ids.length) return alert('กรุณาเลือกรายการอย่างน้อย 1 รายการ');
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = EXPORT_URL_BAR;
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
        setTimeout(() => form.remove(), 0);
    });

$(document)
    .off('click.orderbar-clear', '#' + CLEAR_BTN_ID)
    .on('click.orderbar-clear', '#' + CLEAR_BTN_ID, function () {
        if (!confirm('ต้องการล้างการเลือกทั้งหมดหรือไม่?')) return;
        selectedOrderBar.clear();
        localStorage.removeItem(SELECT_KEY_BAR);
        restoreChecksOnCurrentPageBar();
        updateExportButtonStateBar();
    });

$(document)
    .off('input.tracking-bar', '#tblOrderBar .track-input')
    .on('input.tracking-bar', '#tblOrderBar .track-input', function () {
        const $input = $(this);
        const id = $input.data('id');
        const oldVal = $input.data('old');
        const newVal = $input.val().trim();

        if (newVal === oldVal) return;

        const $saveState = $input.siblings('.save-state');
        const $saveOk = $input.siblings('.save-ok');

        $saveState.show();
        $saveOk.hide();

        if ($input.data('saveTimer')) {
            clearTimeout($input.data('saveTimer'));
        }

        const timer = setTimeout(function () {
            $.ajax({
                url: 'apps/tracking_bwd/xhr/update-tracking.php',
                method: 'POST',
                data: {
                    id: id,
                    tracking: newVal
                },
                success: function (response) {
                    $saveState.hide();
                    $saveOk.show().fadeOut(2000);

                    $input.data('old', newVal);
                },
                error: function (xhr, status, error) {
                    $saveState.hide();
                    alert('เกิดข้อผิดพลาดในการบันทึก: ' + error);
                }
            });
        }, 800);

        $input.data('saveTimer', timer);
    });

$(document).ready(function () {
    try { initializeOrderTable(); }
    catch (err) {
        alert('เกิดข้อผิดพลาดในการเริ่มต้นระบบ กรุณาโหลดหน้าใหม่');
    }
});