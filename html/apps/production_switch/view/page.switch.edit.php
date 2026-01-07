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
        echo '<th class="text-center">LOT ตั้งต้น</th>';
        echo '<th class="text-center">LOT คืน</th>';
        echo '<th class="text-center">วันที่ยืม</th>';
        echo '</tr>';
        echo '<tr>';
        echo '<td class="text-center">';
        echo $switch['id'];
        echo '</td>';
        echo '<td class="text-center">';
        echo $switch['round'];
        echo '</td>';
        echo '<td class="text-center">';
        echo $switch['round_turn'];
        echo '</td>';
        echo '<td class="text-center">';
        echo $switch['created'];
        echo '</td>';

        echo '</tr>';
        echo '</table>';

        echo '<hr>';
        echo '<div class="row">';
        echo '<div class="col-4">';
        echo '<div class="row">';
        echo '<div class="col-md-3 mb-1">';
        echo '<label>รอบการผลิต</label>';
        echo '<select name="round_filter" type="text" class="form-control" >';
        echo '<option value="">No Filter</option>';
        $sql = "SELECT DISTINCT bs_productions.id ,bs_productions.round 
						FROM bs_productions 
						LEFT OUTER JOIN bs_packing_items ON bs_productions.id = bs_packing_items.production_id 
						LEFT OUTER JOIN bs_delivery_pack_items ON bs_packing_items.id = bs_delivery_pack_items.item_id 
						WHERE bs_delivery_pack_items.delivery_id IS NULL AND bs_packing_items.status > -1 ORDER BY `round` DESC";
        $rst = $dbc->Query($sql);
        while ($item = $dbc->Fetch($rst)) {
            echo '<option value="' . $item['id'] . '">' . $item['round'] . '</option>';
        }
        echo '</select>';
        echo '</div>';

        echo '<div class="col-md-5">';
        echo '<label>เลือกจากหมายเลขถุง</label>';
        echo '<select name="code_search" type="text" class="form-control" placeholder="Product Item"></select>';
        echo '</div>';
        echo '<div class="col-md-1">';
        echo '<label class="text-white">No</label>';
        echo '<button onclick="fn.app.production_switch.switch.mapping()" class="btn btn-primary mr-2">Append</button>';
        echo '</div>';
        echo '</div>';

        echo '<div class="row">';
        echo '<div id="select_list_stock" class="col-md-12 form-inline"></div>';
        echo '<div class="col-md-12">';
        echo '<button onclick="fn.app.production_switch.switch.toggle_check()" class="btn btn-warning mr-2">Toggle</button>';
        echo '<button onclick="fn.app.production_switch.switch.append_bulk()" class="btn btn-primary mr-2">Append</button>';
        echo '</div>';
        echo '</div>';
        echo '</div>';
        echo '<div class="col-8">';
        echo '<div>';
        echo '<table id="tblPackitem" class="table table-form table-sm table-bordered">';
        echo '<thead>';
        echo '<tr>';
        echo '<th class="text-center">รหัสถุง</th>';
        echo '<th class="text-center">ประเภทถุง</th>';
        echo '<th class="text-center">รายการถุง</th>';
        echo '<th class="text-center">นำหนัก</th>';
        echo '<th class="text-center">น้ำหนักแพ็คเสร็จ</th>';
        echo '<th class="text-center">สถานะ</th>';
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