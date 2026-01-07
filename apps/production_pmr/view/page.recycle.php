<?php
global $dbc;

$today = time();


?>


<div class="btn-area btn-group mb-2">
    <form name="filter" class="form-inline mr-2" onsubmit="return false;">
        <label class="mr-sm-2">From</label>
        <input name="from" type="date" class="form-control mr-sm-2" value="<?php echo date("Y-m-d", $today - (86400 * 30)); ?>">
        <label class="mr-sm-2">To</label>
        <input name="to" type="date" class="form-control mr-sm-2" value="<?php echo date("Y-m-d", $today + (86400 * 30)); ?>">
        <button type="button" class="btn btn-primary" onclick='$("#tblRecycle").DataTable().draw();'>Lookup</button>
    </form>
</div>
<table id="tblRecycle" class="table table-striped table-bordered table-hover table-middle" width="100%">
    <thead class="bg-dark">
        <tr>
            <th class="text-center text-white text-bold">วันที่ส่งผลิต</th>
            <th class="text-center text-white text-bold">วันที่ submited</th>
            <th class="text-center text-white text-bold">รอบที่</th>
            <th class="text-center text-white text-bold">น้ำหนัก</th>
            <th class="text-center text-white text-bold">PRODUCT</th>
            <th class="text-center text-white text-bold">บันทึก</th>
            <th class="text-center text-white text-bold"></th>
        </tr>
    </thead>
    <tbody>
    </tbody>
</table>