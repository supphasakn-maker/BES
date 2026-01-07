<?php
$today = time();
?>
<div class="mb-2 float-right">
    <input id="selcted_date" type="date" class="form-control" value="<?php echo date("Y-m-d") ?>" onchange="$('#tblRollover').DataTable().draw();">
</div>
<div class="btn-area btn-group mb-2"></div>
<table id="tblRollover" class="table table-striped table-bordered table-hover table-middle" width="100%">
    <thead class="bg-dark text-white font-weight">
        <tr>
            <th class="text-center hidden-xs">
                <span type="checkall" control="chk_rollover" class="far fa-lg fa-square"></span>
            </th>
            <th class="text-center">Buy / Sell</th>
            <th class="text-center">KGS</th>
            <th class="text-center">SPOT</th>
            <th class="text-center">Trade Date</th>
            <th class="text-center">Value Date</th>
            <th class="text-center">Entry</th>
            <th class="text-center">Action</th>
        </tr>
    </thead>
    <tbody>
    </tbody>
</table>