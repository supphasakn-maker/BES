<?php
session_start();
include_once "../../../config/define.php";
include_once "../../../include/db.php";
include_once "../../../include/oceanos.php";

@ini_set('display_errors', DEBUG_MODE ? 1 : 0);
date_default_timezone_set(DEFAULT_TIMEZONE);

global $ui_form, $os;
$dbc = new dbc;
$dbc->Connect();
$os = new oceanos($dbc);

$dt = date($_POST['date']);

$date3 = date("Y-m-d", strtotime("-1 day", strtotime($dt)));
$date2 = date("Y-m-d", strtotime("-90 day", strtotime($dt)));

if ($_POST['bank'] == "BBL") {
    $credit = 350000000;
    $rate = $os->load_variable("bbl_rate");
} else if ($_POST['bank'] == "KBANK") {
    $credit = 10000000;
    $rate = $os->load_variable("kbank_rate");
} else {
    $credit = 220000000;
    $rate = $os->load_variable("scb_rate");
}

$scb_rate = $os->load_variable("scb_rate");
$kbank_rate = $os->load_variable("kbank_rate");
$bbl_rate = $os->load_variable("bbl_rate");

$sql = "SELECT SUM(value_thb_net) AS thbnet , SUM(paid_thb) AS thbpaid , SUM(value_usd_nonfixed) AS nonfix , 
    SUM(paid_usd) AS usdPaid, SUM(value_usd_paid) AS vusd
    FROM bs_transfers WHERE bank = '" . $_POST['bank'] . "' AND  bs_transfers.date BETWEEN '" . $date2 . "' AND '" . $date3 . "'";
$rst = $dbc->Query($sql);
$transferscb = $dbc->Fetch($rst);
$total_usd_paid = $transferscb['usdPaid'] + $transferscb['vusd'];
$valuenonfix = $transferscb['nonfix'] - $total_usd_paid;
$total_remain_credit = $transferscb['thbnet'] - $transferscb['thbpaid'] + $valuenonfix;

$aSumPaid = array(0);
$sql = "SELECT * FROM bs_transfers WHERE bank = '" . $_POST['bank'] . "' ";
$rst = $dbc->Query($sql);
while ($transfer = $dbc->Fetch($rst)) {

    $sql = "SELECT SUM(paid) AS paid FROM bs_transfer_payments WHERE currency = 'THB'  AND bs_transfer_payments.date  = '" . $dt . "' AND transfer_id=" . $transfer['id'];
    $rst_usd = $dbc->Query($sql);
    while ($paid = $dbc->Fetch($rst_usd)) {

        $aSumPaid[0] += $paid['paid'];
    }
}

$aSumTRFIX = array(0);
$sql = "SELECT bs_purchase_usd.rate_exchange AS rate_exchange ,bs_purchase_usd.amount AS amount
    FROM bs_transfers 
    LEFT OUTER JOIN bs_transfer_usd ON bs_transfers.id = bs_transfer_usd.transfer_id
    LEFT OUTER JOIN bs_purchase_usd ON bs_transfer_usd.purchase_id = bs_purchase_usd.id
    WHERE bs_transfer_usd.date = '" . $dt . "' AND bs_transfers.bank = '" . $_POST['bank'] . "'";
$rst = $dbc->Query($sql);
while ($transfer = $dbc->Fetch($rst)) {

    $total_usd_rate_exchange = $transfer['rate_exchange'] * $transfer['amount'];
    $aSumTRFIX[0] += $total_usd_rate_exchange;
}

$aSumTRnonfix = array(0, 0);
$sql = "SELECT * FROM bs_transfers WHERE bank = '" . $_POST['bank'] . "' AND  bs_transfers.date BETWEEN '" . $date2 . "' AND '" . $dt . "'";
$rst = $dbc->Query($sql);
while ($scbnonfix = $dbc->Fetch($rst)) {

    $total_usd_paid = $scbnonfix['paid_usd'] + $scbnonfix['value_usd_paid'];

    $aSumscbnon = $scbnonfix['value_usd_nonfixed'] - $total_usd_paid;

    $aSumTRnonfix[0] += $aSumscbnon;

    $valuescb = $scbnonfix['value_usd_fixed'];

    $aascb = $scbnonfix['value_usd_total'] + $scbnonfix['value_usd_paid'];

    $aSumTRnonfix[1] += $aascb;
}

$sql = "SELECT
    SUM(bs_transfers.value_thb_net-paid_thb) AS unpaid,
    SUM(value_thb_net) AS net,
    SUM(paid_thb) AS paid,
    SUM(paid_usd * rate_counter) AS paid_usd,
    SUM(value_usd_nonfixed * rate_counter) AS value_nonfixed_thb,
    COUNT(bs_transfers.id) AS total_tr
    FROM bs_transfers
    WHERE bs_transfers.bank ='" . $_POST['bank'] . "'";

$rst = $dbc->Query($sql);
$transfer = $dbc->Fetch($rst);
$total_remain_credit = $credit - $transfer['unpaid'] - $transfer['value_nonfixed_thb'] + $transfer['paid_usd'];

$mont = date("Y-m",strtotime($dt));

$aSumINPAID = array(0);
$sql = "SELECT * FROM bs_transfers WHERE bank = '" . $_POST['bank'] . "' ";
$rst = $dbc->Query($sql);
while($paidscb = $dbc->Fetch($rst)){

    $sql = "SELECT * FROM bs_transfer_payments WHERE DATE_FORMAT(bs_transfer_payments.date,'%Y-%m')   = '".$mont."' AND transfer_id=".$paidscb['id'];
    $rst_usd = $dbc->Query($sql);
    while($payment = $dbc->Fetch($rst_usd)){

            $interest = $payment['interest'];

        $aSumINPAID[0] += $interest;
    }

}

$aSumPAID = array(0);
$sql = "SELECT bs_transfer_payments.date AS date, 
bs_transfer_payments.interest AS interest,
bs_transfer_payments.currency AS currency 
FROM bs_transfer_payments 
LEFT JOIN bs_transfers ON bs_transfer_payments.transfer_id = bs_transfers.id 
WHERE bs_transfers.bank = '" . $_POST['bank'] . "' AND bs_transfer_payments.date  = '".$dt."' ";
$rst = $dbc->Query($sql);
while($paidinscb = $dbc->Fetch($rst)){

    $aSumPAID[0] += $paidinscb['interest'];

}
?>
<div class="card mb-2">
    <div class="card-body">
        <form name="form_addtr" onsubmit="fn.app.tr_report.report.add_tr();return false;">
            <table class="table table-bordered table-form">
                <tbody>
                    <tr>
                        <td class="text-left">วันที่</td>
                        <td><input type="text" name="date"  class="form-control text-center" value="<?php echo $dt; ?>"></td>
                    </tr>
                    <tr>
                        <td class="text-left">Bank</td>
                        <td> <input type="text" name="bank"  class="form-control text-center" value="<?php echo $_POST['bank']; ?>"></td>
                    </tr>
                    <tr>
                        <td class="text-left">วงเงิน TR ทั้งหมด</td>
                        <td class="text-center font-weight-bold"><?php echo $credit; ?></td>
                    </tr>
                    <tr>
                        <td class="text-left">ภาระ TR ยกมา</td>
                        <td class="text-center"><input type="text" name="loader" class="form-control text-center" value="<?php echo $transfer['unpaid'] + $transfer['value_nonfixed_thb']; ?>"></td>
                    </tr>
                    <tr>
                        <td class="text-left">TR Paid</td>
                        <td class="text-center"><input type="number"  step="0.01" name="paid" class="form-control text-center" value="<?php echo $aSumPaid[0]; ?>"></td>
                    </tr>
                    <tr>
                        <td class="text-left">OPEN TR FIX</td>
                        <td class="text-center"><input type="number"  step="0.01" name="tr_fix" class="form-control text-center" value="<?php echo $aSumTRFIX[0]; ?>"></td>
                    </tr>
                    <tr>
                        <td class="text-left">OPEN TR NON FIX (B/F)</td>
                        <td class="text-center"><input type="number" step="0.01" name="tr_non_fix" class="form-control text-center" value="<?php echo $aSumTRnonfix[0] * $rate; ?>"></td>
                    </tr>
                    <tr>
                        <td class="text-left">ภาระ TR คงเหลือ</td>
                        <td class="text-center"><input type="text"  name="load_balance" class="form-control text-center" value="<?php echo $transfer['unpaid'] + $transfer['value_nonfixed_thb']; ?>"></td>
                    </tr>
                    <tr>
                        <td class="text-left">วงเงิน TR คงเหลือ</td>
                        <td class="text-center"><input type="text"  name="balance" class="form-control text-center" value="<?php echo $total_remain_credit; ?>"></td>
                    </tr>
                    <tr>
                        <td class="text-left">TR NON FIX (B/F)</td>
                        <td class="text-center"><input type="number"  step="0.01" name="tr_non_fix_usd" class="form-control text-center" value="<?php echo $aSumTRnonfix[0]; ?>"></td>
                    </tr>
                    <tr>
                        <td class="text-left">Interest Summary</td>
                        <td class="text-center"><input type="number"  step="0.01" name="interest_summary" class="form-control text-center" value="<?php echo $aSumINPAID[0]-$aSumPAID[0]; ?>"></td>
                    </tr>
                    <tr>
                        <td class="text-left">Interest Paid</td>
                        <td class="text-center"><input type="number"  step="0.01" name="interest_paid" class="form-control text-center" value="<?php echo $aSumPAID[0]; ?>"></td>
                    </tr>
                </tbody>
            </table>
            <button class="btn btn-primary" type="submit">ทำรายการ</button>
        </form>
    </div>
</div>