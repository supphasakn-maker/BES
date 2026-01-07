<div class="mb-2">
    <button class="btn btn-outline-dark " onclick="window.history.back()">Back</button>
    <div>
        <?php
        global $os;
        $iform = new iform($dbc, $this->auth);
        $switch = $dbc->GetRecord("bs_productions_switch", "*", "id=" . $_GET['id']);

        echo '<table id="tblPmrDetail" data-id="' . $switch['id'] . '" class="table table-bordered table-sm mt-2">';
        echo '<tr>';
        echo '<th class="text-center">id</th>';
        echo '<th class="text-center">LOT ยืม </th>';
        echo '<th class="text-center">LOT คืน</th>';
        echo '<th class="text-center">วันที่คืน</th>';
        echo '</tr>';
        echo '<tr>';
        echo '<td class="text-center">';
        echo $switch['id'];
        echo '</td>';
        echo '<td class="text-center">';
        echo $switch['round_turn'];
        echo '</td>';
        echo '<td class="text-center">';
        echo $switch['round_turn'];
        echo '</td>';
        echo '<td class="text-center">';
        echo $switch['date_back'];
        echo '</td>';

        echo '</tr>';
        echo '</table>';
        echo '<hr>';


        echo '<div class="col-12">';
        echo '<div>';
        echo '<button onclick="fn.app.production_switch.switch.toggle_turn(' . $switch['id'] . ')" class="btn btn-warning mr-2">คืนทั้งหมด</button>';
        echo '<hr>';
        echo '<table id="tblPackTurn" class="table table-form table-sm table-bordered">';
        echo '<thead>';
        echo '<tr>';
        echo '<th class="text-center">รหัสถุง</th>';
        echo '<th class="text-center">ประเภทถุง</th>';
        echo '<th class="text-center">รายการถุง</th>';
        echo '<th class="text-center">นำหนัก</th>';
        echo '<th class="text-center">น้ำหนักแพ็คเสร็จ</th>';
        echo '<th class="text-center">ดำเนินการ</th>';
        echo '</tr>';
        echo '</thead>';
        echo '<tbody>';

        echo '</tbody>';
        echo '</table>';
        echo '</div>';
        echo '</div>';
        echo '</div>';
        echo '';

        ?>

        <ul class="list-group list-group-horizontal">
            <li class="list-group-item flex-fill text-center">
                <div class="text-secondary">Order Value</div><strong><?php echo number_format($switch['weight_out_packing'], 4); ?></strong>
            </li>
            <li class="list-group-item flex-fill text-center">
                <div class="text-secondary">Packing</div><strong id="amount_total">0.00</strong>
            </li>
            <li class="list-group-item flex-fill text-center">
                <div class="text-secondary">Remain</div><strong id="amount_remain">0.00</strong>
            </li>
        </ul>