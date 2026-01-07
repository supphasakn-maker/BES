			<div class="row">
			    <div class="col-12">
			        <table class="table table-bordered table-form">
			            <tbody>
			                <tr>
			                    <td><label>ประเภทการแจ้ง</label></td>
			                    <td>
			                        <?php
                                    $ui_form->EchoItem(array(
                                        "name" => "type",
                                        "type" => "combobox",
                                        "caption" => "ประเภทการแจ้ง",
                                        "source" => array('แจ้งเพื่อทราบและปรับปรุง', 'แจ้งเพื่อเคลมสินค้า'),
                                        "value" => $claim['type']
                                    ));
                                    ?>
			                    </td>
			                <tr>
			                <tr>
			                    <td><label>หมายเลข Order</label></td>
			                    <td>
			                        <?php
                                    $ui_form->EchoItem(array(
                                        "name" => "order_id",
                                        "type" => "comboboxdb",
                                        "caption" => "หมายเลข Order",
                                        "source" => array(
                                            "table" => "bs_orders",
                                            "value" => "id",
                                            "name" => "code"
                                        ),
                                        "value" => $claim['order_id']
                                    ));
                                    ?>
			                    </td>
			                <tr>
			                <tr>
			                    <td><label>บริษัท</label></td>
			                    <td>
			                        <?php
                                    $ui_form->EchoItem(array(
                                        "name" => "org_name",
                                        "caption" => "บริษัท",
                                        "readonly" => "readonly",
                                        "value" => $claim['org_name']
                                    ));
                                    ?>
			                    </td>
			                <tr>
                            <tr>
			                    <td><label>Product</label></td>
			                    <td>
			                        <?php
                                    $ui_form->EchoItem(array(
                                        "name" => "product_id",
                                        "type" => "comboboxdb",
                                        "caption" => "Product",
                                        "source" => array(
                                            "table" => "bs_products",
                                            "value" => "id",
                                            "name" => "name"
                                        ),
                                        "value" => $claim['product_id']
                                    ));
                                    ?>
			                    </td>
			                <tr>
			            </tbody>
			        </table>
			    </div>
			    <div class="col-4">
			        <table class="table table-bordered table-form">
			            <tbody>
			                <tr>
			                    <td><label>ผู้แจ้ง</label></td>
			                    <td>
			                        <?php
                                    $ui_form->EchoItem(array(
                                        "name" => "contact_issuer",
                                        "caption" => "ผู้แจ้ง",
                                        "placeholder" => "ผู้แจ้ง",
                                        "flex" => 3,
                                        "value" => $claim['contact_issuer']
                                    ));
                                    ?>
			                    </td>
                                </tr>
			                <tr>
			                <td><label name="type_work"> ผู้ส่ง</label></td>
			                <td>
			                    <?php
                                $ui_form->EchoItem(array(
                                    "name" => "contact_sender",
                                    "placeholder" => "ผู้ส่ง",
                                    "flex" => 3,
                                    "value" => $claim['contact_sender']
                                ));
                                ?>
			                </td>
			                </tr>
                            <tr>
			                <td><label name="type_work"> พนักงานขาย</label></td>
			                <td>
			                    <?php
                                $ui_form->EchoItem(array(
                                    "name" => "contact_sales",
                                    "placeholder" => "พนักงานขาย",
                                    "flex" => 3,
                                    "value" => $claim['contact_sales']
                                ));
                                ?>
			                </td>
			                </tr>
			            </tbody>
			        </table>
			    </div>
                <div class="col-4">
                <table class="table table-bordered table-form">
			            <tbody>
			                <tr>
			                    <td><label>ประเภทการแจ้ง</label></td>
			                    <td>
			                        <?php
                                    $ui_form->EchoItem(array(
                                        "name" => "issue",
                                        "type" => "combobox",
                                        "caption" => "ประเภทการแจ้ง",
                                        "source" => array('เป็นผง','เม็ดเหลือง/ไม่สวย','แพ็คเก็จไม่สมบูรณ์','น้ำหนักขาด','ความชื้น'),
                                        "value" => $claim['issue']
                                    ));
                                    ?>
			                    </td>
                                </tr>
			                <tr>
			                <td><label name="type_work">วันที่รับแจ้ง</label></td>
			                <td>
			                    <?php
                                $ui_form->EchoItem(array(
                                    "type" => "date",
                                    "name" => "date_claim",
                                    "caption" => "วันที่รับแจ้ง",
                                    "value" => date("Y-m-d"),
                                    "flex" => 4,
                                    "value" => $claim['date_claim']
                                ));
                                ?>
			                </td>
			                </tr>
                            <tr>
			                <td><label name="type_work"> จำนวนปัญหา</label></td>
			                <td>
			                    <?php
                                $ui_form->EchoItem(array(
                                    "name" => "amount",
                                    "caption" => "จำนวนปัญหา",
                                    "placeholder" => "จำนวนปัญหา",
                                    "flex" => 4,
                                    "value" => $claim['amount']
                                ));
                                ?>
			                </td>
			                </tr>
			            </tbody>
			        </table>                   
                </div>
                <div class="col-4">
                <table class="table table-bordered table-form">
			            <tbody>
			                <tr>
			                    <td><label>ขนาดถุง</label></td>
			                    <td>
			                        <?php
                                    $ui_form->EchoItem(array(
                                        "name" => "pack_problem",
                                        "caption" => "ขนาดถุง",
                                        "placeholder" => "ใส่ขนาดถุงที่พบปัญหา",
                                        "value" => $claim['pack_problem']
                                    ));
                                    ?>
			                    </td>
                                </tr>
			                <tr>
			                <td><label name="type_work">จำนวนการเคลม</label></td>
			                <td>
			                    <?php
                                $ui_form->EchoItem(array(
                                    "name" => "pack_claim",
                                    "caption" => "จำนวนการเคลม",
                                    "placeholder" => "ใส่จำนวนที่ต้องการเคลมสินค้า",
                                    "value" => $claim['pack_claim']
                                ));
                                ?>
			                </td>
			                </tr>
			            </tbody>
			        </table>                   
                </div>
                <div class="col-12">
                <table class="table table-bordered table-form">
			            <tbody>
			                <tr>
			                    <td><label>รายละเอียด</label></td>
			                    <td>
			                        <?php
                                    $ui_form->EchoItem(array(
                                        "name" => "detail",
                                        "type" => "textarea",
                                        "caption" => "รายละเอียด",
                                        "placeholder" => "รายละเอียด",
                                        "value" => $claim['detail'],
                                        "rows" => 6,
                                    ));
                                    ?>
			                    </td>
                                </tr>
			            </tbody>
			        </table>                   
                </div>
            </div>