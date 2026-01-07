<?php

class OrderSplitManager
{
    private $dbc;

    public function __construct($dbc)
    {
        $this->dbc = $dbc;
    }


    private function generateSafeOrderId()
    {
        
        $sql1 = "SELECT MAX(id) as max_id FROM bs_orders";
        $result1 = $this->dbc->Query($sql1);
        $row1 = $this->dbc->Fetch($result1);
        $max_orders_id = $row1 ? intval($row1[0]) : 0;

        $sql2 = "SELECT MAX(order_id) as max_id FROM bs_orders_profit";
        $result2 = $this->dbc->Query($sql2);
        $row2 = $this->dbc->Fetch($result2);
        $max_profit_id = $row2 ? intval($row2[0]) : 0;

        
        $start_id = max($max_orders_id, $max_profit_id) + 1000;

        
        while ($this->checkOrderIdExists($start_id)) {
            $start_id += 100;
        }

        return $start_id;
    }

    /**
     * ตรวจสอบว่า order_id มีอยู่ในระบบแล้วหรือไม่
     */
    private function checkOrderIdExists($order_id)
    {
        $sql1 = "SELECT COUNT(*) as count FROM bs_orders WHERE id = " . intval($order_id);
        $result1 = $this->dbc->Query($sql1);
        $row1 = $this->dbc->Fetch($result1);
        $exists_in_orders = $row1 ? intval($row1[0]) : 0;

        $sql2 = "SELECT COUNT(*) as count FROM bs_orders_profit WHERE order_id = " . intval($order_id);
        $result2 = $this->dbc->Query($sql2);
        $row2 = $this->dbc->Fetch($result2);
        $exists_in_profit = $row2 ? intval($row2[0]) : 0;

        return ($exists_in_orders > 0 || $exists_in_profit > 0);
    }

    /**
     * Split order เป็นหลายรายการ
     */
    public function splitOrder($original_order_id, $split_amounts)
    {
        try {
            
            $sql = "SELECT * FROM bs_orders_profit WHERE order_id = " . intval($original_order_id);
            $result = $this->dbc->Query($sql);
            $original_row = $this->dbc->Fetch($result);

            if (!$original_row) {
                throw new Exception("ไม่พบข้อมูล order เดิม");
            }

            
            $original = $this->rowToAssocArray($original_row);

            
            if ($original['is_split'] == 1) {
                throw new Exception("Order นี้ถูก split แล้ว");
            }

            
            $total_split = array_sum($split_amounts);
            if (abs($total_split - $original['amount']) > 0.0001) {
                throw new Exception("ยอดรวมของการแบ่งไม่ตรงกับยอดเดิม Original: " . $original['amount'] . ", Split Total: " . $total_split);
            }

            
            foreach ($split_amounts as $amount) {
                if ($amount <= 0) {
                    throw new Exception("จำนวนในการแบ่งต้องมากกว่า 0");
                }
            }

            $original_order_id_value = $original['order_id'];

            
            $update_sql = "UPDATE bs_orders_profit SET 
                amount = " . floatval($split_amounts[0]) . ",
                total = " . (floatval($original['price']) * floatval($split_amounts[0])) . ",
                net = " . (floatval($original['price']) * floatval($split_amounts[0])) . ",
                is_split = 1,
                parent = " . intval($original_order_id_value) . ",
                split_sequence = 1,
                updated = NOW()
                WHERE order_id = " . intval($original_order_id);

            $this->dbc->Query($update_sql);

            
            $start_order_id = $this->generateSafeOrderId();

            for ($i = 1; $i < count($split_amounts); $i++) {
                $new_order_id = $start_order_id + ($i - 1);

                
                while ($this->checkOrderIdExists($new_order_id)) {
                    $new_order_id += 100;
                }

                
                $escaped_code = $this->escapeValue($original['code']);
                $escaped_customer_name = $this->escapeValue($original['customer_name']);
                $escaped_comment = $this->escapeValue($original['comment']);
                $escaped_shipping_address = $this->escapeValue($original['shipping_address']);
                $escaped_billing_address = $this->escapeValue($original['billing_address']);
                $escaped_billing_id = $this->escapeValue($original['billing_id']);
                $escaped_currency = $this->escapeValue($original['currency']);
                $escaped_info_payment = $this->escapeValue($original['info_payment']);
                $escaped_info_contact = $this->escapeValue($original['info_contact']);
                $escaped_remove_reason = $this->escapeValue($original['remove_reason']);
                $escaped_delivery_time = $this->escapeValue($original['delivery_time']);
                $escaped_store = $this->escapeValue($original['store']);

                
                $insert_sql = "INSERT INTO bs_orders_profit (
                    code, customer_id, customer_name, date, sales, user, type, 
                    parent, is_split, split_sequence, created, updated,
                    amount, price, vat_type, vat, total, net, usd,
                    delivery_date, delivery_time, lock_status, status, comment,
                    shipping_address, billing_address, rate_spot, rate_exchange,
                    billing_id, currency, info_payment, info_contact, delivery_id,
                    remove_reason, product_id, keep_silver, flag_hide, store, order_id
                ) VALUES (
                    $escaped_code,
                    " . intval($original['customer_id']) . ",
                    $escaped_customer_name,
                    '" . $original['date'] . "',
                    " . intval($original['sales']) . ",
                    " . intval($original['user']) . ",
                    " . intval($original['type']) . ",
                    " . intval($original_order_id_value) . ",
                    1,
                    " . ($i + 1) . ",
                    NOW(),
                    NOW(),
                    " . floatval($split_amounts[$i]) . ",
                    " . floatval($original['price']) . ",
                    " . intval($original['vat_type']) . ",
                    " . floatval($original['vat']) . ",
                    " . (floatval($original['price']) * floatval($split_amounts[$i])) . ",
                    " . (floatval($original['price']) * floatval($split_amounts[$i])) . ",
                    " . floatval($original['usd']) . ",
                    " . ($original['delivery_date'] ? "'" . $original['delivery_date'] . "'" : "NULL") . ",
                    $escaped_delivery_time,
                    " . intval($original['lock_status']) . ",
                    " . intval($original['status']) . ",
                    $escaped_comment,
                    $escaped_shipping_address,
                    $escaped_billing_address,
                    " . floatval($original['rate_spot']) . ",
                    " . floatval($original['rate_exchange']) . ",
                    $escaped_billing_id,
                    $escaped_currency,
                    $escaped_info_payment,
                    $escaped_info_contact,
                    " . intval($original['delivery_id']) . ",
                    $escaped_remove_reason,
                    " . intval($original['product_id']) . ",
                    " . intval($original['keep_silver']) . ",
                    " . intval($original['flag_hide']) . ",
                    $escaped_store,
                    " . intval($new_order_id) . "
                )";

                $this->dbc->Query($insert_sql);
            }

            return array('success' => true, 'message' => 'Split order สำเร็จ แบ่งเป็น ' . count($split_amounts) . ' รายการ');
        } catch (Exception $e) {
            return array('success' => false, 'message' => $e->getMessage());
        }
    }

    /**
     * Helper method สำหรับ escape string values
     */
    private function escapeValue($value)
    {
        if ($value === null || $value === '') {
            return 'NULL';
        }

        
        if (method_exists($this->dbc, 'Escape')) {
            return "'" . $this->dbc->Escape($value) . "'";
        } else {
            return "'" . addslashes($value) . "'";
        }
    }

    /**
     * Helper method สำหรับแปลง numeric array เป็น associative array
     * ต้องดู schema ของ bs_orders_profit เพื่อ map columns ที่ถูกต้อง
     */
    private function rowToAssocArray($row)
    {
        if (!$row) return null;

        
        return array(
            'id' => $row[0],
            'code' => $row[1],
            'customer_id' => $row[2],
            'customer_name' => $row[3],
            'date' => $row[4],
            'sales' => $row[5],
            'user' => $row[6],
            'type' => $row[7],
            'parent' => $row[8],
            'is_split' => $row[9],
            'split_sequence' => $row[10],
            'created' => $row[11],
            'updated' => $row[12],
            'amount' => $row[13],
            'price' => $row[14],
            'vat_type' => $row[15],
            'vat' => $row[16],
            'total' => $row[17],
            'net' => $row[18],
            'usd' => $row[19],
            'delivery_date' => $row[20],
            'delivery_time' => $row[21],
            'lock_status' => $row[22],
            'status' => $row[23],
            'comment' => $row[24],
            'shipping_address' => $row[25],
            'billing_address' => $row[26],
            'rate_spot' => $row[27],
            'rate_exchange' => $row[28],
            'billing_id' => $row[29],
            'currency' => $row[30],
            'info_payment' => $row[31],
            'info_contact' => $row[32],
            'delivery_id' => $row[33],
            'remove_reason' => $row[34],
            'product_id' => $row[35],
            'keep_silver' => $row[36],
            'flag_hide' => $row[37],
            'store' => $row[38],
            'order_id' => $row[39]
        );
    }

    /**
     * Unsplit order กลับเป็นรายการเดียว
     */
    public function unsplitOrder($order_id)
    {
        try {
            
            $sql = "SELECT * FROM bs_orders_profit WHERE order_id = " . intval($order_id);
            $result = $this->dbc->Query($sql);
            $current_row = $this->dbc->Fetch($result);

            if (!$current_row) {
                throw new Exception("ไม่พบข้อมูล order");
            }

            
            $current = $this->rowToAssocArray($current_row);

            if ($current['is_split'] == 0) {
                throw new Exception("Order นี้ไม่ได้ถูก split");
            }

            $parent_order_id = $current['parent'];

            
            $sql = "SELECT * FROM bs_orders_profit WHERE parent = " . intval($parent_order_id) . " AND is_split = 1 ORDER BY split_sequence ASC";
            $result = $this->dbc->Query($sql);

            $split_records = array();
            while ($row = $this->dbc->Fetch($result)) {
                $split_records[] = $this->rowToAssocArray($row);
            }

            if (empty($split_records)) {
                throw new Exception("ไม่พบ split records");
            }

            
            $total_amount = 0;
            $original_record = null;

            foreach ($split_records as $record) {
                $total_amount += floatval($record['amount']);
                if ($record['split_sequence'] == 1) {
                    $original_record = $record;
                }
            }

            if (!$original_record) {
                throw new Exception("ไม่พบ original record");
            }

            
            $restore_sql = "UPDATE bs_orders_profit SET 
                amount = " . floatval($total_amount) . ",
                total = " . (floatval($original_record['price']) * floatval($total_amount)) . ",
                net = " . (floatval($original_record['price']) * floatval($total_amount)) . ",
                is_split = 0,
                parent = NULL,
                split_sequence = NULL,
                updated = NOW()
                WHERE order_id = " . intval($original_record['order_id']);

            $this->dbc->Query($restore_sql);

            
            foreach ($split_records as $record) {
                if ($record['split_sequence'] != 1) {
                    $delete_sql = "DELETE FROM bs_orders_profit WHERE order_id = " . intval($record['order_id']);
                    $this->dbc->Query($delete_sql);
                }
            }

            return array('success' => true, 'message' => 'Unsplit order สำเร็จ รวม ' . count($split_records) . ' รายการกลับเป็น 1 รายการ');
        } catch (Exception $e) {
            return array('success' => false, 'message' => $e->getMessage());
        }
    }

    /**
     * ตรวจสอบสถานะการ split
     */
    public function getSplitStatus($order_id)
    {
        $sql = "SELECT * FROM bs_orders_profit WHERE order_id = " . intval($order_id);
        $result = $this->dbc->Query($sql);
        $row = $this->dbc->Fetch($result);

        if (!$row) {
            return array('is_split' => false, 'can_split' => false, 'can_unsplit' => false);
        }

        
        $record = $this->rowToAssocArray($row);

        $can_split = ($record['is_split'] == 0);
        $can_unsplit = ($record['is_split'] == 1);

        
        $split_records = array();
        if ($record['is_split'] == 1 && $record['parent']) {
            $sql = "SELECT * FROM bs_orders_profit WHERE parent = " . intval($record['parent']) . " AND is_split = 1 ORDER BY split_sequence ASC";
            $result = $this->dbc->Query($sql);
            while ($row = $this->dbc->Fetch($result)) {
                $split_records[] = $this->rowToAssocArray($row);
            }
        }

        return array(
            'is_split' => ($record['is_split'] == 1),
            'can_split' => $can_split,
            'can_unsplit' => $can_unsplit,
            'split_records' => $split_records,
            'parent_order_id' => $record['parent'],
            'split_sequence' => $record['split_sequence']
        );
    }

    /**
     * ดึงข้อมูล split records ทั้งหมดของ parent
     */
    public function getSplitRecords($parent_order_id)
    {
        $sql = "SELECT * FROM bs_orders_profit WHERE parent = " . intval($parent_order_id) . " AND is_split = 1 ORDER BY split_sequence ASC";
        $result = $this->dbc->Query($sql);

        $records = array();
        while ($row = $this->dbc->Fetch($result)) {
            $records[] = $this->rowToAssocArray($row);
        }

        return $records;
    }
}
