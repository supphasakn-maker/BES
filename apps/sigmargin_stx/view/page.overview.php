<div class="row gutters-sm">
    <div class="col-xl-12 mb-3">
        <table id="tblHeader" class="table table-bordered">
            <tbody>
                <tr class="bg-dark text-white font-weight">
                    <th>Stone X</th>
                    <td>BOWIN</td>
                    <td>(Bowins Silver Limited Partnership)</td>
                    <td>
                        <input type="date" class="form-control form-control-sm" name="date" value="<?php echo date('Y-m-d'); ?>">
                    </td>
                    <td>
                        <button class="btn btn-primary btn-sm" onclick="$('input[name=date]').change()">Reload</button>
                    </td>
                </tr>
            </tbody>
        </table>
        <div id="output"></div>
    </div>
</div>
<style>
    .tooltip-inner {
        max-width: none;
    }
</style>