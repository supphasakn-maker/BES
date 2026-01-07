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
$modal->setModel("dialog_add_multiple_silver", "เพิ่มหมายเลขแท่ง");
$modal->initiForm("form_add_multiple_silver");
$modal->setExtraClass("modal-lg");
$modal->setButton(array(
    array("close", "btn-secondary", "Dismiss"),
    array("action", "btn-primary", "Save Change", "fn.app.stock_silver.silver.add_multiple()")
));

$blueprint = array(
    array(
        array(
            "type" => "comboboxdb",
            "name" => "customer_po",
            "caption" => "หมายเลข PO",
            "source" => array(
                "name" => "code",
                "value" => "id",
                "table" => "bs_orders_buy",
                "where" => "status = 1 AND DATE(created) > '2025-11-14 '"
            )
        )
    ),
    array(
        array(
            "type" => "combobox",
            "name" => "stock",
            "caption" => "Stock",
            "source" => array(
                array('BWS', "BWS"),
                array('BWF', "BWF")
            ),
        )
    ),
    array(
        array(
            "type" => "date",
            "name" => "date",
            "caption" => "วันที่รับแท่ง",
            "autocomplete" => "off",
            "value" => date("Y-m-d"),
            "flex" => 4
        )
    ),
    array(
        array(
            "name" => "code",
            "caption" => "หมายเลขแท่ง",
            "autocomplete" => "off",
            "aria-autocomplete" => "none",
            "flex" => 4
        ),
        array(
            "name" => "quantity",
            "type" => "number",
            "caption" => "จำนวน",
            "value" => "1",
            "min" => "1",
            "max" => "100",
            "flex" => 2
        )
    ),
    array(
        array(
            "name" => "pack_name",
            "caption" => "ขนาดแท่ง",
            "value" => "SILVER BAR 1 KG",
            "readonly" => "readonly",
            "flex" => 4
        )
    ),
    array(
        array(
            "name" => "pack_type",
            "caption" => "ประเภท",
            "value" => "แท่ง",
            "readonly" => "readonly",
            "flex" => 4
        )
    ),
    array(
        array(
            "name" => "weight_actual",
            "type" => "number",
            "caption" => "น้ำหนัก",
            "value" => "1.0000",
            "readonly" => "readonly",
            "flex" => 4
        )
    ),
    array(
        array(
            "name" => "weight_expected",
            "type" => "number",
            "caption" => "น้ำหนักจริง",
            "value" => "1.0000",
            "readonly" => "readonly",
            "flex" => 4
        )
    )
);

$modal->SetBlueprint($blueprint);
$modal->EchoInterface();

?>
<script>
    $(document).ready(function() {
        var previewHtml = '<div class="row" style="margin-top: 15px;">' +
            '<div class="col-12">' +
            '<div class="form-group">' +
            '<label class="col-form-label" style="font-weight: bold; color: #495057;">ตัวอย่างหมายเลขที่จะเพิ่ม:</label>' +
            '<div id="preview_codes" class="alert alert-info" style="display:none; max-height: 200px; overflow-y: auto; margin-top: 10px;">' +
            '<div id="preview_list"></div>' +
            '</div>' +
            '</div>' +
            '</div>' +
            '</div>';

        $('form[name=form_add_multiple_silver] input[name=quantity]').closest('.row').after(previewHtml);

        console.log('Preview section added');
    });
</script>
<?php

$dbc->Close();
