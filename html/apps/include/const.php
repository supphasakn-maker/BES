<?php
  $aContent = array(
    array(
      "supplier",
      "ยอดยกมา",
      "SELECT 'x'",
      "SELECT 'x'"
    ), 
    array(
      "supplier",
      "ซื้อคืนรอตัด",
      "SELECT '-'",
      "SELECT '-'"
    ), 
    array(
      "supplier",
      "(หัก ซื้อคืนรอตัด)",
      "SELECT '-'",
      "SELECT '-'"
    ), 
    array(
      "supplier",
      "Adjust to Physical",
      "SELECT '-'",
      "SELECT '-'"
    ), 
    array(
      "supplier",
      "Adjust Cost",
      "SELECT FORMAT(-SUM(amount),4) FROM bs_sales_spot WHERE value_date = '#sql_date#' AND supplier_id ='#supplier_id#' AND adjust_id IS NOT NULL",
      "SELECT FORMAT(-SUM(amount*(rate_spot+rate_pmdc)*32.1507),4) FROM bs_sales_spot WHERE value_date = '#sql_date#' AND supplier_id ='#supplier_id#' AND adjust_id IS NOT NULL"
    ), 
    array(
      "supplier",
      "Hedge to physical",
      "SELECT '-'",
      "SELECT '-'"
    ),
    array(
      "supplier",
      "Adjust Stock to Physical",
      "SELECT FORMAT(SUM(amount),4) FROM bs_purchase_spot WHERE date = '#sql_date#' AND supplier_id ='#supplier_id#' AND parent IS NULL AND type='physical-adjust'",
      "SELECT FORMAT(SUM(amount*(rate_spot+rate_pmdc)*32.1507),4) FROM bs_purchase_spot WHERE date = '#sql_date#' AND supplier_id ='#supplier_id#' AND parent IS NULL AND type='physical-adjust'"
    ),
    array(
      "supplier",
      "Purchase Physical",
      "SELECT FORMAT(SUM(amount),4) FROM bs_purchase_spot WHERE date = '#sql_date#' AND supplier_id ='#supplier_id#' AND parent IS NULL AND type='physical'",
      "SELECT FORMAT(SUM(amount*(rate_spot+rate_pmdc)*32.1507),4) FROM bs_purchase_spot WHERE date = '#sql_date#' AND supplier_id ='#supplier_id#' AND parent IS NULL AND type='physical'"
    ), 
    "Purchase Physical-Export", 
    "Hedge (Long)", 
    "Silver Pending",
    array(
      "supplier",
      "Take Shipment  STD (หัก)",
      "SELECT FORMAT(-SUM(amount),4) FROM bs_imports WHERE delivery_date = '#sql_date#' AND supplier_id ='#supplier_id#' AND parent IS NULL",
      "SELECT IF(
        (SELECT COUNT(id) FROM bs_purchase_spot WHERE date = '#sql_date#' AND supplier_id ='#supplier_id#' AND type ='defer') > 0,
        (SELECT FORMAT(-SUM(amount*(rate_spot+rate_pmdc)*32.1507),4) FROM bs_purchase_spot WHERE date = '#sql_date#' AND supplier_id ='#supplier_id#' AND type ='defer'),
        ( SELECT FORMAT(-SUM(bs_purchase_spot.amount*(bs_purchase_spot.rate_spot+bs_purchase_spot.rate_pmdc)*32.1507),4) 
        FROM bs_imports LEFT JOIN bs_purchase_spot ON bs_purchase_spot.import_id = bs_imports.id
        WHERE bs_imports.delivery_date = '#sql_date#' AND bs_imports.supplier_id ='#supplier_id#' AND bs_imports.parent IS NULL)
      )"
    ), 
    "Deferred stock - market value", 
    array(
      "supplier",
      "Fund in - ปิด Defer",
      "SELECT '-'",
      "SELECT IF(
        (SELECT COUNT(id) FROM bs_purchase_spot WHERE date = '#sql_date#' AND supplier_id ='#supplier_id#' AND type ='defer') > 0,
        FORMAT(
          (SELECT SUM(amount*(rate_spot+rate_pmdc)*32.1507) FROM bs_purchase_spot WHERE date = '#sql_date#' AND supplier_id ='#supplier_id#' AND type ='defer')
           - 
          (SELECT SUM(bs_purchase_spot.amount*(bs_purchase_spot.rate_spot+bs_purchase_spot.rate_pmdc)*32.1507)
            FROM bs_imports LEFT JOIN bs_purchase_spot ON bs_purchase_spot.import_id = bs_imports.id
            WHERE bs_imports.delivery_date = '#sql_date#' AND bs_imports.supplier_id ='#supplier_id#' AND bs_imports.parent IS NULL)
        ,4),
        (SELECT '-')
      )"
    ),
    "Hedge (short)", 
    "Different Discount", 
    "Total Pending Available", 
    "Estimate cost for uncover silver", 
    "Transfer to other party", 
    "เงินมัดจำ", 
    "ดอกเบี้ย และ ส่วนต่าง discount",
    "Total Physical untaking Position", 
    "STBLC", 
    array(
      "total",
      "แท่งเงินเข้าเซฟ (ยังไม่ได้หลอม)",
      "SELECT REPLACE(JSON_EXTRACT(data, '$.bar_in_safe'),'\"','') FROM bs_match_data WHERE date = '#sql_date#' ",
      "SELECT 'x'"
    ), 
    array(
      "total",
      "เม็ดเงินที่หลอมวันนี้",
      "SELECT REPLACE(JSON_EXTRACT(data, '$.grain_in'),'\"','') FROM bs_match_data WHERE date = '#sql_date#' ",
      "SELECT 'x'",
    ),
    array(
      "total",
      "เม็ดเงินที่หลอมวันนี้ (จ้างหลอม)",
      "SELECT REPLACE(JSON_EXTRACT(data, '$.grain_in_pmr'),'\"','') FROM bs_match_data WHERE date = '#sql_date#' ",
      "SELECT 'x'"
    ), 
    array(
      "total",
      "Silver Stock (Recycle)",
      "SELECT REPLACE(JSON_EXTRACT(data, '$.stock_recycle'),'\"','') FROM bs_match_data WHERE date = '#sql_date#' ",
      "SELECT 'x'"
    ), 
    array(
      "total",
      "Silver Stock (เม็ด)",
      "SELECT REPLACE(JSON_EXTRACT(data, '$.stock_grain'),'\"','') FROM bs_match_data WHERE date = '#sql_date#' ",
      "SELECT 'x'"
    ), 
    array(
      "total",
      "Silver Stock (แท่ง)",
      "SELECT REPLACE(JSON_EXTRACT(data, '$.stock_bar'),'\"','') FROM bs_match_data WHERE date = '#sql_date#' ",
      "SELECT 'x'"
    ), 
    array(
      "total",
      "Silver Stock (PMR)",
      "SELECT REPLACE(JSON_EXTRACT(data, '$.stock_pmr'),'\"','') FROM bs_match_data WHERE date = '#sql_date#' ",
      "SELECT 'x'"
    ),  
    array(
      "total",
      "ปะการัง + ผงเงิน ในเซฟ",
      "SELECT REPLACE(JSON_EXTRACT(data, '$.remain'),'\"','') FROM bs_match_data WHERE date = '#sql_date#' ",
      "SELECT 'x'"
    )  
    array(
      "total",
      "ของรอเข้า(จ่ายเงินแล้ว)",
      "SELECT REPLACE(JSON_EXTRACT(data, '$.prepared_paid'),'\"','') FROM bs_match_data WHERE date = '#sql_date#' ",
      "SELECT 'x'"
    ),
    array(
      "total",
      "Sigmargin Position",
      "SELECT REPLACE(JSON_EXTRACT(data, '$.sigmargin'),'\"','') FROM bs_match_data WHERE date = '#sql_date#' ",
      "SELECT 'x'"
    ), 
    array(
      "total",
      "Trade Buy",
      "SELECT 2000+500+500+200+200+100+200+200+200+1000+200+200+100+200+200+200+200+200+200+200+100+100+100+100+200+200+200+200+200+200+200+100+200+900",
      "SELECT '-'",
      "BES - Profit trade - Total long"
    ),
    array(
      "total",
      "Trade Sell",
      "SELECT -500-1000-1000-1000-500-500-200-200-100-200-200-200-200-500-500-100-200-200-200-200-200-200-200-200-200-200-200-100-100-200-200-100-200",
      "SELECT '-'",
      "BES - Profit trade - Total short"
    ),
    array(
      "total",
      "Physical Position",
      "SELECT REPLACE(JSON_EXTRACT(data, '$.physical'),'\"','') FROM bs_match_data WHERE date = '#sql_date#' ",
      "SELECT '-'",
      "Balance kilo From Overview"
    ),
    "Physical Position-Export", 
    "Fixed Stock", 
    "Pending Adjust Cost", 
    "Paid stock  (go to stock paid)", 
    "Buy stock (unpaid)", 
    "Hedge (long) (paid) ยกมา", 
    "Hedge (Long)", 
    "Close Hedge (Long) (unpaid)", 
    "Total Hedge (Long) (unpaid)", 
    "Hedge (short) (unpaid) ยกมา", 
    "Hedge (short) (unpaid)", 
    "Close Hedge (Short) (unpaid)", 
    "Total Hedge (Short) (unpaid)", 
    "Total Stock (Unpaid)", 
    "Stock (paid) ยกมา", 
    "Stock to adjust", 
    "Stock to physical", 
    "Buy stock (paid)", 
    "Hedge (Long) (paid) ยกมา", 
    "Hedge (Long)", 
    "Close Hedge (Long)", 
    "Total Hedge (Long) (paid)", 
    "Hedge (short) (paid) ยกมา", 
    "Hedge (short) (paid)", 
    "Close Hedge (Short) (paid)", 
    "Total Hedge (Short) (paid)", 
    "Total Stock (Paid)", 
    "Total Silver on hand", 
    "Physical Hedge", 
    array(
      "total",
      "Customer Pending",
      "SELECT '-'",
      "SELECT '-'",
      "C/F จากเมื่อวานในหน้า Sales Overview"
    ),
    "Total Silver Balance (Thailand)");
?>