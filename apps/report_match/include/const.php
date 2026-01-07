<?php

//Query By Pattaragun Junthhomkai

$aContent = array(

  array(
    "supplier", //0
    "ยอดยกมา",
    "SELECT +
      (SELECT COALESCE(SUM(amount),0) FROM bs_purchase_spot WHERE date BETWEEN '2025-01-01' AND '2025-01-01' AND supplier_id ='#supplier_id#' AND status > 0  AND type != 'defer')
      -
      (SELECT COALESCE(-SUM(amount),0) FROM bs_suppliers_mapping WHERE id = '#supplier_id#' AND date = '2025-01-01' )
     -
      (SELECT COALESCE(SUM(bs_incoming_plans.amount),0) FROM bs_incoming_plans LEFT OUTER JOIN bs_reserve_silver ON bs_incoming_plans.amount = bs_reserve_silver.weight_lock WHERE bs_incoming_plans.import_date BETWEEN '2025-01-01' AND '2025-01-01' AND bs_reserve_silver.supplier_id = '#supplier_id#' ) 
      +(SELECT COALESCE(SUM(CASE WHEN adj_supplier in (16,17,18,20,22,23,24,25,26) THEN amount = '0' ELSE amount END ),0) FROM bs_purchase_spot WHERE date BETWEEN  '2025-01-01' AND '#sql_date2#' AND adj_supplier ='#supplier_id#' AND noted ='Normal' AND parent IS NULL AND type='physical' )
      + (SELECT COALESCE(SUM(CASE WHEN adj_supplier in (1,6,14,19,21,11) THEN amount = '0' ELSE amount END ),0)FROM bs_purchase_spot WHERE  adj_supplier ='#supplier_id#' AND parent IS NULL AND (type='physical-adjust') AND noted ='Open-Adjust' AND date BETWEEN  '2025-01-01' AND '#sql_date2#')
      - (SELECT COALESCE(SUM(amount),0) FROM bs_incoming_plans WHERE import_date BETWEEN  '2025-01-01' AND '#sql_date2#' AND supplier_id = '#supplier_id#')
      - (SELECT COALESCE(SUM(weight_expected),0) FROM bs_stock_silver WHERE submited BETWEEN  '2025-01-01' AND '#sql_date2#' AND supplier_id = '#supplier_id#')
      ",
    "SELECT 
      (SELECT COALESCE(SUM((rate_spot)*amount*32.1507),0) FROM bs_purchase_spot WHERE date BETWEEN '2025-01-01' AND '2025-01-01' AND supplier_id ='#supplier_id#' AND status > 0  AND type != 'defer')  
    -
      (SELECT COALESCE(-SUM(usd),0) FROM bs_suppliers_mapping WHERE id = '#supplier_id#' AND date = '2025-01-01')
       +(SELECT COALESCE(SUM(CASE WHEN adj_supplier in (14,16,17,18,19,20,21,11,22,23,24,25,26) THEN amount = '0' ELSE (rate_spot+rate_pmdc)*amount*32.1507  END ),0) FROM bs_purchase_spot WHERE  adj_supplier ='#supplier_id#' AND noted ='Normal' AND parent IS NULL AND type='physical' AND date BETWEEN  '2025-01-01' AND '#sql_date2#')
       +(SELECT COALESCE(SUM(CASE WHEN adj_supplier in (1,6,14,19,21,11) THEN amount = '0' ELSE (rate_spot+rate_pmdc)*amount*32.1507  END ),0) FROM bs_purchase_spot WHERE  adj_supplier ='#supplier_id#' AND currency ='USD' AND parent IS NULL AND (type='physical-adjust') AND noted ='Open-Adjust' AND date BETWEEN  '2025-01-01' AND '#sql_date2#')
       +(SELECT COALESCE(-SUM(CASE WHEN supplier_id in (14,19,21,11) THEN value_usd_total = '0' ELSE value_usd_total END ),0) FROM bs_transfers WHERE date BETWEEN  '2025-01-01' AND '#sql_date2#'  AND supplier_id = '#supplier_id#')
       +(SELECT COALESCE(-SUM(CASE WHEN supplier_id in (14,19,21,11) THEN value_adjust_trade = '0' ELSE value_adjust_trade END ),0) FROM bs_transfers WHERE date BETWEEN  '2025-01-01' AND '#sql_date2#' AND supplier_id ='#supplier_id#')
        +(SELECT COALESCE(SUM(usd),0) FROM bs_match_deposit  WHERE date BETWEEN  '2025-01-01' AND '#sql_date#'  AND supplier_id = '#supplier_id#')
        +(SELECT COALESCE(SUM(usd),0) FROM bs_match_stx  WHERE date BETWEEN  '2025-01-01' AND '#sql_date#'  AND supplier_id = '#supplier_id#')
       +(SELECT COALESCE(SUM(CASE WHEN supplier_id in (14,16,17,18,19,20,21,11,22,23,24,25,26) THEN defer = '0' ELSE defer END ),0) FROM bs_defer_cost  WHERE date_defer BETWEEN  '2025-01-01' AND '#sql_date2#'  AND supplier_id = '#supplier_id#')
       -(SELECT COALESCE(-SUM(usd),0) FROM bs_match WHERE date BETWEEN  '2025-01-01' AND '#sql_date2#' AND supplier_id ='#supplier_id#')
       "
  ),

  array(
    "supplier", //1
    "Purchase Physical",
    "SELECT (SELECT COALESCE(SUM(CASE WHEN adj_supplier in (16,17,18,20,22,23,24,25,26) THEN amount = '0' ELSE amount END ),0) FROM bs_purchase_spot WHERE  adj_supplier ='#supplier_id#' AND parent IS NULL AND type='physical' AND noted ='Normal' AND date = '#sql_date#')
    + (SELECT COALESCE(SUM(CASE WHEN adj_supplier in (1,6,14,19,21,11) THEN amount = '0' ELSE amount END ),0)FROM bs_purchase_spot WHERE  adj_supplier ='#supplier_id#' AND parent IS NULL AND (type='physical-adjust') AND noted ='Open-Adjust' AND date = '#sql_date#')",
    "SELECT (SELECT COALESCE(SUM(CASE WHEN adj_supplier in (14,16,17,18,19,20,21,11,22,23,24,25,26) THEN (rate_spot+rate_pmdc)*amount*32.1507 = '0' ELSE (rate_spot+rate_pmdc)*amount*32.1507 END ),0) FROM bs_purchase_spot WHERE  adj_supplier ='#supplier_id#' AND currency ='USD' AND noted ='Normal' AND parent IS NULL AND type='physical' AND date = '#sql_date#')
  + (SELECT COALESCE(SUM(CASE WHEN adj_supplier in (1,6,14,19,21,11) THEN amount = '0' ELSE (rate_spot+rate_pmdc)*amount*32.1507  END ),0) FROM bs_purchase_spot WHERE  adj_supplier ='#supplier_id#' AND currency ='USD' AND parent IS NULL AND (type='physical-adjust') AND noted ='Open-Adjust' AND date = '#sql_date#')
"
  ),
  array( //2
    "supplier",
    "Take Shipment (หัก)",
    "SELECT (SELECT COALESCE(-SUM(amount),0) FROM bs_incoming_plans WHERE 	import_date = '#sql_date#' AND supplier_id ='#supplier_id#')
    +(SELECT COALESCE(-SUM(weight_expected),0) FROM  bs_stock_silver WHERE submited = '#sql_date#' AND supplier_id ='#supplier_id#')",
    "SELECT (SELECT COALESCE(-SUM(value_usd_total),0) FROM bs_transfers WHERE date = '#sql_date#' AND supplier_id ='#supplier_id#')
      + (SELECT COALESCE(-SUM(value_adjust_trade),0) FROM bs_transfers WHERE date = '#sql_date#' AND supplier_id ='#supplier_id#')",

  ),
  "Total Purchase", // 3
  array(
    "supplier",
    "ปิด Defer", //4
    "SELECT '-'",
    "SELECT (SELECT SUM(defer) FROM bs_defer_cost WHERE date_defer = '#sql_date#' AND supplier_id ='#supplier_id#')"
  ),
  array(
    "supplier",
    "ปิด Adjust", //5
    "SELECT '-'",
    "SELECT (SELECT COALESCE(SUM(value_profit),0) FROM bs_adjust_cost  WHERE date_adjust = '#sql_date#' AND supplier_id ='#supplier_id#')"
  ),
  array(
    "supplier",
    "Adjust cost (Buy) ยกมา", //6
    "SELECT '-'",
    "SELECT '-'"
  ),
  array(
    "supplier",
    "Open Adjust cost (Buy)", //7
    "SELECT '-'",
    "SELECT '-'"
  ),
  array(
    "supplier",
    "Close Adjust cost (Buy)", //8
    "SELECT '-'",
    "SELECT '-'"
  ),
  array(
    "supplier",
    "Adjust cost remaining (Buy)", //9
    "SELECT '-'",
    "SELECT '-'"
  ),
  array(
    "supplier",
    "Adjust cost (Sell) ยกมา", //10
    "SELECT 
    (SELECT COALESCE(-SUM(amount),0) FROM bs_sales_spot WHERE  supplier_id ='#supplier_id#' AND type='physical' AND status = 1 AND value_date BETWEEN '2024-01-10' AND '#sql_date2#' )
    +(SELECT COALESCE(SUM(amount),0) FROM bs_purchase_spot WHERE  supplier_id ='#supplier_id#' AND parent IS NULL AND type='physical' AND noted ='Close-Adjust' AND date BETWEEN '2024-01-10' AND '#sql_date2#')",
    "SELECT 
    (SELECT COALESCE(-SUM((rate_spot)*amount*32.1507),0) FROM bs_sales_spot WHERE  supplier_id ='#supplier_id#' AND type='physical' AND status = 1 AND value_date BETWEEN '2024-01-10' AND '#sql_date2#' )
    +(SELECT COALESCE(SUM((rate_spot)*amount*32.1507),0) FROM bs_purchase_spot WHERE  supplier_id ='#supplier_id#' AND parent IS NULL AND type='physical' AND noted ='Close-Adjust' AND date BETWEEN '2024-01-10' AND '#sql_date2#')
    +(SELECT COALESCE(SUM(value_profit),0) FROM bs_adjust_cost  WHERE date_adjust  BETWEEN '2024-01-10' AND '#sql_date2#' AND supplier_id ='#supplier_id#')"
  ),
  array(
    "supplier",
    "Open Adjust cost (Sell)", //11
    "SELECT (SELECT COALESCE(-SUM(amount),0) FROM bs_sales_spot WHERE  supplier_id ='#supplier_id#' AND type='physical' AND status = 1 AND value_date = '#sql_date#')",
    "SELECT (SELECT COALESCE(-SUM((rate_spot)*amount*32.1507),0) FROM bs_sales_spot WHERE  supplier_id ='#supplier_id#' AND type='physical' AND status = 1 AND value_date = '#sql_date#')"
  ),
  array(
    "supplier",
    "Close Adjust cost (Sell)", //12
    "SELECT (SELECT COALESCE(SUM(amount),0) FROM bs_purchase_spot WHERE  supplier_id ='#supplier_id#' AND parent IS NULL AND type='physical' AND noted ='Close-Adjust' AND date = '#sql_date#')",
    "SELECT (SELECT COALESCE(SUM((rate_spot)*amount*32.1507),0) FROM bs_purchase_spot WHERE  supplier_id ='#supplier_id#' AND parent IS NULL  AND noted ='Close-Adjust' AND type='physical'  AND date = '#sql_date#')"
  ),
  array(
    "supplier",
    "Adjust cost remaining (Sell)", //13
    "SELECT 
    (SELECT COALESCE(-SUM(amount),0) FROM bs_sales_spot WHERE  supplier_id ='#supplier_id#' AND type='physical' AND status = 1 AND value_date BETWEEN '2024-01-10' AND '#sql_date2#' )
    +(SELECT COALESCE(SUM(amount),0) FROM bs_purchase_spot WHERE  supplier_id ='#supplier_id#' AND parent IS NULL AND type='physical' AND noted ='Close-Adjust' AND date BETWEEN '2024-01-10' AND '#sql_date2#')
    +(SELECT COALESCE(-SUM(amount),0) FROM bs_sales_spot WHERE  supplier_id ='#supplier_id#' AND type='physical' AND status = 1 AND value_date = '#sql_date#')
    +(SELECT COALESCE(SUM(amount),0) FROM bs_purchase_spot WHERE  supplier_id ='#supplier_id#' AND parent IS NULL AND type='physical' AND noted ='Close-Adjust' AND date = '#sql_date#')",
    "SELECT 
    (SELECT COALESCE(-SUM((rate_spot)*amount*32.1507),0) FROM bs_sales_spot WHERE  supplier_id ='#supplier_id#' AND type='physical' AND status = 1 AND value_date BETWEEN '2024-01-10' AND '#sql_date#' )
    +(SELECT COALESCE(SUM((rate_spot)*amount*32.1507),0) FROM bs_purchase_spot WHERE  supplier_id ='#supplier_id#' AND parent IS NULL AND type='physical' AND noted ='Close-Adjust' AND date BETWEEN '2024-01-10' AND '#sql_date#')
    +(SELECT COALESCE(SUM(value_profit),0) FROM bs_adjust_cost  WHERE date_adjust  BETWEEN '2024-01-10' AND '#sql_date2#' AND supplier_id ='#supplier_id#')"
  ),
  "Total Purchase Available", // 14
  array(
    "total",
    "แท่งเงินเข้าเซฟ(ยังไม่ได้หลอม) SILVER", // 15
    "SELECT  COALESCE(SUM(amount),0) FROM bs_productions_silver_save  
      LEFT OUTER JOIN bs_productions ON  bs_productions.id=bs_productions_silver_save.round 
      WHERE  bs_productions.product_id = '1' AND bs_productions_silver_save.date = '#sql_date#' ",
    // "SELECT  COALESCE(SUM(JSON_EXTRACT(data, '$.bar_in_safe')),0) FROM bs_match_data WHERE date = '#sql_date#' ",
    "SELECT 'x'"
  ),
  array(
    "total",
    "แท่งเงินเข้าเซฟ(ยังไม่ได้หลอม) LBMA", // 16
    "SELECT  COALESCE(SUM(amount),0) FROM bs_productions_silver_save  
      LEFT OUTER JOIN bs_productions ON  bs_productions.id=bs_productions_silver_save.round 
      WHERE  bs_productions.product_id = '3' AND bs_productions_silver_save.date = '#sql_date#' ",
    // "SELECT  COALESCE(SUM(JSON_EXTRACT(data, '$.bar_in_safe_lbma')),0) FROM bs_match_data WHERE date = '#sql_date#' ",
    "SELECT 'x'"
  ),
  array(
    "total",
    "แท่งเงินเข้าเซฟ(ยังไม่ได้หลอม) RECYCLE", // 17
    "SELECT  COALESCE(SUM(amount),0) FROM bs_productions_silver_save  
      LEFT OUTER JOIN bs_productions ON  bs_productions.id=bs_productions_silver_save.round 
      WHERE  bs_productions.product_id = '4' AND bs_productions_silver_save.date = '#sql_date#' ",
    // "SELECT  COALESCE(SUM(JSON_EXTRACT(data, '$.bar_in_safe_recycle')),0) FROM bs_match_data WHERE date = '#sql_date#' ",
    "SELECT 'x'"
  ),
  array(
    "total",
    "แท่งเงินเข้าเซฟ(ยังไม่ได้หลอม) SILVER PLATE", // 18
    "SELECT  COALESCE(SUM(amount),0) FROM bs_productions_silver_save  
      LEFT OUTER JOIN bs_productions ON  bs_productions.id=bs_productions_silver_save.round 
      WHERE  bs_productions.product_id = '6' AND bs_productions_silver_save.date = '#sql_date#' ",
    // "SELECT  COALESCE(SUM(JSON_EXTRACT(data, '$.bar_in_safe')),0) FROM bs_match_data WHERE date = '#sql_date#' ",
    "SELECT 'x'"
  ),
  array(
    "total",
    "แท่งเงินเข้าเซฟ(ยังไม่ได้หลอม) SILVER ARTICLE", // 19
    "SELECT  COALESCE(SUM(amount),0) FROM bs_productions_silver_save  
      LEFT OUTER JOIN bs_productions ON  bs_productions.id=bs_productions_silver_save.round 
      WHERE  bs_productions.product_id = '8' AND bs_productions_silver_save.date = '#sql_date#' ",
    // "SELECT  COALESCE(SUM(JSON_EXTRACT(data, '$.bar_in_safe')),0) FROM bs_match_data WHERE date = '#sql_date#' ",
    "SELECT 'x'"
  ),
  array(
    "total",
    "แท่งเงินเข้าเซฟ(ยังไม่ได้หลอม) SILVER 999", // 20
    "SELECT  COALESCE(SUM(amount),0) FROM bs_productions_silver_save  
      LEFT OUTER JOIN bs_productions ON  bs_productions.id=bs_productions_silver_save.round 
      WHERE  bs_productions.product_id = '10' AND bs_productions_silver_save.date = '#sql_date#' ",
    // "SELECT  COALESCE(SUM(JSON_EXTRACT(data, '$.bar_in_safe')),0) FROM bs_match_data WHE RE date = '#sql_date#' ",
    "SELECT 'x'"
  ),
  array(
    "total",
    "จ้างหลอม (PMR - แท่งเงิน)", //21
    "SELECT (SELECT COALESCE(SUM(weight_out_packing),0) FROM bs_productions_pmr  WHERE product_id = '3' AND remark REGEXP '(ส่งเม็ดผลิต|ส่งเม็ด)' AND submited  BETWEEN '2025-01-03' AND '#sql_date#')
    - (SELECT COALESCE(SUM(weight_out_packing),0) FROM bs_productions WHERE product_id = '2' AND submited BETWEEN '2025-01-03' AND '#sql_date#') 
    ",
    "SELECT 'x'"
  ),
  array(
    "total",
    "เม็ดเงินที่หลอมวันนี้(จ้างหลอม) (PMR RECYCLE)", //22
    "SELECT (SELECT COALESCE(SUM(weight_out_total),0)
        FROM bs_productions_pmr
        WHERE product_id = '4'
          AND status = 1
          AND submited BETWEEN '2025-03-25' AND '#sql_date#' )
       - (SELECT COALESCE(SUM(bip.amount),0)
          FROM bs_productions bp
          LEFT OUTER JOIN bs_incoming_plans bip ON bip.import_lot COLLATE utf8mb3_unicode_ci = bp.round COLLATE utf8mb3_unicode_ci
          WHERE bp.product_id = '4'
            AND bp.submited BETWEEN '2025-03-24' AND '#sql_date#'
            AND bp.PMR = 'PMR')",


    "SELECT 'x'"
  ),
  array(
    "total",
    "เม็ดเงินที่หลอมวันนี้(จ้างหลอม)-Shining gold", //23
    "SELECT (SELECT COALESCE(SUM(weight_out_packing),0) FROM bs_productions_pmr  WHERE export_id = '1' AND submited BETWEEN '2022-05-04' AND '#sql_date#')
    -(SELECT COALESCE(SUM(weight_out_packing),0) FROM bs_productions WHERE product_id = '6' AND PMR = 'PMR' AND submited BETWEEN '2022-05-04' AND '#sql_date#') 
    -(SELECT COALESCE(SUM(amount),0) FROM bs_stock_adjusted WHERE product_id = '5' AND date BETWEEN '2024-05-01' AND '#sql_date#')-(1.3003)",
    "SELECT 'x'"
  ),

  array(
    "total",
    "Silver Stock (SILVER)", //24
    "SELECT (SELECT COALESCE(SUM(weight_out_packing),0)+1204 FROM bs_productions WHERE product_id = '1' AND submited BETWEEN '2022-05-04' AND '#sql_date#')
    +
    (SELECT COALESCE(SUM(weight_out_total),0) FROM bs_productions_in WHERE product_id = '1' AND submited BETWEEN '2022-05-04' AND '#sql_date#')
      -
      (SELECT COALESCE(SUM(amount),0) FROM bs_orders WHERE product_id = '1' AND status > 0 AND delivery_date BETWEEN '2022-05-04' AND '#sql_date2#')
      -
      (SELECT COALESCE(SUM(amount),0) FROM bs_stock_adjusted LEFT JOIN bs_stock_adjuest_types ON bs_stock_adjusted.type_id = bs_stock_adjuest_types.id WHERE bs_stock_adjuest_types.type = 1 AND bs_stock_adjusted.product_id = 1 AND date BETWEEN '2022-03-29' AND '#sql_date2#')
      +
      (SELECT COALESCE(SUM(amount),0) FROM bs_stock_adjusted LEFT JOIN bs_stock_adjuest_types ON bs_stock_adjusted.type_id = bs_stock_adjuest_types.id WHERE bs_stock_adjuest_types.type = 2 AND bs_stock_adjusted.product_id = 1 AND bs_stock_adjusted.type_id = 3 AND date BETWEEN '2022-03-29' AND '#sql_date2#')
      -
      (SELECT COALESCE(SUM(amount),0) FROM bs_stock_adjusted WHERE product_id = '1' AND date = '2023-12-22')
      -
      (SELECT COALESCE(SUM(weight_out_packing),0) FROM bs_productions_pmr WHERE product_id = '1' AND submited BETWEEN '2022-05-04' AND '#sql_date#')
      -
      (SELECT COALESCE(SUM(amount),0) FROM bs_orders WHERE product_id = '1' AND status > 0 AND delivery_date = '#sql_date#')",
    "SELECT 'x'"
  ),
  array(
    "total",
    "Silver Stock (LBMA)", //25
    "SELECT (SELECT COALESCE(SUM(weight_out_packing),0)+206.7689 FROM bs_productions WHERE product_id = '3' AND submited BETWEEN '2022-05-04' AND '#sql_date#')
        +
    (SELECT COALESCE(SUM(weight_out_total),0) FROM bs_productions_in WHERE product_id = '3' AND submited BETWEEN '2022-05-04' AND '#sql_date#')
      -
      (SELECT COALESCE(SUM(amount),0) FROM bs_orders WHERE product_id = '3' AND status > 0 AND delivery_date BETWEEN '2022-05-04' AND '#sql_date2#')
      -
      (SELECT COALESCE(SUM(weight_out_packing),0) FROM bs_productions_pmr WHERE product_id = '3' AND submited BETWEEN '2022-05-04' AND '#sql_date#')
      +
      (SELECT COALESCE(SUM(weight_out_packing),0) FROM bs_productions_pmr WHERE product_id = '3' AND submited = '2024-01-17')
      -
      (SELECT COALESCE(SUM(amount),0) FROM bs_orders WHERE product_id = '3' AND status > 0 AND delivery_date = '#sql_date#')",
    "SELECT 'x'"
  ),
  array(
    "total",
    "Silver Stock (Recycle)", //26
    "SELECT (SELECT COALESCE(SUM(weight_out_packing),0)+0.018 FROM bs_productions WHERE product_id = '4' AND submited BETWEEN '2023-02-27' AND '#sql_date#')
        +
    (SELECT COALESCE(SUM(weight_out_total),0) FROM bs_productions_in WHERE product_id = '4' AND submited BETWEEN '2022-05-04' AND '#sql_date#')
    -
      (SELECT COALESCE(SUM(amount),0) FROM bs_stock_adjusted LEFT JOIN bs_stock_adjuest_types ON bs_stock_adjusted.type_id = bs_stock_adjuest_types.id WHERE bs_stock_adjuest_types.type = 1 AND bs_stock_adjusted.product_id = 4 AND date BETWEEN '2022-03-29' AND '#sql_date2#')
      +
      (SELECT COALESCE(SUM(amount),0) FROM bs_stock_adjusted LEFT JOIN bs_stock_adjuest_types ON bs_stock_adjusted.type_id = bs_stock_adjuest_types.id WHERE bs_stock_adjuest_types.type = 2 AND bs_stock_adjusted.product_id = 4 AND bs_stock_adjusted.type_id = 9 AND date BETWEEN '2022-03-29' AND '#sql_date2#')
      
    -      
    (SELECT COALESCE(SUM(amount),0) FROM bs_orders WHERE product_id = '4' AND status > 0 AND delivery_date BETWEEN '2022-05-04' AND '#sql_date2#')
      -
      (SELECT COALESCE(SUM(weight_out_packing),0) FROM bs_productions_pmr WHERE product_id = '4' AND submited BETWEEN '2022-05-04' AND '#sql_date#')
      -
      (SELECT COALESCE(SUM(amount),0) FROM bs_orders WHERE product_id = '4' AND status > 0 AND delivery_date = '#sql_date#')",
    "SELECT 'x'"
  ),
  array(
    "total",
    "Silver Stock (Local Recycle)", //27
    "SELECT (SELECT COALESCE(SUM(weight_out_packing),0) FROM bs_productions WHERE product_id = '7' AND submited BETWEEN '2022-05-04' AND '#sql_date#')
        +
    (SELECT COALESCE(SUM(weight_out_total),0) FROM bs_productions_in WHERE product_id = '7' AND submited BETWEEN '2022-05-04' AND '#sql_date#')
      -
      (SELECT COALESCE(SUM(amount),0) FROM bs_orders WHERE product_id = '7' AND status > 0 AND delivery_date BETWEEN '2022-05-04' AND '#sql_date2#')
      +
      (SELECT COALESCE(SUM(weight_expected),0) FROM bs_stock_silver WHERE product_id = '7' AND submited BETWEEN '2022-05-04' AND '#sql_date2#')
      -
      (SELECT COALESCE(SUM(weight_out_packing),0) FROM bs_productions_pmr WHERE product_id = '7' AND submited BETWEEN '2022-05-04' AND '#sql_date#')
      -  
      (SELECT COALESCE(SUM(amount),0) FROM bs_orders WHERE product_id = '7' AND status > 0 AND delivery_date = '#sql_date#')",
    "SELECT 'x'"
  ),
  array(
    "total",
    "Silver Stock Thai (เม็ด)", //28
    "SELECT (SELECT COALESCE(SUM(weight_out_packing),0) FROM bs_productions WHERE product_id = '5' AND submited BETWEEN '2024-02-22' AND '#sql_date#')
        +
    (SELECT COALESCE(SUM(weight_out_total),0) FROM bs_productions_in WHERE product_id = '5' AND submited BETWEEN '2022-05-04' AND '#sql_date#')
      -
      (SELECT COALESCE(SUM(amount),0) FROM bs_orders WHERE product_id = '5' AND status > 0 AND delivery_date BETWEEN '2022-05-04' AND '#sql_date2#')
      + 
      (SELECT COALESCE(SUM(amount),0) FROM bs_stock_adjusted WHERE product_id = '5' AND date BETWEEN '2024-05-01' AND '#sql_date#')
      +
      (SELECT COALESCE(SUM(weight_expected),0) FROM bs_stock_silver WHERE product_id = '5' AND submited BETWEEN '2022-05-04' AND '#sql_date2#')
      -
      (SELECT COALESCE(SUM(weight_out_packing),0) FROM bs_productions_pmr WHERE product_id = '5' AND submited BETWEEN '2022-05-04' AND '#sql_date#')
      -
      (SELECT COALESCE(SUM(amount),0) FROM bs_orders WHERE product_id = '5' AND status > 0 AND delivery_date = '#sql_date#')
      +(1.3003)",
    "SELECT 'x'"
  ),
  array(
    "total",
    "Silver Stock (แท่ง)", //29
    "SELECT (SELECT COALESCE(SUM(weight_out_packing),0)+53 FROM bs_productions WHERE product_id = '2' AND submited BETWEEN '2022-05-04' AND '#sql_date#')
        +
    (SELECT COALESCE(SUM(weight_out_total),0) FROM bs_productions_in WHERE product_id = '2' AND submited BETWEEN '2022-05-04' AND '#sql_date#')
      -
      (SELECT COALESCE(SUM(amount),0) FROM bs_orders WHERE product_id = '2' AND status > 0 AND delivery_date BETWEEN '2022-05-04' AND '#sql_date2#')
      +
      (SELECT COALESCE(SUM(weight_expected),0) FROM bs_stock_silver WHERE product_id = '2' AND submited BETWEEN '2022-05-04' AND '#sql_date#')
      -
      (SELECT COALESCE(SUM(amount),0) FROM bs_orders WHERE product_id = '2' AND status > 0 AND delivery_date = '#sql_date#')",
    "SELECT 'x'"
  ),
  array(
    "total",
    "Silver Stock (SILVER PLATE)", //30
    "SELECT (SELECT COALESCE(SUM(weight_out_packing),0) FROM bs_productions WHERE product_id = '6' AND submited BETWEEN '2022-05-04' AND '#sql_date#')
        +
    (SELECT COALESCE(SUM(weight_out_total),0) FROM bs_productions_in WHERE product_id = '6' AND submited BETWEEN '2022-05-04' AND '#sql_date#')
      -
      (SELECT COALESCE(SUM(amount),0) FROM bs_orders WHERE product_id = '6' AND status > 0 AND delivery_date BETWEEN '2022-05-04' AND '#sql_date2#')
      +
      (SELECT COALESCE(SUM(weight_expected),0) FROM bs_stock_silver WHERE product_id = '6' AND submited BETWEEN '2022-05-04' AND '#sql_date2#')
      -
      (SELECT COALESCE(SUM(weight_out_packing),0) FROM bs_productions_pmr WHERE product_id = '6' AND submited BETWEEN '2022-05-04' AND '#sql_date#')
      -  
      (SELECT COALESCE(SUM(amount),0) FROM bs_orders WHERE product_id = '6' AND status > 0 AND delivery_date = '#sql_date#')",
    "SELECT 'x'"
  ),
  array(
    "total",
    "Silver Stock (SILVER ARTICLE)", //31
    "SELECT (SELECT COALESCE(SUM(weight_out_packing),0) FROM bs_productions WHERE product_id = '8' AND submited BETWEEN '2022-05-04' AND '#sql_date#')
        +
    (SELECT COALESCE(SUM(weight_out_total),0) FROM bs_productions_in WHERE product_id = '8' AND submited BETWEEN '2022-05-04' AND '#sql_date#')
      -
      (SELECT COALESCE(SUM(amount),0) FROM bs_orders WHERE product_id = '8' AND status > 0 AND delivery_date BETWEEN '2022-05-04' AND '#sql_date2#')
      +
      (SELECT COALESCE(SUM(weight_expected),0) FROM bs_stock_silver WHERE product_id = '8' AND submited BETWEEN '2022-05-04' AND '#sql_date2#')
      -
      (SELECT COALESCE(SUM(weight_out_packing),0) FROM bs_productions_pmr WHERE product_id = '8' AND submited BETWEEN '2022-05-04' AND '#sql_date#')
      -  
      (SELECT COALESCE(SUM(amount),0) FROM bs_orders WHERE product_id = '8' AND status > 0 AND delivery_date = '#sql_date#')",
    "SELECT 'x'"
  ),
  array(
    "total",
    "Silver Stock (SILVER 999)", //32
    "SELECT (SELECT COALESCE(SUM(weight_out_packing),0) FROM bs_productions WHERE product_id = '10' AND submited BETWEEN '2022-05-04' AND '#sql_date#')
        +
    (SELECT COALESCE(SUM(weight_out_total),0) FROM bs_productions_in WHERE product_id = '10' AND submited BETWEEN '2022-05-04' AND '#sql_date#')
      -
      (SELECT COALESCE(SUM(amount),0) FROM bs_orders WHERE product_id = '10' AND status > 0 AND delivery_date BETWEEN '2022-05-04' AND '#sql_date2#')
      +
      (SELECT COALESCE(SUM(weight_expected),0) FROM bs_stock_silver WHERE product_id = '10' AND submited BETWEEN '2022-05-04' AND '#sql_date2#')
      -
      (SELECT COALESCE(SUM(weight_out_packing),0) FROM bs_productions_pmr WHERE product_id = '10' AND submited BETWEEN '2022-05-04' AND '#sql_date#')
      -  
      (SELECT COALESCE(SUM(amount),0) FROM bs_orders WHERE product_id = '10' AND status > 0 AND delivery_date = '#sql_date#')",
    "SELECT 'x'"
  ),
  array(
    "total",
    "เม็ดเสียรอการผลิต + รอ Refine(Silver)", //33
    "SELECT  COALESCE(SUM(JSON_EXTRACT(data, '$.stock_grain')),0) FROM bs_match_data WHERE date = '#sql_date#'",
    "SELECT 'x'"
  ),
  array(
    "total",
    "เม็ดเสียรอการผลิต + รอ Refine(LBMA)", //34
    "SELECT  COALESCE(SUM(JSON_EXTRACT(data, '$.stock_pmr')),0) FROM bs_match_data WHERE date = '#sql_date#'",
    "SELECT 'x'"
  ),
  array(
    "total",
    "เม็ดเสียรอการผลิต + รอ Refine(Recycled)", //35
    "SELECT  COALESCE(SUM(JSON_EXTRACT(data, '$.stock_bar')),0) FROM bs_match_data WHERE date = '#sql_date#'",
    "SELECT 'x'"
  ),
  array(
    "total",
    "เม็ดเสียรอการผลิต + รอ Refine(Silver Plate)", //36
    "SELECT  COALESCE(SUM(JSON_EXTRACT(data, '$.customer_pending')),0) FROM bs_match_data WHERE date = '#sql_date#'",
    "SELECT 'x'"
  ),
  array(
    "total",
    "เม็ดเสียรอการผลิต + รอ Refine(SILVER ARTICLE GRAIN)", //37
    "SELECT  COALESCE(SUM(JSON_EXTRACT(data, '$.scrap_silverarticle_grain')),0) FROM bs_match_data WHERE date = '#sql_date#'",
    "SELECT 'x'"
  ),
  array(
    "total",
    "เม็ดเสียรอการผลิต + รอ Refine(SILVER ARTICLE)", //38
    "SELECT  COALESCE(SUM(JSON_EXTRACT(data, '$.scrap_silverarticle')),0) FROM bs_match_data WHERE date = '#sql_date#'",
    "SELECT 'x'"
  ),
  array(
    "total",
    "เม็ดเสียรอการผลิต + รอ Refine(SILVER 999)", //39
    "SELECT  COALESCE(SUM(JSON_EXTRACT(data, '$.scrap_silver999')),0) FROM bs_match_data WHERE date = '#sql_date#'",
    "SELECT 'x'"
  ),
  array(
    "total",
    "เม็ดเสียรอการผลิต + รอ Refine(SILVER BAR)", //40
    "SELECT  COALESCE(SUM(JSON_EXTRACT(data, '$.scrap_silverbar')),0) FROM bs_match_data WHERE date = '#sql_date#'",
    "SELECT 'x'"
  ),
  array(
    "total",
    "เม็ดเกิน", //41
    "SELECT (SELECT COALESCE(SUM(amount),0) FROM bs_stock_adjusted_over WHERE type_id = '1' AND date BETWEEN '2024-05-28' AND '#sql_date#')
    - (SELECT COALESCE(SUM(amount),0) FROM bs_stock_adjusted_over WHERE type_id = '2' AND date BETWEEN '2024-05-28' AND '#sql_date#')",
    // "SELECT  COALESCE(SUM(JSON_EXTRACT(data, '$.physical')),0) FROM bs_match_data WHERE date = '#sql_date#'",
    "SELECT 'x'"
  ),
  array(
    "total",
    "เศษรอRefine(เศษเบ้า+ผงดำ)", //42
    "SELECT  COALESCE(SUM(JSON_EXTRACT(data, '$.remain')),0) FROM bs_match_data WHERE date = '#sql_date#'",
    "SELECT 'x'"
  ),
  array(
    "total",
    "Trade Buy ICBC", //43
    "SELECT  (SELECT COALESCE(SUM(amount),0) FROM bs_purchase_spot WHERE date = '#sql_date#' AND supplier_id ='1' AND status > 0  AND type = 'trade')",
    "SELECT 'x'"
  ),
  array(
    "total",
    "Trade Sell ICBC", //44
    "SELECT  (SELECT COALESCE(SUM(amount),0) FROM bs_sales_spot WHERE date = '#sql_date#' AND supplier_id ='1' AND status > 0  AND type = 'trade')",
    "SELECT 'x'"
  ),
  array(
    "total",
    "Balance Position Trader ICBC", //45
    "SELECT  (SELECT COALESCE(SUM(amount),0) FROM bs_purchase_spot WHERE date BETWEEN '2024-07-01' AND '#sql_date#' AND supplier_id ='1' AND status > 0  AND type = 'trade')
    -(SELECT COALESCE(SUM(amount),0) FROM bs_sales_spot WHERE date BETWEEN '2024-07-01' AND '#sql_date#' AND supplier_id ='1' AND status > 0  AND type = 'trade')",
    "SELECT 'x'"
  ),
  array(
    "supplier",
    "ของรอเข้า(จ่ายเงินแล้ว)", //46
    "SELECT (SELECT COALESCE(SUM(amount),0) FROM bs_incoming_plans 
      LEFT OUTER JOIN bs_products_import ON bs_incoming_plans.remark = bs_products_import.id
      WHERE bs_incoming_plans.bank_date BETWEEN '2024-01-01' AND '#sql_date#' AND bs_products_import.status ='#supplier_id#')
    -(SELECT COALESCE(SUM(amount),0) FROM bs_incoming_plans 
      LEFT OUTER JOIN bs_products_import ON bs_incoming_plans.remark = bs_products_import.id
      WHERE bs_incoming_plans.import_date BETWEEN '2024-01-01' AND '#sql_date#' AND bs_incoming_plans.bank_date IS NOT NULL AND bs_products_import.status ='#supplier_id#')",
    "SELECT 'x'"
  ),
  "Adjust", // 47
  array(
    "total",
    "Silver Physical Position", //48
    "SELECT (SELECT COALESCE(-SUM(amount),0) AS amount FROM bs_orders WHERE DATE(date) BETWEEN '2023-10-03' AND '#sql_date#' AND bs_orders.parent IS NULL AND bs_orders.status > -1)
      -(SELECT COALESCE(-SUM(amount),0) AS amount FROM bs_purchase_spot WHERE date BETWEEN '2023-10-03' AND '#sql_date#' AND  (bs_purchase_spot.type LIKE 'physical' OR bs_purchase_spot.type LIKE 'stock')AND rate_spot > 0 AND status > -1  AND confirm IS NOT NULL);
      ",
    "SELECT '-'",
    "BES - Profit trade - Amount balance"
  ),
  array(
    "total",
    "Physical Position", //49
    "SELECT (SELECT COALESCE(-SUM(amount),0) AS amount FROM bs_orders WHERE DATE(date) BETWEEN '2023-10-03' AND '#sql_date#' AND bs_orders.parent IS NULL AND bs_orders.status > -1)
      -(SELECT COALESCE(-SUM(amount),0) AS amount FROM bs_purchase_spot WHERE date BETWEEN '2023-10-03' AND '#sql_date#' AND  (bs_purchase_spot.type LIKE 'physical' OR bs_purchase_spot.type LIKE 'stock')AND rate_spot > 0 AND status > -1  AND confirm IS NOT NULL)",
    "SELECT '-'",
    "Balance kilo From Overview"
  ),
  array(
    "total", //50
    "Total Silver on hand",
    "SELECT 
        + (
            -- Purchase spot amount for specific date and supplier
            (SELECT COALESCE(SUM(amount), 0) 
             FROM bs_purchase_spot 
             WHERE date BETWEEN '2024-01-01' AND '2024-01-01' 
               AND supplier_id = '1' 
               AND status > 0 
               AND type != 'defer')
            
            -- Subtract supplier mapping adjustment
            - (SELECT COALESCE(-SUM(amount), 0) 
               FROM bs_suppliers_mapping_1 
               WHERE id = '1' 
                 AND date = '2024-01-01')
            
            -- Subtract incoming plans with silver reserve
            - (SELECT COALESCE(SUM(bs_incoming_plans.amount), 0) 
               FROM bs_incoming_plans 
               LEFT OUTER JOIN bs_reserve_silver 
                 ON bs_incoming_plans.amount = bs_reserve_silver.weight_lock 
               WHERE bs_incoming_plans.import_date BETWEEN '2024-01-01' AND '2024-01-01' 
                 AND bs_reserve_silver.supplier_id IN (1))
            
            -- Physical purchase adjustments (specific suppliers)
            + (SELECT COALESCE(SUM(CASE 
                                    WHEN adj_supplier IN (16,17,18,20,22,23,24,25,26) THEN amount = '0' 
                                    ELSE amount 
                                  END), 0) 
               FROM bs_purchase_spot 
               WHERE date BETWEEN '2024-01-01' AND '#sql_date2#' 
                 AND adj_supplier IN (1,6,14,19,21,11) 
                 AND noted = 'Normal' 
                 AND parent IS NULL 
                 AND type = 'physical')
            
            -- Defer/physical-adjust calculations
            + (SELECT COALESCE(SUM(CASE 
                                    WHEN adj_supplier IN (1,6,14,19,21,11) THEN amount = '0' 
                                    ELSE amount 
                                  END), 0)
               FROM bs_purchase_spot 
               WHERE adj_supplier IN (16,17,18,20,22,23,24,25,26) 
                 AND parent IS NULL 
                 AND (type = 'defer' OR type = 'physical-adjust') 
                 AND noted = 'Open-Adjust' 
                 AND date BETWEEN '2024-01-01' AND '#sql_date2#')
            
            -- Subtract incoming plans range
            - (SELECT COALESCE(SUM(amount), 0) 
               FROM bs_incoming_plans 
               WHERE import_date BETWEEN '2024-01-01' AND '#sql_date2#' 
                 AND supplier_id IN (1,6,14,16,17,18,19,20,21,11,22,23,24,25,26))
            
            -- Subtract stock silver expected weight
            - (SELECT COALESCE(SUM(weight_expected), 0) 
               FROM bs_stock_silver 
               WHERE submited BETWEEN '2024-01-01' AND '#sql_date2#' 
                 AND supplier_id IN (1,6,14,16,17,18,19,20,21,11,22,23,24,25,26))
            
            -- Subtract physical adjustments
            - (SELECT COALESCE(SUM(amount), 0) 
               FROM bs_adjust_physical_adjust 
               WHERE date BETWEEN '2024-01-01' AND '#sql_date2#' 
                 AND supplier_id IN (1,6,14,16,17,18,19,20,21,11,22,23,24,25,26))
        )
        
        + (
            -- Daily physical purchase (specific suppliers)
            (SELECT COALESCE(SUM(CASE 
                                  WHEN adj_supplier IN (16,17,18,20,22,23,24,25,26) THEN amount = '0' 
                                  ELSE amount 
                                END), 0) 
             FROM bs_purchase_spot 
             WHERE adj_supplier IN (1,6,14,19,21,11) 
               AND parent IS NULL 
               AND type = 'physical' 
               AND noted = 'Normal' 
               AND date = '#sql_date#')
            
            -- Daily defer/physical-adjust
            + (SELECT COALESCE(SUM(CASE 
                                    WHEN adj_supplier IN (1,6,14,19,21,11) THEN amount = '0' 
                                    ELSE amount 
                                  END), 0)
               FROM bs_purchase_spot 
               WHERE adj_supplier IN (16,17,18,20,22,23,24,25,26) 
                 AND parent IS NULL 
                 AND (type = 'defer' OR type = 'physical-adjust') 
                 AND noted = 'Open-Adjust' 
                 AND date = '#sql_date#')
        )
        
        -- ========================================
        -- DAILY STOCK MOVEMENTS
        -- ========================================
        + (
            -- Daily incoming plans
            (SELECT COALESCE(-SUM(amount), 0) 
             FROM bs_incoming_plans 
             WHERE import_date = '#sql_date#' 
               AND supplier_id IN (1,6,14,16,17,18,19,20,21,11,22,23,24,25,26))
            
            -- Daily stock silver expected
            + (SELECT COALESCE(-SUM(weight_expected), 0) 
               FROM bs_stock_silver 
               WHERE submited = '#sql_date#' 
                 AND supplier_id IN (1,6,14,16,17,18,19,20,21,11,22,23,24,25,26))
        )
        
        -- SALES AND PURCHASES (RANGE & DAILY)
        + (
            -- Sales spot range (suppliers 1,6)
            (SELECT COALESCE(-SUM(amount), 0) 
             FROM bs_sales_spot 
             WHERE supplier_id IN (1,6) 
               AND type = 'physical' 
               AND status = 1 
               AND value_date BETWEEN '2024-01-01' AND '#sql_date2#')
            
            -- Purchase spot close-adjust range
            + (SELECT COALESCE(SUM(amount), 0) 
               FROM bs_purchase_spot 
               WHERE supplier_id IN (1,6) 
                 AND parent IS NULL 
                 AND type = 'physical' 
                 AND noted = 'Close-Adjust' 
                 AND date BETWEEN '2024-01-01' AND '#sql_date2#')
        )
        
        -- Daily sales and purchases
        + (
            -- Daily sales spot
            (SELECT COALESCE(-SUM(amount), 0) 
             FROM bs_sales_spot 
             WHERE supplier_id IN (1,6) 
               AND type = 'physical' 
               AND status = 1 
               AND value_date = '#sql_date#')
            
            -- Daily purchase close-adjust
            + (SELECT COALESCE(SUM(amount), 0) 
               FROM bs_purchase_spot 
               WHERE supplier_id IN (1,6) 
                 AND parent IS NULL 
                 AND type = 'physical' 
                 AND noted = 'Close-Adjust' 
                 AND date = '#sql_date#')
        )
        
        -- ========================================
        -- PRODUCTION DATA (DAILY)
        -- ========================================
        -- Product ID 1
        + (SELECT COALESCE(SUM(amount), 0) 
           FROM bs_productions_silver_save  
           LEFT OUTER JOIN bs_productions ON bs_productions.id = bs_productions_silver_save.round 
           WHERE bs_productions.product_id = '1' 
             AND bs_productions_silver_save.date = '#sql_date#')
        
        -- Product ID 3
        + (SELECT COALESCE(SUM(amount), 0) 
           FROM bs_productions_silver_save  
           LEFT OUTER JOIN bs_productions ON bs_productions.id = bs_productions_silver_save.round 
           WHERE bs_productions.product_id = '3' 
             AND bs_productions_silver_save.date = '#sql_date#')
        
        -- Product ID 4
        + (SELECT COALESCE(SUM(amount), 0) 
           FROM bs_productions_silver_save  
           LEFT OUTER JOIN bs_productions ON bs_productions.id = bs_productions_silver_save.round 
           WHERE bs_productions.product_id = '4' 
             AND bs_productions_silver_save.date = '#sql_date#')
        
        -- Product ID 6
        + (SELECT COALESCE(SUM(amount), 0) 
           FROM bs_productions_silver_save  
           LEFT OUTER JOIN bs_productions ON bs_productions.id = bs_productions_silver_save.round 
           WHERE bs_productions.product_id = '6' 
             AND bs_productions_silver_save.date = '#sql_date#')

      -- Product ID 8
        + (SELECT COALESCE(SUM(amount), 0) 
           FROM bs_productions_silver_save  
           LEFT OUTER JOIN bs_productions ON bs_productions.id = bs_productions_silver_save.round 
           WHERE bs_productions.product_id = '8' 
             AND bs_productions_silver_save.date = '#sql_date#')
             
      -- Product ID 10
        + (SELECT COALESCE(SUM(amount), 0) 
           FROM bs_productions_silver_save  
           LEFT OUTER JOIN bs_productions ON bs_productions.id = bs_productions_silver_save.round 
           WHERE bs_productions.product_id = '10' 
             AND bs_productions_silver_save.date = '#sql_date#')
        -- ========================================
        -- PMR PRODUCTION ADJUSTMENTS
        -- ========================================
        + (SELECT COALESCE(SUM(weight_out_total), 0)
           FROM bs_productions_pmr
           WHERE product_id = '4'
             AND status = 1
             AND submited BETWEEN '2025-03-25' AND '#sql_date#')
        
        - (SELECT COALESCE(SUM(bip.amount), 0)
           FROM bs_productions bp
           LEFT OUTER JOIN bs_incoming_plans bip 
             ON bip.import_lot COLLATE utf8mb3_unicode_ci = bp.round COLLATE utf8mb3_unicode_ci
           WHERE bp.product_id = '4'
             AND bp.submited BETWEEN '2025-03-24' AND '#sql_date#'
             AND bp.PMR = 'PMR')
        
        -- ========================================
        -- MATCH DATA (JSON EXTRACTS)
        -- ========================================
        + (SELECT COALESCE(SUM(JSON_EXTRACT(data, '$.stock_grain')), 0) 
           FROM bs_match_data 
           WHERE date = '#sql_date#')
        
        + (SELECT COALESCE(SUM(JSON_EXTRACT(data, '$.sigmargin')), 0) 
           FROM bs_match_data 
           WHERE date = '#sql_date#')
        
        + (SELECT COALESCE(SUM(JSON_EXTRACT(data, '$.stock_bar')), 0) 
           FROM bs_match_data 
           WHERE date = '#sql_date#')
        
        + (SELECT COALESCE(SUM(JSON_EXTRACT(data, '$.stock_pmr')), 0) 
           FROM bs_match_data 
           WHERE date = '#sql_date#')
        
        + (SELECT COALESCE(SUM(JSON_EXTRACT(data, '$.customer_pending')), 0) 
           FROM bs_match_data 
           WHERE date = '#sql_date#')

        + (SELECT COALESCE(SUM(JSON_EXTRACT(data, '$.scrap_silverarticle_grain')), 0) 
           FROM bs_match_data 
           WHERE date = '#sql_date#')

       + (SELECT COALESCE(SUM(JSON_EXTRACT(data, '$.scrap_silverarticle')), 0) 
           FROM bs_match_data 
           WHERE date = '#sql_date#')

        + (SELECT COALESCE(SUM(JSON_EXTRACT(data, '$.scrap_silver999')), 0) 
            FROM bs_match_data 
            WHERE date = '#sql_date#')

        + (SELECT COALESCE(SUM(JSON_EXTRACT(data, '$.scrap_silverbar')), 0) 
          FROM bs_match_data 
          WHERE date = '#sql_date#')
        
        -- ========================================
        -- PRODUCT IMPORT CALCULATIONS
        -- ========================================
        + (
            -- Bank date vs import date difference
            (SELECT COALESCE(SUM(amount), 0) 
             FROM bs_incoming_plans 
             LEFT OUTER JOIN bs_products_import 
               ON bs_incoming_plans.remark = bs_products_import.id
             WHERE bs_incoming_plans.bank_date BETWEEN '2024-01-01' AND '#sql_date#' 
               AND bs_products_import.status = '1')
            
            - (SELECT COALESCE(SUM(amount), 0) 
               FROM bs_incoming_plans 
               LEFT OUTER JOIN bs_products_import 
                 ON bs_incoming_plans.remark = bs_products_import.id
               WHERE bs_incoming_plans.import_date BETWEEN '2024-01-01' AND '#sql_date#' 
                 AND bs_incoming_plans.bank_date IS NOT NULL 
                 AND bs_products_import.status = '1')
        )
        
        -- ========================================
        -- PRODUCTION WEIGHT CALCULATIONS
        -- ========================================
        + (
            -- PMR Product 3 vs Production Product 2
            (SELECT COALESCE(SUM(weight_out_packing), 0) 
             FROM bs_productions_pmr  
             WHERE product_id = '3'  AND remark REGEXP '(ส่งเม็ดผลิต|ส่งเม็ด)'
               AND submited BETWEEN '2025-01-03' AND '#sql_date#')
            
            - (SELECT COALESCE(SUM(weight_out_packing), 0) 
               FROM bs_productions 
               WHERE product_id = '2' 
                 AND submited BETWEEN '2025-01-03' AND '#sql_date#')
        )
        
        + (
            -- Export calculations
            (SELECT COALESCE(SUM(weight_out_packing), 0) 
             FROM bs_productions_pmr  
             WHERE export_id = '1' 
               AND submited BETWEEN '2024-05-01' AND '#sql_date#')
            
            - (SELECT COALESCE(SUM(weight_out_packing), 0) 
               FROM bs_productions 
               WHERE product_id = '6' 
                 AND PMR = 'PMR' 
                 AND submited BETWEEN '2024-05-01' AND '#sql_date#')
            
            - (SELECT COALESCE(SUM(amount), 0) 
               FROM bs_stock_adjusted 
               WHERE product_id = '5' 
                 AND date BETWEEN '2024-05-01' AND '#sql_date#')
        )
        
        -- Daily production weights
        + (SELECT COALESCE(SUM(weight_out_packing), 0) 
           FROM bs_productions 
           WHERE product_id = '1' 
             AND submited = '#sql_date#')
        
        + (SELECT COALESCE(SUM(weight_out_packing), 0) 
           FROM bs_productions 
           WHERE product_id = '3' 
             AND submited = '#sql_date#')
        
        -- ========================================
        -- PRODUCT INVENTORY CALCULATIONS (Products 1-7)
        -- ========================================
        
        -- PRODUCT 1 INVENTORY
        + (
            (SELECT COALESCE(SUM(weight_out_packing), 0) + 1204 
             FROM bs_productions 
             WHERE product_id = '1' 
               AND submited BETWEEN '2022-05-04' AND '#sql_date2#')
            
            + (SELECT COALESCE(SUM(weight_out_total), 0) 
               FROM bs_productions_in 
               WHERE product_id = '1' 
                 AND submited BETWEEN '2022-05-04' AND '#sql_date#')
            
            - (SELECT COALESCE(SUM(amount), 0) 
               FROM bs_orders 
               WHERE product_id = '1' 
                 AND status > 0 
                 AND delivery_date BETWEEN '2022-05-04' AND '#sql_date2#')
            
            - (SELECT COALESCE(SUM(amount), 0) 
               FROM bs_stock_adjusted 
               LEFT JOIN bs_stock_adjuest_types 
                 ON bs_stock_adjusted.type_id = bs_stock_adjuest_types.id 
               WHERE bs_stock_adjuest_types.type = 1 
                 AND bs_stock_adjusted.product_id = 1 
                 AND date BETWEEN '2022-03-29' AND '#sql_date2#')
            
            + (SELECT COALESCE(SUM(amount), 0) 
               FROM bs_stock_adjusted 
               LEFT JOIN bs_stock_adjuest_types 
                 ON bs_stock_adjusted.type_id = bs_stock_adjuest_types.id 
               WHERE bs_stock_adjuest_types.type = 2 
                 AND bs_stock_adjusted.product_id = 1 
                 AND bs_stock_adjusted.type_id = 3 
                 AND date BETWEEN '2022-03-29' AND '#sql_date2#')
            
            - (SELECT COALESCE(SUM(amount), 0) 
               FROM bs_stock_adjusted 
               WHERE product_id = '1' 
                 AND date = '2023-12-22')
            
            - (SELECT COALESCE(SUM(weight_out_packing), 0) 
               FROM bs_productions_pmr 
               WHERE product_id = '1' 
                 AND submited BETWEEN '2022-05-04' AND '#sql_date#')
            
            - (SELECT COALESCE(SUM(amount), 0) 
               FROM bs_orders 
               WHERE product_id = '1' 
                 AND status > 0 
                 AND delivery_date = '#sql_date#')
        )
        
        -- PRODUCT 3 INVENTORY
        + (
            (SELECT COALESCE(SUM(weight_out_packing), 0) + 206.7689 
             FROM bs_productions 
             WHERE product_id = '3' 
               AND submited BETWEEN '2022-05-04' AND '#sql_date2#')
            
            + (SELECT COALESCE(SUM(weight_out_total), 0) 
               FROM bs_productions_in 
               WHERE product_id = '3' 
                 AND submited BETWEEN '2022-05-04' AND '#sql_date#')
            
            - (SELECT COALESCE(SUM(amount), 0) 
               FROM bs_orders 
               WHERE product_id = '3' 
                 AND status > 0 
                 AND delivery_date BETWEEN '2022-05-04' AND '#sql_date2#')
            
            - (SELECT COALESCE(SUM(weight_out_packing), 0) 
               FROM bs_productions_pmr 
               WHERE product_id = '3' 
                 AND submited BETWEEN '2022-05-04' AND '#sql_date#')
            
            + (SELECT COALESCE(SUM(weight_out_packing), 0) 
               FROM bs_productions_pmr 
               WHERE product_id = '3' 
                 AND submited = '2024-01-17')
            
            - (SELECT COALESCE(SUM(amount), 0) 
               FROM bs_orders 
               WHERE product_id = '3' 
                 AND status > 0 
                 AND delivery_date = '#sql_date#')
        )
        
        -- PRODUCT 4 INVENTORY
        + (
            (SELECT COALESCE(SUM(weight_out_packing), 0) + 0.018 
             FROM bs_productions 
             WHERE product_id = '4' 
               AND submited BETWEEN '2023-02-27' AND '#sql_date#')
            
            + (SELECT COALESCE(SUM(weight_out_total), 0) 
               FROM bs_productions_in 
               WHERE product_id = '4' 
                 AND submited BETWEEN '2022-05-04' AND '#sql_date#')
            
            - (SELECT COALESCE(SUM(amount), 0) 
               FROM bs_stock_adjusted 
               LEFT JOIN bs_stock_adjuest_types 
                 ON bs_stock_adjusted.type_id = bs_stock_adjuest_types.id 
               WHERE bs_stock_adjuest_types.type = 1 
                 AND bs_stock_adjusted.product_id = 4 
                 AND date BETWEEN '2022-03-29' AND '#sql_date2#')
            
            + (SELECT COALESCE(SUM(amount), 0) 
               FROM bs_stock_adjusted 
               LEFT JOIN bs_stock_adjuest_types 
                 ON bs_stock_adjusted.type_id = bs_stock_adjuest_types.id 
               WHERE bs_stock_adjuest_types.type = 2 
                 AND bs_stock_adjusted.product_id = 4 
                 AND bs_stock_adjusted.type_id = 9 
                 AND date BETWEEN '2022-03-29' AND '#sql_date2#')
            
            - (SELECT COALESCE(SUM(weight_out_packing), 0) 
               FROM bs_productions_pmr 
               WHERE product_id = '4' 
                 AND submited BETWEEN '2022-05-04' AND '#sql_date#')
            
            - (SELECT COALESCE(SUM(amount), 0) 
               FROM bs_orders 
               WHERE product_id = '4' 
                 AND status > 0 
                 AND delivery_date BETWEEN '2022-05-04' AND '#sql_date2#')
            
            - (SELECT COALESCE(SUM(amount), 0) 
               FROM bs_orders 
               WHERE product_id = '4' 
                 AND status > 0 
                 AND delivery_date = '#sql_date#')
        )
        
        -- PRODUCT 5 INVENTORY
        + (
            (SELECT COALESCE(SUM(weight_out_packing), 0) 
             FROM bs_productions 
             WHERE product_id = '5' 
               AND submited BETWEEN '2024-02-22' AND '#sql_date#')
            
            + (SELECT COALESCE(SUM(weight_out_total), 0) 
               FROM bs_productions_in 
               WHERE product_id = '5' 
                 AND submited BETWEEN '2022-05-04' AND '#sql_date#')
            
            - (SELECT COALESCE(SUM(amount), 0) 
               FROM bs_orders 
               WHERE product_id = '5' 
                 AND status > 0 
                 AND delivery_date BETWEEN '2022-05-04' AND '#sql_date2#')
            
            + (SELECT COALESCE(SUM(weight_expected), 0) 
               FROM bs_stock_silver 
               WHERE product_id = '5' 
                 AND submited BETWEEN '2022-05-04' AND '#sql_date2#')
            
            + (SELECT COALESCE(SUM(amount), 0) 
               FROM bs_stock_adjusted 
               WHERE product_id = '5' 
                 AND date BETWEEN '2024-05-01' AND '#sql_date#')
            
            - (SELECT COALESCE(SUM(weight_out_packing), 0) 
               FROM bs_productions_pmr 
               WHERE product_id = '5' 
                 AND submited BETWEEN '2022-05-04' AND '#sql_date#')
            
            - (SELECT COALESCE(SUM(amount), 0) 
               FROM bs_orders 
               WHERE product_id = '5' 
                 AND status > 0 
                 AND delivery_date = '#sql_date#')
        )
        
        -- PRODUCT 2 INVENTORY
        + (
            (SELECT COALESCE(SUM(weight_out_packing), 0) + 53 
             FROM bs_productions 
             WHERE product_id = '2' 
               AND submited BETWEEN '2022-05-04' AND '#sql_date#')
            
            + (SELECT COALESCE(SUM(weight_out_total), 0) 
               FROM bs_productions_in 
               WHERE product_id = '2' 
                 AND submited BETWEEN '2022-05-04' AND '#sql_date#')
            
            - (SELECT COALESCE(SUM(amount), 0) 
               FROM bs_orders 
               WHERE product_id = '2' 
                 AND status > 0 
                 AND delivery_date BETWEEN '2022-05-04' AND '#sql_date2#')
            
            + (SELECT COALESCE(SUM(weight_expected), 0) 
               FROM bs_stock_silver 
               WHERE product_id = '2' 
                 AND submited BETWEEN '2022-05-04' AND '#sql_date#')
            
            - (SELECT COALESCE(SUM(amount), 0) 
               FROM bs_orders 
               WHERE product_id = '2' 
                 AND status > 0 
                 AND delivery_date = '#sql_date#')
        )
        
        -- PRODUCT 6 INVENTORY
        + (
            (SELECT COALESCE(SUM(weight_out_packing), 0) 
             FROM bs_productions 
             WHERE product_id = '6' 
               AND submited BETWEEN '2022-05-04' AND '#sql_date#')
            
            + (SELECT COALESCE(SUM(weight_out_total), 0) 
               FROM bs_productions_in 
               WHERE product_id = '6' 
                 AND submited BETWEEN '2022-05-04' AND '#sql_date#')
            
            - (SELECT COALESCE(SUM(amount), 0) 
               FROM bs_orders 
               WHERE product_id = '6' 
                 AND status > 0 
                 AND delivery_date BETWEEN '2022-05-04' AND '#sql_date2#')
            
            + (SELECT COALESCE(SUM(weight_expected), 0) 
               FROM bs_stock_silver 
               WHERE product_id = '6' 
                 AND submited BETWEEN '2022-05-04' AND '#sql_date#')
            
            - (SELECT COALESCE(SUM(weight_out_packing), 0) 
               FROM bs_productions_pmr 
               WHERE product_id = '6' 
                 AND submited BETWEEN '2022-05-04' AND '#sql_date#')
            
            - (SELECT COALESCE(SUM(amount), 0) 
               FROM bs_orders 
               WHERE product_id = '6' 
                 AND status > 0 
                 AND delivery_date = '#sql_date#')
        )
        
        -- PRODUCT 7 INVENTORY
        + (
            (SELECT COALESCE(SUM(weight_out_packing), 0) 
             FROM bs_productions 
             WHERE product_id = '7' 
               AND submited BETWEEN '2022-05-04' AND '#sql_date#')
            
            + (SELECT COALESCE(SUM(weight_out_total), 0) 
               FROM bs_productions_in 
               WHERE product_id = '7' 
                 AND submited BETWEEN '2022-05-04' AND '#sql_date#')
            
            - (SELECT COALESCE(SUM(amount), 0) 
               FROM bs_orders 
               WHERE product_id = '7' 
                 AND status > 0 
                 AND delivery_date BETWEEN '2022-05-04' AND '#sql_date2#')
            
            + (SELECT COALESCE(SUM(weight_expected), 0) 
               FROM bs_stock_silver 
               WHERE product_id = '7' 
                 AND submited BETWEEN '2022-05-04' AND '#sql_date#')
            
            - (SELECT COALESCE(SUM(weight_out_packing), 0) 
               FROM bs_productions_pmr 
               WHERE product_id = '7' 
                 AND submited BETWEEN '2022-05-04' AND '#sql_date#')
            
            - (SELECT COALESCE(SUM(amount), 0) 
               FROM bs_orders 
               WHERE product_id = '7' 
                 AND status > 0 
                 AND delivery_date = '#sql_date#')
        )
               -- PRODUCT 8 INVENTORY
        + (
            (SELECT COALESCE(SUM(weight_out_packing), 0) 
             FROM bs_productions 
             WHERE product_id = '8' 
               AND submited BETWEEN '2022-05-04' AND '#sql_date#')
            
            + (SELECT COALESCE(SUM(weight_out_total), 0) 
               FROM bs_productions_in 
               WHERE product_id = '8' 
                 AND submited BETWEEN '2022-05-04' AND '#sql_date#')
            
            - (SELECT COALESCE(SUM(amount), 0) 
               FROM bs_orders 
               WHERE product_id = '8' 
                 AND status > 0 
                 AND delivery_date BETWEEN '2022-05-04' AND '#sql_date2#')
            
            + (SELECT COALESCE(SUM(weight_expected), 0) 
               FROM bs_stock_silver 
               WHERE product_id = '8' 
                 AND submited BETWEEN '2022-05-04' AND '#sql_date#')
            
            - (SELECT COALESCE(SUM(weight_out_packing), 0) 
               FROM bs_productions_pmr 
               WHERE product_id = '8' 
                 AND submited BETWEEN '2022-05-04' AND '#sql_date#')
            
            - (SELECT COALESCE(SUM(amount), 0) 
               FROM bs_orders 
               WHERE product_id = '8' 
                 AND status > 0 
                 AND delivery_date = '#sql_date#')
        )

                      -- PRODUCT 10 INVENTORY
        + (
            (SELECT COALESCE(SUM(weight_out_packing), 0) 
             FROM bs_productions 
             WHERE product_id = '10' 
               AND submited BETWEEN '2022-05-04' AND '#sql_date#')
            
            + (SELECT COALESCE(SUM(weight_out_total), 0) 
               FROM bs_productions_in 
               WHERE product_id = '10' 
                 AND submited BETWEEN '2022-05-04' AND '#sql_date#')
            
            - (SELECT COALESCE(SUM(amount), 0) 
               FROM bs_orders 
               WHERE product_id = '10' 
                 AND status > 0 
                 AND delivery_date BETWEEN '2022-05-04' AND '#sql_date2#')
            
            + (SELECT COALESCE(SUM(weight_expected), 0) 
               FROM bs_stock_silver 
               WHERE product_id = '10' 
                 AND submited BETWEEN '2022-05-04' AND '#sql_date#')
            
            - (SELECT COALESCE(SUM(weight_out_packing), 0) 
               FROM bs_productions_pmr 
               WHERE product_id = '10' 
                 AND submited BETWEEN '2022-05-04' AND '#sql_date#')
            
            - (SELECT COALESCE(SUM(amount), 0) 
               FROM bs_orders 
               WHERE product_id = '10' 
                 AND status > 0 
                 AND delivery_date = '#sql_date#')
        )
 
 
        
        - (
            -- Orders adjustment
            (SELECT COALESCE(-SUM(amount), 0) AS amount 
             FROM bs_orders 
             WHERE DATE(date) BETWEEN '2023-10-03' AND '#sql_date#' 
               AND bs_orders.parent IS NULL 
               AND bs_orders.status > -1)
            
            -- Purchase spot adjustment
            - (SELECT COALESCE(-SUM(amount), 0) AS amount 
               FROM bs_purchase_spot 
               WHERE date BETWEEN '2023-10-03' AND '#sql_date#' 
                 AND (bs_purchase_spot.type LIKE 'physical' OR bs_purchase_spot.type LIKE 'stock')
                 AND rate_spot > 0 
                 AND status > -1  
                 AND confirm IS NOT NULL)
        )
    ",
    "SELECT '-'",
  ),
  "Physical Hedge", //51
  array(
    "total", //52
    "Customer Pending",
    "SELECT COALESCE((SELECT SUM(bs_orders.amount) AS amount
      FROM bs_orders
      WHERE DATE(date) LIKE '#sql_date#' AND bs_orders.status > 0), 0)
     - COALESCE((SELECT SUM(bs_deliveries.amount) AS amount
      FROM bs_deliveries
      LEFT JOIN bs_orders ON bs_deliveries.id = bs_orders.delivery_id
      WHERE DATE(bs_deliveries.delivery_date) LIKE '#sql_date#' AND bs_orders.status > 0), 0)
     + COALESCE((SELECT SUM(bs_orders.amount) AS amount 
      FROM bs_orders 
      WHERE DATE(date) < '#sql_date#' 
        AND DATE(date) >= '2022-05-05' 
        AND bs_orders.status > 0), 0) + 10482
     - COALESCE((SELECT SUM(bs_orders.amount) AS amount
      FROM bs_deliveries
      LEFT JOIN bs_orders ON bs_deliveries.id = bs_orders.delivery_id
      WHERE DATE(bs_deliveries.delivery_date) < '#sql_date#' 
        AND DATE(bs_deliveries.delivery_date) >= '2022-05-05'
        AND bs_orders.status > 0), 0)
      ",
    "SELECT '-'",
    "C/F จากเมื่อวานในหน้า Sales Overview"
  ),
  array(
    "total",
    "Total Silver Balance (Thailand)", //53
    "SELECT 
        + (
            -- Purchase spot amount for specific date and supplier
            (SELECT COALESCE(SUM(amount), 0) 
             FROM bs_purchase_spot 
             WHERE date BETWEEN '2024-01-01' AND '2024-01-01' 
               AND supplier_id = '1' 
               AND status > 0 
               AND type != 'defer')
            
            -- Subtract supplier mapping adjustment
            - (SELECT COALESCE(-SUM(amount), 0) 
               FROM bs_suppliers_mapping_1 
               WHERE id = '1' 
                 AND date = '2024-01-01')
            
            -- Subtract incoming plans with silver reserve
            - (SELECT COALESCE(SUM(bs_incoming_plans.amount), 0) 
               FROM bs_incoming_plans 
               LEFT OUTER JOIN bs_reserve_silver 
                 ON bs_incoming_plans.amount = bs_reserve_silver.weight_lock 
               WHERE bs_incoming_plans.import_date BETWEEN '2024-01-01' AND '2024-01-01' 
                 AND bs_reserve_silver.supplier_id IN (1))
            
            -- Physical purchase adjustments (specific suppliers)
            + (SELECT COALESCE(SUM(CASE 
                                    WHEN adj_supplier IN (16,17,18,20,22,23,24,25,26) THEN amount = '0' 
                                    ELSE amount 
                                  END), 0) 
               FROM bs_purchase_spot 
               WHERE date BETWEEN '2024-01-01' AND '#sql_date2#' 
                 AND adj_supplier IN (1,6,14,19,21,11) 
                 AND noted = 'Normal' 
                 AND parent IS NULL 
                 AND type = 'physical')
            
            -- Defer/physical-adjust calculations
            + (SELECT COALESCE(SUM(CASE 
                                    WHEN adj_supplier IN (1,6,14,19,21,11) THEN amount = '0' 
                                    ELSE amount 
                                  END), 0)
               FROM bs_purchase_spot 
               WHERE adj_supplier IN (16,17,18,20,22,23,24,25,26) 
                 AND parent IS NULL 
                 AND (type = 'defer' OR type = 'physical-adjust') 
                 AND noted = 'Open-Adjust' 
                 AND date BETWEEN '2024-01-01' AND '#sql_date2#')
            
            -- Subtract incoming plans range
            - (SELECT COALESCE(SUM(amount), 0) 
               FROM bs_incoming_plans 
               WHERE import_date BETWEEN '2024-01-01' AND '#sql_date2#' 
                 AND supplier_id IN (1,6,14,16,17,18,19,20,21,11,22,23,24,25,26))
            
            -- Subtract stock silver expected weight
            - (SELECT COALESCE(SUM(weight_expected), 0) 
               FROM bs_stock_silver 
               WHERE submited BETWEEN '2024-01-01' AND '#sql_date2#' 
                 AND supplier_id IN (1,6,14,16,17,18,19,20,21,11,22,23,24,25,26))
            
            -- Subtract physical adjustments
            - (SELECT COALESCE(SUM(amount), 0) 
               FROM bs_adjust_physical_adjust 
               WHERE date BETWEEN '2024-01-01' AND '#sql_date2#' 
                 AND supplier_id IN (1,6,14,16,17,18,19,20,21,11,22,23,24,25,26))
        )
        
        + (
            -- Daily physical purchase (specific suppliers)
            (SELECT COALESCE(SUM(CASE 
                                  WHEN adj_supplier IN (16,17,18,20,22,23,24,25,26) THEN amount = '0' 
                                  ELSE amount 
                                END), 0) 
             FROM bs_purchase_spot 
             WHERE adj_supplier IN (1,6,14,19,21,11) 
               AND parent IS NULL 
               AND type = 'physical' 
               AND noted = 'Normal' 
               AND date = '#sql_date#')
            
            -- Daily defer/physical-adjust
            + (SELECT COALESCE(SUM(CASE 
                                    WHEN adj_supplier IN (1,6,14,19,21,11) THEN amount = '0' 
                                    ELSE amount 
                                  END), 0)
               FROM bs_purchase_spot 
               WHERE adj_supplier IN (16,17,18,20,22,23,24,25,26) 
                 AND parent IS NULL 
                 AND (type = 'defer' OR type = 'physical-adjust') 
                 AND noted = 'Open-Adjust' 
                 AND date = '#sql_date#')
        )
        
        + (
            -- Daily incoming plans
            (SELECT COALESCE(-SUM(amount), 0) 
             FROM bs_incoming_plans 
             WHERE import_date = '#sql_date#' 
               AND supplier_id IN (1,6,14,16,17,18,19,20,21,11,22,23,24,25,26))
            
            -- Daily stock silver expected
            + (SELECT COALESCE(-SUM(weight_expected), 0) 
               FROM bs_stock_silver 
               WHERE submited = '#sql_date#' 
                 AND supplier_id IN (1,6,14,16,17,18,19,20,21,11,22,23,24,25,26))
        )
        
        + (
            -- Sales spot range (suppliers 1,6)
            (SELECT COALESCE(-SUM(amount), 0) 
             FROM bs_sales_spot 
             WHERE supplier_id IN (1,6) 
               AND type = 'physical' 
               AND status = 1 
               AND value_date BETWEEN '2024-01-01' AND '#sql_date2#')
            
            -- Purchase spot close-adjust range
            + (SELECT COALESCE(SUM(amount), 0) 
               FROM bs_purchase_spot 
               WHERE supplier_id IN (1,6) 
                 AND parent IS NULL 
                 AND type = 'physical' 
                 AND noted = 'Close-Adjust' 
                 AND date BETWEEN '2024-01-01' AND '#sql_date2#')
        )
        
        -- Daily sales and purchases
        + (
            -- Daily sales spot
            (SELECT COALESCE(-SUM(amount), 0) 
             FROM bs_sales_spot 
             WHERE supplier_id IN (1,6) 
               AND type = 'physical' 
               AND status = 1 
               AND value_date = '#sql_date#')
            
            -- Daily purchase close-adjust
            + (SELECT COALESCE(SUM(amount), 0) 
               FROM bs_purchase_spot 
               WHERE supplier_id IN (1,6) 
                 AND parent IS NULL 
                 AND type = 'physical' 
                 AND noted = 'Close-Adjust' 
                 AND date = '#sql_date#')
        )
        
        -- Product ID 1
        + (SELECT COALESCE(SUM(amount), 0) 
           FROM bs_productions_silver_save  
           LEFT OUTER JOIN bs_productions ON bs_productions.id = bs_productions_silver_save.round 
           WHERE bs_productions.product_id = '1' 
             AND bs_productions_silver_save.date = '#sql_date#')
        
        -- Product ID 3
        + (SELECT COALESCE(SUM(amount), 0) 
           FROM bs_productions_silver_save  
           LEFT OUTER JOIN bs_productions ON bs_productions.id = bs_productions_silver_save.round 
           WHERE bs_productions.product_id = '3' 
             AND bs_productions_silver_save.date = '#sql_date#')
        
        -- Product ID 4
        + (SELECT COALESCE(SUM(amount), 0) 
           FROM bs_productions_silver_save  
           LEFT OUTER JOIN bs_productions ON bs_productions.id = bs_productions_silver_save.round 
           WHERE bs_productions.product_id = '4' 
             AND bs_productions_silver_save.date = '#sql_date#')
        
        -- Product ID 6
        + (SELECT COALESCE(SUM(amount), 0) 
           FROM bs_productions_silver_save  
           LEFT OUTER JOIN bs_productions ON bs_productions.id = bs_productions_silver_save.round 
           WHERE bs_productions.product_id = '6' 
             AND bs_productions_silver_save.date = '#sql_date#')

      -- Product ID 8
        + (SELECT COALESCE(SUM(amount), 0) 
           FROM bs_productions_silver_save  
           LEFT OUTER JOIN bs_productions ON bs_productions.id = bs_productions_silver_save.round 
           WHERE bs_productions.product_id = '8' 
             AND bs_productions_silver_save.date = '#sql_date#')

        + (SELECT COALESCE(SUM(amount), 0) 
           FROM bs_productions_silver_save  
           LEFT OUTER JOIN bs_productions ON bs_productions.id = bs_productions_silver_save.round 
           WHERE bs_productions.product_id = '10' 
             AND bs_productions_silver_save.date = '#sql_date#')
        
        
        -- ========================================
        -- PMR PRODUCTION ADJUSTMENTS
        -- ========================================
        + (SELECT COALESCE(SUM(weight_out_total), 0)
           FROM bs_productions_pmr
           WHERE product_id = '4'
             AND status = 1
             AND submited BETWEEN '2025-03-25' AND '#sql_date#')
        
        - (SELECT COALESCE(SUM(bip.amount), 0)
           FROM bs_productions bp
           LEFT OUTER JOIN bs_incoming_plans bip 
             ON bip.import_lot COLLATE utf8mb3_unicode_ci = bp.round COLLATE utf8mb3_unicode_ci
           WHERE bp.product_id = '4'
             AND bp.submited BETWEEN '2025-03-24' AND '#sql_date#'
             AND bp.PMR = 'PMR')
        
        -- ========================================
        -- MATCH DATA (JSON EXTRACTS)
        -- ========================================
        + (SELECT COALESCE(SUM(JSON_EXTRACT(data, '$.stock_grain')), 0) 
           FROM bs_match_data 
           WHERE date = '#sql_date#')
        
        + (SELECT COALESCE(SUM(JSON_EXTRACT(data, '$.sigmargin')), 0) 
           FROM bs_match_data 
           WHERE date = '#sql_date#')
        
        + (SELECT COALESCE(SUM(JSON_EXTRACT(data, '$.stock_bar')), 0) 
           FROM bs_match_data 
           WHERE date = '#sql_date#')
        
        + (SELECT COALESCE(SUM(JSON_EXTRACT(data, '$.stock_pmr')), 0) 
           FROM bs_match_data 
           WHERE date = '#sql_date#')
        
        + (SELECT COALESCE(SUM(JSON_EXTRACT(data, '$.customer_pending')), 0) 
           FROM bs_match_data 
           WHERE date = '#sql_date#')
      
        + (SELECT COALESCE(SUM(JSON_EXTRACT(data, '$.scrap_silverarticle_grain')), 0) 
          FROM bs_match_data 
          WHERE date = '#sql_date#')

        + (SELECT COALESCE(SUM(JSON_EXTRACT(data, '$.scrap_silverarticle')), 0) 
          FROM bs_match_data 
          WHERE date = '#sql_date#')

        + (SELECT COALESCE(SUM(JSON_EXTRACT(data, '$.scrap_silver999')), 0) 
          FROM bs_match_data 
          WHERE date = '#sql_date#')

        + (SELECT COALESCE(SUM(JSON_EXTRACT(data, '$.scrap_silverbar')), 0) 
          FROM bs_match_data 
          WHERE date = '#sql_date#')
        
        -- ========================================
        -- PRODUCT IMPORT CALCULATIONS
        -- ========================================
        + (
            -- Bank date vs import date difference
            (SELECT COALESCE(SUM(amount), 0) 
             FROM bs_incoming_plans 
             LEFT OUTER JOIN bs_products_import 
               ON bs_incoming_plans.remark = bs_products_import.id
             WHERE bs_incoming_plans.bank_date BETWEEN '2024-01-01' AND '#sql_date#' 
               AND bs_products_import.status = '1')
            
            - (SELECT COALESCE(SUM(amount), 0) 
               FROM bs_incoming_plans 
               LEFT OUTER JOIN bs_products_import 
                 ON bs_incoming_plans.remark = bs_products_import.id
               WHERE bs_incoming_plans.import_date BETWEEN '2024-01-01' AND '#sql_date#' 
                 AND bs_incoming_plans.bank_date IS NOT NULL 
                 AND bs_products_import.status = '1')
        )
        
        -- ========================================
        -- PRODUCTION WEIGHT CALCULATIONS
        -- ========================================
        + (
            -- PMR Product 3 vs Production Product 2
            (SELECT COALESCE(SUM(weight_out_packing), 0) 
             FROM bs_productions_pmr  
             WHERE product_id = '3'  AND remark REGEXP '(ส่งเม็ดผลิต|ส่งเม็ด)'
               AND submited BETWEEN '2025-01-03' AND '#sql_date#')
            
            - (SELECT COALESCE(SUM(weight_out_packing), 0) 
               FROM bs_productions 
               WHERE product_id = '2' 
                 AND submited BETWEEN '2025-01-03' AND '#sql_date#')
        )
        
        + (
            -- Export calculations
            (SELECT COALESCE(SUM(weight_out_packing), 0) 
             FROM bs_productions_pmr  
             WHERE export_id = '1' 
               AND submited BETWEEN '2024-05-01' AND '#sql_date#')
            
            - (SELECT COALESCE(SUM(weight_out_packing), 0) 
               FROM bs_productions 
               WHERE product_id = '6' 
                 AND PMR = 'PMR' 
                 AND submited BETWEEN '2024-05-01' AND '#sql_date#')
            
            - (SELECT COALESCE(SUM(amount), 0) 
               FROM bs_stock_adjusted 
               WHERE product_id = '5' 
                 AND date BETWEEN '2024-05-01' AND '#sql_date#')
        )
        
        -- Daily production weights
        + (SELECT COALESCE(SUM(weight_out_packing), 0) 
           FROM bs_productions 
           WHERE product_id = '1' 
             AND submited = '#sql_date#')
        
        + (SELECT COALESCE(SUM(weight_out_packing), 0) 
           FROM bs_productions 
           WHERE product_id = '3' 
             AND submited = '#sql_date#')
        
        -- ========================================
        -- PRODUCT INVENTORY CALCULATIONS (Products 1-7)
        -- ========================================
        
        -- PRODUCT 1 INVENTORY
        + (
            (SELECT COALESCE(SUM(weight_out_packing), 0) + 1204 
             FROM bs_productions 
             WHERE product_id = '1' 
               AND submited BETWEEN '2022-05-04' AND '#sql_date2#')
            
            + (SELECT COALESCE(SUM(weight_out_total), 0) 
               FROM bs_productions_in 
               WHERE product_id = '1' 
                 AND submited BETWEEN '2022-05-04' AND '#sql_date#')
            
            - (SELECT COALESCE(SUM(amount), 0) 
               FROM bs_orders 
               WHERE product_id = '1' 
                 AND status > 0 
                 AND delivery_date BETWEEN '2022-05-04' AND '#sql_date2#')
            
            - (SELECT COALESCE(SUM(amount), 0) 
               FROM bs_stock_adjusted 
               LEFT JOIN bs_stock_adjuest_types 
                 ON bs_stock_adjusted.type_id = bs_stock_adjuest_types.id 
               WHERE bs_stock_adjuest_types.type = 1 
                 AND bs_stock_adjusted.product_id = 1 
                 AND date BETWEEN '2022-03-29' AND '#sql_date2#')
            
            + (SELECT COALESCE(SUM(amount), 0) 
               FROM bs_stock_adjusted 
               LEFT JOIN bs_stock_adjuest_types 
                 ON bs_stock_adjusted.type_id = bs_stock_adjuest_types.id 
               WHERE bs_stock_adjuest_types.type = 2 
                 AND bs_stock_adjusted.product_id = 1 
                 AND bs_stock_adjusted.type_id = 3 
                 AND date BETWEEN '2022-03-29' AND '#sql_date2#')
            
            - (SELECT COALESCE(SUM(amount), 0) 
               FROM bs_stock_adjusted 
               WHERE product_id = '1' 
                 AND date = '2023-12-22')
            
            - (SELECT COALESCE(SUM(weight_out_packing), 0) 
               FROM bs_productions_pmr 
               WHERE product_id = '1' 
                 AND submited BETWEEN '2022-05-04' AND '#sql_date#')
            
            - (SELECT COALESCE(SUM(amount), 0) 
               FROM bs_orders 
               WHERE product_id = '1' 
                 AND status > 0 
                 AND delivery_date = '#sql_date#')
        )
        
        -- PRODUCT 3 INVENTORY
        + (
            (SELECT COALESCE(SUM(weight_out_packing), 0) + 206.7689 
             FROM bs_productions 
             WHERE product_id = '3' 
               AND submited BETWEEN '2022-05-04' AND '#sql_date2#')
            
            + (SELECT COALESCE(SUM(weight_out_total), 0) 
               FROM bs_productions_in 
               WHERE product_id = '3' 
                 AND submited BETWEEN '2022-05-04' AND '#sql_date#')
            
            - (SELECT COALESCE(SUM(amount), 0) 
               FROM bs_orders 
               WHERE product_id = '3' 
                 AND status > 0 
                 AND delivery_date BETWEEN '2022-05-04' AND '#sql_date2#')
            
            - (SELECT COALESCE(SUM(weight_out_packing), 0) 
               FROM bs_productions_pmr 
               WHERE product_id = '3' 
                 AND submited BETWEEN '2022-05-04' AND '#sql_date#')
            
            + (SELECT COALESCE(SUM(weight_out_packing), 0) 
               FROM bs_productions_pmr 
               WHERE product_id = '3' 
                 AND submited = '2024-01-17')
            
            - (SELECT COALESCE(SUM(amount), 0) 
               FROM bs_orders 
               WHERE product_id = '3' 
                 AND status > 0 
                 AND delivery_date = '#sql_date#')
        )
        
        -- PRODUCT 4 INVENTORY
        + (
            (SELECT COALESCE(SUM(weight_out_packing), 0) + 0.018 
             FROM bs_productions 
             WHERE product_id = '4' 
               AND submited BETWEEN '2023-02-27' AND '#sql_date#')
            
            + (SELECT COALESCE(SUM(weight_out_total), 0) 
               FROM bs_productions_in 
               WHERE product_id = '4' 
                 AND submited BETWEEN '2022-05-04' AND '#sql_date#')
            
            - (SELECT COALESCE(SUM(amount), 0) 
               FROM bs_stock_adjusted 
               LEFT JOIN bs_stock_adjuest_types 
                 ON bs_stock_adjusted.type_id = bs_stock_adjuest_types.id 
               WHERE bs_stock_adjuest_types.type = 1 
                 AND bs_stock_adjusted.product_id = 4 
                 AND date BETWEEN '2022-03-29' AND '#sql_date2#')
            
            + (SELECT COALESCE(SUM(amount), 0) 
               FROM bs_stock_adjusted 
               LEFT JOIN bs_stock_adjuest_types 
                 ON bs_stock_adjusted.type_id = bs_stock_adjuest_types.id 
               WHERE bs_stock_adjuest_types.type = 2 
                 AND bs_stock_adjusted.product_id = 4 
                 AND bs_stock_adjusted.type_id = 9 
                 AND date BETWEEN '2022-03-29' AND '#sql_date2#')
            
            - (SELECT COALESCE(SUM(weight_out_packing), 0) 
               FROM bs_productions_pmr 
               WHERE product_id = '4' 
                 AND submited BETWEEN '2022-05-04' AND '#sql_date#')
            
            - (SELECT COALESCE(SUM(amount), 0) 
               FROM bs_orders 
               WHERE product_id = '4' 
                 AND status > 0 
                 AND delivery_date BETWEEN '2022-05-04' AND '#sql_date2#')
            
            - (SELECT COALESCE(SUM(amount), 0) 
               FROM bs_orders 
               WHERE product_id = '4' 
                 AND status > 0 
                 AND delivery_date = '#sql_date#')
        )
        
        -- PRODUCT 5 INVENTORY
        + (
            (SELECT COALESCE(SUM(weight_out_packing), 0) 
             FROM bs_productions 
             WHERE product_id = '5' 
               AND submited BETWEEN '2024-02-22' AND '#sql_date#')
            
            + (SELECT COALESCE(SUM(weight_out_total), 0) 
               FROM bs_productions_in 
               WHERE product_id = '5' 
                 AND submited BETWEEN '2022-05-04' AND '#sql_date#')
            
            - (SELECT COALESCE(SUM(amount), 0) 
               FROM bs_orders 
               WHERE product_id = '5' 
                 AND status > 0 
                 AND delivery_date BETWEEN '2022-05-04' AND '#sql_date2#')
            
            + (SELECT COALESCE(SUM(weight_expected), 0) 
               FROM bs_stock_silver 
               WHERE product_id = '5' 
                 AND submited BETWEEN '2022-05-04' AND '#sql_date2#')
            
            + (SELECT COALESCE(SUM(amount), 0) 
               FROM bs_stock_adjusted 
               WHERE product_id = '5' 
                 AND date BETWEEN '2024-05-01' AND '#sql_date#')
            
            - (SELECT COALESCE(SUM(weight_out_packing), 0) 
               FROM bs_productions_pmr 
               WHERE product_id = '5' 
                 AND submited BETWEEN '2022-05-04' AND '#sql_date#')
            
            - (SELECT COALESCE(SUM(amount), 0) 
               FROM bs_orders 
               WHERE product_id = '5' 
                 AND status > 0 
                 AND delivery_date = '#sql_date#')
        )
        
        -- PRODUCT 2 INVENTORY
        + (
            (SELECT COALESCE(SUM(weight_out_packing), 0) + 53 
             FROM bs_productions 
             WHERE product_id = '2' 
               AND submited BETWEEN '2022-05-04' AND '#sql_date#')
            
            + (SELECT COALESCE(SUM(weight_out_total), 0) 
               FROM bs_productions_in 
               WHERE product_id = '2' 
                 AND submited BETWEEN '2022-05-04' AND '#sql_date#')
            
            - (SELECT COALESCE(SUM(amount), 0) 
               FROM bs_orders 
               WHERE product_id = '2' 
                 AND status > 0 
                 AND delivery_date BETWEEN '2022-05-04' AND '#sql_date2#')
            
            + (SELECT COALESCE(SUM(weight_expected), 0) 
               FROM bs_stock_silver 
               WHERE product_id = '2' 
                 AND submited BETWEEN '2022-05-04' AND '#sql_date#')
            
            - (SELECT COALESCE(SUM(amount), 0) 
               FROM bs_orders 
               WHERE product_id = '2' 
                 AND status > 0 
                 AND delivery_date = '#sql_date#')
        )
        
        -- PRODUCT 6 INVENTORY
        + (
            (SELECT COALESCE(SUM(weight_out_packing), 0) 
             FROM bs_productions 
             WHERE product_id = '6' 
               AND submited BETWEEN '2022-05-04' AND '#sql_date#')
            
            + (SELECT COALESCE(SUM(weight_out_total), 0) 
               FROM bs_productions_in 
               WHERE product_id = '6' 
                 AND submited BETWEEN '2022-05-04' AND '#sql_date#')
            
            - (SELECT COALESCE(SUM(amount), 0) 
               FROM bs_orders 
               WHERE product_id = '6' 
                 AND status > 0 
                 AND delivery_date BETWEEN '2022-05-04' AND '#sql_date2#')
            
            + (SELECT COALESCE(SUM(weight_expected), 0) 
               FROM bs_stock_silver 
               WHERE product_id = '6' 
                 AND submited BETWEEN '2022-05-04' AND '#sql_date#')
            
            - (SELECT COALESCE(SUM(weight_out_packing), 0) 
               FROM bs_productions_pmr 
               WHERE product_id = '6' 
                 AND submited BETWEEN '2022-05-04' AND '#sql_date#')
            
            - (SELECT COALESCE(SUM(amount), 0) 
               FROM bs_orders 
               WHERE product_id = '6' 
                 AND status > 0 
                 AND delivery_date = '#sql_date#')
        )
        
        -- PRODUCT 7 INVENTORY
        + (
            (SELECT COALESCE(SUM(weight_out_packing), 0) 
             FROM bs_productions 
             WHERE product_id = '7' 
               AND submited BETWEEN '2022-05-04' AND '#sql_date#')
            
            + (SELECT COALESCE(SUM(weight_out_total), 0) 
               FROM bs_productions_in 
               WHERE product_id = '7' 
                 AND submited BETWEEN '2022-05-04' AND '#sql_date#')
            
            - (SELECT COALESCE(SUM(amount), 0) 
               FROM bs_orders 
               WHERE product_id = '7' 
                 AND status > 0 
                 AND delivery_date BETWEEN '2022-05-04' AND '#sql_date2#')
            
            + (SELECT COALESCE(SUM(weight_expected), 0) 
               FROM bs_stock_silver 
               WHERE product_id = '7' 
                 AND submited BETWEEN '2022-05-04' AND '#sql_date#')
            
            - (SELECT COALESCE(SUM(weight_out_packing), 0) 
               FROM bs_productions_pmr 
               WHERE product_id = '7' 
                 AND submited BETWEEN '2022-05-04' AND '#sql_date#')
            
            - (SELECT COALESCE(SUM(amount), 0) 
               FROM bs_orders 
               WHERE product_id = '7' 
                 AND status > 0 
                 AND delivery_date = '#sql_date#')
        )
        -- PRODUCT 8 INVENTORY
        + (
            (SELECT COALESCE(SUM(weight_out_packing), 0) 
             FROM bs_productions 
             WHERE product_id = '8' 
               AND submited BETWEEN '2022-05-04' AND '#sql_date#')
            
            + (SELECT COALESCE(SUM(weight_out_total), 0) 
               FROM bs_productions_in 
               WHERE product_id = '8' 
                 AND submited BETWEEN '2022-05-04' AND '#sql_date#')
            
            - (SELECT COALESCE(SUM(amount), 0) 
               FROM bs_orders 
               WHERE product_id = '8' 
                 AND status > 0 
                 AND delivery_date BETWEEN '2022-05-04' AND '#sql_date2#')
            
            + (SELECT COALESCE(SUM(weight_expected), 0) 
               FROM bs_stock_silver 
               WHERE product_id = '8' 
                 AND submited BETWEEN '2022-05-04' AND '#sql_date#')
            
            - (SELECT COALESCE(SUM(weight_out_packing), 0) 
               FROM bs_productions_pmr 
               WHERE product_id = '8' 
                 AND submited BETWEEN '2022-05-04' AND '#sql_date#')
            
            - (SELECT COALESCE(SUM(amount), 0) 
               FROM bs_orders 
               WHERE product_id = '8' 
                 AND status > 0 
                 AND delivery_date = '#sql_date#')
        )    

         -- PRODUCT 10 INVENTORY
        + (
            (SELECT COALESCE(SUM(weight_out_packing), 0) 
             FROM bs_productions 
             WHERE product_id = '10' 
               AND submited BETWEEN '2022-05-04' AND '#sql_date#')
            
            + (SELECT COALESCE(SUM(weight_out_total), 0) 
               FROM bs_productions_in 
               WHERE product_id = '10' 
                 AND submited BETWEEN '2022-05-04' AND '#sql_date#')
            
            - (SELECT COALESCE(SUM(amount), 0) 
               FROM bs_orders 
               WHERE product_id = '10' 
                 AND status > 0 
                 AND delivery_date BETWEEN '2022-05-04' AND '#sql_date2#')
            
            + (SELECT COALESCE(SUM(weight_expected), 0) 
               FROM bs_stock_silver 
               WHERE product_id = '10' 
                 AND submited BETWEEN '2022-05-04' AND '#sql_date#')
            
            - (SELECT COALESCE(SUM(weight_out_packing), 0) 
               FROM bs_productions_pmr 
               WHERE product_id = '10' 
                 AND submited BETWEEN '2022-05-04' AND '#sql_date#')
            
            - (SELECT COALESCE(SUM(amount), 0) 
               FROM bs_orders 
               WHERE product_id = '10' 
                 AND status > 0 
                 AND delivery_date = '#sql_date#')
        )           
            
        -- ========================================
        -- INITIAL FINAL ADJUSTMENTS
        -- ========================================
        - (
            -- Orders adjustment
            (SELECT COALESCE(-SUM(amount), 0) AS amount 
             FROM bs_orders 
             WHERE DATE(date) BETWEEN '2023-10-03' AND '#sql_date#' 
               AND bs_orders.parent IS NULL 
               AND bs_orders.status > -1)
            
            -- Purchase spot adjustment
            - (SELECT COALESCE(-SUM(amount), 0) AS amount 
               FROM bs_purchase_spot 
               WHERE date BETWEEN '2023-10-03' AND '#sql_date#' 
                 AND (bs_purchase_spot.type LIKE 'physical' OR bs_purchase_spot.type LIKE 'stock')
                 AND rate_spot > 0 
                 AND status > -1  
                 AND confirm IS NOT NULL)
        )
        
        -- ========================================
        -- ORDERS AND DELIVERIES BALANCE
        -- ========================================
        - (
-- Daily orders
            COALESCE((SELECT SUM(bs_orders.amount) AS amount
             FROM bs_orders
             WHERE DATE(date) LIKE '#sql_date#' 
               AND bs_orders.status > 0), 0)
            
            -- Subtract daily deliveries
            - COALESCE((SELECT SUM(bs_deliveries.amount) AS amount
               FROM bs_deliveries
               LEFT JOIN bs_orders ON bs_deliveries.id = bs_orders.delivery_id
               WHERE DATE(bs_deliveries.delivery_date) LIKE '#sql_date#' 
                 AND bs_orders.status > 0), 0)
            
            -- Add historical orders (with base adjustment +10482)
            + COALESCE((SELECT SUM(bs_orders.amount) AS amount 
               FROM bs_orders 
               WHERE DATE(date) < '#sql_date#' 
                 AND DATE(date) >= '2022-05-05' 
                 AND bs_orders.status > 0), 0) + 10482
            
            -- Subtract historical deliveries
            - COALESCE((SELECT SUM(bs_orders.amount) AS amount
               FROM bs_deliveries
               LEFT JOIN bs_orders ON bs_deliveries.id = bs_orders.delivery_id
               WHERE DATE(bs_deliveries.delivery_date) < '#sql_date#' 
                 AND DATE(bs_deliveries.delivery_date) >= '2022-05-05'
                 AND bs_orders.status > 0), 0)
        )
    ",
    "SELECT '-'"
  )
);
