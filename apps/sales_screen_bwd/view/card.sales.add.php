<style>
    body {
        background-color: #f8f9fa;
        font-family: 'Sarabun', sans-serif;
    }

    .main-container {
        background: white;
        border-radius: 10px;
        box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
        padding: 30px;
        margin: 20px auto;
        max-width: 1400px;
    }

    .page-header {
        background: linear-gradient(135deg, #00204E 0%, #00204E 100%);
        color: white;
        padding: 20px;
        border-radius: 10px;
        margin-bottom: 30px;
    }

    .page-header h2 {
        margin: 0;
        font-weight: 700;
    }

    .section-card {
        border: 1px solid #dee2e6;
        border-radius: 8px;
        padding: 20px;
        margin-bottom: 20px;
        background: #ffffff;
    }

    .section-header {
        display: flex;
        align-items: center;
        margin-bottom: 20px;
        padding-bottom: 10px;
        border-bottom: 2px solid #00204E;
    }

    .section-header i {
        font-size: 1.5rem;
        color: #00204E;
        margin-right: 10px;
    }

    .section-header h4 {
        margin: 0;
        color: #2d3748;
        font-weight: 600;
    }

    .form-label {
        font-weight: 600;
        color: #4a5568;
        margin-bottom: 5px;
    }

    .required::after {
        content: " *";
        color: #e53e3e;
    }

    .item-row {
        background: #f7fafc;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        padding: 15px;
        margin-bottom: 15px;
        position: relative;
    }

    .item-row-header {
        background: #edf2f7;
        padding: 8px 12px;
        border-radius: 5px;
        margin-bottom: 15px;
        font-weight: 600;
        color: #2d3748;
    }

    .btn-remove-item {
        position: absolute;
        top: 10px;
        right: 10px;
    }

    .summary-box {
        background: linear-gradient(135deg, #00204E 0%, #00204E 100%);
        color: white;
        border-radius: 10px;
        padding: 20px;
        margin-top: 20px;
    }

    .summary-row {
        display: flex;
        justify-content: space-between;
        padding: 8px 0;
        border-bottom: 1px solid rgba(255, 255, 255, 0.2);
    }

    .summary-row:last-child {
        border-bottom: none;
        font-size: 1.3rem;
        font-weight: 700;
        padding-top: 15px;
        margin-top: 10px;
        border-top: 2px solid rgba(255, 255, 255, 0.5);
    }

    .btn-submit {
        background: linear-gradient(135deg, #00204E 0%, #00204E 100%);
        border: none;
        padding: 12px 40px;
        font-size: 1.1rem;
        font-weight: 600;
        border-radius: 8px;
        box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
        transition: all 0.3s;
    }

    .btn-submit:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(102, 126, 234, 0.6);
    }

    .badge-remote {
        font-size: 1rem;
        padding: 5px 10px;
    }

    /* Customer Search Visual Feedback */
    .customer-found {
        border-color: #28a745 !important;
        box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.25);
        transition: all 0.3s ease;
    }

    .searching-customer {
        background-image: url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMjAiIGhlaWdodD0iMjAiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+PGNpcmNsZSBjeD0iMTAiIGN5PSIxMCIgcj0iOCIgc3Ryb2tlPSIjNjY3ZWVhIiBzdHJva2Utd2lkdGg9IjIiIGZpbGw9Im5vbmUiPjxhbmltYXRlVHJhbnNmb3JtIGF0dHJpYnV0ZU5hbWU9InRyYW5zZm9ybSIgdHlwZT0icm90YXRlIiBmcm9tPSIwIDEwIDEwIiB0bz0iMzYwIDEwIDEwIiBkdXI9IjFzIiByZXBlYXRDb3VudD0iaW5kZWZpbml0ZSIvPjwvY2lyY2xlPjwvc3ZnPg==');
        background-repeat: no-repeat;
        background-position: right 10px center;
        background-size: 20px 20px;
    }
</style>

<div class="container-fluid">
    <div class="main-container">
        <!-- Page Header -->
        <div class="page-header">
            <h2>
                <i class="fas fa-shopping-cart mr-3"></i>
                ระบบ ขายแท่ง- Bowins Design
            </h2>
            <p class="mb-0 mt-2">ระบบสร้างออเดอร์พร้อมคำนวณค่าส่งอัตโนมัติ</p>
        </div>

        <!-- Form Start -->
        <form name="form_multi_order" id="form_multi_order" method="post">
            <!-- ส่วนที่ 1: ข้อมูลลูกค้า -->
            <div class="section-card">
                <div class="section-header">
                    <i class="fas fa-user"></i>
                    <h4>ข้อมูลลูกค้า</h4>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="form-label required">ชื่อลูกค้า</label>
                            <input type="text"
                                class="form-control"
                                name="customer_name"
                                placeholder="ระบุชื่อลูกค้า"
                                required>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="form-label required">เบอร์โทรศัพท์</label>
                            <input type="text"
                                class="form-control"
                                name="phone">
                            <small class="form-text text-muted">
                                <i class="fas fa-search mr-1"></i>
                                พิมพ์เบอร์โทรเพื่อค้นหาข้อมูลลูกค้าอัตโนมัติ
                            </small>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="form-label">Username</label>
                            <input type="text"
                                class="form-control"
                                name="username"
                                placeholder="Username (ถ้ามี)">
                            <small class="form-text text-muted">
                                <i class="fas fa-search mr-1"></i>
                                หรือค้นหาด้วย Username
                            </small>
                        </div>
                    </div>
                </div>

                <!-- แถวใหม่: Platform + เลข Order จาก Platform + VAT -->
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="form-label required">Platform</label>
                            <select class="form-control" name="platform" required>
                                <option value="">-- เลือก Platform --</option>
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
                            <label class="form-label">เลข Order จาก Platform อื่นๆ</label>
                            <input type="text"
                                class="form-control"
                                name="order_platform"
                                placeholder="เช่น เลขออเดอร์ Shopee / Lazada / TikTok">
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="form-label required">VAT</label>
                            <select class="form-control" name="vat_type" required>
                                <option value="">-- เลือก VAT --</option>
                                <option value="2">มี VAT</option>
                                <option value="0">ไม่มี VAT</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- แถววันที่สั่งซื้อ / วันที่จัดส่ง -->
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">วันที่สั่งซื้อ</label>
                            <input type="date"
                                class="form-control"
                                name="date">
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">วันที่จัดส่ง</label>
                            <input type="date"
                                class="form-control"
                                name="delivery_date">
                        </div>
                    </div>
                </div>
            </div>

            <!-- ส่วนที่ 2: ที่อยู่จัดส่ง -->
            <div class="section-card">
                <div class="section-header">
                    <i class="fas fa-map-marker-alt"></i>
                    <h4>ที่อยู่จัดส่ง</h4>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label required">ที่อยู่จัดส่ง</label>
                            <textarea class="form-control"
                                name="shipping_address"
                                id="shipping_address"
                                rows="3"
                                placeholder="กรอกที่อยู่พร้อมรหัสไปรษณีย์ 5 หลัก (เพื่อตรวจสอบพื้นที่ห่างไกล)"
                                required></textarea>
                            <small class="form-text text-muted">
                                <i class="fas fa-info-circle"></i>
                                ระบบจะตรวจสอบรหัสไปรษณีย์อัตโนมัติเพื่อคำนวณค่าพื้นที่ห่างไกล
                            </small>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">ที่อยู่ออกบิล</label>
                            <textarea class="form-control"
                                name="billing_address"
                                rows="3"
                                placeholder="ที่อยู่ออกบิล (ถ้าไม่ระบุจะใช้ที่อยู่จัดส่ง)"></textarea>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="form-label required">รูปแบบการจัดส่ง</label>
                            <select class="form-control"
                                name="orderable_type"
                                id="orderable_type"
                                required>
                                <option value="">-- เลือกรูปแบบการจัดส่ง --</option>
                                <option value="post_office">ไปรษณีย์ (EMS)</option>
                                <option value="delivered_by_company">ส่งโดยบริษัท</option>
                                <option value="receive_at_company">รับที่บริษัท</option>
                                <option value="receive_at_luckgems">รับที่ Lucky Gems</option>
                            </select>
                            <small class="form-text text-muted">
                                <i class="fas fa-info-circle"></i>
                                ค่าพื้นที่ห่างไกลคิดเฉพาะ "ไปรษณีย์ (EMS)" เท่านั้น
                            </small>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="form-label">วิธีการส่ง</label>
                            <select class="form-control" name="shipping" id="shipping">
                                <option value="auto">คำนวณอัตโนมัติ</option>
                                <option value="4">ส่งฟรี (0 บาท)</option>
                                <option value="1">บังคับ EMS (50 บาท)</option>
                                <option value="2">บังคับ EMS รับประกัน (100 บาท)</option>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="form-label">ค่าพื้นที่ห่างไกล (Override)</label>
                            <select class="form-control" id="remote_area_fee">
                                <option value="0">ตรวจสอบอัตโนมัติ</option>
                                <option value="50">บังคับพื้นที่ห่างไกล (+50 บาท)</option>
                            </select>
                            <small class="form-text text-muted">
                                <i class="fas fa-info-circle"></i>
                                ใช้เฉพาะกรณีต้องการ Override
                            </small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ส่วนที่ 3: รายการสินค้า -->
            <div class="section-card">
                <div class="section-header">
                    <i class="fas fa-box"></i>
                    <h4>รายการสินค้า</h4>
                </div>

                <div id="items-container">
                    <!-- Item Row Template (รายการแรก) -->
                    <div class="item-row">
                        <div class="item-row-header">
                            <i class="fas fa-cube mr-2"></i>รายการที่ 1
                        </div>
                        <button type="button"
                            class="btn btn-danger btn-sm btn-remove-item"
                            style="position:absolute; top:10px; right:10px;">
                            <i class="fas fa-times"></i>
                        </button>
                        <div class="row">
                            <!-- สินค้า -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label required">สินค้า</label>
                                    <select class="form-control product-select"
                                        name="items[0][product_id]"
                                        required>
                                        <option value="">-- เลือกสินค้า --</option>
                                        <?php
                                        $sql = "SELECT * FROM bs_products_bwd WHERE status = 1";
                                        $result = $dbc->query($sql);
                                        if ($result && $result->num_rows > 0) {
                                            while ($row = $result->fetch_assoc()) {
                                                echo '<option value="' . $row['id'] . '">' . $row['name'] . '</option>';
                                            }
                                        } else {
                                            echo '<option value="">ไม่มีสินค้า</option>';
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>

                            <!-- ประเภทสินค้า -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label required">ประเภทสินค้า</label>
                                    <select class="form-control product-type-select"
                                        name="items[0][product_type]"
                                        required>
                                        <option value="">-- เลือกสินค้าก่อน --</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <!-- จำนวน -->
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="form-label required">จำนวน</label>
                                    <input type="number"
                                        class="form-control amount-input"
                                        name="items[0][amount]"
                                        value="1"
                                        min="1"
                                        step="1"
                                        required>
                                </div>
                            </div>

                            <!-- ราคา/แท่ง -->
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="form-label required">ราคา/แท่ง</label>
                                    <input type="number"
                                        class="form-control price-input"
                                        name="items[0][price]"
                                        min="0"
                                        step="0.01"
                                        required>
                                </div>
                            </div>

                            <!-- ส่วนลด -->
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="form-label">ส่วนลด</label>
                                    <select class="form-control discount-select" name="items[0][discount]">
                                        <option value="0">ไม่มี</option>
                                        <option value="5">5%</option>
                                        <option value="10">10%</option>
                                        <option value="15">15%</option>
                                        <option value="20">20%</option>
                                        <option value="25">25%</option>
                                        <option value="30">30%</option>
                                    </select>
                                </div>
                            </div>

                            <!-- ยอดรวม -->
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="form-label">ยอดรวม</label>
                                    <input type="text"
                                        class="form-control item-total-display"
                                        readonly
                                        style="background: #f0f0f0; font-weight: bold; font-size: 0.9rem;"
                                        value="0.00">
                                </div>
                            </div>
                        </div>


                        <div class="row">
                            <!-- การสลักข้อความ -->
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="form-label">สลักข้อความ</label>
                                    <div>
                                        <div class="custom-control custom-radio custom-control-inline">
                                            <input type="radio"
                                                class="custom-control-input engrave-radio"
                                                id="engrave_no_0"
                                                name="items[0][engrave]"
                                                value="ไม่สลักข้อความบนแท่งเงิน"
                                                checked>
                                            <label class="custom-control-label" for="engrave_no_0">
                                                ไม่สลัก
                                            </label>
                                        </div>
                                        <div class="custom-control custom-radio custom-control-inline">
                                            <input type="radio"
                                                class="custom-control-input engrave-radio"
                                                id="engrave_yes_0"
                                                name="items[0][engrave]"
                                                value="สลักข้อความบนแท่งเงิน">
                                            <label class="custom-control-label" for="engrave_yes_0">
                                                สลักข้อความ (+300฿/แท่ง)
                                            </label>
                                        </div>
                                    </div>
                                    <small class="form-text text-muted">
                                        * Shopee, Lazada, TikTok ไม่คิดค่าสลัก
                                    </small>
                                </div>
                            </div>

                            <!-- ข้อความที่ต้องการสลัก -->
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="form-label">ข้อความที่ต้องการสลัก</label>
                                    <input type="text"
                                        class="form-control carving-input"
                                        name="items[0][carving]"
                                        placeholder="ระบุข้อความ (ถ้าเลือกสลัก)"
                                        readonly>
                                </div>
                            </div>

                            <!-- Font -->
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label class="form-label">Font</label>
                                    <select class="form-control font-select"
                                        name="items[0][font]"
                                        disabled>
                                        <option value="">-- เลือก Font --</option>
                                        <?php
                                        $sql = "SELECT * FROM bs_fonts_bwd WHERE status = 1";
                                        $result = $dbc->query($sql);
                                        if ($result && $result->num_rows > 0) {
                                            while ($row = $result->fetch_assoc()) {
                                                echo '<option value="' . $row['name'] . '">' . $row['name'] . '</option>';
                                            }
                                        } else {
                                            echo '<option value="">ไม่มี Font</option>';
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>

                            <!-- AI -->
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="form-label">AI รูปภาพ</label>
                                    <select class="form-control ai-select" name="items[0][ai]">
                                        <option value="0">ไม่มี AI รูปภาพ</option>
                                        <option value="1">มี AI รูปภาพ (+400฿/แท่ง)</option>
                                    </select>
                                    <small class="form-text text-muted">
                                        * Shopee, Lazada, TikTok ไม่คิดค่า AI รูปภาพ
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- End Item Row -->
                </div>
                <!-- End Items Container -->

                <!-- ปุ่มเพิ่มรายการ -->
                <div class="text-center mt-3">
                    <button type="button"
                        class="btn btn-primary btn-add-item"
                        id="btn-add-item">
                        <i class="fas fa-plus-circle mr-2"></i>
                        เพิ่มรายการสินค้า
                    </button>
                </div>

            </div>

            <!-- ส่วนที่ 4: ค่าธรรมเนียมและหมายเหตุ -->
            <div class="section-card">
                <div class="section-header">
                    <i class="fas fa-file-invoice-dollar"></i>
                    <h4>ค่าธรรมเนียมและหมายเหตุ</h4>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">ค่าธรรมเนียม (บาท)</label>
                            <input type="number"
                                class="form-control"
                                name="fee"
                                id="fee"
                                value="0"
                                min="0"
                                step="0.01"
                                placeholder="0.00">
                            <small class="form-text text-muted">
                                <i class="fas fa-info-circle"></i>
                                ค่าธรรมเนียมจะหักออกจากยอดรวม
                            </small>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">หมายเหตุ</label>
                            <textarea class="form-control"
                                name="comment"
                                rows="3"
                                placeholder="หมายเหตุเพิ่มเติม (ถ้ามี)"></textarea>
                        </div>
                    </div>
                </div>
            </div>

            <div id="shipping-breakdown" style="display: none;"></div>

            <div class="section-card">
                <div class="section-header">
                    <i class="fas fa-calculator"></i>
                    <h4>สรุปยอดรวม</h4>
                </div>

                <div class="summary-box">
                    <div class="summary-row">
                        <div>
                            <i class="fas fa-shopping-bag mr-2"></i>
                            <strong>มูลค่าสินค้ารวม:</strong>
                        </div>
                        <div id="subtotal-amount">0.00</div>
                    </div>

                    <div class="summary-row">
                        <div>
                            <i class="fas fa-percentage mr-2"></i>
                            <strong>ส่วนลดรวม:</strong>
                        </div>
                        <div class="text-warning" id="discount-amount">0.00</div>
                    </div>

                    <div class="summary-row">
                        <div>
                            <i class="fas fa-tag mr-2"></i>
                            <strong>ยอดหลังหักส่วนลด:</strong>
                        </div>
                        <div id="subtotal-after-discount">0.00</div>
                    </div>

                    <!-- ค่าสลักข้อความ -->
                    <div class="summary-row">
                        <div>
                            <i class="fas fa-pen-nib mr-2"></i>
                            <strong>ค่าสลักข้อความ:</strong>
                        </div>
                        <div class="text-info" id="engrave-amount">0.00</div>
                    </div>

                    <!-- ค่า AI Design -->
                    <div class="summary-row">
                        <div>
                            <i class="fas fa-robot mr-2"></i>
                            <strong>ค่า AI รูปภาพ:</strong>
                        </div>
                        <div class="text-info" id="ai-amount">0.00</div>
                    </div>

                    <div class="summary-row">
                        <div>
                            <i class="fas fa-shipping-fast mr-2"></i>
                            <strong>ค่าจัดส่ง:</strong>
                        </div>
                        <div id="shipping-amount">0.00</div>
                    </div>

                    <div class="summary-row">
                        <div>
                            <i class="fas fa-hand-holding-usd mr-2"></i>
                            <strong>ค่าธรรมเนียม:</strong>
                        </div>
                        <div class="text-danger" id="fee-amount">0.00</div>
                    </div>

                    <div class="summary-row">
                        <div>
                            <i class="fas fa-money-bill-wave mr-2"></i>
                            <strong>ยอดรวมสุทธิ:</strong>
                        </div>
                        <div id="grand-total">0.00</div>
                    </div>
                </div>

                <div class="alert alert-info mt-3 mb-0">
                    <i class="fas fa-info-circle mr-2"></i>
                    <strong>หมายเหตุ:</strong>
                    <ul class="mb-0 mt-2">
                        <li>ค่าส่งคำนวณตามมูลค่าสินค้า: 1-14,999฿ = 50฿ | 15,000-50,000฿ = 100฿</li>
                        <li>ยอดเกิน 50,000 บาท จะแยกกล่องอัตโนมัติ (แต่ละกล่องไม่เกิน 50,000฿) เฉพาะ ที่ไม่ใช่ รับสินค้าที่บริษัทและรับที่ Luck gems</li>
                        <li>กล่อง Premium คิดค่ากล่อง +25฿/กล่อง</li>
                        <li>กล่องไม้คิดค่ากล่อง +100฿/กล่อง</li>
                        <li>พื้นที่ห่างไกล คิด +50฿/กล่อง (เฉพาะไปรษณีย์)</li>
                        <li><strong>สลักข้อความ +300฿/แท่ง | AI Design +400฿/แท่ง</strong></li>
                        <li><strong>Platform Shopee, Lazada, TikTok: ไม่คิดค่าสลักข้อความและ AI รูปภาพ</strong></li>
                    </ul>
                </div>
            </div>

            <!-- ปุ่ม Submit -->
            <div class="text-center mt-4 mb-3">
                <button type="submit"
                    class="btn btn-primary btn-lg btn-submit"
                    id="btn-submit">
                    <i class="fas fa-save mr-2"></i>
                    สร้าง Order
                </button>

                <button type="button"
                    class="btn btn-secondary btn-lg ml-3"
                    onclick="fn.app.sales_screen_bwd.multiorder.resetForm()">
                    <i class="fas fa-redo mr-2"></i>
                    Reset
                </button>
            </div>

        </form>
        <!-- End Form -->

    </div>
    <!-- End Main Container -->
</div>