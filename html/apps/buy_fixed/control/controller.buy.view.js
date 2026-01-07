$(document).ready(function () {
    // Initialize selected data for both tables
    $("#tblPurchase").data("selected", []);
    $("#tblBuyWeChat").data("selected", []);

    // Columns for Buy Fixed table (with Action column)
    const purchaseColumns = [
        { "bSortable": true, "data": "created", "class": "text-center" },      // Confirm
        { "bSortable": true, "data": "date", "class": "text-center" },         // Purchase Date
        { "bSortable": true, "data": "supplier", "class": "text-center" },     // Supplier
        { "bSortable": true, "data": "name", "class": "text-center" },         // Product
        { "bSortable": true, "data": "type", "class": "text-center" },         // Type
        { "bSortable": true, "data": "amount", "class": "text-right" },        // Amount
        { "bSortable": true, "data": "ounces", "class": "text-right" },        // Ounces
        { "bSortable": true, "data": "method", "class": "text-center" },       // Maturity
        { "bSortable": false, "data": "img", "class": "text-center" },         // รูปภาพ
        { "bSortable": true, "data": "user", "class": "text-center" },         // User
        { "bSortable": false, "data": "id", "sClass": "text-center", "sWidth": "120px" } // Action
    ];

    // Columns for WeChat table (without Action column)
    const wechatColumns = [
        { "bSortable": true, "data": "created", "class": "text-center" },      // Confirm
        { "bSortable": true, "data": "date", "class": "text-center" },         // Purchase Date
        { "bSortable": true, "data": "supplier", "class": "text-center" },     // Supplier
        { "bSortable": true, "data": "name", "class": "text-center" },         // Product
        { "bSortable": true, "data": "type", "class": "text-center" },         // Type
        { "bSortable": true, "data": "amount", "class": "text-right" },        // Amount
        { "bSortable": true, "data": "ounces", "class": "text-right" },        // Ounces
        { "bSortable": true, "data": "method", "class": "text-center" },       // Maturity
        { "bSortable": false, "data": "img", "class": "text-center" },         // รูปภาพ
        { "bSortable": true, "data": "user", "class": "text-center" }          // User
    ];

    const commonDataTableConfig = {
        responsive: true,
        "autoWidth": true,
        "processing": true,
        "serverSide": true,
        "order": [[0, "desc"]]
    };

    // Initialize Buy Fixed Table (ICBC)
    var tablePurchase = $("#tblPurchase").DataTable({
        ...commonDataTableConfig,
        "aoColumns": purchaseColumns,
        "ajax": {
            "url": "apps/buy_fixed/store/store-buy.php",
        },
        "createdRow": function (row, data, index) {
            createRowContent(row, data, 'buy_fixed', true); // true = has action column
        },
        "drawCallback": function (settings) {
            addFilterButtons('tblPurchase', 'ICBC');
            updateMissingCount('tblPurchase');
        }
    });

    // Initialize WeChat Table
    var tableBuyWeChat = $("#tblBuyWeChat").DataTable({
        ...commonDataTableConfig,
        "aoColumns": wechatColumns,
        "ajax": {
            "url": "apps/buy_fixed/store/store-wechat.php",
        },
        "createdRow": function (row, data, index) {
            createRowContent(row, data, 'wechat', false); // false = no action column
        },
        "drawCallback": function (settings) {
            addFilterButtons('tblBuyWeChat', 'WeChat');
            updateMissingCount('tblBuyWeChat');
        }
    });

    // Shared function to create row content
    function createRowContent(row, data, tableType, hasActionColumn) {
        // Image column (always index 8)
        var imgHtml = createImageHtml(data, tableType);
        $("td", row).eq(8).html(imgHtml);

        // Action column (only for tables that have it)
        if (hasActionColumn) {
            var actionHtml = createActionButtons(data, tableType);
            $("td", row).eq(10).html(actionHtml); // Action column is at index 10
        }

        // Add missing image class if needed
        var hasImage = data.img && data.img !== '' && data.img !== null;
        if (!hasImage) {
            $(row).addClass('missing-image missing-image-pulse');
        }
    }

    function createImageHtml(data, tableType) {
        var imgHtml = '';
        var hasImage = data.img && data.img !== '' && data.img !== null;
        var uploadFunction = tableType === 'buy_fixed'
            ? 'fn.app.buy_fixed.buy.upload_image'
            : 'fn.app.buy_fixed.buy.upload_image_wechat';

        var imagePath = tableType === 'buy_fixed' ? 'binary/purchase/' : 'binary/wechat/';

        if (hasImage) {
            imgHtml = '<div class="img-container">';
            imgHtml += '<a href="' + imagePath + data.img + '" target="_blank" title="ดูรูปเต็ม">';
            imgHtml += '<img src="' + imagePath + data.img + '" style="width: 35px; height: 35px; object-fit: cover; border-radius: 8px; border: 2px solid #00204E; box-shadow: 0 2px 8px rgba(0, 32, 78, 0.15);" />';
            imgHtml += '</a>';
            imgHtml += '<br><small><a href="javascript:void(0)" onclick="' + uploadFunction + '(' + data.id + ')" style="color: #00204E; font-weight: 500; text-decoration: none;">เปลี่ยนรูป</a></small>';
            imgHtml += '</div>';
        } else {
            imgHtml = '<div class="missing-image-tooltip">';
            imgHtml += '<button type="button" class="upload-btn-missing" onclick="' + uploadFunction + '(' + data.id + ')" title="แนบรูปภาพ">';
            imgHtml += '<i class="fas fa-camera"></i> แนบรูป';
            imgHtml += '</button>';
            imgHtml += '</div>';
        }
        return imgHtml;
    }

    // Function to create action buttons (only for tables with action column)
    function createActionButtons(data, tableType) {
        var removeFunction = tableType === 'buy_fixed'
            ? 'fn.app.buy_fixed.buy.remove'
            : 'fn.app.buy_fixed.wechat.remove';

        return fn.ui.button("btn btn-xs btn-outline-danger mr-1", "far fa-trash", removeFunction + "(" + data.id + ")");
    }

    // Function to add filter buttons
    function addFilterButtons(tableId, tableLabel) {
        // Remove existing filter buttons for this table
        $('#' + tableId + '_wrapper .missing-image-filter').remove();

        var filterHtml = '<div class="missing-image-filter" style="margin: 10px 0; display: flex; align-items: center; flex-wrap: wrap; gap: 10px;">';
        filterHtml += '<div style="display: flex; gap: 8px;">';
        filterHtml += '<button type="button" class="btn btn-sm btn-outline-warning showMissingOnly" data-table="' + tableId + '" title="แสดงเฉพาะรายการที่ยังไม่ได้แนบรูป">';
        filterHtml += '<i class="fas fa-filter"></i> ยังไม่แนบรูป (' + tableLabel + ')';
        filterHtml += '</button>';
        filterHtml += '<button type="button" class="btn btn-sm btn-outline-secondary showAll" data-table="' + tableId + '" title="แสดงรายการทั้งหมด">';
        filterHtml += '<i class="fas fa-list"></i> แสดงทั้งหมด (' + tableLabel + ')';
        filterHtml += '</button>';
        filterHtml += '</div>';
        filterHtml += '<div class="missing-count-' + tableId + '" style="font-size: 13px; color: #F59E0B; font-weight: 500; display: flex; align-items: center; gap: 5px;"></div>';
        filterHtml += '</div>';

        $('#' + tableId + '_wrapper .dataTables_filter').after(filterHtml);
    }

    // Function to update missing count
    function updateMissingCount(tableId) {
        var missingCount = $('#' + tableId + ' tbody tr.missing-image').length;
        var totalCount = $('#' + tableId + ' tbody tr').length;

        if (missingCount > 0) {
            $('.missing-count-' + tableId).html(`
                <i class="fas fa-exclamation-triangle"></i> 
                <span>ยังไม่ได้แนบรูป: <strong>${missingCount}</strong> จาก ${totalCount} รายการ</span>
            `);
        } else {
            $('.missing-count-' + tableId).html(`
                <i class="fas fa-check-circle" style="color: #10B981;"></i> 
                <span style="color: #10B981;">แนบรูปครบทุกรายการแล้ว</span>
            `);
        }
    }

    // Event handlers for filter buttons
    $(document).on('click', '.showMissingOnly', function () {
        var tableId = $(this).data('table');
        var $button = $(this);
        var $allButton = $button.siblings('.showAll');

        if ($button.hasClass('active')) {
            $allButton.click();
            return;
        }

        $('#' + tableId + ' tbody tr').hide();
        $('#' + tableId + ' tbody tr.missing-image').show();
        $button.addClass('active').removeClass('btn-outline-warning').addClass('btn-warning');
        $allButton.removeClass('active btn-secondary').addClass('btn-outline-secondary');

        var visibleCount = $('#' + tableId + ' tbody tr.missing-image').length;
        var tableLabel = tableId === 'tblPurchase' ? 'ICBC' : 'WeChat';
        showNotification(`แสดง ${visibleCount} รายการที่ยังไม่ได้แนบรูป (${tableLabel})`, 'info');
    });

    $(document).on('click', '.showAll', function () {
        var tableId = $(this).data('table');
        var $button = $(this);
        var $missingButton = $button.siblings('.showMissingOnly');

        if ($button.hasClass('active')) {
            return;
        }

        $('#' + tableId + ' tbody tr').show();
        $button.addClass('active').removeClass('btn-outline-secondary').addClass('btn-secondary');
        $missingButton.removeClass('active btn-warning').addClass('btn-outline-warning');

        var totalCount = $('#' + tableId + ' tbody tr').length;
        var tableLabel = tableId === 'tblPurchase' ? 'ICBC' : 'WeChat';
        showNotification(`แสดงรายการทั้งหมด ${totalCount} รายการ (${tableLabel})`, 'info');
    });

    // Window functions for image upload success
    window.onImageUploadSuccess = function (recordId, tableType) {
        if (tableType === 'buy_fixed' || tableType === undefined) {
            tablePurchase.ajax.reload(null, false);
        } else if (tableType === 'wechat') {
            tableBuyWeChat.ajax.reload(null, false);
        }
        showNotification('อัพโหลดรูปภาพสำเร็จ!', 'success');
    };

    window.onBuyFixedImageUploadSuccess = function (recordId) {
        window.onImageUploadSuccess(recordId, 'buy_fixed');
    };

    window.onWeChatImageUploadSuccess = function (recordId) {
        window.onImageUploadSuccess(recordId, 'wechat');
    };

    // Notification function
    function showNotification(message, type = 'info') {
        var config = {
            'success': { bg: '#10B981', icon: 'fas fa-check-circle' },
            'warning': { bg: '#F59E0B', icon: 'fas fa-exclamation-triangle' },
            'error': { bg: '#EF4444', icon: 'fas fa-times-circle' },
            'info': { bg: '#00204E', icon: 'fas fa-info-circle' }
        };

        var setting = config[type] || config['info'];

        var notification = $(`
            <div class="custom-notification" style="
                position: fixed;
                top: 20px;
                right: 20px;
                background: ${setting.bg};
                color: white;
                padding: 12px 20px;
                border-radius: 8px;
                box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
                z-index: 9999;
                font-size: 14px;
                font-weight: 500;
                max-width: 350px;
                transform: translateX(100%);
                transition: transform 0.3s ease;
                cursor: pointer;
                word-wrap: break-word;
            ">
                <i class="${setting.icon}" style="margin-right: 8px;"></i>
                ${message}
            </div>
        `);

        $('body').append(notification);

        setTimeout(() => {
            notification.css('transform', 'translateX(0)');
        }, 100);

        notification.on('click', function () {
            $(this).css('transform', 'translateX(100%)');
            setTimeout(() => $(this).remove(), 300);
        });

        setTimeout(() => {
            notification.css('transform', 'translateX(100%)');
            setTimeout(() => notification.remove(), 300);
        }, 4000);
    }

    // Bulk upload functions
    window.bulkUploadImages = function (tableId) {
        var missingImageRows = $('#' + tableId + ' tbody tr.missing-image');
        if (missingImageRows.length === 0) {
            var tableLabel = tableId === 'tblPurchase' ? 'ICBC' : 'WeChat';
            showNotification(`ไม่มีรายการที่ต้องแนบรูปใน ${tableLabel}`, 'info');
            return;
        }

        var ids = [];
        missingImageRows.each(function () {
            var id = $(this).find('td:first').text();
            ids.push(id);
        });

        var tableLabel = tableId === 'tblPurchase' ? 'ICBC' : 'WeChat';
        showNotification(`เตรียม bulk upload สำหรับ ${ids.length} รายการ (${tableLabel})`, 'info');
    };

    // Keyboard shortcuts
    $(document).on('keydown', function (e) {
        // Alt + M: Show missing only for active table
        if (e.altKey && e.key === 'm') {
            e.preventDefault();
            $('.showMissingOnly:first').click();
        }

        // Alt + A: Show all for active table
        if (e.altKey && e.key === 'a') {
            e.preventDefault();
            $('.showAll:first').click();
        }

        // Alt + 1: Focus ICBC table
        if (e.altKey && e.key === '1') {
            e.preventDefault();
            $('#tblPurchase').focus();
            showNotification('เลือก ICBC Table', 'info');
        }

        // Alt + 2: Focus WeChat table
        if (e.altKey && e.key === '2') {
            e.preventDefault();
            $('#tblBuyWeChat').focus();
            showNotification('เลือก WeChat Table', 'info');
        }
    });

    // Enhanced tooltips
    $(document).on('mouseenter', '.showMissingOnly', function () {
        var tableId = $(this).data('table');
        var tableLabel = tableId === 'tblPurchase' ? 'ICBC' : 'WeChat';
        $(this).attr('title', `แสดงเฉพาะรายการที่ยังไม่ได้แนบรูป (${tableLabel}) - Alt+M`);
    });

    $(document).on('mouseenter', '.showAll', function () {
        var tableId = $(this).data('table');
        var tableLabel = tableId === 'tblPurchase' ? 'ICBC' : 'WeChat';
        $(this).attr('title', `แสดงรายการทั้งหมด (${tableLabel}) - Alt+A`);
    });

    // Initialize notifications
    setTimeout(() => {
        showNotification('ระบบพร้อมใช้งาน!\n📋 ICBC: มีปุ่มลบ\n💬 WeChat: ดูข้อมูลอย่างเดียว\n⌨️ กด Alt+1/2 เพื่อสลับตาราง', 'info');
    }, 1000);

    // Summary function for both tables
    window.getImageSummary = function () {
        var purchaseMissing = $('#tblPurchase tbody tr.missing-image').length;
        var purchaseTotal = $('#tblPurchase tbody tr').length;
        var wechatMissing = $('#tblBuyWeChat tbody tr.missing-image').length;
        var wechatTotal = $('#tblBuyWeChat tbody tr').length;

        var summary = `📊 สรุปสถานะรูปภาพ:\n`;
        summary += `🏦 ICBC: ${purchaseTotal - purchaseMissing}/${purchaseTotal} รายการ\n`;
        summary += `💬 WeChat: ${wechatTotal - wechatMissing}/${wechatTotal} รายการ\n`;
        summary += `📋 รวม: ${(purchaseTotal + wechatTotal) - (purchaseMissing + wechatMissing)}/${purchaseTotal + wechatTotal} รายการ`;

        showNotification(summary, 'info');
        return {
            icbc: { missing: purchaseMissing, total: purchaseTotal },
            wechat: { missing: wechatMissing, total: wechatTotal }
        };
    };

    // Auto refresh every 5 minutes
    setInterval(function () {
        console.log('Auto refreshing tables...');
        tablePurchase.ajax.reload(null, false);
        tableBuyWeChat.ajax.reload(null, false);
    }, 300000); // 5 minutes

});