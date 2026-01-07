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

        // ดึงข้อมูล main order
        $main_order = $dbc->GetRecord("bs_orders_bwd", "*", "id=" . $_POST['id']);

        // ดึงข้อมูล sub orders ถ้ามี
        $sub_orders = array();
        if (is_null($main_order['parent'])) {
            // ถ้าเป็น main order ให้หา sub orders
            $sql = "SELECT * FROM bs_orders_bwd WHERE parent = " . $_POST['id'] . " AND status > 0 ORDER BY id";
            $result = $dbc->query($sql);
            if ($result && $result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $sub_orders[] = $row;
                }
            }
        } else {
            // ถ้าเป็น sub order ให้หา main order และ sub orders อื่นๆ
            $main_order = $dbc->GetRecord("bs_orders_bwd", "*", "id=" . $main_order['parent']);
            $sql = "SELECT * FROM bs_orders_bwd WHERE parent = " . $main_order['id'] . " AND status > 0 ORDER BY id";
            $result = $dbc->query($sql);
            if ($result && $result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $sub_orders[] = $row;
                }
            }
        }

        $all_orders = array_merge(array($main_order), $sub_orders);
?>
        <form name="form_editorder">
            <input type="hidden" name="main_order_id" value="<?php echo $main_order['id']; ?>">

            <!-- Customer Information (ใช้ข้อมูลจาก main order) -->
            <div class="customer-section mb-4">
                <h5><i class="fas fa-user mr-2"></i>ข้อมูลลูกค้า</h5>
                <div class="form-row">
                    <div class="form-group col-md-4">
                        <label>ชื่อลูกค้า</label>
                        <input type="text" class="form-control" name="customer_name" placeholder="ชื่อลูกค้า" autocomplete="off" value="<?php echo htmlspecialchars($main_order["customer_name"]); ?>">
                    </div>
                    <div class="form-group col-md-4">
                        <label>Username</label>
                        <input type="text" class="form-control" name="username" placeholder="Username" autocomplete="off" value="<?php echo htmlspecialchars($main_order["customer_name"]); ?>">
                    </div>
                    <div class="form-group col-md-4">
                        <label>Platform</label>
                        <select name="platform" class="form-control">
                            <option value="">กรุณาเลือกรายการ</option>
                            <option <?php if ($main_order['platform'] == 'Facebook') echo "selected"; ?> value="Facebook">Facebook</option>
                            <option <?php if ($main_order['platform'] == 'LINE') echo "selected"; ?> value="LINE">LINE</option>
                            <option <?php if ($main_order['platform'] == 'IG') echo "selected"; ?> value="IG">IG</option>
                            <option <?php if ($main_order['platform'] == 'Shopee') echo "selected"; ?> value="Shopee">Shopee</option>
                            <option <?php if ($main_order['platform'] == 'Lazada') echo "selected"; ?> value="Lazada">Lazada</option>
                            <option <?php if ($main_order['platform'] == 'Website') echo "selected"; ?> value="Website">Website</option>
                            <option <?php if ($main_order['platform'] == 'LuckGems') echo "selected"; ?> value="LuckGems">LuckGems</option>
                            <option <?php if ($main_order['platform'] == 'TikTok') echo "selected"; ?> value="TikTok">TikTok</option>
                            <option <?php if ($main_order['platform'] == 'SilverNow') echo "selected"; ?> value="SilverNow">SilverNow</option>
                            <option <?php if ($main_order['platform'] == 'WalkIN') echo "selected"; ?> value="WalkIN">WalkIN</option>
                            <option <?php if ($main_order['platform'] == 'Exhibition') echo "selected"; ?> value="Exhibition">Exhibition</option>
                        </select>
                    </div>
                    <div class="form-group col-md-4">
                        <label>เบอร์</label>
                        <input type="text"
                            class="form-control"
                            name="phone"
                            placeholder="0 หรือ 08xxxxxxxxx"
                            value="<?php echo htmlspecialchars($main_order['phone']); ?>"
                            autocomplete="off"
                            pattern="^0$|^[0-9]{10}$"
                            title="กรุณากรอก 0 ตัวเดียว หรือเบอร์โทรศัพท์ 10 หลัก (เฉพาะตัวเลข)"
                            inputmode="numeric"
                            oninput="this.value = this.value.replace(/[^0-9]/g, '');">
                    </div>
                </div>
            </div>

            <!-- Orders Items Section -->
            <div class="orders-section">
                <h5><i class="fas fa-box mr-2"></i>รายการสินค้า</h5>
                <div id="orders-container">
                    <?php foreach ($all_orders as $index => $order): ?>
                        <div class="order-item border rounded p-3 mb-3" data-order-id="<?php echo $order['id']; ?>">
                            <div class="order-header mb-3">
                                <h6 class="mb-0">
                                    <i class="fas fa-gem mr-2"></i>
                                    <?php echo $index === 0 ? 'รายการหลัก' : 'รายการที่ ' . $index; ?>
                                    <small class="text-muted">(ID: <?php echo $order['id']; ?>)</small>
                                </h6>
                            </div>

                            <input type="hidden" name="orders[<?php echo $index; ?>][id]" value="<?php echo $order['id']; ?>">
                            <input type="hidden" name="orders[<?php echo $index; ?>][is_main]" value="<?php echo $index === 0 ? '1' : '0'; ?>">

                            <div class="form-row">
                                <div class="form-group col-md-4">
                                    <label>จำนวนแท่ง</label>
                                    <input type="number" class="form-control amount-input" name="orders[<?php echo $index; ?>][amount]" placeholder="แท่ง" value="<?php echo $order['amount']; ?>" autocomplete="off" min="0">
                                </div>
                                <div class="form-group col-md-4">
                                    <label>ราคา</label>
                                    <input type="number" class="form-control price-input" name="orders[<?php echo $index; ?>][price]" placeholder="ราคา" value="<?php echo $order['price']; ?>" autocomplete="off" min="0">
                                </div>
                                <div class="form-group col-md-4">
                                    <label>ส่วนลด</label>
                                    <select name="orders[<?php echo $index; ?>][discount_type]" class="form-control discount-select">
                                        <option <?php if ($order['discount_type'] == '0') echo "selected"; ?> value="0">ไม่มีส่วนลด</option>
                                        <option <?php if ($order['discount_type'] == '5') echo "selected"; ?> value="5">5%</option>
                                        <option <?php if ($order['discount_type'] == '10') echo "selected"; ?> value="10">10%</option>
                                        <option <?php if ($order['discount_type'] == '15') echo "selected"; ?> value="15">15%</option>
                                        <option <?php if ($order['discount_type'] == '20') echo "selected"; ?> value="20">20%</option>
                                        <option <?php if ($order['discount_type'] == '25') echo "selected"; ?> value="25">25%</option>
                                        <option <?php if ($order['discount_type'] == '30') echo "selected"; ?> value="30">30%</option>
                                    </select>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label>สินค้า</label>
                                    <select name="orders[<?php echo $index; ?>][product_id]" class="form-control product-select">
                                        <option value="">Select Product</option>
                                        <?php
                                        $sql = "SELECT * FROM bs_products_bwd WHERE status = 1";
                                        $result = $dbc->query($sql);
                                        if ($result->num_rows > 0) {
                                            while ($row = $result->fetch_assoc()) {
                                                $selected = ($order['product_id'] == $row["id"]) ? "selected" : "";
                                                echo '<option value="' . $row['id'] . '" ' . $selected . '>' . $row['name'] . '</option>';
                                            }
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="form-group col-md-6">
                                    <label>ประเภทสินค้า</label>
                                    <select name="orders[<?php echo $index; ?>][product_type]" class="form-control product-type-select">
                                        <option value="">Select Type</option>
                                        <?php
                                        $sql = "SELECT * FROM bs_products_type WHERE status ='1' AND id = '" . $order['product_type'] . "'";
                                        $result = $dbc->query($sql);
                                        if ($result->num_rows > 0) {
                                            while ($row = $result->fetch_assoc()) {
                                                $selected = ($order['product_type'] == $row["id"]) ? "selected" : "";
                                                echo '<option value="' . $row['id'] . '" ' . $selected . '>' . $row['name'] . '</option>';
                                            }
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>

                            <!-- Engraving Section -->
                            <fieldset class="form-group">
                                <div class="row">
                                    <legend class="col-form-label col-sm-3 pt-0">สลักข้อความ</legend>
                                    <div class="col-sm-9">
                                        <div class="form-check">
                                            <input class="form-check-input engrave-radio" <?php if ($order['engrave'] == 'สลักข้อความบนแท่งเงิน') echo "checked"; ?> type="radio" name="orders[<?php echo $index; ?>][engrave]" id="engrave_yes_<?php echo $index; ?>" value="สลักข้อความบนแท่งเงิน">
                                            <label class="form-check-label" for="engrave_yes_<?php echo $index; ?>">สลักข้อความ</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input engrave-radio" <?php if ($order['engrave'] == 'ไม่สลักข้อความบนแท่งเงิน') echo "checked"; ?> type="radio" name="orders[<?php echo $index; ?>][engrave]" id="engrave_no_<?php echo $index; ?>" value="ไม่สลักข้อความบนแท่งเงิน">
                                            <label class="form-check-label" for="engrave_no_<?php echo $index; ?>">ไม่สลักข้อความ</label>
                                        </div>
                                    </div>
                                </div>
                            </fieldset>

                            <div class="form-row">
                                <div class="form-group col-md-3">
                                    <label>Fonts</label>
                                    <select name="orders[<?php echo $index; ?>][font]" class="form-control font-select" <?php echo ($order['engrave'] != 'สลักข้อความบนแท่งเงิน') ? 'disabled' : ''; ?>>
                                        <option value="">Select Font</option>
                                        <?php
                                        $sql = "SELECT * FROM bs_fonts_bwd WHERE status ='1'";
                                        $result = $dbc->query($sql);
                                        if ($result->num_rows > 0) {
                                            while ($row = $result->fetch_assoc()) {
                                                $selected = ($order['font'] == $row["name"]) ? "selected" : "";
                                                echo '<option value="' . $row['name'] . '" ' . $selected . '>' . $row['name'] . '</option>';
                                            }
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="form-group col-md-6">
                                    <label>สลักข้อความ</label>
                                    <input type="text" class="form-control carving-input" value="<?php echo htmlspecialchars($order['carving']); ?>" name="orders[<?php echo $index; ?>][carving]" placeholder="สลักข้อความ" autocomplete="off" <?php echo ($order['engrave'] != 'สลักข้อความบนแท่งเงิน') ? 'readonly' : ''; ?>>
                                </div>
                                <div class="form-group col-md-3">
                                    <label>มีรูปภาพ คิดเพิ่ม 200.-</label>
                                    <select name="orders[<?php echo $index; ?>][ai]" class="form-control ai-select">
                                        <option <?php if ($order['ai'] == '0') echo "selected"; ?> value="0">ไม่มีภาพ</option>
                                        <option <?php if ($order['ai'] == '1') echo "selected"; ?> value="1">มีภาพ AI</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Common Information (ใช้ข้อมูลจาก main order) -->
            <div class="common-section mt-4">
                <h5><i class="fas fa-cog mr-2"></i>ข้อมูลทั่วไป</h5>

                <div class="form-row">
                    <div class="form-group col-md-4">
                        <label>วันที่ซื้อ</label>
                        <input type="text" name="date" class="form-control" value="<?php echo $main_order['date']; ?>" readonly>
                    </div>
                    <div class="form-group col-md-4">
                        <label>วันที่จัดส่ง</label>
                        <input type="date" name="delivery_date" class="form-control" value="<?php echo $main_order['delivery_date']; ?>">
                    </div>
                    <div class="form-group col-md-4">
                        <label>วิธีการส่งสินค้า</label>
                        <select name="shipping" class="form-control">
                            <?php
                            $sql = "SELECT * FROM bs_shipping_bwd WHERE status = 1 ORDER BY FIELD(id,4,1,2,3,5,6)";
                            $result = $dbc->query($sql);
                            if ($result->num_rows > 0) {
                                while ($row = $result->fetch_assoc()) {
                                    $selected = ($main_order['shipping'] == $row["id"]) ? "selected" : "";
                                    echo '<option value="' . $row['id'] . '" ' . $selected . '>' . $row['name'] . '</option>';
                                }
                            }
                            ?>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label>ที่อยู่จัดส่ง</label>
                    <textarea class="form-control" name="shipping_address" rows="3" placeholder="ที่อยู่ในการจัดส่ง"><?php echo htmlspecialchars($main_order['shipping_address']); ?></textarea>
                </div>

                <div class="form-group">
                    <label>ที่อยู่ออกใบเสร็จ</label>
                    <textarea class="form-control" name="billing_address" rows="3" placeholder="ที่อยู่ในการออกใบเสร็จ"><?php echo htmlspecialchars($main_order['billing_address']); ?></textarea>
                </div>

                <div class="form-group">
                    <label>หมายเหตุ</label>
                    <textarea class="form-control" name="comment" rows="3" placeholder="หมายเหตุ"><?php echo htmlspecialchars($main_order['comment']); ?></textarea>
                </div>

                <div class="form-group">
                    <label>หมายเลข Tracking</label>
                    <input type="text" name="Tracking" class="form-control" placeholder="หมายเลข Tracking" value="<?php echo htmlspecialchars($main_order['Tracking']); ?>">
                </div>
            </div>
        </form>

<?php
    }
}

$modal = new myModel($dbc, $os->auth);
$modal->setParam($_POST);
$modal->setModel("dialog_edit_order", "EDIT MULTI ORDERS");
$modal->setExtraClass("modal-xl"); // ใช้ modal-xl เพื่อให้กว้างขึ้น
$modal->setButton(array(
    array("close", "btn-secondary", "Dismiss", "data-dismiss='modal'"),
    array("action", "btn-primary", "Save Changes", "fn.app.sales_screen_bwd_2.multiorder.edit()")
));
$modal->EchoInterface();

$dbc->Close();
?>

<style>
    /* Additional styles for multi-order edit */
    .order-item {
        background: rgba(0, 32, 78, 0.02);
        border: 2px solid rgba(0, 32, 78, 0.1) !important;
    }

    .order-header {
        border-bottom: 1px solid rgba(0, 32, 78, 0.1);
        padding-bottom: 0.5rem;
    }

    .customer-section,
    .common-section {
        background: rgba(0, 32, 78, 0.05);
        padding: 1rem;
        border-radius: 8px;
        border: 1px solid rgba(0, 32, 78, 0.1);
    }

    .orders-section {
        max-height: 60vh;
        overflow-y: auto;
    }

    /* Modal size adjustment */
    .modal-xl {
        max-width: 1200px;
    }

    @media (max-width: 1200px) {
        .modal-xl {
            max-width: 95%;
        }
    }
</style>

<script>
    $(document).ready(function() {
        // Handle engrave radio buttons for each order
        $(document).on('change', '.engrave-radio', function() {
            const orderItem = $(this).closest('.order-item');
            const selectedValue = $(this).val();
            const carvingInput = orderItem.find('.carving-input');
            const fontSelect = orderItem.find('.font-select');

            if (selectedValue === 'สลักข้อความบนแท่งเงิน') {
                carvingInput.removeAttr("readonly");
                fontSelect.prop('disabled', false);
            } else {
                carvingInput.attr("readonly", true).val('');
                fontSelect.prop('disabled', true).val('');
            }
        });

        // Handle product selection for each order
        $(document).on('change', '.product-select', function() {
            const orderItem = $(this).closest('.order-item');
            const productId = $(this).val();
            const productTypeSelect = orderItem.find('.product-type-select');

            if (productId) {
                $.ajax({
                    type: 'POST',
                    url: 'apps/sales_bwd/xhr/action-load-Type.php',
                    data: 'id=' + productId,
                    success: function(html) {
                        productTypeSelect.html(html);
                    },
                    error: function() {
                        productTypeSelect.html('<option value="">เกิดข้อผิดพลาด</option>');
                    }
                });
            } else {
                productTypeSelect.html('<option value="">Select Product Type</option>');
            }
        });
    });
</script>