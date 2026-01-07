<?php
global $dbc;

$value_month = '';
if (isset($_GET['month'])) {
    $value_month = $_GET['month'];
}

?>
<div class="btn-area btn-group mb-2">
    <form onsubmit="return false;" name="filter" class="form-inline mr-2">
        <select name="type" class="form-control">
            <option value="none">ข้อมูลโดยละเอียด</option>
            <option value="daily">ข้อมูลรายวัน</option>
            <option value="monthly">ข้อมูลรายเดือน</option>
        </select>
        <input type="month" name="month" class="form-control mr-2" value="<?php echo $value_month; ?>">
        <button type="button" class="btn btn-primary" onclick="fn.app.profit_loss.trade.search()">Search</button>
    </form>
</div>
<div id="output" class="row gutters-sm">

</div>