<?php
$today = time();
?>
<div class="mb-2 float-right">
    <input id="selcted_date" type="date" class="form-control" value="<?php echo date("Y-m-d", $today) ?>" onchange="$('#tblTransfer').DataTable().draw();">
</div>
<div class="btn-area btn-group mb-2"></div>
<table id="tblDaily" class="table table-striped table-bordered table-hover table-middle" width="100%">
    <thead class="bg-dark text-white font-weight-bold">
        <tr>
            <th class="text-center hidden-xs">
                <span type="checkall" control="chk_transfer" class="far fa-lg fa-square"></span>
            </th>
            <th class="text-center">Date</th>
            <th class="text-center">Spot Sell</th>
            <th class="text-center">Spot Buy</th>
            <th class="text-center">Cash</th>
            <th class="text-center">Action</th>
        </tr>
    </thead>
    <tbody>
    </tbody>
</table>