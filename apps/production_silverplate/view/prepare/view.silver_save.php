<?php

$dbc = new dbc;
$dbc->Connect();
$os = new oceanos($dbc);

global $ui_form, $dbc;
$production = $dbc->GetRecord("bs_productions", "*", "id=" . $_GET['production_id']);


?>
<div class="row">
    <div class="col-12">
        <div class="mb-1">
            <a type="button" onclick="fn.app.production_silverplate.prepare.dialog_add_silver_save(<?php echo $production['id']; ?>)" class="btn btn-primary mr-2">เพิ่ม</a>
        </div>

        <table id="tblSilver" data-id="<?php echo $production['id']; ?>" class=" table table-striped table-bordered" style="width:100%">
            <thead class="bg-dark">
                <tr>
                    <th></th>
                    <th class="text-center font-weight-bold text-white">วันที่</th>
                    <th class="text-center font-weight-bold text-white">จำนวนแท่ง</th>
                    <th class="text-center font-weight-bold text-white">น้ำหนักแท่งเงิน</th>
                    <th class="text-center font-weight-bold text-white">เวลา</th>
                    <th class="text-center font-weight-bold text-white">ผู้เปิดเซฟ</th>
                    <th class="text-center font-weight-bold text-white">สถานะ</th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
    </div>
</div>