<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ระบบสั่งซื้อแท่งเงิน Bowins Design - Multi Order</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/4.6.2/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container-fluid py-5">
        <div class="row justify-content-center">
            <div class="col-12 col-xl-10">
                <div class="main-card">
                    <!-- Header -->
                    <div class="form-header">
                        <div class="header-content">
                            <div class="header-icon">
                                <i class="fas fa-coins"></i>
                            </div>
                            <div>
                                <h2 class="header-title">ระบบสั่งซื้อแท่งเงิน Bowins Design</h2>
                            </div>
                        </div>
                        <div class="header-decoration"></div>
                    </div>

                    <!-- Form Content -->
                    <div class="form-content">
                        <form name="form_multi_order">
                            <!-- Customer Information Section -->
                            <div class="section-card">
                                <div class="section-header">
                                    <i class="fas fa-user-circle"></i>
                                    <h4>ข้อมูลลูกค้า</h4>
                                </div>
                                <div class="form-row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="customer_name"><i class="fas fa-user mr-2"></i>ชื่อลูกค้า</label>
                                            <input type="text" class="form-control custom-input" name="customer_name" id="customer_name" placeholder="ชื่อลูกค้า" autocomplete="off" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="username"><i class="fas fa-at mr-2"></i>Username</label>
                                            <input type="text" class="form-control custom-input" name="username" id="username" placeholder="Username " autocomplete="off">
                                        </div>
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="phone"><i class="fas fa-phone mr-2"></i>เบอร์โทรศัพท์</label>
                                            <input type="text" id="phone" name="phone" class="form-control custom-input"
                                                placeholder="0 หรือ 08xxxxxxxxx"
                                                pattern="^0$|^[0-9]{10}$" autocomplete="off"
                                                title="กรุณากรอก 0 ตัวเดียว หรือเบอร์โทรศัพท์ 10 หลัก (เฉพาะตัวเลข)"
                                                inputmode="numeric"
                                                oninput="this.value = this.value.replace(/[^0-9]/g, '');">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="platform"><i class="fas fa-globe mr-2"></i>Platform</label>
                                            <select name="platform" id="platform" class="form-control custom-select" required>
                                                <option value="">กรุณาเลือกรายการ</option>
                                                <option value="Facebook">Facebook</option>
                                                <option value="LINE">LINE</option>
                                                <option value="IG">Instagram</option>
                                                <option value="Shopee">Shopee</option>
                                                <option value="Lazada">Lazada</option>
                                                <option value="Website">Website</option>
                                                <option value="LuckGems">Luck Gems</option>
                                                <option value="TikTok">TikTok</option>
                                                <option value="SilverNow">Silver Now</option>
                                                <option value="WalkIN">WalkIN</option>
                                                <option value="Exhibition">Exhibition</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="vat_type"><i class="fa-solid fa-money-bill-wave mr-2"></i>Vats</label>
                                            <select name="vat_type" id="vat_type" class="form-control custom-select" required>
                                                <option value="">กรุณาเลือกรายการ</option>
                                                <option value="2">เสีย Vats</option>
                                                <option value="0">ไม่เสีย Vats</option>

                                            </select>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Product Items Section -->
    <div class="section-card">
        <div class="section-header">
            <i class="fas fa-box-open"></i>
            <h4>รายการสินค้า</h4>
            <div class="ml-auto">
                <button type="button" class="btn btn-add-item" onclick="multiOrderManager.addItem()">
                    <i class="fas fa-plus mr-2"></i>เพิ่มรายการ
                </button>
            </div>
        </div>

        <div id="items-container">
            <!-- Default first item -->
            <div class="item-row" data-item="1">
                <div class="item-header">
                    <h5><i class="fas fa-gem mr-2"></i>รายการที่ 1</h5>
                    <!-- รายการที่ 1 ไม่มีปุ่มลบ -->
                </div>

                <div class="form-row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label><i class="fas fa-gem mr-2"></i>สินค้า</label>
                            <select name="items[1][product_id]" class="form-control custom-select product-select" required>
                                <option value="">Select Product</option>
                                <?php
                                $sql = "SELECT * FROM bs_products_bwd WHERE status = 1";
                                $result = $dbc->query($sql);
                                if ($result->num_rows > 0) {
                                    while ($row = $result->fetch_assoc()) {
                                        echo '<option value="' . $row['id'] . '">' . $row['name'] . '</option>';
                                    }
                                } else {
                                    echo '<option value="">Product not available</option>';
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label><i class="fas fa-tags mr-2"></i>ประเภทสินค้า</label>
                            <select name="items[1][product_type]" class="form-control custom-select product-type-select" required>
                                <option value="">Select Type</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="form-row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label><i class="fas fa-sort-numeric-up mr-2"></i>จำนวนแท่ง</label>
                            <input type="number" class="form-control custom-input amount-input" name="items[1][amount]" placeholder="แท่ง" min="1" value="1" oninput="this.value = this.value.replace(/[^0-9.]/g, '');" required>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label><i class="fas fa-money-bill-wave mr-2"></i>ราคา</label>
                            <input type="text" class="form-control custom-input price-input" name="items[1][price]" placeholder="ราคา" autocomplete="off" required>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label><i class="fas fa-percentage mr-2"></i>ส่วนลด</label>
                            <select name="items[1][discount]" class="form-control custom-select discount-select">
                                <option value="0">ไม่มีส่วนลด</option>
                                <option value="5">5%</option>
                                <option value="10">10%</option>
                                <option value="15">15%</option>
                                <option value="20">20%</option>
                                <option value="25">25%</option>
                                <option value="30">30%</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label><i class="fas fa-calculator mr-2"></i>ยอดรวม</label>
                            <input type="text" class="form-control custom-input total-input" name="items[1][total]" placeholder="ยอดรวม" readonly>
                        </div>
                    </div>
                </div>

                <!-- Engraving Section for each item -->
                <div class="engrave-section">
                    <h6><i class="fas fa-edit mr-2"></i>บริการสลักข้อความ</h6>
                    <div class="engrave-options mb-3">
                        <div class="form-check custom-radio">
                            <input class="form-check-input engrave-radio" type="radio" name="items[1][engrave]" id="engrave_yes_1" value="สลักข้อความบนแท่งเงิน">
                            <label class="form-check-label" for="engrave_yes_1">
                                <i class="fas fa-check-circle mr-2"></i>สลักข้อความ
                            </label>
                        </div>
                        <div class="form-check custom-radio">
                            <input class="form-check-input engrave-radio" type="radio" name="items[1][engrave]" id="engrave_no_1" value="ไม่สลักข้อความบนแท่งเงิน" checked>
                            <label class="form-check-label" for="engrave_no_1">
                                <i class="fas fa-times-circle mr-2"></i>ไม่สลักข้อความ
                            </label>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label><i class="fas fa-font mr-2"></i>Fonts</label>
                                <select name="items[1][font]" class="form-control custom-select font-select" disabled>
                                    <option value="">Select Font</option>
                                    <?php
                                    $sql = "SELECT * FROM bs_fonts_bwd WHERE status = 1";
                                    $result = $dbc->query($sql);
                                    if ($result->num_rows > 0) {
                                        while ($row = $result->fetch_assoc()) {
                                            echo '<option value="' . $row['name'] . '">' . $row['name'] . '</option>';
                                        }
                                    } else {
                                        echo '<option value="">Font not available</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label><i class="fas fa-pen-nib mr-2"></i>สลักข้อความ คิดเพิ่ม 200</label>
                                <input type="text" class="form-control custom-input carving-input" name="items[1][carving]" placeholder="สลักข้อความ" autocomplete="off" readonly>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label><i class="fas fa-image mr-2"></i>มีรูปภาพ คิดเพิ่ม 300.-</label>
                                <select name="items[1][ai]" class="form-control custom-select ai-select">
                                    <option value="0">ไม่มีภาพ</option>
                                    <option value="1">มีภาพ AI</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Order Summary -->
        <div class="order-summary">
            <div class="row">
                <div class="col-md-6 offset-md-6">
                    <div class="summary-card">
                        <h6><i class="fas fa-receipt mr-2"></i>สรุปคำสั่งซื้อ</h6>
                        <table class="table table-borderless summary-table">
                            <tr>
                                <td>จำนวนรายการ:</td>
                                <td class="text-right"><span id="total-items">1</span> รายการ</td>
                            </tr>
                            <tr>
                                <td>ยอดรวมก่อนส่วนลด:</td>
                                <td class="text-right"><span id="subtotal">0.00</span> บาท</td>
                            </tr>
                            <tr>
                                <td>ส่วนลดรวม:</td>
                                <td class="text-right">-<span id="total-discount">0.00</span> บาท</td>
                            </tr>
                            <tr>
                                <td>ค่าสลักข้อความ:</td>
                                <td class="text-right"><span id="engrave-cost-sign">+</span><span id="engrave-cost">0.00</span> บาท</td>
                            </tr>
                            <tr>
                                <td>ค่ารูปภาพ AI:</td>
                                <td class="text-right"><span id="ai-cost-sign">+</span><span id="ai-cost">0.00</span> บาท</td>
                            </tr>

                            <tr>
                                <td>ค่าจัดส่ง:</td>
                                <td class="text-right">+<span id="shipping-cost">0.00</span> บาท</td>
                            </tr>
                            <tr>
                                <td>ค่าธรรมเนียม:</td>
                                <td class="text-right">-<span id="fee-cost">0.00</span> บาท</td>
                            </tr>

                            <tr class="table-primary font-weight-bold">
                                <td><strong>ยอดรวมสุทธิ:</strong></td>
                                <td class="text-right"><strong><span id="grand-total">0.00</span> บาท</strong></td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Date & Shipping Section -->
    <div class="section-card">
        <div class="section-header">
            <i class="fas fa-shipping-fast"></i>
            <h4>วันที่และการจัดส่ง</h4>
        </div>
        <div class="form-row">
            <div class="col-md-3">
                <div class="form-group">
                    <label><i class="fas fa-calendar-alt mr-2"></i>วันที่ซื้อ</label>
                    <input type="date" name="date" class="form-control custom-input" value="<?php echo date('Y-m-d'); ?>" required>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label><i class="fas fa-truck mr-2"></i>วันที่จัดส่ง</label>
                    <input type="date" name="delivery_date" class="form-control custom-input" value="<?php echo date('Y-m-d', strtotime('+3 days')); ?>">
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label for="shipping"><i class="fas fa-dolly mr-2"></i>วิธีการส่งสินค้า</label>
                    <select name="shipping" id="shipping" class="form-control custom-select">
                        <?php
                        $sql = "SELECT * FROM bs_shipping_bwd WHERE status = 1 ORDER BY FIELD(id,4,1,2,3,5,6)";
                        $result = $dbc->query($sql);
                        while ($row = $result->fetch_assoc()) {
                            echo '<option value="' . $row['id'] . '">' . $row['name'] . '</option>';
                        }
                        ?>
                    </select>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label for="fee"><i class="fas fa-hand-holding-usd mr-2"></i>ค่าธรรมเนียม</label>
                    <input type="number" step="0.01" min="0" class="form-control custom-input"
                        name="fee" id="fee" placeholder="ค่าธรรมเนียม (ใส่ 0 ถ้าไม่มี)" value="0">
                </div>
            </div>

        </div>
    </div>

    <!-- Address Section -->
    <div class="section-card">
        <div class="section-header">
            <i class="fas fa-map-marker-alt"></i>
            <h4>ที่อยู่</h4>
        </div>
        <div class="form-row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="shipping_address"><i class="fas fa-home mr-2"></i>ที่อยู่จัดส่ง</label>
                    <textarea class="form-control custom-textarea" name="shipping_address" id="shipping_address" rows="3" placeholder="ที่อยู่ในการจัดส่ง"></textarea>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="billing_address"><i class="fas fa-receipt mr-2"></i>ที่อยู่ออกใบเสร็จ</label>
                    <textarea class="form-control custom-textarea" name="billing_address" id="billing_address" rows="3" placeholder="ที่อยู่ในการออกใบเสร็จ"></textarea>
                </div>
            </div>
        </div>
    </div>

    <!-- Comment Section -->
    <div class="section-card">
        <div class="section-header">
            <i class="fas fa-comment-dots"></i>
            <h4>หมายเหตุ</h4>
        </div>
        <div class="form-group">
            <label for="comment"><i class="fas fa-sticky-note mr-2"></i>หมายเหตุ</label>
            <textarea class="form-control custom-textarea" name="comment" id="comment" rows="3" placeholder="หมายเหตุ"></textarea>
        </div>
    </div>

    <!-- Submit Button -->
    <div class="text-center mt-4">
        <button class="btn btn-submit bb" type="submit">
            <i class="fas fa-paper-plane mr-2"></i>
            ทำรายการ (<span id="submit-total">0.00</span> บาท)
            <div class="btn-shine"></div>
        </button>
    </div>

    <script>
        (function() {
            'use strict';

            // Namespace
            window.SalesScreenApp = window.SalesScreenApp || {};
            window.SalesScreenApp.itemCounter = 1;

            // ฟังก์ชันตรวจ Platform ที่ "ไม่คิดค่า" engrave/AI (Shopee/Lazada/TikTok)
            function isDiscountPlatform() {
                const platform = $('#platform').val();
                return ['Shopee', 'Lazada', 'TikTok'].includes(platform);
            }

            // --------------------- Customer Management ---------------------
            window.SalesScreenApp.customerManager = {
                originalCustomerData: null,
                currentCustomerId: null,

                searchCustomer: function(searchTerm) {
                    if (!searchTerm || searchTerm.length < 2) return;

                    $.post("apps/sales_screen_bwd_2/xhr/action-search-customer.php", {
                            search: searchTerm
                        },
                        function(response) {
                            if (response.success && response.found) {
                                // Store original data และ customer ID
                                window.SalesScreenApp.customerManager.originalCustomerData = response.customer;
                                window.SalesScreenApp.customerManager.currentCustomerId = response.customer.id;

                                // Fill customer data
                                $('#customer_name').val(response.customer.customer_name || '');
                                $('#username').val(response.customer.username || '');
                                $('#phone').val(response.customer.phone || '');
                                $('#shipping_address').val(response.customer.shipping_address || '');
                                $('#billing_address').val(response.customer.billing_address || '');

                                // bind auto-save
                                window.SalesScreenApp.customerManager.bindAutoSaveEvents();

                                if (typeof fn?.notify?.successbox === 'function') {
                                    fn.notify.successbox("พบข้อมูลลูกค้า", "พบข้อมูล");
                                } else {
                                    alert("พบข้อมูลลูกค้า: " + (response.customer.customer_name || '-'));
                                }
                            } else if (response.success && !response.found) {
                                window.SalesScreenApp.customerManager.resetCustomerState();
                                if (typeof fn?.notify?.infobox === 'function') {
                                    fn.notify.infobox("ไม่พบข้อมูลลูกค้า จะสร้างใหม่เมื่อบันทึกออเดอร์", "ลูกค้าใหม่");
                                }
                            }
                        },
                        "json"
                    ).fail(function(xhr, status, error) {
                        console.log("AJAX Error Details:", status, error, xhr.responseText);
                        if (typeof fn?.notify?.warnbox === 'function') {
                            fn.notify.warnbox("เกิดข้อผิดพลาดในการค้นหาลูกค้า", "Connection Error");
                        }
                    });
                },

                bindAutoSaveEvents: function() {
                    // clear old
                    $('#customer_name, #shipping_address, #billing_address').off('blur.autosave input.autosave');

                    // add new
                    $('#customer_name, #shipping_address, #billing_address').on('input.autosave', function() {
                        if (window.SalesScreenApp.customerManager.currentCustomerId) {
                            $(this).addClass('field-modified');
                        }
                    });

                    $('#customer_name, #shipping_address, #billing_address').on('blur.autosave', function() {
                        if (window.SalesScreenApp.customerManager.currentCustomerId && $(this).hasClass('field-modified')) {
                            window.SalesScreenApp.customerManager.showEditingIndicator(this);
                            window.SalesScreenApp.customerManager.autoSaveCustomer();
                            $(this).removeClass('field-modified');
                        }
                    });
                },

                showEditingIndicator: function(field) {
                    $(field).addClass('field-editing');

                    if (!$(field).next('.auto-save-icon').length) {
                        $(field).after('<i class="fas fa-save auto-save-icon text-success ml-2" title="กำลังบันทึกอัตโนมัติ"></i>');
                    }

                    setTimeout(function() {
                        $(field).removeClass('field-editing');
                        $(field).next('.auto-save-icon').remove();
                    }, 2000);
                },

                autoSaveCustomer: function() {
                    var self = window.SalesScreenApp.customerManager;
                    if (!self.currentCustomerId) return;

                    const customerData = {
                        id: self.currentCustomerId,
                        customer_name: $('[name="customer_name"]').val().trim(),
                        phone: $('[name="phone"]').val().trim(),
                        username: $('[name="username"]').val().trim(),
                        shipping_address: $('[name="shipping_address"]').val().trim(),
                        billing_address: $('[name="billing_address"]').val().trim()
                    };

                    // check changed
                    if (self.hasDataChanged(customerData)) {
                        $.post("apps/sales_screen_bwd_2/xhr/action-update-customer.php", {
                                data: JSON.stringify(customerData)
                            },
                            function(response) {
                                if (response.success) {
                                    self.originalCustomerData = customerData;
                                    if (typeof fn?.notify?.infobox === 'function') {
                                        fn.notify.infobox("บันทึกข้อมูลลูกค้าอัตโนมัติ", "Auto Saved", 2000);
                                    }
                                }
                            },
                            "json"
                        );
                    }
                },

                hasDataChanged: function(newData) {
                    var self = window.SalesScreenApp.customerManager;
                    if (!self.originalCustomerData) return true;

                    return (
                        self.originalCustomerData.customer_name !== newData.customer_name ||
                        self.originalCustomerData.shipping_address !== newData.shipping_address ||
                        self.originalCustomerData.billing_address !== newData.billing_address
                    );
                },

                resetCustomerState: function() {
                    var self = window.SalesScreenApp.customerManager;
                    self.originalCustomerData = null;
                    self.currentCustomerId = null;

                    $('#customer_name, #shipping_address, #billing_address').off('blur.autosave input.autosave');
                },

                clearCustomerForm: function() {
                    $('#customer_name').val('');
                    $('#username').val('');
                    $('#phone').val('');
                    $('#shipping_address').val('');
                    $('#billing_address').val('');
                    window.SalesScreenApp.customerManager.resetCustomerState();
                }
            };

            // --------------------- Multi-Order Manager ---------------------
            window.SalesScreenApp.multiOrderManager = {
                addItem: function() {
                    window.SalesScreenApp.itemCounter++;
                    var itemCounter = window.SalesScreenApp.itemCounter;

                    const productOptions = $('#items-container .product-select:first').html();
                    const fontOptions = $('#items-container .font-select:first').html();

                    const newItemHtml = `
<div class="item-row" data-item="${itemCounter}">
    <div class="item-header">
        <h5><i class="fas fa-gem mr-2"></i>รายการที่ ${itemCounter}</h5>
        <button type="button" class="btn btn-remove-item" onclick="window.SalesScreenApp.multiOrderManager.removeItem(${itemCounter})">
            <i class="fas fa-trash"></i>
        </button>
    </div>

    <div class="form-row">
        <div class="col-md-6">
            <div class="form-group">
                <label><i class="fas fa-gem mr-2"></i>สินค้า</label>
                <select name="items[${itemCounter}][product_id]" class="form-control custom-select product-select" required>
                    ${productOptions}
                </select>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label><i class="fas fa-tags mr-2"></i>ประเภทสินค้า</label>
                <select name="items[${itemCounter}][product_type]" class="form-control custom-select product-type-select" required>
                    <option value="">Select Type</option>
                </select>
            </div>
        </div>
    </div>

    <div class="form-row">
        <div class="col-md-3">
            <div class="form-group">
                <label><i class="fas fa-sort-numeric-up mr-2"></i>จำนวนแท่ง</label>
                <input type="number" class="form-control custom-input amount-input" name="items[${itemCounter}][amount]" placeholder="แท่ง" min="1" value="1" oninput="this.value = this.value.replace(/[^0-9.]/g, '');" required>
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                <label><i class="fas fa-money-bill-wave mr-2"></i>ราคา</label>
                <input type="text" class="form-control custom-input price-input" name="items[${itemCounter}][price]" placeholder="ราคา" autocomplete="off" required>
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                <label><i class="fas fa-percentage mr-2"></i>ส่วนลด</label>
                <select name="items[${itemCounter}][discount]" class="form-control custom-select discount-select">
                    <option value="0">ไม่มีส่วนลด</option>
                    <option value="5">5%</option>
                    <option value="10">10%</option>
                    <option value="15">15%</option>
                    <option value="20">20%</option>
                    <option value="25">25%</option>
                    <option value="30">30%</option>
                </select>
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                <label><i class="fas fa-calculator mr-2"></i>ยอดรวม</label>
                <input type="text" class="form-control custom-input total-input" name="items[${itemCounter}][total]" placeholder="ยอดรวม" readonly>
            </div>
        </div>
    </div>

    <div class="engrave-section">
        <h6><i class="fas fa-edit mr-2"></i>บริการสลักข้อความ</h6>
        <div class="engrave-options mb-3">
            <div class="form-check custom-radio">
                <input class="form-check-input engrave-radio" type="radio" name="items[${itemCounter}][engrave]" id="engrave_yes_${itemCounter}" value="สลักข้อความบนแท่งเงิน">
                <label class="form-check-label" for="engrave_yes_${itemCounter}">
                    <i class="fas fa-check-circle mr-2"></i>สลักข้อความ
                </label>
            </div>
            <div class="form-check custom-radio">
                <input class="form-check-input engrave-radio" type="radio" name="items[${itemCounter}][engrave]" id="engrave_no_${itemCounter}" value="ไม่สลักข้อความบนแท่งเงิน" checked>
                <label class="form-check-label" for="engrave_no_${itemCounter}">
                    <i class="fas fa-times-circle mr-2"></i>ไม่สลักข้อความ
                </label>
            </div>
        </div>

        <div class="form-row">
            <div class="col-md-3">
                <div class="form-group">
                    <label><i class="fas fa-font mr-2"></i>Fonts</label>
                    <select name="items[${itemCounter}][font]" class="form-control custom-select font-select" disabled>
                        ${fontOptions}
                    </select>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label><i class="fas fa-pen-nib mr-2"></i>สลักข้อความ คิดเพิ่ม 100</label>
                    <input type="text" class="form-control custom-input carving-input" name="items[${itemCounter}][carving]" placeholder="สลักข้อความ" autocomplete="off" readonly>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label><i class="fas fa-image mr-2"></i>มีรูปภาพ คิดเพิ่ม 200.-</label>
                    <select name="items[${itemCounter}][ai]" class="form-control custom-select ai-select">
                        <option value="0">ไม่มีภาพ</option>
                        <option value="1">มีภาพ AI</option>
                    </select>
                </div>
            </div>
        </div>
    </div>
</div>
`;

                    $('#items-container').append(newItemHtml);
                    this.updateRemoveButtons();
                    this.bindNewItemEvents();
                    this.calculateTotal();
                },

                removeItem: function(itemId) {
                    $(`.item-row[data-item="${itemId}"]`).remove();
                    this.updateRemoveButtons();
                    this.calculateTotal();
                },

                updateRemoveButtons: function() {
                    const itemCount = $('.item-row').length;
                    $('.item-row').each(function(index) {
                        const removeBtn = $(this).find('.btn-remove-item');
                        if (index === 0) {
                            removeBtn.hide();
                        } else if (itemCount > 1) {
                            removeBtn.show();
                        } else {
                            removeBtn.hide();
                        }
                    });
                    $('#total-items').text(itemCount);
                },

                bindNewItemEvents: function() {
                    this.bindEngraveEvents();
                    this.bindProductEvents();
                    this.bindCalculationEvents();
                    this.bindShippingEvents();
                    this.bindPlatformEvents();
                },

                // --------------------- Event Bindings ---------------------
                bindEngraveEvents: function() {
                    $("input:radio[name*='[engrave]']").off('click.engrave').on('click.engrave', function() {
                        const itemRow = $(this).closest('.item-row');
                        const selectedValue = $(this).val();
                        const carvingInput = itemRow.find('.carving-input');
                        const fontSelect = itemRow.find('.font-select');

                        if (selectedValue === 'สลักข้อความบนแท่งเงิน') {
                            carvingInput.removeAttr("readonly");
                            fontSelect.prop('disabled', false);
                        } else {
                            carvingInput.attr("readonly", true).val('');
                            fontSelect.prop('disabled', true);
                        }
                        window.SalesScreenApp.multiOrderManager.calculateTotal();
                    });
                },

                bindProductEvents: function() {
                    $('.product-select').off('change.product').on('change.product', function() {
                        const itemRow = $(this).closest('.item-row');
                        const productId = $(this).val();
                        const productTypeSelect = itemRow.find('.product-type-select');

                        if (productId) {
                            $.ajax({
                                type: 'POST',
                                url: 'apps/sales_screen_bwd_2/xhr/action-load-Type.php',
                                data: 'id=' + productId,
                                success: function(html) {
                                    productTypeSelect.html(html);
                                }
                            });
                        } else {
                            productTypeSelect.html('<option value="">Select Product Type</option>');
                        }
                        window.SalesScreenApp.multiOrderManager.calculateTotal();
                    });
                },

                bindCalculationEvents: function() {
                    $('.amount-input, .price-input, .discount-select, .ai-select')
                        .off('change.calc input.calc')
                        .on('change.calc input.calc', function() {
                            window.SalesScreenApp.multiOrderManager.calculateItemTotal($(this).closest('.item-row'));
                            window.SalesScreenApp.multiOrderManager.calculateTotal();
                        });
                },

                bindShippingEvents: function() {
                    $('#shipping').off('change.shipping').on('change.shipping', function() {
                        window.SalesScreenApp.multiOrderManager.calculateTotal();
                    });
                },

                bindPlatformEvents: function() {
                    $('#platform').off('change.platform').on('change.platform', function() {
                        window.SalesScreenApp.multiOrderManager.calculateTotal();
                    });
                },

                bindFeeEvents: function() {
                    $('#fee')
                        .off('input.fee change.fee')
                        .on('input.fee change.fee', function() {
                            window.SalesScreenApp.multiOrderManager.calculateTotal();
                        });
                },


                // --------------------- Calculations ---------------------
                calculateItemTotal: function(itemRow) {
                    const amount = parseFloat(itemRow.find('.amount-input').val()) || 0;
                    const price = parseFloat(itemRow.find('.price-input').val()) || 0;
                    const discount = parseFloat(itemRow.find('.discount-select').val()) || 0;
                    const hasEngrave = itemRow.find('.engrave-radio:checked').val() === 'สลักข้อความบนแท่งเงิน';
                    const hasAI = itemRow.find('.ai-select').val() == '1';

                    let itemTotal = amount * price;
                    let discountAmount = itemTotal * (discount / 100);
                    itemTotal -= discountAmount;

                    const isMarketplace = isDiscountPlatform(); // Shopee/Lazada/TikTok

                    // เงื่อนไขใหม่: marketplace -> ไม่กระทบยอด (ไม่บวก/ไม่ลบ)
                    if (!isMarketplace) {
                        if (hasEngrave) itemTotal += (amount * 200);
                        if (hasAI) itemTotal += (amount * 300);
                    }

                    itemRow.find('.total-input').val(itemTotal.toFixed(2));
                },

                calculateTotal: function() {
                    let subtotal = 0;
                    let totalDiscount = 0;
                    let engraveCost = 0;
                    let aiCost = 0;
                    let itemCount = 0;
                    const isMarketplace = isDiscountPlatform();

                    $('.item-row').each(function() {
                        const amount = parseFloat($(this).find('.amount-input').val()) || 0;
                        const price = parseFloat($(this).find('.price-input').val()) || 0;
                        const discount = parseFloat($(this).find('.discount-select').val()) || 0;
                        const hasEngrave = $(this).find('.engrave-radio:checked').val() === 'สลักข้อความบนแท่งเงิน';
                        const hasAI = $(this).find('.ai-select').val() == '1';

                        if (amount > 0 && price > 0) {
                            itemCount++;
                            const itemSubtotal = amount * price;
                            const itemDiscount = itemSubtotal * (discount / 100);

                            subtotal += itemSubtotal;
                            totalDiscount += itemDiscount;

                            if (!isMarketplace) {
                                if (hasEngrave) engraveCost += (amount * 200);
                                if (hasAI) aiCost += (amount * 300);
                            }
                        }
                    });

                    // Shipping
                    let shippingCost = 0;
                    const shippingMethod = $('#shipping').val();
                    if (shippingMethod == "1") shippingCost = 50;
                    else if (shippingMethod == "2") shippingCost = 100;
                    else if (shippingMethod == "3") shippingCost = 150;
                    else if (shippingMethod == "4") shippingCost = 0;

                    // ค่าธรรมเนียม
                    const fee = parseFloat($('#fee').val()) || 0;

                    const grandTotal = subtotal - totalDiscount + engraveCost + aiCost + shippingCost - fee;

                    // Update UI
                    $('#total-items').text(itemCount);
                    $('#subtotal').text(subtotal.toFixed(2));
                    $('#total-discount').text(totalDiscount.toFixed(2));
                    $('#engrave-cost').text(Math.abs(engraveCost).toFixed(2));
                    $('#ai-cost').text(Math.abs(aiCost).toFixed(2));
                    $('#shipping-cost').text(shippingCost.toFixed(2));
                    $('#fee-cost').text(fee.toFixed(2));
                    $('#grand-total').text(grandTotal.toFixed(2));
                    $('#submit-total').text(grandTotal.toFixed(2));
                }

            };

            // --------------------- Bind customer search events ---------------------
            function bindCustomerSearchEvents() {
                $('#phone').off('blur.customer').on('blur.customer', function() {
                    const phone = $(this).val().trim();
                    if (phone && phone.length >= 9) {
                        window.SalesScreenApp.customerManager.searchCustomer(phone);
                    }
                });

                $('#username').off('blur.customer').on('blur.customer', function() {
                    const username = $(this).val().trim();
                    if (username && username.length >= 2) {
                        window.SalesScreenApp.customerManager.searchCustomer(username);
                    }
                });

                // เคลียร์ original state เมื่อแก้ไข phone/username
                $('#phone, #username').off('input.customer').on('input.customer', function() {
                    window.SalesScreenApp.customerManager.resetCustomerState();
                });
            }

            // --------------------- Initialize ---------------------
            $(document).ready(function() {
                var mm = window.SalesScreenApp.multiOrderManager;
                mm.bindEngraveEvents();
                mm.bindProductEvents();
                mm.bindCalculationEvents();
                mm.bindShippingEvents();
                mm.bindPlatformEvents();
                mm.bindFeeEvents(); // ← เพิ่มบรรทัดนี้
                mm.updateRemoveButtons();
                mm.calculateTotal();
                bindCustomerSearchEvents();

                // ป้องกัน submit ปกติ
                $('form[name="form_multi_order"]').off('submit.multiorder').on('submit.multiorder', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    return false;
                });
            });

            // export
            window.multiOrderManager = window.SalesScreenApp.multiOrderManager;

        })(); // IIFE
    </script>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            min-height: 100vh;
            margin: 0;
            padding: 0;
            overflow-x: hidden;
        }

        .container-fluid {
            overflow: visible;
            max-width: 100% !important;
            margin: 0 !important;
            padding: 0 !important;
        }

        .main-card {
            background: #fafafa;
            border-radius: 0 !important;
            box-shadow: none !important;
            /* ยกเลิก shadow */
            overflow: hidden;
            position: relative;
            max-width: 100% !important;
            width: 100% !important;
            margin: 0 !important;
        }

        .form-header {
            background: linear-gradient(135deg, #00204E 0%, #003875 100%);
            color: white;
            padding: 1.5rem 2rem !important;
            position: relative;
            overflow: hidden;
            border-radius: 0 !important;
        }

        .header-content {
            display: flex;
            align-items: center;
            gap: 1rem;
            position: relative;
            z-index: 2;
            max-width: 100% !important;
            margin: 0;
        }

        .header-icon {
            background: rgba(255, 255, 255, 0.2);
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            backdrop-filter: blur(10px);
        }

        .header-title {
            margin: 0;
            font-size: 1.4rem;
            font-weight: 600;
        }

        .header-subtitle {
            margin: 0;
            opacity: 0.8;
            font-size: 0.9rem;
        }

        .header-decoration {
            position: absolute;
            top: -50px;
            right: -50px;
            width: 150px;
            height: 150px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
        }

        .form-content {
            padding: 2rem;
        }

        .section-card {
            background: rgba(0, 32, 78, 0.02);
            border: 1px solid rgba(0, 32, 78, 0.1);
            border-radius: 16px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            transition: all 0.3s ease;
            position: relative;
            overflow: visible;
            z-index: 1;
        }

        .section-card:hover {
            background: rgba(0, 32, 78, 0.03);
            border-color: rgba(0, 32, 78, 0.15);
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0, 32, 78, 0.1);
            z-index: 2;
        }

        .section-header {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            margin-bottom: 1.5rem;
            padding-bottom: 0.75rem;
            border-bottom: 2px solid #00204E;
            position: relative;
        }

        .section-header::after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 0;
            height: 2px;
            width: 60px;
            background: linear-gradient(90deg, #00204E, #003875);
        }

        .section-header i {
            color: #00204E;
            font-size: 1.2rem;
        }

        .section-header h4 {
            margin: 0;
            color: #00204E;
            font-weight: 600;
            font-size: 1.1rem;
        }

        .btn-add-item {
            background-image: linear-gradient(to top, #48c6ef 0%, #6f86d6 100%);
            border: none;
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 8px;
            font-size: 0.9rem;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .btn-add-item:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(40, 167, 69, 0.3);
            color: white;
        }

        .item-row {
            background: rgba(255, 255, 255, 0.8);
            border: 1px solid rgba(0, 32, 78, 0.1);
            border-radius: 12px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            transition: all 0.3s ease;
        }

        .item-row:hover {
            border-color: rgba(0, 32, 78, 0.2);
            box-shadow: 0 4px 15px rgba(0, 32, 78, 0.08);
        }

        .item-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
            padding-bottom: 0.5rem;
            border-bottom: 1px solid rgba(0, 32, 78, 0.1);
        }

        .item-header h5 {
            margin: 0;
            color: #00204E;
            font-weight: 600;
        }

        .btn-remove-item {
            background: #dc3545;
            border: none;
            color: white;
            padding: 0.25rem 0.5rem;
            border-radius: 6px;
            font-size: 0.8rem;
            transition: all 0.3s ease;
        }

        .btn-remove-item:hover {
            background: #c82333;
            transform: translateY(-1px);
        }

        .engrave-section {
            background: rgba(0, 32, 78, 0.02);
            border-radius: 8px;
            padding: 1rem;
            margin-top: 1rem;
        }

        .engrave-section h6 {
            color: #00204E;
            font-weight: 600;
            margin-bottom: 0.75rem;
        }

        .engrave-options {
            display: flex;
            gap: 2rem;
            justify-content: center;
        }

        .order-summary {
            margin-top: 2rem;
            padding-top: 1.5rem;
            border-top: 2px solid rgba(0, 32, 78, 0.1);
        }

        .summary-card {
            background: rgba(0, 32, 78, 0.05);
            border-radius: 12px;
            padding: 1.5rem;
        }

        .summary-card h6 {
            color: #00204E;
            font-weight: 600;
            margin-bottom: 1rem;
        }

        .summary-table td {
            padding: 0.5rem 0;
            border: none;
        }

        .summary-table .table-primary td {
            font-size: 1.1rem;
            border-top: 2px solid #00204E;
            padding-top: 1rem;
        }

        .form-group label {
            color: #00204E;
            font-weight: 500;
            font-size: 0.95rem;
            margin-bottom: 0.5rem;
        }

        .field-editing {
            border-color: #28a745 !important;
            box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.25) !important;
        }

        .field-modified {
            border-color: #ffc107 !important;
            background-color: #fffbf0 !important;
        }

        .auto-save-icon {
            position: absolute;
            margin-top: 15px;
            animation: pulse 1s infinite;
        }

        @keyframes pulse {
            0% {
                opacity: 1;
            }

            50% {
                opacity: 0.5;
            }

            100% {
                opacity: 1;
            }
        }

        .custom-input,
        .custom-select,
        .custom-textarea {
            border: 2px solid rgba(0, 32, 78, 0.2);
            border-radius: 12px;
            transition: all 0.3s ease;
            font-size: 0.95rem;
            padding: 0.75rem 1rem;
            position: relative;
            z-index: 1;
            height: auto;
            min-height: 50px;
            line-height: 1.5;
        }

        .custom-input:focus,
        .custom-select:focus,
        .custom-textarea:focus {
            border-color: #00204E;
            box-shadow: 0 0 0 0.2rem rgba(0, 32, 78, 0.15);
            outline: none;
            z-index: 10;
        }

        .form-group {
            position: relative;
            z-index: 1;
        }

        .form-group:focus-within {
            z-index: 10;
        }

        select.custom-select {
            appearance: none;
            -webkit-appearance: none;
            -moz-appearance: none;
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%2300204E' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='m6 8 4 4 4-4'/%3e%3c/svg%3e");
            background-position: right 1rem center;
            background-repeat: no-repeat;
            background-size: 1.5em 1.5em;
            padding-right: 3rem;
            padding-top: 0.875rem;
            padding-bottom: 0.875rem;
            height: 50px;
            display: flex;
            align-items: center;
        }

        select.custom-select option {
            padding: 0.5rem 1rem;
            font-size: 0.95rem;
            line-height: 1.5;
            background-color: white;
            color: #00204E;
        }

        .custom-radio .form-check-input {
            width: 1.25rem;
            height: 1.25rem;
            border: 2px solid #00204E;
            margin-top: 0.1rem;
        }

        .custom-radio .form-check-input:checked {
            background-color: #00204E;
            border-color: #00204E;
        }

        .custom-radio .form-check-label {
            font-weight: 500;
            color: #00204E;
            cursor: pointer;
            padding: 0.5rem 1rem;
            border-radius: 8px;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
        }

        .custom-radio .form-check-label:hover {
            background: rgba(0, 32, 78, 0.05);
        }

        .btn-submit {
            background: linear-gradient(135deg, #00204E 0%, #003875 100%);
            border: none;
            color: white;
            padding: 1rem 3rem;
            border-radius: 50px;
            font-size: 1.1rem;
            font-weight: 600;
            position: relative;
            overflow: hidden;
            transition: all 0.3s ease;
            min-width: 200px;
            box-shadow: 0 8px 25px rgba(0, 32, 78, 0.3);
        }

        .btn-submit:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 35px rgba(0, 32, 78, 0.4);
            color: white;
        }

        .btn-submit:focus {
            outline: none;
            box-shadow: 0 0 0 0.2rem rgba(0, 32, 78, 0.25);
        }

        .btn-shine {
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.5s;
        }

        .btn-submit:hover .btn-shine {
            left: 100%;
        }

        .form-check {
            padding-left: 1.5rem;
        }

        .form-check-input {
            margin-left: -1.5rem;
        }

        @media (max-width: 768px) {
            .form-header {
                padding: 1.5rem;
            }

            .header-title {
                font-size: 1.2rem;
            }

            .form-content {
                padding: 1rem;
            }

            .section-card {
                padding: 1rem;
            }

            .item-row {
                padding: 1rem;
            }

            .engrave-options {
                flex-direction: column;
                gap: 1rem;
            }

            .btn-submit {
                padding: 0.875rem 2rem;
                font-size: 1rem;
                min-width: 180px;
            }
        }

        .section-card {
            animation: fadeInUp 0.6s ease forwards;
            opacity: 0;
            transform: translateY(20px);
        }

        .section-card:nth-child(1) {
            animation-delay: 0.1s;
        }

        .section-card:nth-child(2) {
            animation-delay: 0.2s;
        }

        .section-card:nth-child(3) {
            animation-delay: 0.3s;
        }

        .section-card:nth-child(4) {
            animation-delay: 0.4s;
        }

        .section-card:nth-child(5) {
            animation-delay: 0.5s;
        }

        .section-card:nth-child(6) {
            animation-delay: 0.6s;
        }

        @keyframes fadeInUp {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</body>

</html>