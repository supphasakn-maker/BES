<?php
session_start();
include_once "../../../config/define.php";
@ini_set('display_errors', DEBUG_MODE ? 1 : 0);
date_default_timezone_set(DEFAULT_TIMEZONE);

include_once "../../../include/db.php";
include_once "../../../include/oceanos.php";
include_once "../../../include/iface.php";
include_once "../../../include/session.php";

$dbc = new dbc;
$dbc->Connect();

$os = new oceanos($dbc);

$modal = new imodal($dbc, $os->auth);

class myModel extends imodal
{
    function body()
    {
        global $os;
        $dbc = $this->dbc;
        $ui_form = new iform($dbc, $os->auth);
        $orders = $dbc->GetRecord("bs_orders_bwd", "*", "id=" . $_POST['id']);
?>
        <form name="form_editorder">
            <div class="form-row">
                <div class="form-group col-md-4">
                    <label>ชื่อลูกค้า</label>
                    <input type="text" class="form-control" name="customer_name" placeholder="ชื่อลูกค้า" autocomplete="off" value="<?php echo $orders["customer_name"]; ?>">
                    <input type="hidden" name="id" class="form-control" value="<?php echo $orders['id']; ?>">
                </div>
                <div class="form-group col-md-4">
                    <label>Platform</label>
                    <select name="platform" class="form-control">
                        <option value="">กรุณาเลือกรายการ</option>
                        <option <?php if ($orders['platform'] == 'Facebook') echo "selected"; ?> value="Facebook">Facebook</option>
                        <option <?php if ($orders['platform'] == 'LINE') echo "selected"; ?> value="LINE">LINE</option>
                        <option <?php if ($orders['platform'] == 'IG') echo "selected"; ?> value="IG">IG</option>
                        <option <?php if ($orders['platform'] == 'Shopee') echo "selected"; ?> value="Shopee">Shopee</option>
                        <option <?php if ($orders['platform'] == 'Lazada') echo "selected"; ?> value="Lazada">Lazada</option>
                        <option <?php if ($orders['platform'] == 'Website') echo "selected"; ?> value="Website">Website</option>
                        <option <?php if ($orders['platform'] == 'LuckGems') echo "selected"; ?> value="LuckGems">LuckGems</option>
                        <option <?php if ($orders['platform'] == 'TikTok') echo "selected"; ?> value="TikTok">TikTok</option>
                        <option <?php if ($orders['platform'] == 'SilverNow') echo "selected"; ?> value="SilverNow">SilverNow</option>
                        <option <?php if ($orders['platform'] == 'WalkIN') echo "selected"; ?> value="WalkIN">WalkIN</option>
                    </select>
                </div>
                <div class="form-group col-md-4">
                    <label>เบอร์</label>
                    <input type="text"
                        class="form-control"
                        name="phone"
                        placeholder="0 หรือ 08xxxxxxxxx"
                        value="<?php echo htmlspecialchars($orders['phone']); ?>"
                        autocomplete="off"
                        pattern="^0$|^[0-9]{10}$"
                        title="กรุณากรอก 0 ตัวเดียว หรือเบอร์โทรศัพท์ 10 หลัก (เฉพาะตัวเลข)"
                        inputmode="numeric"
                        oninput="this.value = this.value.replace(/[^0-9]/g, '');">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group col-md-4">
                    <label>จำนวนแท่ง</label>
                    <input type="number" class="form-control" name="amount" placeholder="แท่ง" value="<?php echo $orders['amount']; ?>" autocomplete="off" min="0" oninput="this.value = this.value.replace(/[^0-9.]/g, '');">
                </div>
                <div class="form-group col-md-4">
                    <label>ราคา</label>
                    <input type="number" class="form-control" name="price" placeholder="ราคา" value="<?php echo $orders['price']; ?>" autocomplete="off" min="0">
                </div>
                <div class="form-group col-md-4">
                    <label>ส่วนลด</label>
                    <select name="discount_type" id="discount_type" class="form-control">
                        <option <?php if ($orders['discount_type'] == '0') echo "selected"; ?> value="0">ไม่มีส่วนลด</option>
                        <option <?php if ($orders['discount_type'] == '5') echo "selected"; ?> value="5">5%</option>
                        <option <?php if ($orders['discount_type'] == '10') echo "selected"; ?> value="10">10%</option>
                        <option <?php if ($orders['discount_type'] == '15') echo "selected"; ?> value="15">15%</option>
                        <option <?php if ($orders['discount_type'] == '20') echo "selected"; ?> value="20">20%</option>
                        <option <?php if ($orders['discount_type'] == '25') echo "selected"; ?> value="25">25%</option>
                        <option <?php if ($orders['discount_type'] == '30') echo "selected"; ?> value="30">30%</option>
                    </select>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group col-md-6">
                    <label>สินค้า</label>
                    <select id="product_id" name="product_id" class="form-control">
                        <option value="">Select Product</option>
                        <?php
                        $sql = "SELECT * FROM bs_products_bwd WHERE status = 1";
                        $result = $dbc->query($sql);
                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                if ($orders['product_id'] == $row["id"]) {
                                    $sel = "selected";
                                } else {
                                    $sel = "";
                                }
                                echo '<option value="' . $row['id'] . '" ' . $sel . '>' . $row['name'] . '</option>';
                            }
                        } else {
                            echo '<option value="">Product not available</option>';
                        }
                        ?>
                    </select>
                </div>
                <div class="form-group col-md-6">
                    <label>ประเภทสินค้า</label>
                    <select id="product_type" name="product_type" class="form-control">
                        <option value="">Select Type</option>
                        <?php
                        $sql = "SELECT * FROM bs_products_type WHERE status ='1' AND id = '" . $orders['product_type'] . "'";
                        $result = $dbc->query($sql);
                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                if ($orders['product_type'] == $row["id"]) {
                                    $sel = "selected";
                                } else {
                                    $sel = "";
                                }
                                echo '<option value="' . $row['id'] . '" ' . $sel . '>' . $row['name'] . '</option>';
                            }
                        } else {
                            echo '<option value="">Product Type not available</option>';
                        }
                        ?>
                    </select>
                </div>
            </div>

            <fieldset class="form-group">
                <div class="row">
                    <legend class="col-form-label col-sm-2 pt-0">สลักข้อความ</legend>
                    <div class="col-sm-10">
                        <div class="form-check">
                            <input class="form-check-input option-เลือกบริการสลักข้อความ" <?php if ($orders['engrave'] == 'สลักข้อความบนแท่งเงิน') echo "checked"; ?> type="radio" name="engrave" id="gridRadios1" value="สลักข้อความบนแท่งเงิน">
                            <label class="form-check-label" for="gridRadios1">
                                สลักข้อความ
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input option-เลือกบริการสลักข้อความ" <?php if ($orders['engrave'] == 'ไม่สลักข้อความบนแท่งเงิน') echo "checked"; ?> type="radio" name="engrave" id="gridRadios2" value="ไม่สลักข้อความบนแท่งเงิน">
                            <label class="form-check-label" for="gridRadios2">
                                ไม่สลักข้อความ
                            </label>
                        </div>
                    </div>
                </div>
            </fieldset>

            <div class="form-row">
                <div class="form-group col-md-3">
                    <label>Fonts</label>
                    <select id="เลือกฟอนต์เพื่อสลักข้อความลงบนแท่งเงิน" name="font" class="form-control">
                        <option value="">Select Font</option>
                        <?php
                        $sql = "SELECT * FROM bs_fonts_bwd WHERE status ='1'";
                        $result = $dbc->query($sql);
                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                if ($orders['font'] == $row["name"]) {
                                    $sel = "selected";
                                } else {
                                    $sel = "";
                                }
                                echo '<option value="' . $row['name'] . '" ' . $sel . '>' . $row['name'] . '</option>';
                            }
                        } else {
                            echo '<option value="">Font Type not available</option>';
                        }
                        ?>
                    </select>
                </div>
                <div class="form-group col-md-6">
                    <label>สลักข้อความ</label>
                    <input type="text" class="form-control carving" value="<?php echo $orders['carving']; ?>" id="สลักข้อความบนเงินแท่ง" name="carving" placeholder="สลักข้อความ" autocomplete="off">
                </div>
                <div class="form-group col-md-3">
                    <label>มีรูปภาพ คิดเพิ่ม 200.-</label>
                    <select name="ai" id="ai" class="form-control">
                        <option <?php if ($orders['ai'] == '0') echo "selected"; ?> value="0">ไม่มีภาพ</option>
                        <option <?php if ($orders['ai'] == '1') echo "selected"; ?> value="1">มีภาพ AI</option>
                    </select>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group col-md-4">
                    <label>วันที่ซื้อ</label>
                    <input type="text" name="date" class="form-control" value="<?php echo $orders['date']; ?>" readonly>
                </div>
                <div class="form-group col-md-4">
                    <label>วันที่จัดส่ง</label>
                    <input type="date" name="delivery_date" class="form-control" value="<?php echo $orders['delivery_date']; ?>">
                </div>
                <div class="form-group col-md-4">
                    <label>วิธีการส่งสินค้า</label>
                    <select name="shipping" id="shipping" class="form-control">
                        <?php
                        $sql = "SELECT * FROM bs_shipping_bwd WHERE status = 1 ORDER BY FIELD(id,4,1,2,3,5,6)";
                        $result = $dbc->query($sql);
                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                if ($orders['shipping'] == $row["id"]) {
                                    $sel = "selected";
                                } else {
                                    $sel = "";
                                }
                                echo '<option value="' . $row['id'] . '" ' . $sel . '>' . $row['name'] . '</option>';
                            }
                        } else {
                            echo '<option value="">Shipping not available</option>';
                        }
                        ?>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label>ที่อยู่จัดส่ง</label>
                <textarea class="form-control" name="shipping_address" rows="5" placeholder="ที่อยู่ในการจัดส่ง"><?php echo $orders['shipping_address']; ?></textarea>
            </div>

            <div class="form-group">
                <label>ที่อยู่ออกใบเสร็จ</label>
                <textarea class="form-control" name="billing_address" rows="5" placeholder="ที่อยู่ในการออกใบเสร็จ"><?php echo $orders['billing_address']; ?></textarea>
            </div>

            <div class="form-group">
                <label>หมายเหตุ</label>
                <textarea class="form-control" name="comment" rows="3" placeholder="หมายเหตุ"><?php echo $orders['comment']; ?></textarea>
            </div>
            <div class="form-group">
                <label>หมายเลข Tracking</label>
                <input type="text" name="Tracking" class="form-control" placeholder="หมายเลข Tracking" value="<?php echo $orders['Tracking']; ?>">
            </div>
        </form>

<?php
    }
}

$modal = new myModel($dbc, $os->auth);
$modal->setParam($_POST);
$modal->setModel("dialog_edit_order", "EDIT ORDERS");
$modal->setExtraClass("modal-lg");
$modal->setButton(array(
    array("close", "btn-secondary", "Dismiss", "data-dismiss='modal'"),
    array("action", "btn-danger", "Submit", "fn.app.sales_bwd.order.edit()")
));
$modal->EchoInterface();

$dbc->Close();
?>

<style>
    /* Modal และ form controls สำหรับ Edit Order */
    .modal#dialog_edit_order .modal-dialog {
        max-width: 800px;
        pointer-events: auto;
    }

    .modal#dialog_edit_order .modal-content {
        border: none;
        border-radius: 12px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
    }

    /* Form Controls */
    #dialog_edit_order .modal-body .form-control,
    #dialog_edit_order .modal-body .form-select,
    #dialog_edit_order .modal-body select,
    #dialog_edit_order .modal-body input[type="text"],
    #dialog_edit_order .modal-body input[type="date"],
    #dialog_edit_order .modal-body input[type="number"],
    #dialog_edit_order .modal-body textarea {
        border: 2px solid #00204E;
        border-radius: 6px;
        padding: 12px 15px;
        font-size: 1rem;
        transition: all 0.3s ease;
        background: white;
        width: 100%;
        height: 55px;
        line-height: 30px;
        vertical-align: middle;
        box-sizing: border-box;
    }

    /* เฉพาะ textarea ให้สูงกว่า */
    #dialog_edit_order .modal-body textarea {
        height: 120px;
        line-height: 1.4;
        resize: vertical;
    }

    /* แก้ไข select โดยเฉพาะ */
    #dialog_edit_order .modal-body select {
        appearance: none;
        background-image: url("data:image/svg+xml;charset=US-ASCII,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 4 5'><path fill='%2300204E' d='M2 0L0 2h4zm0 5L0 3h4z'/></svg>");
        background-repeat: no-repeat;
        background-position: right 12px center;
        background-size: 0.7rem auto;
        padding-right: 40px;
        cursor: pointer;
        font-weight: 500;
    }

    /* Focus states */
    #dialog_edit_order .modal-body .form-control:focus,
    #dialog_edit_order .modal-body .form-select:focus,
    #dialog_edit_order .modal-body select:focus,
    #dialog_edit_order .modal-body input:focus,
    #dialog_edit_order .modal-body textarea:focus {
        border-color: #00204E;
        box-shadow: 0 0 0 3px rgba(0, 32, 78, 0.15);
        outline: none;
    }

    /* Labels */
    #dialog_edit_order .modal-body label {
        font-weight: 600;
        color: #00204E;
        font-size: 0.9rem;
        margin-bottom: 6px;
        display: block;
    }

    /* Modal Header */
    #dialog_edit_order .modal-header {
        background: linear-gradient(135deg, #00204E 0%, #003d7a 100%);
        color: white;
        border-bottom: none;
        padding: 1rem 1.5rem;
        border-radius: 12px 12px 0 0;
    }

    #dialog_edit_order .modal-header .modal-title {
        color: white;
        font-weight: 600;
        font-size: 1.2rem;
    }

    #dialog_edit_order .modal-header .btn-close,
    #dialog_edit_order .modal-header .close {
        color: white;
        opacity: 0.8;
        cursor: pointer;
    }

    #dialog_edit_order .modal-header .btn-close:hover,
    #dialog_edit_order .modal-header .close:hover {
        opacity: 1;
    }

    /* Modal Body */
    #dialog_edit_order .modal-body {
        padding: 1.5rem;
        background: white;
        max-height: 70vh;
        overflow-y: auto;
    }

    /* Modal Footer */
    #dialog_edit_order .modal-footer {
        padding: 1rem 1.5rem;
        background: white;
        border-top: 1px solid #e9ecef;
        border-radius: 0 0 12px 12px;
    }

    #dialog_edit_order .modal-footer .btn {
        padding: 0.6rem 1.5rem;
        font-weight: 600;
        border-radius: 6px;
        font-size: 0.95rem;
    }

    /* Form Layout */
    #dialog_edit_order .form-row {
        display: flex;
        flex-wrap: wrap;
        margin-left: -10px;
        margin-right: -10px;
        margin-bottom: 0.5rem;
    }

    #dialog_edit_order .form-group {
        margin-bottom: 1rem;
        padding-left: 10px;
        padding-right: 10px;
        position: relative;
    }

    /* Grid columns */
    #dialog_edit_order .col-md-3 {
        flex: 0 0 25%;
        max-width: 25%;
    }

    #dialog_edit_order .col-md-4 {
        flex: 0 0 33.333333%;
        max-width: 33.333333%;
    }

    #dialog_edit_order .col-md-6 {
        flex: 0 0 50%;
        max-width: 50%;
    }

    #dialog_edit_order .col-sm-2 {
        flex: 0 0 16.666667%;
        max-width: 16.666667%;
    }

    #dialog_edit_order .col-sm-10 {
        flex: 0 0 83.333333%;
        max-width: 83.333333%;
    }

    /* Fieldset */
    #dialog_edit_order fieldset {
        padding: 1rem;
        margin: 1rem 0;
        border: 2px solid #00204E;
        border-radius: 0.5rem;
        background-color: #f8f9fa;
    }

    #dialog_edit_order fieldset legend {
        font-weight: 700;
        color: #00204E;
        font-size: 1rem;
        padding: 0 0.75rem;
        margin-bottom: 0;
    }

    /* Radio Buttons */
    #dialog_edit_order .form-check {
        position: relative;
        display: block;
        padding-left: 1.25rem;
        margin-bottom: 0.5rem;
    }

    #dialog_edit_order .form-check-input {
        position: absolute;
        margin-top: 0.25rem;
        margin-left: -1.25rem;
        width: 16px;
        height: 16px;
        accent-color: #00204E;
        cursor: pointer;
    }

    #dialog_edit_order .form-check-label {
        margin-bottom: 0;
        color: #495057;
        cursor: pointer;
        font-size: 0.95rem;
        line-height: 1.4;
    }

    /* Modal backdrop และ modal state fixes */
    .modal-backdrop {
        position: fixed;
        top: 0;
        left: 0;
        z-index: 1040;
        width: 100vw;
        height: 100vh;
        background-color: #000;
    }

    .modal {
        position: fixed;
        top: 0;
        left: 0;
        z-index: 1050;
        width: 100%;
        height: 100%;
        overflow-x: hidden;
        overflow-y: auto;
        outline: 0;
    }

    /* ป้องกันปัญหา modal ปิดไม่ได้ */
    .modal.show .modal-dialog {
        transform: none;
        pointer-events: auto;
    }

    /* Responsive */
    @media (max-width: 768px) {

        #dialog_edit_order .col-md-3,
        #dialog_edit_order .col-md-4,
        #dialog_edit_order .col-md-6 {
            flex: 0 0 100%;
            max-width: 100%;
        }

        #dialog_edit_order .col-sm-2,
        #dialog_edit_order .col-sm-10 {
            flex: 0 0 100%;
            max-width: 100%;
        }

        #dialog_edit_order .modal-body {
            padding: 1rem;
        }

        #dialog_edit_order .form-row {
            margin-left: 0;
            margin-right: 0;
        }

        #dialog_edit_order .form-group {
            padding-left: 0;
            padding-right: 0;
        }

        #dialog_edit_order .modal-body .form-control,
        #dialog_edit_order .modal-body .form-select,
        #dialog_edit_order .modal-body select,
        #dialog_edit_order .modal-body input,
        #dialog_edit_order .modal-body textarea {
            font-size: 16px;
            height: 50px;
            padding: 10px 12px;
        }

        #dialog_edit_order .modal-body textarea {
            height: 100px;
        }

        .modal-dialog {
            margin: 0.25rem;
        }
    }
</style>

<script>
    $(document).ready(function() {
        // ตัวแปรป้องกันการรันซ้ำ
        let modalInitialized = false;

        // ตั้งค่า Modal attributes สำหรับ Bootstrap 4
        $('#dialog_edit_order').attr({
            'data-backdrop': 'true',
            'data-keyboard': 'true',
            'tabindex': '-1',
            'role': 'dialog',
            'aria-labelledby': 'dialog_edit_order_title',
            'aria-hidden': 'true'
        });

        // เมื่อ Modal เริ่มแสดง
        $('#dialog_edit_order').on('show.bs.modal', function(e) {
            if (modalInitialized) return;
            modalInitialized = true;
            console.log('Edit Order Modal is showing');
        });

        // เมื่อ Modal แสดงเสร็จแล้ว
        $('#dialog_edit_order').on('shown.bs.modal', function() {
            $(this).find('input:first').focus();
            console.log('Edit Order Modal shown');
        });

        // เมื่อ Modal ปิด
        $('#dialog_edit_order').on('hidden.bs.modal', function() {
            console.log('Edit Order Modal hidden');
            modalInitialized = false;

            // ลบ modal backdrop ที่ค้างอยู่
            $('.modal-backdrop').remove();
            $('body').removeClass('modal-open');
            $('body').css('padding-right', '');
        });

        // ป้องกัน form submission ที่อาจทำให้ Modal ไม่ปิด
        $(document).on('submit', 'form[name="form_editorder"]', function(e) {
            e.preventDefault();
            console.log('Form submission prevented');
            return false;
        });

        // จัดการ radio buttons สำหรับการสลักข้อความ
        $(document).on('change', "input:radio[name=engrave]", function() {
            var selectedRadio = $("input:radio[name=engrave]:checked").val();
            var txt = document.getElementById("สลักข้อความบนเงินแท่ง");
            var font = document.getElementById("เลือกฟอนต์เพื่อสลักข้อความลงบนแท่งเงิน");

            if (txt && font) {
                switch (selectedRadio) {
                    case 'สลักข้อความบนแท่งเงิน':
                        txt.removeAttribute("readonly");
                        font.disabled = false;
                        break;

                    case 'ไม่สลักข้อความบนแท่งเงิน':
                        txt.setAttribute("readonly", true);
                        txt.value = "";
                        font.disabled = true;
                        font.value = "";
                        break;
                }
            }
        });

        // จัดการการเปลี่ยนสินค้า
        $(document).on('change', '#product_id', function() {
            var id = $(this).val();
            if (id) {
                $.ajax({
                    type: 'POST',
                    url: 'apps/sales_bwd/xhr/action-load-Type.php',
                    data: 'id=' + id,
                    timeout: 10000,
                    success: function(html) {
                        $('#product_type').html(html);
                    },
                    error: function(xhr, status, error) {
                        console.error('AJAX Error:', error);
                        $('#product_type').html('<option value="">เกิดข้อผิดพลาด</option>');
                    }
                });
            } else {
                $('#product_type').html('<option value="">Select Product Type</option>');
            }
        });

        // เพิ่มฟังก์ชันปิด modal แบบ manual
        window.closeEditOrderModal = function() {
            $('#dialog_edit_order').modal('hide');
            $('.modal-backdrop').remove();
            $('body').removeClass('modal-open');
            $('body').css('padding-right', '');
        };

        // ESC key handler
        $(document).on('keydown', function(e) {
            if (e.key === 'Escape' && $('#dialog_edit_order').hasClass('show')) {
                window.closeEditOrderModal();
            }
        });

        // Backdrop click handler
        $(document).on('click', '.modal-backdrop', function() {
            if ($('#dialog_edit_order').hasClass('show')) {
                window.closeEditOrderModal();
            }
        });

        // เพิ่ม event handler สำหรับปุ่ม close
        $(document).on('click', '#dialog_edit_order .modal-header .close, #dialog_edit_order .modal-footer .btn-secondary', function() {
            window.closeEditOrderModal();
        });

        // รันการตรวจสอบเมื่อมีการคลิกปุ่มใดๆ
        $(document).on('click', '#dialog_edit_order .modal-footer .btn', function(e) {
            // หากเป็นปุ่ม Dismiss ให้ปิด modal
            if ($(this).hasClass('btn-secondary') || $(this).text().toLowerCase().includes('dismiss')) {
                e.preventDefault();
                window.closeEditOrderModal();
            }
        });
    });

    // Debug function สำหรับ Edit Order
    function debugEditOrderModal() {
        console.log('=== Edit Order Modal Debug ===');
        console.log('Modal exists:', $('#dialog_edit_order').length > 0);
        console.log('Modal is visible:', $('#dialog_edit_order').is(':visible'));
        console.log('Modal has show class:', $('#dialog_edit_order').hasClass('show'));
        console.log('Backdrop exists:', $('.modal-backdrop').length);
        console.log('Body has modal-open:', $('body').hasClass('modal-open'));
    }

    // สร้างฟังก์ชันสำหรับปิด modal แบบ force
    window.forceCloseEditOrderModal = function() {
        console.log('Force closing Edit Order modal...');

        $('#dialog_edit_order').modal('hide');

        setTimeout(function() {
            $('#dialog_edit_order').removeClass('show');
            $('.modal-backdrop').remove();
            $('body').removeClass('modal-open');
            $('body').css('padding-right', '');
            $('#dialog_edit_order').hide();

            console.log('Edit Order Modal force closed');
        }, 300);
    };

    // เพิ่ม global function สำหรับการใช้งานจาก console
    console.log('=== Edit Order Modal Functions ===');
    console.log('Available functions:');
    console.log('- debugEditOrderModal() - Debug modal state');
    console.log('- forceCloseEditOrderModal() - Force close modal');
    console.log('- closeEditOrderModal() - Normal close modal');
</script>