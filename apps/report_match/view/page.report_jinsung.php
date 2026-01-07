<div class="card">
    <div class="card-header border-bottom">
        <h5 class="card-title p-2"><i class="far fa-link mr-2" aria-hidden="true"></i>Supplier Adjust Cost</h5>
    </div>
    <div class="card-body">
        <div class="btn-area btn-group mb-2">
            <input type="date" name="date" class="form-control" value="<?php echo date("Y-m-d") ?>">
            <button class="btn btn-danger" onclick="$('input[name=date]').change()">Lookup</button>
        </div>
        <div id="display_area">
        </div>
    </div>
</div>