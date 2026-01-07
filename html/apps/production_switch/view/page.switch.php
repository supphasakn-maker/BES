<?php
global $dbc;

if ($this->GetSection() == "prepare") {
    include "view/page.switch.edit.php";
} else if ($this->GetSection() == "turn_around") {
    include "view/page.turn.php";
} else {
    $today = time();


?>
    <div class="btn-area btn-group mb-2">
        <form name="filter" class="form-inline mr-2" onsubmit="return false;">
            <label class="mr-sm-2">From</label>
            <input name="from" type="date" class="form-control mr-sm-2" value="<?php echo date("Y-m-d", $today - (86400 * 30)); ?>">
            <label class="mr-sm-2">To</label>
            <input name="to" type="date" class="form-control mr-sm-2" value="<?php echo date("Y-m-d", $today + (86400 * 30)); ?>">
            <button type="button" class="btn btn-primary" onclick='$("#tblSwitch").DataTable().draw();'>Lookup</button>
        </form>
    </div>
    <table id="tblSwitch" class="table table-striped table-bordered table-hover table-middle" width="100%">
        <thead class="bg-dark">
            <tr>
                <th class="text-center text-white text-bold">วันที่สร้าง</th>
                <th class="text-center text-white text-bold">วันที่ Submited</th>
                <th class="text-center text-white text-bold">วันที่ คืน</th>
                <th class="text-center text-white text-bold">LOT ตั้งต้น</th>
                <th class="text-center text-white text-bold">LOT คืน</th>
                <th class="text-center text-white text-bold">น้ำหนักที่ยืม</th>
                <th class="text-center text-white text-bold">น้ำหนักที่ต้องคืน</th>
                <th class="text-center text-white text-bold">ประเภท</th>
                <th class="text-center text-white text-bold">ประเภทที่ต้องคืน</th>
                <th class="text-center text-white text-bold">REMARK</th>
                <th class="text-center text-white text-bold"></th>
            </tr>
        </thead>
        <tbody>
        </tbody>
    </table>
<?php
}
?>