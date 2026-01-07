<?php
global $dbc;

?>
<div class="btn-area btn-group mb-2">
    <form onsubmit="return false;" name="filter" class="form-inline mr-2">
        <label class="col-sm-2 col-form-label text-right">วัน</label>
        <div class="col-sm-5">
            <input name="date" type="date" class="form-control" value="<?php echo date("Y-m-d"); ?>" onchange="fn.app.profit_loss.daily.search()">
        </div>
        <div class="col-sm-2">
            <button type="button" class="btn btn-primary">Search</button>
        </div>
        <br>
        <div class="col-sm-3">
            <button onclick="printDiv('printThis')" class="btn-area btn btn-warning" style="margin-left: 10px;">Print</button>
        </div>
    </form>
</div>
<div id="output" class="row gutters-sm">

</div>