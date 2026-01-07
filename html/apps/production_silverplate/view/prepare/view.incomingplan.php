<?php

$dbc = new dbc;
$dbc->Connect();
$os = new oceanos($dbc);

global $ui_form, $dbc;
$production = $dbc->GetRecord("bs_productions", "*", "id=" . $_GET['production_id']);


?>
<form style="display:none;" name="incoming" enctype="multipart/form-data">
    <input name="file" type="file">
</form>

<table id="tblIncomingplan" class="table table-striped table-bordered table-hover table-middle" style="width:100%">
    <thead class="bg-dark">
        <tr>
            <th></th>
            <th class="text-center font-weight-bold text-white">ศนะ ์น.</th>
            <th class="text-center font-weight-bold text-white">เตาหลอม</th>
            <th class="text-center font-weight-bold text-white">เวลาเปิด</th>
            <th class="text-center font-weight-bold text-white">เวลาปิด</th>
            <th class="text-center font-weight-bold text-white">เบ้าหลอม</th>
            <th class="text-center font-weight-bold text-white">จำนวน</th>
            <th class="text-center font-weight-bold text-white">remark</th>
            <th class="text-center font-weight-bold text-white">พนักงาน</th>
            <th class="text-center font-weight-bold text-white">สถานะ</th>
        </tr>
    </thead>
    <tbody>
    </tbody>
</table>