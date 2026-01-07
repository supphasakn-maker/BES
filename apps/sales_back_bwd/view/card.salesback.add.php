<div class="card p-4">
    <form name="form_addquick_order" onsubmit="fn.app.sales_back_bwd.sale_back.add();return false;">
        <div class="form-row">
            <div class="form-group col-md-4">
                <label>ชื่อลูกค้า</label>
                <input type="text" class="form-control" name="customer_name" placeholder="ชื่อลูกค้า" autocomplete="off">
            </div>
            <div class="form-group col-md-4">
                <label>Platform</label>
                <select name="platform" class="form-control">
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
            <div class="form-group col-md-4">
                <label>เบอร์</label>
                <input type="text" class="form-control" name="phone" placeholder="เบอร์โทรศัพท์" autocomplete="off">
            </div>
        </div>
        <div class="form-row">
            <div class="form-group col-md-4">
                <label>จำนวนแท่ง</label>
                <input type="number" class="form-control" name="amount" placeholder="แท่ง" autocomplete="off">
            </div>
            <div class="form-group col-md-4">
                <label>ราคารับซื้อ</label>
                <input type="text" class="form-control" name="price" placeholder="ราคา" autocomplete="off">
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
                            echo '<option value="' . $row['id'] . '">' . $row['name'] . '</option>';
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
                </select>
            </div>
        </div>
        <fieldset class="form-group">
            <div class="row">
                <legend class="col-form-label col-sm-2 pt-0">สลักข้อความ</legend>
                <div class="col-sm-10">
                    <div class="form-check">
                        <input class="form-check-input option-เลือกบริการสลักข้อความ" type="radio" name="engrave" id="gridRadios1" value="สลักข้อความบนแท่งเงิน">
                        <label class="form-check-label">
                            แท่งดี
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input option-เลือกบริการสลักข้อความ" type="radio" name="engrave" id="gridRadios1" value="ไม่สลักข้อความบนแท่งเงิน">
                        <label class="form-check-label">
                            แท่งชำรุด
                        </label>
                    </div>
                </div>
            </div>
        </fieldset>
        <div class="form-row">
            <div class="form-group col-md-4">
                <label>Vats</label>
                <select name="vat_type" class="form-control">
                    <option value="">กรุณาเลือกรายการ</option>
                    <option value="0">ไม่มี Vats</option>
                    <option value="7">มี Vats</option>
                </select>
            </div>
            <div class="form-group col-md-4">
                <label>วันที่ขายคืน</label>
                <?php
                $ui_form->EchoItem(array(
                    "type" => "date",
                    "name" => "date",
                    "placeholder" => "Date",
                    "value" => date("Y-m-d")
                ));
                ?>
            </div>
        </div>
        <button class="btn btn-primary" type="submit">ทำรายการ</button>
    </form>
</div>
<script>
    $(document).ready(function() {
        $('#product_id').on('change', function() {
            var id = $(this).val();
            if (id) {
                $.ajax({
                    type: 'POST',
                    url: 'apps/sales_screen_bwd/xhr/action-load-Type.php',
                    data: 'id=' + id,
                    success: function(html) {
                        $('#product_type').html(html);
                    }
                });
            } else {
                $('#product_type').html('<option value="">Select Product Type</option>');
            }
        });
    });
</script>