<?php

$aContent = array(

  array(
    "supplier", //0
    "ยอดยกมา",
    "SELECT 
      (SELECT COALESCE(SUM(amount),0) FROM bs_purchase_spot WHERE date BETWEEN '2023-09-29' AND '2023-10-01' AND supplier_id ='#supplier_id#' AND status > 0  AND type != 'defer')
      -
      (SELECT COALESCE(-SUM(amount),0) FROM bs_suppliers_mapping WHERE id = '#supplier_id#' AND date = '2023-09-29')
     -
      (SELECT COALESCE(SUM(bs_incoming_plans.amount),0) FROM bs_incoming_plans LEFT OUTER JOIN bs_reserve_silver ON bs_incoming_plans.amount = bs_reserve_silver.weight_lock WHERE bs_incoming_plans.import_date BETWEEN '2023-09-29' AND '2023-10-01' AND bs_reserve_silver.supplier_id = '#supplier_id#' ) 
      +(SELECT COALESCE(SUM(CASE WHEN adj_supplier in (6,14,15,16,17,18,19) THEN amount = '0' ELSE amount END ),0) FROM bs_purchase_spot WHERE date BETWEEN  '2023-09-29' AND '#sql_date2#' AND adj_supplier ='#supplier_id#' AND parent IS NULL AND type='physical' )
      - (SELECT COALESCE(SUM(amount),0) FROM bs_transfers WHERE date BETWEEN  '2023-09-29' AND '#sql_date2#' AND supplier_id = '#supplier_id#')
      + (SELECT COALESCE(SUM(amount),0) FROM bs_adjust_purchase WHERE date BETWEEN  '2023-09-29' AND '#sql_date2#' AND supplier_id = '#supplier_id#')",
    "SELECT 
      (SELECT COALESCE(SUM((rate_spot)*amount*32.1507),0) FROM bs_purchase_spot WHERE date BETWEEN '2023-09-29' AND '2023-10-01' AND supplier_id ='#supplier_id#' AND status > 0  AND type != 'defer')
      -(SELECT COALESCE(-SUM(usd),0) FROM bs_suppliers_mapping WHERE id = '#supplier_id#' AND date = '2023-09-29')
       +(SELECT COALESCE(SUM(CASE WHEN adj_supplier in (6,14,15,16,17,18,19) THEN (rate_spot+rate_pmdc)*amount*32.1507 = '0' ELSE (rate_spot+rate_pmdc)*amount*32.1507  END ),0) FROM bs_purchase_spot WHERE  adj_supplier ='#supplier_id#' AND parent IS NULL AND type='physical' AND date BETWEEN  '2023-09-29' AND '#sql_date2#')
       - (SELECT COALESCE(SUM(value_usd_total),0) FROM bs_transfers WHERE date BETWEEN  '2023-09-29' AND '#sql_date2#'  AND supplier_id = '#supplier_id#')
       + (SELECT COALESCE(-SUM(value_adjust_trade),0) FROM bs_transfers WHERE date BETWEEN  '2023-09-29' AND '#sql_date2#' AND supplier_id ='#supplier_id#')
       +(SELECT COALESCE(SUM(value_adjust_type),0) FROM bs_adjust_defer  WHERE date BETWEEN  '2023-09-29' AND '#sql_date2#'  AND supplier_id = '#supplier_id#')
       +(SELECT COALESCE(SUM(usd),0) FROM bs_adjust_purchase  WHERE date BETWEEN  '2023-09-29' AND '#sql_date2#'  AND supplier_id = '#supplier_id#')"
  ),
  array( //1
    "supplier",
    "Purchase Adjust Cost",
    "SELECT COALESCE(SUM(amount),0) FROM bs_adjust_purchase WHERE date = '#sql_date#' AND supplier_id ='#supplier_id#'",
    "SELECT COALESCE(SUM(usd),0) FROM bs_adjust_purchase WHERE date = '#sql_date#' AND supplier_id ='#supplier_id#'"
  ),
  array(
    "supplier", //2
    "Purchase Physical",
    "SELECT COALESCE(SUM(CASE WHEN adj_supplier in (6,14,15,16,17,18,19) THEN amount = '0' ELSE amount END ),0) FROM bs_purchase_spot WHERE  adj_supplier ='#supplier_id#' AND parent IS NULL AND type='physical' AND date = '#sql_date#'",
    "SELECT COALESCE(SUM(CASE WHEN adj_supplier in (6,14,15,16,17,18,19) THEN (rate_spot+rate_pmdc)*amount*32.1507 = '0' ELSE (rate_spot+rate_pmdc)*amount*32.1507 END ),0) FROM bs_purchase_spot WHERE  adj_supplier ='#supplier_id#' AND parent IS NULL AND type='physical' AND date = '#sql_date#'"
  ),
  array( //3
    "supplier",
    "Take Shipment (หัก)",
    "SELECT FORMAT(-SUM(amount),4) FROM bs_transfers WHERE date = '#sql_date#' AND supplier_id ='#supplier_id#'",
    "SELECT (SELECT ROUND(-SUM(value_usd_total),4) FROM bs_transfers WHERE date = '#sql_date#' AND supplier_id ='#supplier_id#')
      + (SELECT ROUND(-SUM(value_adjust_trade),4) FROM bs_transfers WHERE date = '#sql_date#' AND supplier_id ='#supplier_id#')"
  ),
  array(
    "supplier",
    "Fund in - ปิด Defer", //4
    "SELECT '-'",
    "SELECT (SELECT SUM(value_adjust_type) FROM bs_adjust_defer WHERE date = '#sql_date#' AND supplier_id ='#supplier_id#')"
  ),

  "Total Purchase Available", // 5
  array( // 6
    "total",
    "แท่งเงินเข้าเซฟ (ยังไม่ได้หลอม)",
    "SELECT  COALESCE(SUM(JSON_EXTRACT(data, '$.bar_in_safe')),0) FROM bs_match_data WHERE date = '#sql_date#' ",
    "SELECT 'x'"
  ),
  array( //7
    "total",
    "เม็ดเงินที่หลอมวันนี้",
    "SELECT COALESCE(SUM(weight_out_packing),0) FROM bs_productions WHERE product_id = '1' AND submited = '#sql_date#' ",
    "SELECT 'x'",
  ),
  array( //8
    "total",
    "แท่งเงินที่หลอมวันนี้ (จ้างหลอม)",
    "SELECT COALESCE(SUM(weight_out_packing),0) FROM bs_productions WHERE product_id = '3' AND submited = '#sql_date#' ",
    "SELECT 'x'"
  ),
  array( //9
    "total",
    "Silver Stock (Recycle)",
    "SELECT (SELECT COALESCE(SUM(weight_out_packing),0) FROM bs_productions WHERE product_id = '4' AND submited BETWEEN '2022-05-04' AND '#sql_date2#')
      -
      (SELECT COALESCE(SUM(amount),0) FROM bs_orders WHERE product_id = '4' AND status > 0 AND delivery_date BETWEEN '2022-05-04' AND '#sql_date2#')
      -
      (SELECT COALESCE(SUM(amount),0) FROM bs_orders WHERE product_id = '4' AND status > 0 AND delivery_date = '#sql_date#')",
    "SELECT 'x'"
  ),
  array( //10
    "total",
    "Silver Stock (NON LBMA)",
    "SELECT (SELECT COALESCE(SUM(weight_out_packing),0)+1204 FROM bs_productions WHERE product_id = '1' AND submited BETWEEN '2022-05-04' AND '#sql_date2#')
      -
      (SELECT COALESCE(SUM(amount),0) FROM bs_orders WHERE product_id = '1' AND status > 0 AND delivery_date BETWEEN '2022-05-04' AND '#sql_date2#')
      -
      (SELECT COALESCE(SUM(amount),0) FROM bs_stock_adjusted LEFT JOIN bs_stock_adjuest_types ON bs_stock_adjusted.type_id = bs_stock_adjuest_types.id WHERE bs_stock_adjuest_types.type = 1 AND bs_stock_adjusted.product_id = 1 AND date BETWEEN '2022-03-29' AND '#sql_date2#')
      +
      (SELECT COALESCE(SUM(amount),0) FROM bs_stock_adjusted LEFT JOIN bs_stock_adjuest_types ON bs_stock_adjusted.type_id = bs_stock_adjuest_types.id WHERE bs_stock_adjuest_types.type = 2 AND bs_stock_adjusted.product_id = 1 AND bs_stock_adjusted.type_id = 3 AND date BETWEEN '2022-03-29' AND '#sql_date2#')
      -
      (SELECT COALESCE(SUM(amount),0) FROM bs_orders WHERE product_id = '1' AND status > 0 AND delivery_date = '#sql_date#')",
    "SELECT 'x'"
  ),
  array( //11
    "total",
    "Silver Stock (แท่ง)",
    "SELECT (SELECT COALESCE(SUM(weight_out_packing),0)+53 FROM bs_productions WHERE product_id = '2' AND submited BETWEEN '2022-05-04' AND '#sql_date2#')
      -
      (SELECT COALESCE(SUM(amount),0) FROM bs_orders WHERE product_id = '2' AND status > 0 AND delivery_date BETWEEN '2022-05-04' AND '#sql_date2#')
      +
      (SELECT COALESCE(SUM(weight_expected),0) FROM bs_stock_silver WHERE product_id = '2' AND submited BETWEEN '2022-05-04' AND '#sql_date2#')
      -
      (SELECT COALESCE(SUM(amount),0) FROM bs_orders WHERE product_id = '2' AND status > 0 AND delivery_date = '#sql_date#')",
    "SELECT 'x'"
  ),
  array( //12
    "total",
    "Silver Stock (LBMA)",
    "SELECT (SELECT COALESCE(SUM(weight_out_packing),0)+206.7689 FROM bs_productions WHERE product_id = '3' AND submited BETWEEN '2022-05-04' AND '#sql_date2#')
      -
      (SELECT COALESCE(SUM(amount),0) FROM bs_orders WHERE product_id = '3' AND status > 0 AND delivery_date BETWEEN '2022-05-04' AND '#sql_date2#')
      -
      (SELECT COALESCE(SUM(weight_out_packing),0) FROM bs_productions_pmr WHERE product_id = '3' AND submited BETWEEN '2022-05-04' AND '#sql_date2#')
      -
      (SELECT COALESCE(SUM(amount),0) FROM bs_orders WHERE product_id = '3' AND status > 0 AND delivery_date = '#sql_date#')",
    "SELECT 'x'"
  ),
  array( //13
    "total",
    "เม็ดเสียรอการผลิต",
    "SELECT SUM(weight_expected) AS amount FROM bs_scrap_items WHERE bs_scrap_items.status > -1 AND pack_name = 'เม็ดเสียรอการผลิต' AND DATE(created) BETWEEN '2023-06-26' AND  '#sql_date#'",
    "SELECT 'x'"
  ),array( //14
    "total",
    "เม็ดเสียรอ Refine",
    "SELECT SUM(weight_expected) AS amount FROM bs_scrap_items WHERE bs_scrap_items.status > -1 AND pack_name = 'เม็ดเสียรอการ Refine' AND DATE(created) BETWEEN '2023-06-26' AND  '#sql_date#'",
    "SELECT 'x'"
  ),
  array( //15
    "supplier",
    "ของรอเข้า(จ่ายเงินแล้ว)",
    "SELECT COALESCE(SUM(amount),0) FROM bs_incoming_plans 
      LEFT OUTER JOIN bs_products_import ON bs_incoming_plans.remark = bs_products_import.id
      WHERE bs_incoming_plans.import_date = '#sql_date#' AND bs_products_import.status ='#supplier_id#'",
    "SELECT 'x'"
  ),
  "Adjust", // 16
  array( //17
    "total",
    "Trade Buy",
    "SELECT 2000+500+500+200+200+100+200+200+200+1000+200+200+100+200+200+200+200+200+200+200+100+100+100+100+200+200+200+200+200+200+200+100+200+900",
    "SELECT '-'",
    "BES - Profit trade - Total long"
  ),
  array( //18
    "total",
    "Trade Sell",
    "SELECT -500-1000-1000-1000-500-500-200-200-100-200-200-200-200-500-500-100-200-200-200-200-200-200-200-200-200-200-200-100-100-200-200-100-200",
    "SELECT '-'",
    "BES - Profit trade - Total short"
  ), array( //19
    "total",
    "Total Adjust",
    "SELECT (SELECT COALESCE(-SUM(amount),0) AS amount FROM bs_orders WHERE DATE(date) BETWEEN '2023-10-03' AND '#sql_date#' AND bs_orders.parent IS NULL AND bs_orders.status > -1)
      -(SELECT COALESCE(-SUM(amount),0) AS amount FROM bs_purchase_spot WHERE date BETWEEN '2023-10-03' AND '#sql_date#' AND  (bs_purchase_spot.type LIKE 'physical' OR bs_purchase_spot.type LIKE 'stock')AND rate_spot > 0 AND status > -1  AND confirm IS NOT NULL);
      ",
    "SELECT '-'",
    "BES - Profit trade - Amount balance"
  ),
  array( //20
    "total",
    "Physical Position",
    "SELECT REPLACE(JSON_EXTRACT(data, '$.physical'),'\"','') FROM bs_match_data WHERE date = '#sql_date#' ",
    "SELECT '-'",
    "Balance kilo From Overview"
  ),
  "Physical Position-Export", //21
  "Fixed Stock", //22
  "Pending Adjust Cost",  //23
  "Paid stock  (go to stock paid)",  //24
  "Buy stock (unpaid)", //25
  "Hedge (long) (paid) ยกมา", //26
  "Hedge (Long)", //27
  "Close Hedge (Long) (unpaid)", //28
  "Total Hedge (Long) (unpaid)", //29
  array(
    "supplier",
    "Hedge (short) (unpaid) ยกมา", //30
    "SELECT 
    (SELECT COALESCE(-SUM(amount),0) FROM bs_adjust_amount WHERE date BETWEEN '2023-10-31' AND '#sql_date2#' AND supplier_id ='#supplier_id#')
    +
    (SELECT COALESCE(-SUM(amount),0) FROM bs_purchase_spot WHERE date BETWEEN '2023-10-31' AND '#sql_date2#' AND adj_supplier ='#supplier_id#' AND parent IS NULL AND type='physical' AND ref LIKE '%adj%')
    ",
    "SELECT(SELECT COALESCE(-SUM(amount*(rate_spot)*32.1507),0) FROM bs_purchase_spot WHERE date BETWEEN '2023-10-31' AND '#sql_date2#' AND adj_supplier ='#supplier_id#' AND parent IS NULL AND type='physical' AND ref LIKE '%adj%')
    +(SELECT COALESCE(-SUM(usd),0) FROM bs_adjust_amount WHERE date BETWEEN '2023-10-31' AND '#sql_date2#' AND supplier_id ='#supplier_id#')"
  ),
  array(
    "supplier",
    "Sales Adj STD", //31
    "SELECT (SELECT COALESCE(SUM(amount),0) FROM bs_adjust_amount WHERE date = '#sql_date#' AND supplier_id ='#supplier_id#')",
    "SELECT (SELECT COALESCE(SUM(usd),0) FROM bs_adjust_amount WHERE date = '#sql_date#' AND supplier_id ='#supplier_id#')"
  ),
  array(
    "supplier",
    "Hedge (short) (unpaid)", //32
    "SELECT COALESCE(SUM(amount),0) FROM bs_purchase_spot WHERE date = '#sql_date#' AND adj_supplier ='#supplier_id#' AND parent IS NULL AND type='physical' AND ref LIKE '%adj%'",
    "SELECT COALESCE(SUM(amount*(rate_spot)*32.1507),0) FROM bs_purchase_spot WHERE date = '#sql_date#' AND adj_supplier ='#supplier_id#' AND parent IS NULL AND type='physical' AND ref LIKE '%adj%' "
  ),

  array(
    "supplier",
    "Close Hedge (Short) (unpaid)", //33
    "SELECT 
    (SELECT COALESCE(-SUM(amount),0) FROM bs_purchase_spot WHERE date BETWEEN '2023-10-31' AND '#sql_date2#' AND adj_supplier ='#supplier_id#' AND parent IS NULL AND type='physical' AND ref LIKE '%adj%')
   +(SELECT COALESCE(-SUM(amount),0) FROM bs_adjust_amount WHERE date BETWEEN '2023-10-31' AND '#sql_date2#' AND supplier_id ='#supplier_id#')",
    "SELECT (SELECT COALESCE(-SUM(usd),0) FROM bs_adjust_amount WHERE date BETWEEN '2023-10-31' AND '#sql_date#' AND supplier_id ='#supplier_id#')
    +(SELECT COALESCE(-SUM(amount*(rate_spot)*32.1507),0) FROM bs_purchase_spot WHERE date BETWEEN '2023-10-31' AND '#sql_date#' AND adj_supplier ='#supplier_id#' AND parent IS NULL AND type='physical' AND ref LIKE '%adj%')
    "
  ),
  "Total Hedge (Short) (unpaid)", //34
  "Total Stock (Unpaid)", //35
  "Stock (paid) ยกมา", //36
  "Stock to adjust", //37
  "Stock to physical", //38
  "Buy stock (paid)", //39
  "Hedge (Long) (paid) ยกมา", //40
  "Hedge (Long)", //41
  "Close Hedge (Long)", //42
  "Total Hedge (Long) (paid)", //43
  "Hedge (short) (paid) ยกมา", //44
  "Hedge (short) (paid)", //45
  "Close Hedge (Short) (paid)", //46
  "Total Hedge (Short) (paid)", //47
  "Total Stock (Paid)", //48

  array(
    "total", //49
    "Total Silver on hand",
    "SELECT 
    (SELECT COALESCE(SUM(amount),0) FROM bs_purchase_spot WHERE date BETWEEN '2023-09-29' AND '2023-10-01' AND status > 0  AND type != 'defer')
    -
    (SELECT COALESCE(-SUM(amount),0) FROM bs_suppliers_mapping WHERE id = '1' AND date = '2023-09-29')
   -
    (SELECT COALESCE(SUM(bs_incoming_plans.amount),0) FROM bs_incoming_plans LEFT OUTER JOIN bs_reserve_silver ON bs_incoming_plans.amount = bs_reserve_silver.weight_lock WHERE bs_incoming_plans.import_date BETWEEN '2023-09-29' AND '2023-10-01') 
    +
    (SELECT COALESCE(SUM(amount),0) FROM bs_purchase_spot WHERE  
      CASE WHEN adj_supplier = '14' THEN date = '#sql_date#'
      ELSE date BETWEEN  '2023-09-29' AND '#sql_date#' END AND parent IS NULL AND type='physical')
    - 
    (SELECT COALESCE(SUM(amount),0) FROM bs_transfers WHERE date BETWEEN  '2023-09-29' AND '#sql_date#')
    + 
    (SELECT  COALESCE(SUM(JSON_EXTRACT(data, '$.bar_in_safe')),0) FROM bs_match_data WHERE date = '#sql_date#')
    + 
    (SELECT COALESCE(SUM(weight_out_packing),0) FROM bs_productions WHERE product_id = '1' AND submited = '#sql_date#')
    +
    (SELECT COALESCE(SUM(weight_out_packing),0) FROM bs_productions WHERE product_id = '3' AND submited = '#sql_date#')
    +
    (SELECT (SELECT COALESCE(SUM(weight_out_packing),0) FROM bs_productions WHERE product_id = '4' AND submited BETWEEN '2022-05-04' AND '#sql_date2#')
      -
      (SELECT COALESCE(SUM(amount),0) FROM bs_orders WHERE product_id = '4' AND status > 0 AND delivery_date BETWEEN '2022-05-04' AND '#sql_date2#')
      -
      (SELECT COALESCE(SUM(amount),0) FROM bs_orders WHERE product_id = '4' AND status > 0 AND delivery_date = '#sql_date#'))
    + 
    (SELECT (SELECT COALESCE(SUM(weight_out_packing),0)+1204 FROM bs_productions WHERE product_id = '1' AND submited BETWEEN '2022-05-04' AND '#sql_date2#')
      -
      (SELECT COALESCE(SUM(amount),0) FROM bs_orders WHERE product_id = '1' AND status > 0 AND delivery_date BETWEEN '2022-05-04' AND '#sql_date2#')
      -
      (SELECT COALESCE(SUM(amount),0) FROM bs_stock_adjusted LEFT JOIN bs_stock_adjuest_types ON bs_stock_adjusted.type_id = bs_stock_adjuest_types.id WHERE bs_stock_adjuest_types.type = 1 AND bs_stock_adjusted.product_id = 1 AND date BETWEEN '2022-03-29' AND '#sql_date2#')
      +
      (SELECT COALESCE(SUM(amount),0) FROM bs_stock_adjusted LEFT JOIN bs_stock_adjuest_types ON bs_stock_adjusted.type_id = bs_stock_adjuest_types.id WHERE bs_stock_adjuest_types.type = 2 AND bs_stock_adjusted.product_id = 1 AND bs_stock_adjusted.type_id = 3 AND date BETWEEN '2022-03-29' AND '#sql_date2#')
      -
      (SELECT COALESCE(SUM(amount),0) FROM bs_orders WHERE product_id = '1' AND status > 0 AND delivery_date = '#sql_date#'))
    +
    (SELECT (SELECT COALESCE(SUM(weight_out_packing),0)+53 FROM bs_productions WHERE product_id = '2' AND submited BETWEEN '2022-05-04' AND '#sql_date2#')
      -
      (SELECT COALESCE(SUM(amount),0) FROM bs_orders WHERE product_id = '2' AND status > 0 AND delivery_date BETWEEN '2022-05-04' AND '#sql_date2#')
      +
      (SELECT COALESCE(SUM(weight_expected),0) FROM bs_stock_silver WHERE product_id = '2' AND submited BETWEEN '2022-05-04' AND '#sql_date2#')
      -
      (SELECT COALESCE(SUM(amount),0) FROM bs_orders WHERE product_id = '2' AND status > 0 AND delivery_date = '#sql_date#'))
    +
    (SELECT (SELECT COALESCE(SUM(weight_out_packing),0)+206.7689 FROM bs_productions WHERE product_id = '3' AND submited BETWEEN '2022-05-04' AND '#sql_date2#')
      -
      (SELECT COALESCE(SUM(amount),0) FROM bs_orders WHERE product_id = '3' AND status > 0 AND delivery_date BETWEEN '2022-05-04' AND '#sql_date2#')
      -
      (SELECT COALESCE(SUM(weight_out_packing),0) FROM bs_productions_pmr WHERE product_id = '3' AND submited BETWEEN '2022-05-04' AND '#sql_date2#')
      -
      (SELECT COALESCE(SUM(amount),0) FROM bs_orders WHERE product_id = '3' AND status > 0 AND delivery_date = '#sql_date#'))
    +
    (SELECT SUM(weight_expected) AS amount FROM bs_scrap_items WHERE bs_scrap_items.status > -1 AND pack_name = 'เม็ดเสียรอการผลิต' AND DATE(created) BETWEEN '2023-06-26' AND  '#sql_date#')
    -
    (SELECT (SELECT COALESCE(-SUM(amount),0) AS amount FROM bs_orders WHERE DATE(date) BETWEEN '2023-10-03' AND '#sql_date#' AND bs_orders.parent IS NULL AND bs_orders.status > -1)
      -(SELECT COALESCE(-SUM(amount),0) AS amount FROM bs_purchase_spot WHERE date BETWEEN '2023-10-03' AND '#sql_date#' AND  (bs_purchase_spot.type LIKE 'physical' OR bs_purchase_spot.type LIKE 'stock')AND rate_spot > 0 AND status > -1  AND confirm IS NOT NULL))
    ",
    "SELECT '-'"
  ),
  "Physical Hedge", //50
  array(
    "total", //51
    "Customer Pending",
    "SELECT (SELECT SUM(bs_orders.amount) AS amount
      FROM bs_orders
      WHERE DATE(date) LIKE '#sql_date#' AND bs_orders.status > 0)
     - (SELECT SUM(bs_deliveries.amount) AS amount
      FROM bs_deliveries
      LEFT JOIN bs_orders ON bs_deliveries.id = bs_orders.delivery_id
      WHERE DATE(bs_deliveries.delivery_date) LIKE '#sql_date#' AND bs_orders.status > 0)
        + 
        (SELECT SUM(bs_orders.amount)+10482 AS amount FROM bs_orders WHERE DATE(date) < '#sql_date#' AND DATE(date) >= '2022-05-05' AND bs_orders.status > 0)
        - (SELECT
        SUM(bs_orders.amount) AS amount
      FROM bs_deliveries
      LEFT JOIN bs_orders ON bs_deliveries.id = bs_orders.delivery_id
      WHERE DATE(bs_deliveries.delivery_date) <  '#sql_date#' 
        AND DATE(bs_deliveries.delivery_date) >= '2022-05-05'
        AND bs_orders.status > 0)
      ",
    "SELECT '-'",
    "C/F จากเมื่อวานในหน้า Sales Overview"
  ),
  array(
    "total",
    "Total Silver Balance (Thailand)", //52
    "SELECT 
    (SELECT COALESCE(SUM(amount),0) FROM bs_purchase_spot WHERE date BETWEEN '2023-09-29' AND '2023-10-01' AND status > 0  AND type != 'defer')
    -
    (SELECT COALESCE(-SUM(amount),0) FROM bs_suppliers_mapping WHERE id = '1' AND date = '2023-09-29')
   -
    (SELECT COALESCE(SUM(bs_incoming_plans.amount),0) FROM bs_incoming_plans LEFT OUTER JOIN bs_reserve_silver ON bs_incoming_plans.amount = bs_reserve_silver.weight_lock WHERE bs_incoming_plans.import_date BETWEEN '2023-09-29' AND '2023-10-01') 
    +
    (SELECT COALESCE(SUM(amount),0) FROM bs_purchase_spot WHERE  
      CASE WHEN adj_supplier = '14' THEN date = '#sql_date#'
      ELSE date BETWEEN  '2023-09-29' AND '#sql_date#' END AND parent IS NULL AND type='physical')
    - 
    (SELECT COALESCE(SUM(amount),0) FROM bs_transfers WHERE date BETWEEN  '2023-09-29' AND '#sql_date#')
    + 
    (SELECT  COALESCE(SUM(JSON_EXTRACT(data, '$.bar_in_safe')),0) FROM bs_match_data WHERE date = '#sql_date#')
    + 
    (SELECT COALESCE(SUM(weight_out_packing),0) FROM bs_productions WHERE product_id = '1' AND submited = '#sql_date#')
    +
    (SELECT COALESCE(SUM(weight_out_packing),0) FROM bs_productions WHERE product_id = '3' AND submited = '#sql_date#')
    +
    (SELECT (SELECT COALESCE(SUM(weight_out_packing),0) FROM bs_productions WHERE product_id = '4' AND submited BETWEEN '2022-05-04' AND '#sql_date2#')
      -
      (SELECT COALESCE(SUM(amount),0) FROM bs_orders WHERE product_id = '4' AND status > 0 AND delivery_date BETWEEN '2022-05-04' AND '#sql_date2#')
      -
      (SELECT COALESCE(SUM(amount),0) FROM bs_orders WHERE product_id = '4' AND status > 0 AND delivery_date = '#sql_date#'))
    + 
    (SELECT (SELECT COALESCE(SUM(weight_out_packing),0)+1204 FROM bs_productions WHERE product_id = '1' AND submited BETWEEN '2022-05-04' AND '#sql_date2#')
      -
      (SELECT COALESCE(SUM(amount),0) FROM bs_orders WHERE product_id = '1' AND status > 0 AND delivery_date BETWEEN '2022-05-04' AND '#sql_date2#')
      -
      (SELECT COALESCE(SUM(amount),0) FROM bs_stock_adjusted LEFT JOIN bs_stock_adjuest_types ON bs_stock_adjusted.type_id = bs_stock_adjuest_types.id WHERE bs_stock_adjuest_types.type = 1 AND bs_stock_adjusted.product_id = 1 AND date BETWEEN '2022-03-29' AND '#sql_date2#')
      +
      (SELECT COALESCE(SUM(amount),0) FROM bs_stock_adjusted LEFT JOIN bs_stock_adjuest_types ON bs_stock_adjusted.type_id = bs_stock_adjuest_types.id WHERE bs_stock_adjuest_types.type = 2 AND bs_stock_adjusted.product_id = 1 AND bs_stock_adjusted.type_id = 3 AND date BETWEEN '2022-03-29' AND '#sql_date2#')
      -
      (SELECT COALESCE(SUM(amount),0) FROM bs_orders WHERE product_id = '1' AND status > 0 AND delivery_date = '#sql_date#'))
    +
    (SELECT (SELECT COALESCE(SUM(weight_out_packing),0)+53 FROM bs_productions WHERE product_id = '2' AND submited BETWEEN '2022-05-04' AND '#sql_date2#')
      -
      (SELECT COALESCE(SUM(amount),0) FROM bs_orders WHERE product_id = '2' AND status > 0 AND delivery_date BETWEEN '2022-05-04' AND '#sql_date2#')
      +
      (SELECT COALESCE(SUM(weight_expected),0) FROM bs_stock_silver WHERE product_id = '2' AND submited BETWEEN '2022-05-04' AND '#sql_date2#')
      -
      (SELECT COALESCE(SUM(amount),0) FROM bs_orders WHERE product_id = '2' AND status > 0 AND delivery_date = '#sql_date#'))
    +
    (SELECT (SELECT COALESCE(SUM(weight_out_packing),0)+206.7689 FROM bs_productions WHERE product_id = '3' AND submited BETWEEN '2022-05-04' AND '#sql_date2#')
      -
      (SELECT COALESCE(SUM(amount),0) FROM bs_orders WHERE product_id = '3' AND status > 0 AND delivery_date BETWEEN '2022-05-04' AND '#sql_date2#')
      -
      (SELECT COALESCE(SUM(weight_out_packing),0) FROM bs_productions_pmr WHERE product_id = '3' AND submited BETWEEN '2022-05-04' AND '#sql_date2#')
      -
      (SELECT COALESCE(SUM(amount),0) FROM bs_orders WHERE product_id = '3' AND status > 0 AND delivery_date = '#sql_date#'))
    +
    (SELECT SUM(weight_expected) AS amount FROM bs_scrap_items WHERE bs_scrap_items.status > -1 AND pack_name = 'เม็ดเสียรอการผลิต' AND DATE(created) BETWEEN '2023-06-26' AND  '#sql_date#')
    -
    (SELECT (SELECT COALESCE(-SUM(amount),0) AS amount FROM bs_orders WHERE DATE(date) BETWEEN '2023-10-03' AND '#sql_date#' AND bs_orders.parent IS NULL AND bs_orders.status > -1)
    -
    (SELECT COALESCE(-SUM(amount),0) AS amount FROM bs_purchase_spot WHERE date BETWEEN '2023-10-03' AND '#sql_date#' AND  (bs_purchase_spot.type LIKE 'physical' OR bs_purchase_spot.type LIKE 'stock')AND rate_spot > 0 AND status > -1  AND confirm IS NOT NULL))
    -
    (SELECT (SELECT SUM(bs_orders.amount) AS amount
      FROM bs_orders
      WHERE DATE(date) LIKE '#sql_date#' AND bs_orders.status > 0)
     - (SELECT SUM(bs_deliveries.amount) AS amount
      FROM bs_deliveries
      LEFT JOIN bs_orders ON bs_deliveries.id = bs_orders.delivery_id
      WHERE DATE(bs_deliveries.delivery_date) LIKE '#sql_date#' AND bs_orders.status > 0)
        + 
        (SELECT SUM(bs_orders.amount)+10482 AS amount FROM bs_orders WHERE DATE(date) < '#sql_date#' AND DATE(date) >= '2022-05-05' AND bs_orders.status > 0)
        - (SELECT
        SUM(bs_orders.amount) AS amount
      FROM bs_deliveries
      LEFT JOIN bs_orders ON bs_deliveries.id = bs_orders.delivery_id
      WHERE DATE(bs_deliveries.delivery_date) <  '#sql_date#' 
        AND DATE(bs_deliveries.delivery_date) >= '2022-05-05'
        AND bs_orders.status > 0))  
      ",
    "SELECT '-'"
  )
);
