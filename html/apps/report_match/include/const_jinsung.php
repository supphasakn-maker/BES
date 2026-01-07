<?php
$aContent = array(
    array(
        "supplier", //0
        "ยอดยกมา",
        "SELECT +
      (SELECT COALESCE(SUM(amount),0) FROM bs_purchase_spot WHERE date BETWEEN '2025-01-01' AND '2025-01-01' AND supplier_id ='#supplier_id#' AND product_id = '#product_id#' AND status > 0  AND type != 'defer')
     -
      (SELECT COALESCE(SUM(bs_incoming_plans.amount),0) FROM bs_incoming_plans LEFT OUTER JOIN bs_reserve_silver ON bs_incoming_plans.amount = bs_reserve_silver.weight_lock WHERE bs_incoming_plans.import_date BETWEEN '2025-01-01' AND '2025-01-01' AND bs_reserve_silver.supplier_id = '#supplier_id#' AND bs_incoming_plans.product_type_id = '#product_id#') 
      +(SELECT COALESCE(SUM(CASE WHEN adj_supplier in (16,17,18,20,22,23,24) THEN 0 ELSE amount END ),0) FROM bs_purchase_spot WHERE date BETWEEN  '2025-01-01' AND '#sql_date2#' AND adj_supplier ='#supplier_id#' AND product_id = '#product_id#' AND noted ='Normal' AND parent IS NULL AND type='physical' )
      + (SELECT COALESCE(SUM(CASE WHEN adj_supplier in (1,6,14,19,21,11) THEN 0 ELSE amount END ),0)FROM bs_purchase_spot WHERE  adj_supplier ='#supplier_id#' AND parent IS NULL AND (type='physical-adjust') AND product_id = '#product_id#' AND noted ='Open-Adjust' AND date BETWEEN  '2025-01-01' AND '#sql_date2#')
      - (SELECT COALESCE(SUM(amount),0) FROM bs_incoming_plans WHERE import_date BETWEEN  '2025-01-01' AND '#sql_date2#' AND supplier_id = '#supplier_id#' AND product_type_id = '#product_id#')",
        "SELECT 
      (SELECT COALESCE(SUM((rate_spot)*amount*32.1507),0) FROM bs_purchase_spot WHERE date BETWEEN '2025-01-01' AND '2025-01-01' AND supplier_id = '#supplier_id#' AND product_id = '#product_id#' AND status > 0  AND type != 'defer')

       +(SELECT COALESCE(SUM(CASE WHEN adj_supplier in (14,16,17,18,19,20,21,11,22,23.24) THEN 0 ELSE (rate_spot+rate_pmdc)*amount*32.1507 END ),0) FROM bs_purchase_spot WHERE  adj_supplier ='#supplier_id#' AND product_id = '#product_id#' AND noted ='Normal' AND parent IS NULL AND type='physical' AND date BETWEEN  '2025-01-01' AND '#sql_date2#')
       +(SELECT COALESCE(SUM(CASE WHEN adj_supplier in (1,6,14,19,21,11) THEN 0 ELSE (rate_spot+rate_pmdc)*amount*32.1507 END ),0) FROM bs_purchase_spot WHERE  adj_supplier ='#supplier_id#' AND product_id = '#product_id#' AND currency ='USD' AND parent IS NULL AND (type='physical-adjust') AND noted ='Open-Adjust' AND date BETWEEN  '2025-01-01' AND '#sql_date2#')
       +(SELECT COALESCE(-SUM(usd),0) FROM bs_incoming_plans WHERE import_date BETWEEN  '2025-01-01' AND '#sql_date2#' AND supplier_id = '#supplier_id#'  AND product_type_id = '#product_id#')
       +(SELECT COALESCE(SUM(CASE WHEN supplier_id in (14,16,17,18,19,20,21,11,22.24) THEN defer = '0' ELSE defer END ),0) FROM bs_defer_cost  WHERE date_defer BETWEEN  '2025-01-01' AND '#sql_date2#'  AND supplier_id = '#supplier_id#')",
    ),
    array(
        "supplier", //1
        "Purchase Physical",
        "SELECT (SELECT COALESCE(SUM(CASE WHEN adj_supplier in (16,17,18,20,22,23.24) THEN 0 ELSE amount END ),0) FROM bs_purchase_spot WHERE  adj_supplier ='#supplier_id#' AND product_id = '#product_id#' AND parent IS NULL AND type='physical' AND noted ='Normal' AND date = '#sql_date#')
    + (SELECT COALESCE(SUM(CASE WHEN adj_supplier in (1,6,14,19,21,11) THEN 0 ELSE amount END ),0)FROM bs_purchase_spot WHERE  adj_supplier ='#supplier_id#' AND product_id = '#product_id#' AND parent IS NULL AND (type='physical-adjust') AND noted ='Open-Adjust' AND date = '#sql_date#')",
        "SELECT (SELECT COALESCE(SUM(CASE WHEN adj_supplier in (14,16,17,18,19,20,21,11,22,23.24) THEN 0 ELSE (rate_spot+rate_pmdc)*amount*32.1507 END ),0) FROM bs_purchase_spot WHERE  adj_supplier ='#supplier_id#' AND product_id = '#product_id#' AND currency ='USD' AND noted ='Normal' AND parent IS NULL AND type='physical' AND date = '#sql_date#')
  + (SELECT COALESCE(SUM(CASE WHEN adj_supplier in (1,6,14,19,21,11) THEN 0 ELSE (rate_spot+rate_pmdc)*amount*32.1507  END ),0) FROM bs_purchase_spot WHERE  adj_supplier ='#supplier_id#' AND product_id = '#product_id#' AND currency ='USD' AND parent IS NULL AND (type='physical-adjust') AND noted ='Open-Adjust' AND date = '#sql_date#')"
    ),
    array( //2
        "supplier",
        "Take Shipment (หัก)",
        "SELECT (SELECT COALESCE(-SUM(amount),0) FROM bs_incoming_plans WHERE import_date = '#sql_date#' AND supplier_id = '#supplier_id#' AND product_type_id = '#product_id#')",
        "SELECT (SELECT COALESCE(-SUM(usd),0) FROM bs_incoming_plans WHERE import_date = '#sql_date#' AND supplier_id =  '#supplier_id#'  AND product_type_id = '#product_id#')",
    ),
    "Total Purchase Available", // 3
    array(
        "supplier",
        "ปิด Defer", //4
        "SELECT '-'",
        "SELECT (SELECT SUM(defer) FROM bs_defer_cost WHERE date_defer = '#sql_date#' AND supplier_id ='#supplier_id#')"
    ),
    array(
        "supplier",
        "ปิด Adjust", //4
        "SELECT '-'",
        "SELECT (SELECT COALESCE(SUM(bs_adjust_cost.value_profit),0) FROM bs_adjust_cost  
        LEFT OUTER JOIN bs_purchase_spot ON bs_adjust_cost.id = bs_purchase_spot.adjust_id
        WHERE bs_adjust_cost.date_adjust = '#sql_date#' AND bs_purchase_spot.supplier_id = '#supplier_id#' AND product_id = '#product_id#' AND  bs_purchase_spot.adjust_type='new')"
    ),
    array(
        "supplier",
        "Adjust cost (Sell) ยกมา", //5
        "SELECT 
        (SELECT COALESCE(-SUM(amount),0) FROM bs_sales_spot WHERE ref = '#supplier_id#' AND product_id = '#product_id#' AND type='physical' AND status = 1 AND value_date BETWEEN '2025-01-01' AND '#sql_date2#' )
        -
        (SELECT COALESCE(-SUM(amount),0) FROM bs_suppliers_jinsung WHERE product_id = '#product_id#' AND supplier_id = '#supplier_id#' AND date = '2025-01-01')
        +(SELECT COALESCE(SUM(amount),0) FROM bs_purchase_spot WHERE adj_supplier = '#supplier_id#' AND product_id = '#product_id#' AND parent IS NULL AND type='physical' AND noted ='Close-Adjust' AND date BETWEEN '2025-01-01' AND '#sql_date2#')",

        "SELECT 
        (SELECT COALESCE(-SUM((rate_spot)*amount*32.1507),0) FROM bs_sales_spot WHERE ref = '#supplier_id#' AND product_id = '#product_id#' AND type='physical' AND status = 1 AND value_date BETWEEN '2025-01-01' AND '#sql_date2#' )
        +(SELECT COALESCE(SUM((rate_spot)*amount*32.1507),0) FROM bs_purchase_spot WHERE adj_supplier = '#supplier_id#' AND product_id = '#product_id#' AND parent IS NULL AND type='physical' AND noted ='Close-Adjust' AND date BETWEEN '2025-01-01' AND '#sql_date2#')
        +(SELECT COALESCE(SUM(bs_adjust_cost.value_profit),0) FROM bs_adjust_cost  
        LEFT OUTER JOIN bs_purchase_spot ON bs_adjust_cost.id = bs_purchase_spot.adjust_id
        WHERE bs_adjust_cost.date_adjust BETWEEN '2025-01-01' AND '#sql_date2#' AND bs_purchase_spot.adj_supplier = '#supplier_id#' AND product_id = '#product_id#' AND bs_purchase_spot.adjust_type='new')      
        -
        (SELECT COALESCE(-SUM(usd),0) FROM bs_suppliers_jinsung WHERE supplier_id = '#supplier_id#' AND product_id = '#product_id#' AND date = '2025-01-01')"
    ),
    array(
        "supplier",
        "Open Adjust cost (Sell)", //6
        "SELECT (SELECT COALESCE(-SUM(amount),0) FROM bs_sales_spot WHERE  ref = '#supplier_id#' AND product_id = '#product_id#' AND type='physical' AND status = 1 AND value_date = '#sql_date#')",
        "SELECT (SELECT COALESCE(-SUM((rate_spot)*amount*32.1507),0) FROM bs_sales_spot WHERE  ref = '#supplier_id#' AND product_id = '#product_id#' AND type='physical' AND status = 1 AND value_date = '#sql_date#')"
    ),
    array(
        "supplier",
        "Close Adjust cost (Sell)", //7
        "SELECT (SELECT COALESCE(SUM(amount),0) FROM bs_purchase_spot WHERE  adj_supplier = '#supplier_id#' AND product_id = '#product_id#' AND parent IS NULL AND type='physical' AND noted ='Close-Adjust' AND date = '#sql_date#')",
        "SELECT (SELECT COALESCE(SUM((rate_spot)*amount*32.1507),0) FROM bs_purchase_spot WHERE  adj_supplier = '#supplier_id#' AND product_id = '#product_id#' AND parent IS NULL  AND noted ='Close-Adjust' AND type='physical'  AND date = '#sql_date#')"
    ),
    array(
        "supplier",
        "Adjust cost remaining (Sell)", //8
        "SELECT 
    (SELECT COALESCE(-SUM(amount),0) FROM bs_sales_spot WHERE  ref = '#supplier_id#' AND product_id = '#product_id#' AND type='physical' AND status = 1 AND value_date BETWEEN '2025-01-01' AND '#sql_date2#' )
    +(SELECT COALESCE(SUM(amount),0) FROM bs_purchase_spot WHERE  adj_supplier = '#supplier_id#' AND product_id = '#product_id#' AND parent IS NULL AND type='physical' AND noted ='Close-Adjust' AND date BETWEEN '2025-01-01' AND '#sql_date2#')
        -
      (SELECT COALESCE(-SUM(amount),0) FROM bs_suppliers_jinsung WHERE supplier_id = '#supplier_id#' AND product_id = '#product_id#' AND date = '2025-01-01')
    +(SELECT COALESCE(-SUM(amount),0) FROM bs_sales_spot WHERE  ref = '#supplier_id#'  AND product_id = '#product_id#' AND type='physical' AND status = 1 AND value_date = '#sql_date#')
    +(SELECT COALESCE(SUM(amount),0) FROM bs_purchase_spot WHERE  adj_supplier = '#supplier_id#'  AND product_id = '#product_id#' AND parent IS NULL AND type='physical' AND noted ='Close-Adjust' AND date = '#sql_date#')",
        "SELECT 
    (SELECT COALESCE(-SUM((rate_spot)*amount*32.1507),0) FROM bs_sales_spot WHERE  ref = '#supplier_id#'  AND product_id = '#product_id#' AND type='physical' AND status = 1 AND value_date BETWEEN '2025-01-01' AND '#sql_date#' )
     -
      (SELECT COALESCE(-SUM(usd),0) FROM bs_suppliers_jinsung WHERE  supplier_id = '#supplier_id#'  AND product_id = '#product_id#' AND date = '2025-01-01')
    +(SELECT COALESCE(SUM((rate_spot)*amount*32.1507),0) FROM bs_purchase_spot WHERE  adj_supplier = '#supplier_id#' AND product_id = '#product_id#' AND parent IS NULL AND type='physical' AND noted ='Close-Adjust' AND date BETWEEN '2025-01-01' AND '#sql_date#')
    +(SELECT COALESCE(SUM(bs_adjust_cost.value_profit),0) FROM bs_adjust_cost  
        LEFT OUTER JOIN bs_purchase_spot ON bs_adjust_cost.id = bs_purchase_spot.adjust_id
        WHERE bs_adjust_cost.date_adjust   BETWEEN '2025-01-01' AND '#sql_date2#' AND bs_purchase_spot.supplier_id = '#supplier_id#' AND product_id = '#product_id#' AND  bs_purchase_spot.adjust_type='new')"
    )
);
