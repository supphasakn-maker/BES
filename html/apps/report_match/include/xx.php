"Physical Position-Export", //24
"Fixed Stock", //25
"Pending Adjust Cost", //26
"Paid stock (go to stock paid)", //27
"Buy stock (unpaid)", //28
"Hedge (long) (paid) ยกมา", //29
"Hedge (Long)", //30
"Close Hedge (Long) (unpaid)", //31
"Total Hedge (Long) (unpaid)", //32

array(
"supplier",
"Hedge (short) (unpaid) ยกมา", //33
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
"Sales Adj STD", //34
"SELECT (SELECT COALESCE(SUM(amount),0) FROM bs_adjust_amount WHERE date = '#sql_date#' AND supplier_id ='#supplier_id#')",
"SELECT (SELECT COALESCE(SUM(usd),0) FROM bs_adjust_amount WHERE date = '#sql_date#' AND supplier_id ='#supplier_id#')"
),
array(
"supplier",
"Hedge (short) (unpaid)", //35
"SELECT COALESCE(SUM(amount),0) FROM bs_purchase_spot WHERE date = '#sql_date#' AND adj_supplier ='#supplier_id#' AND parent IS NULL AND type='physical' AND ref LIKE '%adj%'",
"SELECT COALESCE(SUM(amount*(rate_spot)*32.1507),0) FROM bs_purchase_spot WHERE date = '#sql_date#' AND adj_supplier ='#supplier_id#' AND parent IS NULL AND type='physical' AND ref LIKE '%adj%' "
),

array(
"supplier",
"Close Hedge (Short) (unpaid)", //36
"SELECT
(SELECT COALESCE(-SUM(amount),0) FROM bs_purchase_spot WHERE date BETWEEN '2023-10-31' AND '#sql_date2#' AND adj_supplier ='#supplier_id#' AND parent IS NULL AND type='physical' AND ref LIKE '%adj%')
+(SELECT COALESCE(-SUM(amount),0) FROM bs_adjust_amount WHERE date BETWEEN '2023-10-31' AND '#sql_date2#' AND supplier_id ='#supplier_id#')",
"SELECT (SELECT COALESCE(-SUM(usd),0) FROM bs_adjust_amount WHERE date BETWEEN '2023-10-31' AND '#sql_date#' AND supplier_id ='#supplier_id#')
+(SELECT COALESCE(-SUM(amount*(rate_spot)*32.1507),0) FROM bs_purchase_spot WHERE date BETWEEN '2023-10-31' AND '#sql_date#' AND adj_supplier ='#supplier_id#' AND parent IS NULL AND type='physical' AND ref LIKE '%adj%')
"
),

"Total Hedge (Short) (unpaid)", //37
"Total Stock (Unpaid)", //38
"Stock (paid) ยกมา", //39
"Stock to adjust", //40
"Stock to physical", //41
"Buy stock (paid)", //42
"Hedge (Long) (paid) ยกมา", //43
"Hedge (Long)", //44
"Close Hedge (Long)", //45
"Total Hedge (Long) (paid)", //46
"Hedge (short) (paid) ยกมา", //47
"Hedge (short) (paid)", //48
"Close Hedge (Short) (paid)", //49
"Total Hedge (Short) (paid)", //50
"Total Stock (Paid)", //51