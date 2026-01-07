<?php
$dt = date($_POST['date']);

$date3 = date("Y-m-d", strtotime("-1 day", strtotime($dt)));


$sql = "SELECT SUM(balance) AS balance, SUM(credit) AS credit
FROM bs_transfer_report WHERE bank = 'SCB' AND  bs_transfer_report.date = '" . $date3 . "' ";
$rst = $dbc->Query($sql);
$transferscb = $dbc->Fetch($rst);
$balancescb = $transferscb['balance'];
$total_credit_scb = $transferscb['credit'];

$sql = "SELECT SUM(balance) AS balance, SUM(credit) AS credit
FROM bs_transfer_report WHERE bank = 'KBANK' AND  bs_transfer_report.date = '" . $date3 . "' ";
$rst = $dbc->Query($sql);
$transferkbank = $dbc->Fetch($rst);
$balancekbank = $transferkbank['balance'];
$total_credit_kbank = $transferkbank['credit'];

$sql = "SELECT SUM(balance) AS balance, SUM(credit) AS credit
FROM bs_transfer_report WHERE bank = 'BBL' AND  bs_transfer_report.date = '" . $date3 . "' ";
$rst = $dbc->Query($sql);
$transferbbl = $dbc->Fetch($rst);
$balancebbl = $transferbbl['balance'];
$total_credit_bbl = $transferbbl['credit'];

$sql = "SELECT SUM(balance) AS balance, SUM(credit) AS credit
FROM bs_transfer_report WHERE bank = 'BAY' AND  bs_transfer_report.date = '" . $date3 . "' ";
$rst = $dbc->Query($sql);
$transferbay = $dbc->Fetch($rst);
$balancebay  = $transferbay['balance'];
$total_credit_bay  = $transferbay['credit'];



$sql = "SELECT SUM(balance) AS balance, SUM(credit) AS credit
FROM bs_transfer_report WHERE bank = 'SCB' AND  bs_transfer_report.date = '" . $dt . "' ";
$rst = $dbc->Query($sql);
$transferscb = $dbc->Fetch($rst);
$balancescb_now = $transferscb['balance'];
$total_credit_scb_now = $transferscb['credit'];

$sql = "SELECT SUM(balance) AS balance, SUM(credit) AS credit
FROM bs_transfer_report WHERE bank = 'KBANK' AND  bs_transfer_report.date = '" . $dt . "' ";
$rst = $dbc->Query($sql);
$transferkbank = $dbc->Fetch($rst);
$balancekbank_now = $transferkbank['balance'];
$total_credit_kbank_now = $transferkbank['credit'];

$sql = "SELECT SUM(balance) AS balance, SUM(credit) AS credit
FROM bs_transfer_report WHERE bank = 'BBL' AND  bs_transfer_report.date = '" . $dt . "' ";
$rst = $dbc->Query($sql);
$transferbbl = $dbc->Fetch($rst);
$balancebbl_now = $transferbbl['balance'];
$total_credit_bbl_now = $transferbbl['credit'];

$sql = "SELECT SUM(balance) AS balance, SUM(credit) AS credit
FROM bs_transfer_report WHERE bank = 'BAY' AND  bs_transfer_report.date = '" . $dt . "' ";
$rst = $dbc->Query($sql);
$transferbay = $dbc->Fetch($rst);
$balancebay_now  = $transferbay['balance'];
$total_credit_bay_now  = $transferbay['credit'];



$scb_rate = $os->load_variable("scb_rate");
$scb_paid = $os->load_variable("scb_paid");

$kbank_rate = $os->load_variable("kbank_rate");

$bbl_rate = $os->load_variable("bbl_rate");
$bbl_paid = $os->load_variable("bbl_paid");

$bay_rate = $os->load_variable("bay_rate");


$date2 = date("Y-m-d", strtotime("-90 day", strtotime($dt)));


///ภาระ TR ยกมา///
$aSumscbplus = array(0);
$sql = "SELECT * FROM bs_transfers WHERE bank = 'SCB' ";
$rst = $dbc->Query($sql);
while ($transfer = $dbc->Fetch($rst)) {

    $sql = "SELECT SUM(paid) AS paid FROM bs_transfer_payments WHERE currency = 'THB'  AND bs_transfer_payments.date  = '" . $dt . "' AND transfer_id=" . $transfer['id'];
    $rst_usd = $dbc->Query($sql);
    while ($scb = $dbc->Fetch($rst_usd)) {

        $aSumscbplus[0] += $scb['paid'];
    }
}

$aSumkbank = array(0);
$sql = "SELECT * FROM bs_transfers WHERE bank = 'KBANK' ";
$rst = $dbc->Query($sql);
while ($transfer = $dbc->Fetch($rst)) {

    $sql = "SELECT SUM(paid) AS paid FROM bs_transfer_payments WHERE currency = 'THB' AND bs_transfer_payments.date  = '" . $dt . "' AND transfer_id=" . $transfer['id'];
    $rst_usd = $dbc->Query($sql);
    while ($kbank = $dbc->Fetch($rst_usd)) {

        $aSumkbank[0] += $kbank['paid'];
    }
}

$aSumbbl = array(0);
$sql = "SELECT * FROM bs_transfers WHERE bank = 'BBL' ";
$rst = $dbc->Query($sql);
while ($transfer = $dbc->Fetch($rst)) {

    $sql = "SELECT SUM(paid) AS paid FROM bs_transfer_payments WHERE currency = 'THB' AND bs_transfer_payments.date  = '" . $dt . "' AND transfer_id=" . $transfer['id'];
    $rst_usd = $dbc->Query($sql);
    while ($bbl = $dbc->Fetch($rst_usd)) {

        $aSumbbl[0] += $bbl['paid'];
    }
}

$aSumbay = array(0);
$sql = "SELECT * FROM bs_transfers WHERE bank = 'BAY' ";
$rst = $dbc->Query($sql);
while ($transfer = $dbc->Fetch($rst)) {

    $sql = "SELECT SUM(paid) AS paid FROM bs_transfer_payments WHERE currency = 'THB' AND bs_transfer_payments.date  = '" . $dt . "' AND transfer_id=" . $transfer['id'];
    $rst_usd = $dbc->Query($sql);
    while ($bay = $dbc->Fetch($rst_usd)) {

        $aSumbay[0] += $bay['paid'];
    }
}

///ปิด///


/// เปิด OPEN TR FIX ยกมา///

$aSumSCBFIX = array(0);
$sql = "SELECT bs_purchase_usd.rate_exchange AS rate_exchange ,bs_purchase_usd.amount AS amount
    FROM bs_transfers 
    LEFT OUTER JOIN bs_transfer_usd ON bs_transfers.id = bs_transfer_usd.transfer_id
    LEFT OUTER JOIN bs_purchase_usd ON bs_transfer_usd.purchase_id = bs_purchase_usd.id
    WHERE bs_transfer_usd.date = '" . $dt . "' AND bs_transfers.bank = 'SCB'";
$rst = $dbc->Query($sql);
while ($transfer = $dbc->Fetch($rst)) {
    $total_usd_rate_exchange = $transfer['rate_exchange'] * $transfer['amount'];
    $aSumSCBFIX[0] += $total_usd_rate_exchange;
}

$aSumBBLFIX = array(0);
$sql = "SELECT bs_purchase_usd.rate_exchange AS rate_exchange ,bs_purchase_usd.amount AS amount
     FROM bs_transfers 
     LEFT OUTER JOIN bs_transfer_usd ON bs_transfers.id = bs_transfer_usd.transfer_id
     LEFT OUTER JOIN bs_purchase_usd ON bs_transfer_usd.purchase_id = bs_purchase_usd.id
     WHERE bs_transfer_usd.date = '" . $dt . "' AND bs_transfers.bank = 'BBL'";
$rst = $dbc->Query($sql);
while ($transfer = $dbc->Fetch($rst)) {

    $total_usd_rate_exchange = $transfer['rate_exchange'] * $transfer['amount'];
    $aSumBBLFIX[0] += $total_usd_rate_exchange;
}

$aSumKBANKFIX = array(0);
$sql = "SELECT bs_purchase_usd.rate_exchange AS rate_exchange ,bs_purchase_usd.amount AS amount
      FROM bs_transfers 
      LEFT OUTER JOIN bs_transfer_usd ON bs_transfers.id = bs_transfer_usd.transfer_id
      LEFT OUTER JOIN bs_purchase_usd ON bs_transfer_usd.purchase_id = bs_purchase_usd.id
      WHERE bs_transfer_usd.date = '" . $dt . "' AND bs_transfers.bank = 'KBANK'";
$rst = $dbc->Query($sql);
while ($transfer = $dbc->Fetch($rst)) {

    $total_usd_rate_exchange = $transfer['rate_exchange'] * $transfer['amount'];
    $aSumKBANKFIX[0] += $total_usd_rate_exchange;
}

$aSumBAYFIX = array(0);
$sql = "SELECT bs_purchase_usd.rate_exchange AS rate_exchange ,bs_purchase_usd.amount AS amount
      FROM bs_transfers 
      LEFT OUTER JOIN bs_transfer_usd ON bs_transfers.id = bs_transfer_usd.transfer_id
      LEFT OUTER JOIN bs_purchase_usd ON bs_transfer_usd.purchase_id = bs_purchase_usd.id
      WHERE bs_transfer_usd.date = '" . $dt . "' AND bs_transfers.bank = 'BAY'";
$rst = $dbc->Query($sql);
while ($transfer = $dbc->Fetch($rst)) {

    $total_usd_rate_exchange = $transfer['rate_exchange'] * $transfer['amount'];
    $aSumBAYFIX[0] += $total_usd_rate_exchange;
}

/// ปิด///


$sql = "SELECT SUM(value_thb_net) AS thbnet , SUM(paid_thb) AS thbpaid , SUM(value_usd_nonfixed) AS nonfix , 
    SUM(paid_usd) AS usdPaid, SUM(value_usd_paid) AS vusd
    FROM bs_transfers WHERE bank = 'SCB' AND  bs_transfers.date BETWEEN '" . $date2 . "' AND '" . $date3 . "'";
$rst = $dbc->Query($sql);
$transferscb = $dbc->Fetch($rst);
$total_usd_paid = $transferscb['usdPaid'] + $transferscb['vusd'];
$valuenonfix = $transferscb['nonfix'] - $total_usd_paid;
$total_remain_creditscb = $transferscb['thbnet'] - $transferscb['thbpaid'] + $valuenonfix * $scb_rate;


$sql = " SELECT SUM(value_thb_net) AS thbnet , SUM(paid_thb) AS thbpaid , SUM(value_usd_nonfixed) AS nonfix , 
    SUM(paid_usd) AS usdPaid, SUM(value_usd_paid) AS vusd
    FROM bs_transfers WHERE bank = 'KBANK' AND  bs_transfers.date BETWEEN '" . $date2 . "' AND '" . $date3 . "'";
$rst = $dbc->Query($sql);
$transferkbank = $dbc->Fetch($rst);
$total_usd_paid = $transferkbank['usdPaid'] + $transferkbank['vusd'];
$valuenonfix = $transferkbank['nonfix'] - $total_usd_paid;
$total_remain_creditkbank = $transferkbank['thbnet'] - $transferkbank['thbpaid'] + $valuenonfix * $kbank_rate;


$sql = " SELECT SUM(value_thb_net) AS thbnet , SUM(paid_thb) AS thbpaid , SUM(value_usd_nonfixed) AS nonfix , 
    SUM(paid_usd) AS usdPaid, SUM(value_usd_paid) AS vusd
    FROM bs_transfers WHERE bank = 'BBL' AND  bs_transfers.date BETWEEN '" . $date2 . "' AND '" . $date3 . "'";
$rst = $dbc->Query($sql);
$transferbbl = $dbc->Fetch($rst);
$total_usd_paid = $transferbbl['usdPaid'] + $transferbbl['vusd'];
$valuenonfix = $transferbbl['nonfix'] - $total_usd_paid;
$total_remain_creditbbl = $transferbbl['thbnet'] - $transferbbl['thbpaid'] + $valuenonfix * $bbl_rate;


$aSumscb = array(0);
$sql = "SELECT * FROM bs_transfers WHERE bank = 'SCB' ";
$rst = $dbc->Query($sql);
while ($transfer = $dbc->Fetch($rst)) {

    $sql = "SELECT SUM(paid) AS paid FROM bs_transfer_payments WHERE currency = 'THB' AND bs_transfer_payments.date = '" . $dt . "' AND transfer_id=" . $transfer['id'];
    $rst_usd = $dbc->Query($sql);
    while ($scb = $dbc->Fetch($rst_usd)) {

        $aSumscb[0] += $scb['paid'];
    }
}


$aSumscbnonfix = array(0, 0);
$sql = "SELECT * FROM bs_transfers WHERE bank = 'SCB' AND  bs_transfers.date BETWEEN '" . $date2 . "' AND '" . $dt . "'";
$rst = $dbc->Query($sql);
while ($scbnonfix = $dbc->Fetch($rst)) {

    $total_usd_paid = $scbnonfix['paid_usd'] + $scbnonfix['value_usd_paid'];

    $aSumscbnon = $scbnonfix['value_usd_nonfixed'] - $total_usd_paid;

    $aSumscbnonfix[0] += $aSumscbnon * $scb_rate;

    $aSumscbnonfix[1] += $aSumscbnon;
}

$aSumscbnonfix1 = array(0, 0);
$sql = "SELECT * FROM bs_transfers WHERE bank = 'SCB' AND  bs_transfers.date = '" . $dt . "'";
$rst = $dbc->Query($sql);
while ($scbnonfix = $dbc->Fetch($rst)) {

    $total_usd_paid = $scbnonfix['paid_usd'] + $scbnonfix['value_usd_paid'];

    $aSumscbnon = $scbnonfix['value_usd_nonfixed'] - $total_usd_paid;

    $aSumscbnonfix1[0] += $aSumscbnon  * $scb_rate;

    $valuescb = $scbnonfix['value_usd_fixed'];

    $aascb = $scbnonfix['value_usd_total'] + $scbnonfix['value_usd_paid'];

    $aSumscbnonfix1[1] += $aascb;
}


$aSumkbanknonfix = array(0, 0);
$sql = "SELECT * FROM bs_transfers WHERE bank = 'KBANK' AND  bs_transfers.date = '" . $dt . "'";
$rst = $dbc->Query($sql);
while ($kbanknonfix = $dbc->Fetch($rst)) {

    $total_usd_paid = $kbanknonfix['paid_usd'] + $kbanknonfix['value_usd_paid'];

    $aSumkbanknon = $kbanknonfix['value_usd_nonfixed'] - $total_usd_paid;

    $aSumkbanknonfix[0] += $aSumkbanknon * $kbank_rate;
    $aSumkbanknonfix[1] += $aSumkbanknon;
}

$aSumbblnonfix = array(0, 0);
$sql = "SELECT * FROM bs_transfers WHERE bank = 'BBL' AND  bs_transfers.date BETWEEN '" . $date2 . "' AND '" . $dt . "'";
$rst = $dbc->Query($sql);
while ($bblnonfix = $dbc->Fetch($rst)) {

    $total_usd_paid = $bblnonfix['paid_usd'] + $bblnonfix['value_usd_paid'];

    $aSumbblnon = $bblnonfix['value_usd_nonfixed'] - $total_usd_paid;

    $aSumbblnonfix[0] += $aSumbblnon * $bbl_rate;
    $aSumbblnonfix[1] += $aSumbblnon;
}

$aSumbblnonfix1 = array(0, 0);
$sql = "SELECT * FROM bs_transfers WHERE bank = 'BBL' AND  bs_transfers.date = '" . $dt . "'";
$rst = $dbc->Query($sql);
while ($bblnonfix = $dbc->Fetch($rst)) {

    $total_usd_paid = $bblnonfix['paid_usd'] + $bblnonfix['value_usd_paid'];

    $aSumscbnon = $bblnonfix['value_usd_nonfixed'] - $total_usd_paid;

    $aSumbblnonfix1[0] += $aSumscbnon  * $bbl_rate;

    $valuescb = $bblnonfix['value_usd_fixed'];

    $aascb = $bblnonfix['value_usd_total'] + $bblnonfix['value_usd_paid'];

    $aSumbblnonfix1[1] += $aascb;
}

$aSumbaynonfix = array(0, 0);
$sql = "SELECT * FROM bs_transfers WHERE bank = 'BAY' AND  bs_transfers.date BETWEEN '" . $date2 . "' AND '" . $dt . "'";
$rst = $dbc->Query($sql);
while ($baynonfix = $dbc->Fetch($rst)) {

    $total_usd_paid = $baynonfix['paid_usd'] + $baynonfix['value_usd_paid'];

    $aSumbaynon = $baynonfix['value_usd_nonfixed'] - $total_usd_paid;

    $aSumbaynonfix[0] += $aSumbaynon * $bay_rate;
    $aSumbaynonfix[1] += $aSumbaynon;
}

$totaltr = 220000000 + 10000000 + 350000000 + 70000000;


$total_remain_creditscb1 =  $total_remain_creditscb  - $aSumscb[0];
$total_remain_creditkbank1 = $total_remain_creditkbank - $aSumkbank[0];
$total_remain_creditbbl1 = $total_remain_creditbbl - $aSumbbl[0];
$aSumtrSCB = $total_remain_creditscb1 + $aSumscb[0] + $aSumscbnonfix1[0] * $scb_rate;
$aSumtrKBANK = $total_remain_creditkbank1 + $aSumkbank[0] + $aSumkbanknonfix[0] * $kbank_rate;
$aSumtrBBL = $total_remain_creditbbl1 + $aSumbbl[0] + $aSumbblnonfix1[0] * $bbl_rate;

$aSubDSCB = 220000000 - $aSumtrSCB;
$aSubDKBANK = 10000000 - $aSumtrKBANK;
$aSubDBBL =  350000000 - $aSumtrBBL;

$SumtotalTR = $balancescb_now + $balancekbank_now + $balancebbl_now + $balancebay_now;
$SumtotalDept = $total_credit_scb_now + $total_credit_kbank_now +  $total_credit_bbl_now + $total_credit_bay_now;

$totalnonfix = $aSumscbnonfix[0] + $aSumkbanknonfix[0] + $aSumbblnonfix[0] + $aSumbaynonfix[0];
$totalnonfix1 = $aSumscbnonfix[1] + $aSumkbanknonfix[1] + $aSumbblnonfix[1] + $aSumbaynonfix[1];


$str = date("Y-m-d", strtotime("first day of this month", strtotime($dt)));
////////////
$aSumINPAIDSCB = array(0);
$sql = "SELECT bs_transfer_payments.interest AS interest
    FROM bs_transfer_payments 
    LEFT JOIN bs_transfers ON bs_transfer_payments.transfer_id = bs_transfers.id 
    WHERE bs_transfers.bank = 'SCB'  AND bs_transfer_payments.date  BETWEEN   '" . $str . "' AND '" . $dt . "'";
$rst = $dbc->Query($sql);
while ($paidscb = $dbc->Fetch($rst)) {
    $aSumINPAIDSCB[0] += $paidscb['interest'];
}



$aSumINPAIDKBANK = array(0);
$sql = "SELECT bs_transfer_payments.interest AS interest
    FROM bs_transfer_payments 
    LEFT JOIN bs_transfers ON bs_transfer_payments.transfer_id = bs_transfers.id 
    WHERE bs_transfers.bank = 'KBANK'   AND bs_transfer_payments.date  BETWEEN   '" . $str . "' AND '" . $dt . "'";
$rst = $dbc->Query($sql);
while ($paidkbank = $dbc->Fetch($rst)) {
    $aSumINPAIDKBANK[0] += $paidkbank['interest'];
}


$aSumINPAIDBBL = array(0);
$sql = "SELECT bs_transfer_payments.interest AS interest
    FROM bs_transfer_payments 
    LEFT JOIN bs_transfers ON bs_transfer_payments.transfer_id = bs_transfers.id 
    WHERE bs_transfers.bank = 'BBL'   AND bs_transfer_payments.date  BETWEEN   '" . $str . "' AND '" . $dt . "'";
$rst = $dbc->Query($sql);
while ($paidbbl = $dbc->Fetch($rst)) {

    $aSumINPAIDBBL[0] += $paidbbl['interest'];
}


$aSumINPAIDBAY = array(0);
$sql = "SELECT bs_transfer_payments.interest AS interest
    FROM bs_transfer_payments 
    LEFT JOIN bs_transfers ON bs_transfer_payments.transfer_id = bs_transfers.id 
    WHERE bs_transfers.bank = 'BAY'  AND bs_transfer_payments.date  BETWEEN   '" . $str . "' AND '" . $dt . "'";
$rst = $dbc->Query($sql);
while ($paidbay = $dbc->Fetch($rst)) {
    $aSumINPAIDBAY[0] += $paidbay['interest'];
}



$aSumPAIDSCB = array(0);
$sql = "SELECT bs_transfer_payments.date AS date, 
    bs_transfer_payments.interest AS interest,
    bs_transfer_payments.currency AS currency 
    FROM bs_transfer_payments 
    LEFT JOIN bs_transfers ON bs_transfer_payments.transfer_id = bs_transfers.id 
    WHERE bs_transfers.bank = 'SCB' AND bs_transfer_payments.date  = '" . $dt . "' ";
$rst = $dbc->Query($sql);
while ($paidinscb = $dbc->Fetch($rst)) {

    $aSumPAIDSCB[0] += $paidinscb['interest'];
}

$aSumPAIDSKBANK = array(0);
$sql = "SELECT bs_transfer_payments.date AS date, 
    bs_transfer_payments.interest AS interest,
    bs_transfer_payments.currency AS currency 
    FROM bs_transfer_payments 
    LEFT JOIN bs_transfers ON bs_transfer_payments.transfer_id = bs_transfers.id 
    WHERE bs_transfers.bank = 'KBANK'  AND bs_transfer_payments.date = '" . $dt . "'";
$rst = $dbc->Query($sql);
while ($paidinkbank = $dbc->Fetch($rst)) {

    $aSumPAIDSKBANK[0] += $paidinkbank['interest'];
}

$aSumPAIDBBL = array(0);
$sql = "SELECT bs_transfer_payments.date AS date, 
    bs_transfer_payments.interest AS interest,
    bs_transfer_payments.currency AS currency 
    FROM bs_transfer_payments 
    LEFT JOIN bs_transfers ON bs_transfer_payments.transfer_id = bs_transfers.id 
    WHERE bs_transfers.bank = 'BBL'  AND bs_transfer_payments.date  = '" . $dt . "'";
$rst = $dbc->Query($sql);
while ($paidinbbl = $dbc->Fetch($rst)) {

    $aSumPAIDBBL[0] += $paidinbbl['interest'];
}






$aSumPAIDBAY = array(0);
$sql = "SELECT bs_transfer_payments.date AS date, 
    bs_transfer_payments.interest AS interest,
    bs_transfer_payments.currency AS currency 
    FROM bs_transfer_payments 
    LEFT JOIN bs_transfers ON bs_transfer_payments.transfer_id = bs_transfers.id 
    WHERE bs_transfers.bank = 'BAY' AND bs_transfer_payments.date  = '" . $dt . "' ";
$rst = $dbc->Query($sql);
while ($paidinbay = $dbc->Fetch($rst)) {

    $aSumPAIDBAY[0] += $paidinbay['interest'];
}

$sql = "SELECT
    SUM(bs_transfers.value_thb_net-paid_thb) AS unpaid,
    SUM(value_thb_net) AS net,
    SUM(paid_thb) AS paid,
    SUM(paid_usd * rate_counter) AS paid_usd,
    SUM(value_usd_nonfixed) AS value_nonfixed_thb,
    COUNT(bs_transfers.id) AS total_tr
    FROM bs_transfers
    WHERE bs_transfers.bank ='SCB'";
$rst = $dbc->Query($sql);
$scb = $dbc->Fetch($rst);
$pbscb = $scb['unpaid'] + $scb['value_nonfixed_thb'] * $scb_rate;

$sql = "SELECT
    SUM(bs_transfers.value_thb_net-paid_thb) AS unpaid,
    SUM(value_thb_net) AS net,
    SUM(paid_thb) AS paid,
    SUM(paid_usd * rate_counter) AS paid_usd,
    SUM(value_usd_nonfixed) AS value_nonfixed_thb,
    COUNT(bs_transfers.id) AS total_tr
    FROM bs_transfers
    WHERE bs_transfers.bank ='KBANK'";
$rst = $dbc->Query($sql);
$kbank = $dbc->Fetch($rst);
$pbkbank = $kbank['unpaid'] + $kbank['value_nonfixed_thb'] * $kbank_rate;

$sql = "SELECT
    SUM(bs_transfers.value_thb_net-paid_thb) AS unpaid,
    SUM(value_thb_net) AS net,
    SUM(paid_thb) AS paid,
    SUM(paid_usd * rate_counter) AS paid_usd,
    SUM(value_usd_nonfixed) AS value_nonfixed_thb,
    COUNT(bs_transfers.id) AS total_tr
    FROM bs_transfers
    WHERE bs_transfers.bank ='BBL'";
$rst = $dbc->Query($sql);
$bbl = $dbc->Fetch($rst);
$pbbbl = $bbl['unpaid'] + $bbl['value_nonfixed_thb'] * $bbl_rate;


$aSumNONFIXSCB = array(0);
$sql = "SELECT * FROM bs_transfers WHERE bank = 'SCB' AND date = '" . $dt . "'";
$rst = $dbc->Query($sql);
while ($scb = $dbc->Fetch($rst)) {

    $aSumNONFIXSCB[0] += $scb['value_thb_net'];
}

$aSumNONFIXKBANK = array(0);
$sql = "SELECT * FROM bs_transfers WHERE bank = 'KBANK' AND date = '" . $dt . "'";
$rst = $dbc->Query($sql);
while ($kbank = $dbc->Fetch($rst)) {

    $aSumNONFIXKBANK[0] += $kbank['value_thb_net'];
}

$aSumNONFIXBBL = array(0);
$sql = "SELECT * FROM bs_transfers WHERE bank = 'BBL' AND date = '" . $dt . "'";
$rst = $dbc->Query($sql);
while ($bbl = $dbc->Fetch($rst)) {

    $aSumNONFIXBBL[0] += $bbl['value_thb_net'];
}

$aSumscbTR = array(0, 0);
$sql = "SELECT * FROM bs_transfers WHERE bank = 'SCB' AND  bs_transfers.date BETWEEN '" . $date2 . "' AND '" . $dt . "'";
$rst = $dbc->Query($sql);
while ($scbnonfix = $dbc->Fetch($rst)) {
    $tb = $scbnonfix['value_thb_net'];
    $aSumscbTR[0] += $tb;
}

$aSumPAIDTOTAL = $aSumINPAIDSCB[0] - $aSumPAIDSCB[0] + $aSumINPAIDKBANK[0] - $aSumPAIDSKBANK[0] + $aSumINPAIDBBL[0] - $aSumPAIDBBL[0] + $aSumINPAIDBAY[0] - $aSumPAIDBAY[0];

$TOTALPAID = $aSumPAIDSCB[0] + $aSumPAIDSKBANK[0] + $aSumPAIDBBL[0] + $aSumPAIDBAY[0];

$TOTALINSCB = $aSumINPAIDSCB[0] +  $aSumPAIDSCB[0];
$TOTALINKBANK = $aSumINPAIDKBANK[0] +  $aSumPAIDSKBANK[0];
$TOTALINBBL = $aSumINPAIDBBL[0] +  $aSumPAIDBBL[0];

$TOTALINBAY = $aSumINPAIDBAY[0] +  $aSumPAIDBAY[0];

$TOTALALL =  $aSumINPAIDSCB[0] + $aSumINPAIDKBANK[0] +  $aSumINPAIDBBL[0] + $aSumINPAIDBAY[0];

$TRpaidSCB = $aSumtrSCB + $aSumscb[0];
$TRpaidKBANK = $aSumtrKBANK + $aSumkbank[0];
$TRpaidBBL = $aSumtrBBL + $aSumbbl[0];





?>
<a class="btn btn-xl btn-outline-dark mr-1" href="#apps/schedule/index.php?view=printabletrustreceipt&date=<?php echo $dt; ?>" target="_blank"><i class="far fa-print"></i></a>
<table class="table table-striped table-sm table-bordered dt-responsive nowrap">
    <thead>
        <tr>
            <th class="text-center table-dark font-weight-bold"><?php echo $dt; ?></th>
            <th class="text-center table-dark font-weight-bold">SCB</th>
            <th class="text-center table-dark font-weight-bold">KBANK</th>
            <th class="text-center table-dark font-weight-bold">BBL</th>
            <th class="text-center table-dark font-weight-bold">BAY</th>
            <th class="text-center table-dark font-weight-bold"></th>
            <th class="text-center table-dark font-weight-bold">TOTAL</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <th class="text-left">วงเงิน TR ทั้งหมด</th>
            <td class="text-center font-weight-bold"><?php echo number_format(220000000, 2); ?></td>
            <td class="text-center font-weight-bold"><?php echo number_format(10000000, 2); ?></td>
            <td class="text-center font-weight-bold"><?php echo number_format(350000000, 2); ?></td>
            <td class="text-center font-weight-bold"><?php echo number_format(70000000, 2); ?></td>
            <td class="text-center font-weight-bold">Total TR Credit</td>
            <td class="text-center font-weight-bold"><?php echo number_format($totaltr, 2); ?></td>
        </tr>
        <tr>
            <th class="text-left">ภาระ TR ยกมา</th>
            <td class="text-center"><?php echo  number_format($balancescb, 2); ?></td>
            <td class="text-center"><?php echo  number_format($balancekbank, 2); ?></td>
            <td class="text-center"><?php echo  number_format($balancebbl, 2); ?></td>
            <td class="text-center"><?php echo  number_format($balancebay, 2); ?></td>
            <td class="text-center"></td>
            <td class="text-center"></td>
        </tr>
        <tr>
            <th class="text-left">TR Paid</th>
            <td class="text-center"><?php echo  number_format($aSumscbplus[0], 2); ?></td>
            <td class="text-center"><?php echo  number_format($aSumkbank[0], 2); ?></td>
            <td class="text-center"><?php echo  number_format($aSumbbl[0], 2); ?></td>
            <td class="text-center"><?php echo  number_format($aSumbay[0], 2); ?></td>
            <td class="text-center"></td>
            <td class="text-center"></td>
        </tr>
        <tr>
            <th class="text-left">OPEN TR FIX</th>
            <td class="text-center"><?php echo  number_format($aSumSCBFIX[0], 2); ?></td>
            <td class="text-center"><?php echo  number_format($aSumKBANKFIX[0], 2); ?></td>
            <td class="text-center"><?php echo  number_format($aSumBBLFIX[0], 2); ?></td>
            <td class="text-center"><?php echo  number_format($aSumBAYFIX[0], 2); ?></td>
            <td class="text-center"></td>
            <td class="text-center"></td>
        </tr>
        <tr>
            <th class="text-left">OPEN TR NON FIX (B/F)</th>
            <td class="text-center"><?php echo  number_format($aSumscbnonfix[0], 2); ?></td>
            <td class="text-center"><?php echo  number_format($aSumkbanknonfix[0], 2); ?></td>
            <td class="text-center"><?php echo  number_format($aSumbblnonfix[0], 2); ?></td>
            <td class="text-center"><?php echo  number_format($aSumbaynonfix[0], 2); ?></td>
            <td class="text-center font-weight-bold">TOTAL TR NON FIX (B/F)</td>
            <td class="text-center font-weight-bold"><?php echo  number_format($totalnonfix, 2); ?></td>
        </tr>
        <tr>
            <th class="text-left">ภาระ TR คงเหลือ</th>
            <td class="text-center"><?php echo  number_format($balancescb_now, 2); ?></td>
            <td class="text-center"><?php echo  number_format($balancekbank_now, 2); ?></td>
            <td class="text-center"><?php echo  number_format($balancebbl_now, 2); ?></td>
            <td class="text-center"><?php echo  number_format($balancebay_now, 2); ?></td>
            <td class="text-center font-weight-bold">ยอดหนี้ TR ทั้งหมด</td>
            <td class="text-center font-weight-bold"><?php echo  number_format($SumtotalTR, 2); ?></td>
        </tr>
        <tr>
            <th class="text-left">วงเงิน TR คงเหลือ</th>
            <td class="text-center"><?php echo  number_format($total_credit_scb_now, 2); ?></td>
            <td class="text-center"><?php echo  number_format($total_credit_kbank_now, 2); ?></td>
            <td class="text-center"><?php echo  number_format($total_credit_bbl_now, 2); ?></td>
            <td class="text-center"><?php echo  number_format($total_credit_bay_now, 2); ?></td>
            <td class="text-center font-weight-bold">วงเงิน TR เหลือทั้งหมด</td>
            <td class="text-center font-weight-bold"><?php echo  number_format($SumtotalDept, 2); ?></td>
        </tr>
    </tbody>
</table>
<table class="table table-striped table-sm table-bordered dt-responsive nowrap">
    <thead class="thead-light">
        <tr>
            <th class="text-center table-dark font-weight-bold"><?php echo $dt; ?></th>
            <th class="text-center table-dark font-weight-bold">SCB</th>
            <th class="text-center table-dark font-weight-bold">KBANK</th>
            <th class="text-center table-dark font-weight-bold">BBL</th>
            <th class="text-center table-dark font-weight-bold">BAY</th>
            <th class="text-center table-dark font-weight-bold"></th>
            <th class="text-center table-dark font-weight-bold">TOTAL</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <th class="text-left">TR NON FIX (B/F)</th>
            <td class="text-center"><?php echo  number_format($aSumscbnonfix[1], 2); ?></td>
            <td class="text-center"><?php echo  number_format($aSumkbanknonfix[1], 2); ?></td>
            <td class="text-center"><?php echo  number_format($aSumbblnonfix[1], 2); ?></td>
            <td class="text-center"><?php echo  number_format($aSumbaynonfix[1], 2); ?></td>
            <td class="text-center font-weight-bold">TOTAL TR NON FIX (B/F)</td>
            <td class="text-center font-weight-bold"><?php echo  number_format($totalnonfix1, 2); ?></td>
        </tr>
        <tr>
            <th class="text-left">TR NON FIX PAID</th>
            <td class="text-center"></td>
            <td class="text-center"></td>
            <td class="text-center"></td>
            <td class="text-center"></td>
            <td class="text-center font-weight-bold">TOTAL TR NON FIX PAID</td>
            <td class="text-center font-weight-bold"></td>
        </tr>
        <tr>
            <th class="text-left">OPEN TR NON FIX</th>
            <td class="text-center"></td>
            <td class="text-center"></td>
            <td class="text-center"></td>
            <td class="text-center"></td>
            <td class="text-center font-weight-bold">TOTAL TR NON FIX PAID</td>
            <td class="text-center"></td>
        </tr>
        <tr>
            <th class="text-left">หนี้ TR NON FIX คงเหลือ</th>
            <td class="text-center"><?php echo  number_format($aSumscbnonfix[1], 2); ?></td>
            <td class="text-center"><?php echo  number_format($aSumkbanknonfix[1], 2); ?></td>
            <td class="text-center"><?php echo  number_format($aSumbblnonfix[1], 2); ?></td>
            <td class="text-center"><?php echo  number_format($aSumbaynonfix[1], 2); ?></td>
            <td class="text-center"></td>
            <td class="text-center font-weight-bold"><?php echo  number_format($totalnonfix, 2); ?></td>
        </tr>
    </tbody>
</table>
<table class="table table-striped table-sm table-bordered dt-responsive nowrap">
    <thead class="thead-light">
        <tr>
            <th class="text-center table-dark font-weight-bold"></th>
            <th class="text-center table-dark font-weight-bold">SCB</th>
            <th class="text-center table-dark font-weight-bold">KBANK</th>
            <th class="text-center table-dark font-weight-bold">BBL</th>
            <th class="text-center table-dark font-weight-bold">BAY</th>
            <th class="text-center table-dark font-weight-bold"></th>
            <th class="text-center table-dark font-weight-bold">TOTAL</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <th class="text-left">Interest Summary</th>
            <td class="text-center"><?php echo number_format($aSumINPAIDSCB[0] - $aSumPAIDSCB[0], 2); ?></td>
            <td class="text-center"><?php echo number_format($aSumINPAIDKBANK[0] - $aSumPAIDSKBANK[0], 2); ?></td>
            <td class="text-center"><?php echo number_format($aSumINPAIDBBL[0] - $aSumPAIDBBL[0], 2); ?></td>
            <td class="text-center"><?php echo number_format($aSumINPAIDBAY[0] - $aSumPAIDBAY[0], 2); ?></td>
            <td class="text-center font-weight-bold">TOTAL SUMMARY</td>
            <td class="text-center font-weight-bold"><?php echo number_format($aSumPAIDTOTAL, 2); ?></td>
        </tr>
        <tr>
            <th class="text-left">Interest Paid</th>
            <td class="text-center"><?php echo number_format($aSumPAIDSCB[0], 2); ?></td>
            <td class="text-center"><?php echo number_format($aSumPAIDSKBANK[0], 2); ?></td>
            <td class="text-center"><?php echo number_format($aSumPAIDBBL[0], 2); ?></td>
            <td class="text-center"><?php echo number_format($aSumPAIDBAY[0], 2); ?></td>
            <td class="text-center font-weight-bold">TOTAL PAID</td>
            <td class="text-center font-weight-bold"><?php echo number_format($TOTALPAID, 2); ?></td>
        </tr>
        <tr>
            <th class="text-left">Total Interest</th>
            <td class="text-center"><?php echo number_format($aSumINPAIDSCB[0], 2); ?></td>
            <td class="text-center"><?php echo number_format($aSumINPAIDKBANK[0], 2); ?></td>
            <td class="text-center"><?php echo number_format($aSumINPAIDBBL[0], 2); ?></td>
            <td class="text-center"><?php echo number_format($aSumINPAIDBAY[0], 2); ?></td>
            <td class="text-center font-weight-bold">TOTAL INTEREST</td>
            <td class="text-center font-weight-bold"><?php echo number_format($TOTALALL, 2); ?></td>
        </tr>
    </tbody>
</table>