<?php

$aContent = array(

  array(
    "supplier", //0
    "ยอดยกมา",
    "SELECT +
      (SELECT COALESCE(SUM(amount),0) FROM bs_purchase_spot WHERE date BETWEEN '2025-01-01' AND '2025-01-01' AND supplier_id ='1' AND product_id = '#product_id#'  AND status > 0  AND type != 'defer')
      -
      (SELECT COALESCE(-SUM(amount),0) FROM bs_suppliers_standard WHERE id = '#product_id#' AND date = '2025-01-01')
       -
      (SELECT COALESCE(SUM(bs_incoming_plans.amount),0) FROM bs_incoming_plans LEFT OUTER JOIN bs_reserve_silver ON bs_incoming_plans.amount = bs_reserve_silver.weight_lock WHERE bs_incoming_plans.import_date BETWEEN '2025-01-01' AND '2025-01-01' AND bs_reserve_silver.supplier_id = '1' AND bs_incoming_plans.product_type_id = '#product_id#' )
       +(SELECT COALESCE(SUM(amount),0) FROM bs_purchase_spot WHERE date BETWEEN  '2025-01-01' AND '#sql_date2#' AND supplier_id ='1' AND  product_id = '#product_id#' AND noted ='Normal' AND parent IS NULL AND type='physical' )
      - (SELECT COALESCE(SUM(amount),0) FROM bs_incoming_plans WHERE import_date BETWEEN  '2025-01-01' AND '#sql_date2#' AND supplier_id = '1'  AND product_type_id = '#product_id#')",
    "SELECT 
      (SELECT COALESCE(SUM((rate_spot)*amount*32.1507),0) FROM bs_purchase_spot WHERE date BETWEEN '2025-01-01' AND '2025-01-01' AND supplier_id ='1' AND product_id = '#product_id#'AND status > 0  AND type != 'defer')
       -(SELECT COALESCE(-SUM(usd),0) FROM bs_suppliers_standard WHERE id = '#product_id#' AND date = '2025-01-01')
       +(SELECT COALESCE(SUM((rate_spot+rate_pmdc)*amount*32.1507),0) FROM bs_purchase_spot WHERE product_id = '#product_id#' AND supplier_id ='1' AND noted ='Normal' AND parent IS NULL AND type='physical' AND date BETWEEN  '2025-01-01' AND '#sql_date2#')
       +(SELECT COALESCE(-SUM(usd),0) FROM bs_incoming_plans WHERE import_date BETWEEN  '2025-01-01' AND '#sql_date2#' AND supplier_id = '1'  AND product_type_id = '#product_id#')
       +(SELECT COALESCE(SUM(bs_defer_cost.defer),0)  FROM bs_incoming_plans LEFT OUTER JOIN bs_defer_cost  ON  bs_incoming_plans.defer_id = bs_defer_cost.id
       WHERE bs_defer_cost.date_defer BETWEEN  '2025-01-01' AND '#sql_date2#'  AND bs_defer_cost.supplier_id = '1' AND bs_incoming_plans.product_type_id = '#product_id#')"
  ),
  array(
    "supplier", //1
    "Purchase Physical",
    "SELECT (SELECT COALESCE(SUM(amount),0) FROM bs_purchase_spot WHERE  product_id = '#product_id#' AND parent IS NULL AND type='physical' AND supplier_id ='1' AND noted ='Normal' AND date = '#sql_date#')
   ",
    "SELECT (SELECT COALESCE(SUM((rate_spot+rate_pmdc)*amount*32.1507),0) FROM bs_purchase_spot WHERE product_id = '#product_id#'  AND supplier_id ='1' AND currency ='USD' AND noted ='Normal' AND parent IS NULL AND type='physical' AND date = '#sql_date#')"
  ),
  array( //2
    "supplier",
    "Take Shipment (หัก)",
    "SELECT (SELECT COALESCE(-SUM(amount),0) FROM bs_incoming_plans WHERE import_date = '#sql_date#' AND supplier_id ='1'  AND product_type_id = '#product_id#')",
    "SELECT (SELECT COALESCE(-SUM(usd),0) FROM bs_incoming_plans WHERE import_date = '#sql_date#' AND supplier_id = '1'  AND product_type_id = '#product_id#')",
  ),
  "Total Purchase Available", // 3
  array(
    "supplier",
    "ปิด Defer", //4
    "SELECT '-'",
    "SELECT(SELECT COALESCE(SUM(bs_defer_cost.defer),0)  FROM bs_incoming_plans LEFT OUTER JOIN bs_defer_cost  ON  bs_incoming_plans.defer_id = bs_defer_cost.id
       WHERE bs_defer_cost.date_defer = '#sql_date#'  AND bs_defer_cost.supplier_id = '1' AND bs_incoming_plans.product_type_id = '#product_id#')"
  )
);
