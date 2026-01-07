<?php

class OrderSplitManager
{
    private $dbc;

    public function __construct($dbc)
    {
        $this->dbc = $dbc;
    }

    private function getNextAvailableOrderId(): int
    {
        $sql1 = "SELECT COALESCE(MAX(order_id), 0) as max_id FROM bs_orders_profit";
        $result1 = $this->dbc->Query($sql1);
        $row1 = $this->dbc->Fetch($result1);
        $max1 = isset($row1['max_id']) ? intval($row1['max_id']) : intval($row1[0]);

        $sql2 = "SELECT COALESCE(MAX(id), 0) as max_id FROM bs_orders";
        $result2 = $this->dbc->Query($sql2);
        $row2 = $this->dbc->Fetch($result2);
        $max2 = isset($row2['max_id']) ? intval($row2['max_id']) : intval($row2[0]);

        $sql3 = "SELECT COALESCE(MAX(id), 0) as max_id FROM order_id_seq";
        $result3 = $this->dbc->Query($sql3);
        $row3 = $this->dbc->Fetch($result3);
        $max3 = isset($row3['max_id']) ? intval($row3['max_id']) : intval($row3[0]);

        $next_id = max($max1, $max2, $max3) + 1;

        error_log("Calculated next available order_id: {$next_id} (max from profit={$max1}, orders={$max2}, seq={$max3})");

        return $next_id;
    }

    private function reserveOrderIds(int $count): array
    {
        if ($count <= 0) return [];

        try {
            $check = $this->dbc->Query("SELECT COUNT(*) as cnt FROM order_id_seq");
            $check_row = $this->dbc->Fetch($check);
            $seq_count = isset($check_row['cnt']) ? intval($check_row['cnt']) : intval($check_row[0]);

            if ($seq_count == 0) {
                $start_id = $this->getNextAvailableOrderId();
                error_log("order_id_seq is empty, starting from: {$start_id}");

                $this->dbc->Query("INSERT INTO order_id_seq (id) VALUES ({$start_id})");

                if ($count > 1) {
                    $values = [];
                    for ($i = 1; $i < $count; $i++) {
                        $values[] = "(" . ($start_id + $i) . ")";
                    }
                    if (!empty($values)) {
                        $this->dbc->Query("INSERT INTO order_id_seq (id) VALUES " . implode(',', $values));
                    }
                }

                $ids = [];
                for ($i = 0; $i < $count; $i++) {
                    $ids[] = $start_id + $i;
                }

                error_log("Reserved Order IDs (from empty): " . implode(', ', $ids));
                return $ids;
            } else {
                $values = implode(',', array_fill(0, $count, '()'));
                $sql = "INSERT INTO order_id_seq VALUES {$values}";
                $this->dbc->Query($sql);

                // ใช้ LAST_INSERT_ID()
                $result = $this->dbc->Query("SELECT LAST_INSERT_ID() as last_id");
                $row = $this->dbc->Fetch($result);

                $first = isset($row['last_id']) ? intval($row['last_id']) : intval($row[0]);

                if ($first <= 0) {
                    error_log("LAST_INSERT_ID returned 0, using fallback method");
                    $result = $this->dbc->Query("SELECT MAX(id) as max_id FROM order_id_seq");
                    $row = $this->dbc->Fetch($result);
                    $max_id = isset($row['max_id']) ? intval($row['max_id']) : intval($row[0]);
                    $first = $max_id - $count + 1;
                }

                $ids = [];
                for ($i = 0; $i < $count; $i++) {
                    $ids[] = $first + $i;
                }

                error_log("Reserved Order IDs (from existing): " . implode(', ', $ids));
                return $ids;
            }
        } catch (Exception $e) {
            error_log("Error in reserveOrderIds: " . $e->getMessage());
            throw new Exception("ไม่สามารถสร้าง Order ID ได้: " . $e->getMessage());
        }
    }

    private function checkOrderIdExists($order_id): bool
    {
        $order_id = intval($order_id);

        $r1 = $this->dbc->Query("SELECT 1 FROM bs_orders_profit WHERE order_id={$order_id} LIMIT 1");
        $row1 = $this->dbc->Fetch($r1);
        if ($row1) return true;

        $r2 = $this->dbc->Query("SELECT 1 FROM bs_orders WHERE id={$order_id} LIMIT 1");
        $row2 = $this->dbc->Fetch($r2);
        if ($row2) return true;

        return false;
    }

    private function escapeValue($value)
    {
        if ($value === null || $value === '') return 'NULL';

        $value = str_replace("'", "''", $value);
        return "'" . $value . "'";
    }

    private function rowToAssocArray($row)
    {
        if (!$row) return null;

        return array(
            'id'               => isset($row['id']) ? $row['id'] : $row[0],
            'code'             => isset($row['code']) ? $row['code'] : $row[1],
            'customer_id'      => isset($row['customer_id']) ? $row['customer_id'] : $row[2],
            'customer_name'    => isset($row['customer_name']) ? $row['customer_name'] : $row[3],
            'date'             => isset($row['date']) ? $row['date'] : $row[4],
            'sales'            => isset($row['sales']) ? $row['sales'] : $row[5],
            'user'             => isset($row['user']) ? $row['user'] : $row[6],
            'type'             => isset($row['type']) ? $row['type'] : $row[7],
            'parent'           => isset($row['parent']) ? $row['parent'] : $row[8],
            'is_split'         => isset($row['is_split']) ? $row['is_split'] : $row[9],
            'split_sequence'   => isset($row['split_sequence']) ? $row['split_sequence'] : $row[10],
            'created'          => isset($row['created']) ? $row['created'] : $row[11],
            'updated'          => isset($row['updated']) ? $row['updated'] : $row[12],
            'amount'           => isset($row['amount']) ? $row['amount'] : $row[13],
            'price'            => isset($row['price']) ? $row['price'] : $row[14],
            'vat_type'         => isset($row['vat_type']) ? $row['vat_type'] : $row[15],
            'vat'              => isset($row['vat']) ? $row['vat'] : $row[16],
            'total'            => isset($row['total']) ? $row['total'] : $row[17],
            'net'              => isset($row['net']) ? $row['net'] : $row[18],
            'usd'              => isset($row['usd']) ? $row['usd'] : $row[19],
            'delivery_date'    => isset($row['delivery_date']) ? $row['delivery_date'] : $row[20],
            'delivery_time'    => isset($row['delivery_time']) ? $row['delivery_time'] : $row[21],
            'lock_status'      => isset($row['lock_status']) ? $row['lock_status'] : $row[22],
            'status'           => isset($row['status']) ? $row['status'] : $row[23],
            'comment'          => isset($row['comment']) ? $row['comment'] : $row[24],
            'shipping_address' => isset($row['shipping_address']) ? $row['shipping_address'] : $row[25],
            'billing_address'  => isset($row['billing_address']) ? $row['billing_address'] : $row[26],
            'rate_spot'        => isset($row['rate_spot']) ? $row['rate_spot'] : $row[27],
            'rate_exchange'    => isset($row['rate_exchange']) ? $row['rate_exchange'] : $row[28],
            'billing_id'       => isset($row['billing_id']) ? $row['billing_id'] : $row[29],
            'currency'         => isset($row['currency']) ? $row['currency'] : $row[30],
            'info_payment'     => isset($row['info_payment']) ? $row['info_payment'] : $row[31],
            'info_contact'     => isset($row['info_contact']) ? $row['info_contact'] : $row[32],
            'delivery_id'      => isset($row['delivery_id']) ? $row['delivery_id'] : $row[33],
            'remove_reason'    => isset($row['remove_reason']) ? $row['remove_reason'] : $row[34],
            'product_id'       => isset($row['product_id']) ? $row['product_id'] : $row[35],
            'keep_silver'      => isset($row['keep_silver']) ? $row['keep_silver'] : $row[36],
            'flag_hide'        => isset($row['flag_hide']) ? $row['flag_hide'] : $row[37],
            'store'            => isset($row['store']) ? $row['store'] : $row[38],
            'orderable_type'   => isset($row['orderable_type']) ? $row['orderable_type'] : (isset($row[39]) ? $row[39] : null),
            'order_id'         => isset($row['order_id']) ? $row['order_id'] : $row[40]
        );
    }

    public function splitOrder($original_order_id, $split_amounts)
    {
        $original_order_id = intval($original_order_id);

        $this->dbc->Query("START TRANSACTION");
        try {
            $sql = "SELECT * FROM bs_orders_profit WHERE order_id = {$original_order_id} FOR UPDATE";
            $result = $this->dbc->Query($sql);

            $original_row = $this->dbc->Fetch($result);
            if (!$original_row) {
                throw new Exception("ไม่พบข้อมูล order เดิม (order_id: {$original_order_id})");
            }

            $original = $this->rowToAssocArray($original_row);

            error_log("=== Split Order Debug ===");
            error_log("Original order_id: " . $original['order_id']);
            error_log("Original amount: " . $original['amount']);
            error_log("Original is_split: " . $original['is_split']);

            if (intval($original['is_split']) === 1) {
                throw new Exception("Order นี้ถูก split แล้ว");
            }

            $total_split = 0.0;
            foreach ($split_amounts as $a) {
                $amt = floatval($a);
                if ($amt <= 0) {
                    throw new Exception("จำนวนในการแบ่งต้องมากกว่า 0");
                }
                $total_split += $amt;
            }

            $original_amount = floatval($original['amount']);
            if (abs($total_split - $original_amount) > 0.0001) {
                throw new Exception("ยอดรวมของการแบ่ง ({$total_split}) ไม่ตรงกับยอดเดิม ({$original_amount})");
            }

            error_log("Split amounts validation passed. Total: {$total_split}");

            $firstAmount = floatval($split_amounts[0]);
            $price = floatval($original['price']);

            $upd = "
                UPDATE bs_orders_profit SET
                    amount = {$firstAmount},
                    total  = " . ($price * $firstAmount) . ",
                    net    = " . ($price * $firstAmount) . ",
                    is_split = 1,
                    parent = {$original_order_id},
                    split_sequence = 1,
                    updated = NOW()
                WHERE order_id = {$original_order_id}
            ";
            $this->dbc->Query($upd);

            error_log("Updated original record as split_sequence = 1");

            $childrenCount = count($split_amounts) - 1;
            if ($childrenCount > 0) {
                error_log("Reserving {$childrenCount} new order IDs...");
                $newIds = $this->reserveOrderIds($childrenCount);
                error_log("Reserved IDs: " . implode(', ', $newIds));

                for ($k = 0; $k < count($newIds); $k++) {
                    $retry_count = 0;
                    while ($this->checkOrderIdExists($newIds[$k]) && $retry_count < 3) {
                        error_log("Warning: order_id {$newIds[$k]} already exists, getting new one (retry {$retry_count})");
                        $more = $this->reserveOrderIds(1);
                        $newIds[$k] = $more[0];
                        $retry_count++;
                    }

                    if ($retry_count >= 3) {
                        throw new Exception("ไม่สามารถสร้าง order_id ที่ไม่ซ้ำได้ หลังจากพยายาม 3 ครั้ง");
                    }
                }

                $escaped_code             = $this->escapeValue($original['code']);
                $escaped_customer_name    = $this->escapeValue($original['customer_name']);
                $escaped_comment          = $this->escapeValue($original['comment']);
                $escaped_shipping_address = $this->escapeValue($original['shipping_address']);
                $escaped_billing_address  = $this->escapeValue($original['billing_address']);
                $escaped_billing_id       = $this->escapeValue($original['billing_id']);
                $escaped_currency         = $this->escapeValue($original['currency']);
                $escaped_info_payment     = $this->escapeValue($original['info_payment']);
                $escaped_info_contact     = $this->escapeValue($original['info_contact']);
                $escaped_remove_reason    = $this->escapeValue($original['remove_reason']);
                $escaped_delivery_time    = $this->escapeValue($original['delivery_time']);
                $escaped_store            = $this->escapeValue($original['store']);
                $escaped_orderable_type   = $this->escapeValue($original['orderable_type']);

                for ($i = 1; $i < count($split_amounts); $i++) {
                    $amt = floatval($split_amounts[$i]);
                    $seq = $i + 1;
                    $child_order_id = intval($newIds[$i - 1]);

                    error_log("Creating child {$i}: order_id={$child_order_id}, amount={$amt}, sequence={$seq}");

                    $insert_sql = "
                        INSERT INTO bs_orders_profit (
                            code, customer_id, customer_name, date, sales, user, type,
                            parent, is_split, split_sequence, created, updated,
                            amount, price, vat_type, vat, total, net, usd,
                            delivery_date, delivery_time, lock_status, status, comment,
                            shipping_address, billing_address, rate_spot, rate_exchange,
                            billing_id, currency, info_payment, info_contact, delivery_id,
                            remove_reason, product_id, keep_silver, flag_hide, store,
                            orderable_type, order_id
                        ) VALUES (
                            {$escaped_code},
                            " . intval($original['customer_id']) . ",
                            {$escaped_customer_name},
                            " . ($original['date'] ? "'" . $original['date'] . "'" : "NULL") . ",
                            " . intval($original['sales']) . ",
                            " . intval($original['user']) . ",
                            " . intval($original['type']) . ",
                            {$original_order_id},
                            1,
                            {$seq},
                            NOW(),
                            NOW(),
                            {$amt},
                            {$price},
                            " . intval($original['vat_type']) . ",
                            " . floatval($original['vat']) . ",
                            " . ($price * $amt) . ",
                            " . ($price * $amt) . ",
                            " . floatval($original['usd']) . ",
                            " . ($original['delivery_date'] ? "'" . $original['delivery_date'] . "'" : "NULL") . ",
                            {$escaped_delivery_time},
                            " . intval($original['lock_status']) . ",
                            " . intval($original['status']) . ",
                            {$escaped_comment},
                            {$escaped_shipping_address},
                            {$escaped_billing_address},
                            " . floatval($original['rate_spot']) . ",
                            " . floatval($original['rate_exchange']) . ",
                            {$escaped_billing_id},
                            {$escaped_currency},
                            {$escaped_info_payment},
                            {$escaped_info_contact},
                            " . intval($original['delivery_id']) . ",
                            {$escaped_remove_reason},
                            " . intval($original['product_id']) . ",
                            " . intval($original['keep_silver']) . ",
                            " . intval($original['flag_hide']) . ",
                            {$escaped_store},
                            {$escaped_orderable_type},
                            {$child_order_id}
                        )
                    ";

                    $this->dbc->Query($insert_sql);
                    error_log("Child {$i} inserted successfully");
                }
            }

            $this->dbc->Query("COMMIT");
            error_log("Split order transaction committed successfully");

            return [
                'success' => true,
                'message' => 'Split order สำเร็จ แบ่งเป็น ' . count($split_amounts) . ' รายการ'
            ];
        } catch (\Throwable $e) {
            $this->dbc->Query("ROLLBACK");
            error_log("Split order failed: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    public function unsplitOrder($parent_order_id)
    {
        $parent_order_id = intval($parent_order_id);
        $this->dbc->Query("START TRANSACTION");

        try {
            error_log("=== Unsplit Order Debug ===");
            error_log("Parent Order ID: " . $parent_order_id);

            $sql = "SELECT * FROM bs_orders_profit
                WHERE parent = {$parent_order_id} AND is_split = 1
                ORDER BY split_sequence ASC
                FOR UPDATE";
            $result = $this->dbc->Query($sql);

            $split_records = [];
            while ($row = $this->dbc->Fetch($result)) {
                $split_records[] = $this->rowToAssocArray($row);
            }

            error_log("Found " . count($split_records) . " split records");

            if (empty($split_records)) {
                throw new Exception("ไม่พบ split records");
            }

            $original_record = null;
            $total_amount = 0.0;
            foreach ($split_records as $rec) {
                $total_amount += floatval($rec['amount']);
                error_log("Split record: order_id=" . $rec['order_id'] . ", sequence=" . $rec['split_sequence'] . ", amount=" . $rec['amount']);

                if (intval($rec['split_sequence']) === 1) {
                    $original_record = $rec;
                }
            }

            if (!$original_record) {
                throw new Exception("ไม่พบ original record (split_sequence = 1)");
            }

            error_log("Total amount to restore: " . $total_amount);

            $price = floatval($original_record['price']);
            $restore_sql = "
            UPDATE bs_orders_profit SET
                amount = {$total_amount},
                total  = " . ($price * $total_amount) . ",
                net    = " . ($price * $total_amount) . ",
                is_split = 0,
                parent = NULL,
                split_sequence = NULL,
                updated = NOW()
            WHERE order_id = " . intval($original_record['order_id']);
            $this->dbc->Query($restore_sql);

            error_log("Restored original record");

            // Delete child records
            $deleted_count = 0;
            foreach ($split_records as $rec) {
                if (intval($rec['split_sequence']) !== 1) {
                    $del = "DELETE FROM bs_orders_profit WHERE order_id = " . intval($rec['order_id']);
                    $this->dbc->Query($del);
                    $deleted_count++;
                    error_log("Deleted child record: order_id=" . $rec['order_id']);
                }
            }

            error_log("Deleted {$deleted_count} child records");

            $this->dbc->Query("COMMIT");
            error_log("Unsplit order transaction committed successfully");

            return [
                'success' => true,
                'message' => 'Unsplit order สำเร็จ รวม ' . count($split_records) . ' รายการกลับเป็น 1 รายการ'
            ];
        } catch (\Throwable $e) {
            $this->dbc->Query("ROLLBACK");
            error_log("Unsplit order failed: " . $e->getMessage());
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    public function getSplitStatus($order_id)
    {
        $order_id = intval($order_id);
        $sql = "SELECT * FROM bs_orders_profit WHERE order_id = {$order_id}";
        $result = $this->dbc->Query($sql);
        $row = $this->dbc->Fetch($result);

        if (!$row) {
            return [
                'is_split' => false,
                'can_split' => false,
                'can_unsplit' => false
            ];
        }

        $record = $this->rowToAssocArray($row);
        $can_split = (intval($record['is_split']) === 0);
        $can_unsplit = (intval($record['is_split']) === 1);

        $split_records = [];
        if ($record['is_split'] == 1 && $record['parent']) {
            $p = intval($record['parent']);
            $sql = "SELECT * FROM bs_orders_profit 
                    WHERE parent = {$p} AND is_split = 1 
                    ORDER BY split_sequence ASC";
            $result = $this->dbc->Query($sql);
            while ($r = $this->dbc->Fetch($result)) {
                $split_records[] = $this->rowToAssocArray($r);
            }
        }

        return [
            'is_split'        => (intval($record['is_split']) === 1),
            'can_split'       => $can_split,
            'can_unsplit'     => $can_unsplit,
            'split_records'   => $split_records,
            'parent_order_id' => $record['parent'],
            'split_sequence'  => $record['split_sequence']
        ];
    }

    public function getSplitRecords($parent_order_id)
    {
        $parent_order_id = intval($parent_order_id);
        $sql = "SELECT * FROM bs_orders_profit 
                WHERE parent = {$parent_order_id} AND is_split = 1 
                ORDER BY split_sequence ASC";
        $result = $this->dbc->Query($sql);

        $records = [];
        while ($row = $this->dbc->Fetch($result)) {
            $records[] = $this->rowToAssocArray($row);
        }
        return $records;
    }
}
