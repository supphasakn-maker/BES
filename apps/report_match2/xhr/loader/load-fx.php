<?php

    $aBalUSDFinance = array(205625.45,290000,0);

    $sql_previous_date = "BETWEEN '2023-03-01' AND '".date("Y-m-d",$date-86400)."'";
    $sql_only_physical = " AND type = 'Physical' AND status = 1";

    $previous_amount_bbl = $dbc->GetRecord("bs_transfers","SUM(value_usd_fixed+value_usd_nonfixed-paid_usd)","date $sql_previous_date AND bank LIKE 'BBL'");
    $previous_amount_scb = $dbc->GetRecord("bs_transfers","SUM(value_usd_fixed+value_usd_nonfixed-paid_usd)","date $sql_previous_date AND bank LIKE 'SCB'");
    $previous_amount_kbank = $dbc->GetRecord("bs_transfers","SUM(value_usd_fixed+value_usd_nonfixed-paid_usd)","date $sql_previous_date AND bank LIKE 'KBANK'");

    $previous_tr_used_bbl = $dbc->GetRecord(
        "bs_transfer_usd 
            LEFT JOIN bs_transfers ON bs_transfers.id = bs_transfer_usd.transfer_id
            LEFT JOIN bs_purchase_usd ON bs_purchase_usd.id = bs_transfer_usd.purchase_id",
        "SUM(bs_purchase_usd.amount)",
        "bs_transfer_usd.date $sql_previous_date AND bs_transfers.bank LIKE 'BBL'");
    $previous_tr_used_scb = $dbc->GetRecord(
        "bs_transfer_usd 
            LEFT JOIN bs_transfers ON bs_transfers.id = bs_transfer_usd.transfer_id
            LEFT JOIN bs_purchase_usd ON bs_purchase_usd.id = bs_transfer_usd.purchase_id",
        "SUM(bs_purchase_usd.amount)",
        "bs_transfer_usd.date $sql_previous_date AND bs_transfers.bank LIKE 'SCB'");
    $previous_tr_used_kbank = $dbc->GetRecord(
        "bs_transfer_usd 
            LEFT JOIN bs_transfers ON bs_transfers.id = bs_transfer_usd.transfer_id
            LEFT JOIN bs_purchase_usd ON bs_purchase_usd.id = bs_transfer_usd.purchase_id",
        "SUM(bs_purchase_usd.amount)",
        "bs_transfer_usd.date $sql_previous_date AND bs_transfers.bank LIKE 'KBANK'");

    $aBalUSDFinance[0] += $previous_amount_scb[0] - $previous_tr_used_scb[0];
    $aBalUSDFinance[1] += $previous_amount_kbank[0] - $previous_tr_used_kbank[0];
    $aBalUSDFinance[2] += $previous_amount_bbl[0] - $previous_tr_used_bbl[0];


    $amount_scb = $dbc->GetRecord("bs_transfers","SUM(value_usd_fixed+value_usd_nonfixed-paid_usd)","date ='".$date_for_sql."' AND bank LIKE 'SCB'");
    $amount_kbank = $dbc->GetRecord("bs_transfers","SUM(value_usd_fixed+value_usd_nonfixed-paid_usd)","date ='".$date_for_sql."' AND bank LIKE 'KBANK'");
    $amount_bbl = $dbc->GetRecord("bs_transfers","SUM(value_usd_fixed+value_usd_nonfixed-paid_usd)","date ='".$date_for_sql."' AND bank LIKE 'BBL'");

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

    //New
    echo '<div class="mb-3">';
        echo '<table class="table table-sm table-bordered">';
            echo '<thead>';
                echo '<tr>';
                    echo '<th class="text-center"></th>';
                    echo '<th class="text-center"></th>';
                    echo '<th class="text-center">B/F</th>';
                    echo '<th class="text-center">LC</th>';
                    echo '<th class="text-center">Interest</th>';
                    echo '<th class="text-center">Charges</th>';
                    echo '<th class="text-center">Gain (Loss) From Trade</th>';
                    echo '<th class="text-center">Deposit</th>';
                    echo '<th class="text-center">Add</th>';
                    echo '<th class="text-center">Used Today</th>';
                    echo '<th class="text-center">Others</th>';
                    echo '<th class="text-center">Deduct Deferred Stock Mkt Value</th>';
                    echo '<th class="text-center">C/F</th>';
                echo '</tr>';
            echo '</thead>';
            echo '<tbody>';
                echo '<tr>';
                    echo '<th class="text-left">Require FX after deduct deposit</th>';
                    echo '<th class="text-left"></th>';
                    echo '<td class="text-right"></td>';
                echo '</tr>';
                echo '<tr>';
                    echo '<th class="text-left">Open Non Fix</th>';
                    echo '<th class="text-left">SCB</th>';
                    echo '<td class="text-right">'.number_format($aBalUSDFinance[0],2).'</td>';
                    echo '<td class="text-right"></td>';
                    echo '<td class="text-right"></td>';
                    echo '<td class="text-right"></td>';
                    echo '<td class="text-right"></td>';
                    echo '<td class="text-right"></td>';
                    echo '<td class="text-right">'.number_format($amount_scb[0],2).'</td>';
                    echo '<td class="text-right">'.number_format(-$tr_used_scb[0],2).'</td>';
                    echo '<td class="text-right"></td>';
                    echo '<td class="text-right"></td>';
                    $remain = $aBalUSDFinance[0]+$amount_scb[0]-$tr_used_scb[0];
                    echo '<td class="text-right">'.number_format($remain,2).'</td>';
                echo '</tr>';
                echo '<tr>';
                    echo '<th class="text-left">Open Non Fix</th>';
                    echo '<th class="text-left">KBANK</th>';
                    echo '<td class="text-right">'.number_format($aBalUSDFinance[1],2).'</td>';
                    echo '<td class="text-right"></td>';
                    echo '<td class="text-right"></td>';
                    echo '<td class="text-right"></td>';
                    echo '<td class="text-right"></td>';
                    echo '<td class="text-right"></td>';
                    echo '<td class="text-right">'.number_format($amount_kbank[0],2).'</td>';
                    echo '<td class="text-right">'.number_format(-$tr_used_kbank[0],2).'</td>';
                    echo '<td class="text-right"></td>';
                    echo '<td class="text-right"></td>';
                    $remain = $aBalUSDFinance[1]+$amount_kbank[0]-$tr_used_kbank[0];
                    echo '<td class="text-right">'.number_format($remain,2).'</td>';
                echo '</tr>';
                echo '<tr>';
                    echo '<th class="text-left">Open Non Fix</th>';
                    echo '<th class="text-left">BBL</th>';
                    echo '<td class="text-right">'.number_format($aBalUSDFinance[2],2).'</td>';
                    echo '<td class="text-right"></td>';
                    echo '<td class="text-right"></td>';
                    echo '<td class="text-right"></td>';
                    echo '<td class="text-right"></td>';
                    echo '<td class="text-right"></td>';
                    echo '<td class="text-right">'.number_format($amount_bbl[0],2).'</td>';
                    echo '<td class="text-right">'.number_format(-$tr_used_bbl[0],2).'</td>';
                    echo '<td class="text-right"></td>';
                    echo '<td class="text-right"></td>';
                    $remain = $aBalUSDFinance[2]+$amount_bbl[0]-$tr_used_bbl[0];
                    echo '<td class="text-right">'.number_format($remain,2).'</td>';
                echo '</tr>';
                echo '<tr>';
                    echo '<th class="text-left">Total require FX amount</th>';
                    echo '<th class="text-left"></th>';
                    echo '<td class="text-right"></td>';
                    echo '<td class="text-right"></td>';
                    echo '<td class="text-right"></td>';
                    echo '<td class="text-right"></td>';
                    echo '<td class="text-right"></td>';
                    echo '<td class="text-right"></td>';
                echo '</tr>';
            echo '</tbody>';
        echo '</table>';
    echo '</div>';

?>