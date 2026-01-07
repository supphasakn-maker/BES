<?php
//Used TR Contract

$aBalUSD = array(0,0,0);
$aBalTHB = array(0,0,0);
$aBalPremium = array(0,0,0);

$sql_previous_date = "BETWEEN '2023-03-01' AND '".date("Y-m-d",$date-86400)."'";
$sql_only_month = "BETWEEN '".date("Y-m-d",strtotime(date("Y-m-01",$date))-86400)."' AND '".date("Y-m-d",$date-86400)."'";
$sql_only_physical = " AND type = 'Physical' AND status = 1";

$previous_amount_bbl = $dbc->GetRecord("bs_purchase_usd",
    "SUM(amount),SUM(amount*rate_exchange)",
    "date $sql_previous_date AND bank LIKE 'BBL' $sql_only_physical");
$previous_amount_scb = $dbc->GetRecord("bs_purchase_usd",
    "SUM(amount),SUM(amount*rate_exchange)",
    "date $sql_previous_date AND bank LIKE 'SCB' $sql_only_physical");
$previous_amount_kbank = $dbc->GetRecord("bs_purchase_usd",
    "SUM(amount),SUM(amount*rate_exchange)",
    "date $sql_previous_date AND bank LIKE 'KBANK' $sql_only_physical");

$previous_tr_used_bbl = $dbc->GetRecord(
    "bs_transfer_usd 
        LEFT JOIN bs_transfers ON bs_transfers.id = bs_transfer_usd.transfer_id
        LEFT JOIN bs_purchase_usd ON bs_purchase_usd.id = bs_transfer_usd.purchase_id",
    "SUM(bs_purchase_usd.amount),
    SUM(bs_purchase_usd.amount*bs_purchase_usd.rate_exchange)",
    "bs_transfer_usd.date $sql_previous_date AND bs_transfers.bank LIKE 'BBL'");
$previous_tr_used_scb = $dbc->GetRecord(
    "bs_transfer_usd 
        LEFT JOIN bs_transfers ON bs_transfers.id = bs_transfer_usd.transfer_id
        LEFT JOIN bs_purchase_usd ON bs_purchase_usd.id = bs_transfer_usd.purchase_id",
    "SUM(bs_purchase_usd.amount),
    SUM(bs_purchase_usd.amount*bs_purchase_usd.rate_exchange)",
    "bs_transfer_usd.date $sql_previous_date AND bs_transfers.bank LIKE 'SCB'");
$previous_tr_used_kbank = $dbc->GetRecord(
    "bs_transfer_usd 
        LEFT JOIN bs_transfers ON bs_transfers.id = bs_transfer_usd.transfer_id
        LEFT JOIN bs_purchase_usd ON bs_purchase_usd.id = bs_transfer_usd.purchase_id",
    "SUM(bs_purchase_usd.amount),
    SUM(bs_purchase_usd.amount*bs_purchase_usd.rate_exchange)",
    "bs_transfer_usd.date $sql_previous_date AND bs_transfers.bank LIKE 'KBANK'");

$aBalUSD[0] += $previous_amount_bbl[0] - $previous_tr_used_bbl[0];
$aBalUSD[1] += $previous_amount_scb[0] - $previous_tr_used_scb[0];
$aBalUSD[2] += $previous_amount_kbank[0] - $previous_tr_used_kbank[0];

$aBalTHB[0] += $previous_amount_bbl[1] - $previous_tr_used_bbl[1];
$aBalTHB[1] += $previous_amount_scb[1] - $previous_tr_used_scb[1];
$aBalTHB[2] += $previous_amount_kbank[1] - $previous_tr_used_kbank[1];

// Premium Balance
$tr_premium_bbl= $dbc->GetRecord(
    "bs_transfer_usd 
        LEFT JOIN bs_transfers ON bs_transfers.id = bs_transfer_usd.transfer_id
        LEFT JOIN bs_purchase_usd ON bs_purchase_usd.id = bs_transfer_usd.purchase_id",
    "SUM(bs_transfer_usd.premium)",
    "bs_transfer_usd.date ".$sql_only_month." AND bs_transfers.bank LIKE 'BBL'");

$tr_premium_scb = $dbc->GetRecord(
    "bs_transfer_usd 
        LEFT JOIN bs_transfers ON bs_transfers.id = bs_transfer_usd.transfer_id
        LEFT JOIN bs_purchase_usd ON bs_purchase_usd.id = bs_transfer_usd.purchase_id",
    "SUM(bs_transfer_usd.premium)",
    "bs_transfer_usd.date ".$sql_only_month." AND bs_transfers.bank LIKE 'SCB'");


$tr_premium_kbank = $dbc->GetRecord(
    "bs_transfer_usd 
        LEFT JOIN bs_transfers ON bs_transfers.id = bs_transfer_usd.transfer_id
        LEFT JOIN bs_purchase_usd ON bs_purchase_usd.id = bs_transfer_usd.purchase_id",
    "SUM(bs_transfer_usd.premium)",
    "bs_transfer_usd.date ".$sql_only_month." AND bs_transfers.bank LIKE 'KBANK'");

$aBalPremium[0] = $tr_premium_bbl[0];
$aBalPremium[1] = $tr_premium_scb[0];
$aBalPremium[2] = $tr_premium_kbank[0];

$amount_bbl = $dbc->GetRecord("bs_purchase_usd","SUM(amount),SUM(amount*rate_exchange)","date ='".$date_for_sql."' AND bank LIKE 'BBL' $sql_only_physical");
$amount_scb = $dbc->GetRecord("bs_purchase_usd","SUM(amount),SUM(amount*rate_exchange)","date ='".$date_for_sql."' AND bank LIKE 'SCB' $sql_only_physical");
$amount_kbank = $dbc->GetRecord("bs_purchase_usd","SUM(amount),SUM(amount*rate_exchange)","date ='".$date_for_sql."' AND bank LIKE 'KBANK' $sql_only_physical");

$tr_used_bbl = $dbc->GetRecord(
    "bs_transfer_usd 
        LEFT JOIN bs_transfers ON bs_transfers.id = bs_transfer_usd.transfer_id
        LEFT JOIN bs_purchase_usd ON bs_purchase_usd.id = bs_transfer_usd.purchase_id",
    "SUM(bs_purchase_usd.amount),
    SUM(bs_purchase_usd.amount*bs_purchase_usd.rate_exchange),
    SUM(bs_purchase_usd.amount*bs_purchase_usd.rate_exchange+bs_transfer_usd.premium),
    SUM(bs_transfer_usd.premium)",
    "bs_transfer_usd.date ='".$date_for_sql."' AND bs_transfers.bank LIKE 'BBL'");
$tr_used_scb = $dbc->GetRecord(
    "bs_transfer_usd 
        LEFT JOIN bs_transfers ON bs_transfers.id = bs_transfer_usd.transfer_id
        LEFT JOIN bs_purchase_usd ON bs_purchase_usd.id = bs_transfer_usd.purchase_id",
    "SUM(bs_purchase_usd.amount),
    SUM(bs_purchase_usd.amount*bs_purchase_usd.rate_exchange),
    SUM(bs_purchase_usd.amount*bs_purchase_usd.rate_exchange+bs_transfer_usd.premium),
    SUM(bs_transfer_usd.premium)",
    "bs_transfer_usd.date ='".$date_for_sql."' AND bs_transfers.bank LIKE 'SCB'");
$tr_used_kbank = $dbc->GetRecord(
    "bs_transfer_usd 
        LEFT JOIN bs_transfers ON bs_transfers.id = bs_transfer_usd.transfer_id
        LEFT JOIN bs_purchase_usd ON bs_purchase_usd.id = bs_transfer_usd.purchase_id",
    "SUM(bs_purchase_usd.amount),
    SUM(bs_purchase_usd.amount*bs_purchase_usd.rate_exchange),
    SUM(bs_purchase_usd.amount*bs_purchase_usd.rate_exchange+bs_transfer_usd.premium),
    SUM(bs_transfer_usd.premium)",
    "bs_transfer_usd.date ='".$date_for_sql."' AND bs_transfers.bank LIKE 'KBANK'");

$allbank_total = 0;
echo '<div class="mb-3">';
    echo '<table class="table table-sm table-bordered">';
        echo '<thead>';
            echo '<tr>';
                echo '<th class="text-center"></th>';
                echo '<th class="text-center"></th>';
                echo '<th class="text-center"></th>';
                echo '<th class="text-center">LC</th>';
                echo '<th class="text-center">+ Add</th>';
                echo '<th class="text-center">- Used Today</th>';
                echo '<th class="text-center">C/F</th>';
                echo '<th class="text-center">Beginning Interest / Premium</th>';
                echo '<th class="text-center">Total Bank</th>';
                echo '<th class="text-center">Interest /Premium</th>';
                echo '<th class="text-center">Interest / Premium(Monthly)</th>';
                echo '<th class="text-center"></th>';
            echo '</tr>';
        echo '</thead>';
        echo '<tbody>';
            echo '<tr>';
                echo '<th class="text-left">Unused Forward Contracts</th>';
                echo '<th class="text-left"></th>';
                echo '<td class="text-right"></td>';
            echo '</tr>';
            echo '<tr>';
                echo '<th class="text-left"></th>';
                echo '<th class="text-left">SCB</th>';
                echo '<td class="text-right">'.number_format($aBalUSD[1],2).'</td>';
                echo '<td class="text-right"></td>';
                echo '<td class="text-right">'.number_format($amount_scb[0],2).'</td>';
                echo '<td class="text-right">'.number_format(-$tr_used_scb[0],2).'</td>';
                $remain = $aBalUSD[1]+$amount_scb[0]-$tr_used_scb[0];
                echo '<td class="text-right">'.number_format($remain,2).'</td>';
                $allbank_total += $remain;
            echo '</tr>';
            echo '<tr>';
                echo '<th class="text-left"></th>';
                echo '<th class="text-left">KBANK</th>';
                echo '<td class="text-right">'.number_format($aBalUSD[2],2).'</td>';
                echo '<td class="text-right"></td>';
                echo '<td class="text-right">'.number_format($amount_kbank[0],2).'</td>';
                echo '<td class="text-right">'.number_format(-$tr_used_kbank[0],2).'</td>';
                $remain = $aBalUSD[2]+$amount_kbank[0]-$tr_used_kbank[0];
                echo '<td class="text-right">'.number_format($remain,2).'</td>';
                $allbank_total += $remain;
            echo '</tr>';
            echo '<tr>';
                echo '<th class="text-left"></th>';
                echo '<th class="text-left">BBL</th>';
                echo '<td class="text-right">'.number_format($aBalUSD[0],2).'</td>';
                echo '<td class="text-right"></td>';
                echo '<td class="text-right">'.number_format($amount_bbl[0],2).'</td>';
                echo '<td class="text-right">'.number_format(-$tr_used_bbl[0],2).'</td>';
                $remain = $aBalUSD[0]+$amount_bbl[0]-$tr_used_bbl[0];
                echo '<td class="text-right">'.number_format($remain,2).'</td>';
                $allbank_total += $remain;
            echo '</tr>';
            echo '<tr>';
                echo '<th class="text-left">Total require FX amount</th>';
                echo '<th class="text-left"></th>';
                echo '<td class="text-right"></td>';
                echo '<td class="text-right"></td>';
                echo '<td class="text-right"></td>';
                echo '<td class="text-right"></td>';
                echo '<td class="text-center">x</td>';
            echo '</tr>';
            echo '<tr>';
                echo '<th class="text-left">Total require FX amount incl. LC</th>';
                echo '<th class="text-left"></th>';
                echo '<td class="text-right"></td>';
                echo '<td class="text-right"></td>';
                echo '<td class="text-right"></td>';
                echo '<td class="text-right"></td>';
                echo '<td class="text-right"></td>';
            echo '</tr>';
            echo '<tr>';
                echo '<th class="text-left"></th>';
                echo '<th class="text-left">SCB</th>';
                echo '<td class="text-right">'.number_format($aBalTHB[1],2).'</td>';
                echo '<td class="text-right"></td>';
                echo '<td class="text-right">'.number_format($amount_scb[1],2).'</td>';
                echo '<td class="text-right">'.number_format(-$tr_used_scb[1],2).'</td>';
                $remain = $aBalTHB[1]+$amount_scb[1]-$tr_used_scb[1];
                echo '<td class="text-right">'.number_format($remain,2).'</td>';
                echo '<td class="text-right">'.number_format($aBalPremium[1],2).'</td>';
                echo '<td class="text-right">'.number_format($tr_used_scb[2],2).'</td>';
                echo '<td class="text-right">'.number_format($tr_used_scb[3],2).'</td>';
                echo '<td class="text-right">'.number_format($aBalPremium[1]-$tr_used_scb[3],2).'</td>';

            echo '</tr>';
            echo '<tr>';
                echo '<th class="text-left"></th>';
                echo '<th class="text-left">KBANK</th>';
                echo '<td class="text-right">'.number_format($aBalTHB[2],2).'</td>';
                echo '<td class="text-right"></td>';
                echo '<td class="text-right">'.number_format($amount_kbank[1],2).'</td>';
                echo '<td class="text-right">'.number_format(-$tr_used_kbank[1],2).'</td>';
                $remain = $aBalTHB[2]+$amount_kbank[1]-$tr_used_kbank[1];
                echo '<td class="text-right">'.number_format($remain,2).'</td>';
                echo '<td class="text-right">'.number_format($aBalPremium[2],2).'</td>';
                echo '<td class="text-right">'.number_format($tr_used_kbank[2],2).'</td>';
                echo '<td class="text-right">'.number_format($tr_used_kbank[3],2).'</td>';
                echo '<td class="text-right">'.number_format($aBalPremium[2]-$tr_used_kbank[3],2).'</td>';
            echo '</tr>';
            echo '<tr>';
                echo '<th class="text-left"></th>';
                echo '<th class="text-left">BBL</th>';
                echo '<td class="text-right">'.number_format($aBalTHB[0],2).'</td>';
                echo '<td class="text-right"></td>';
                echo '<td class="text-right">'.number_format($amount_bbl[1],2).'</td>';
                echo '<td class="text-right">'.number_format(-$tr_used_bbl[1],2).'</td>';
                $remain = $aBalTHB[0]+$amount_bbl[1]-$tr_used_bbl[1];
                echo '<td class="text-right">'.number_format($remain,2).'</td>';
                echo '<td class="text-right">'.number_format($aBalPremium[0],2).'</td>';
                echo '<td class="text-right">'.number_format($tr_used_bbl[2],2).'</td>';
                echo '<td class="text-right">'.number_format($tr_used_bbl[3],2).'</td>';
                echo '<td class="text-right">'.number_format($aBalPremium[0]-$tr_used_bbl[3],2).'</td>';
            echo '</tr>';
            
        echo '</tbody>';
    echo '</table>';
echo '</div>';
?>