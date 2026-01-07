<?php

    $sql = "SELECT
    SUM(bs_transfers.value_thb_net-paid_thb) AS unpaid,
    SUM(value_thb_net) AS net,
    SUM(paid_thb) AS paid,
    SUM(paid_usd * rate_counter) AS paid_usd,
    SUM(value_usd_nonfixed * rate_counter) AS value_nonfixed_thb,
    COUNT(bs_transfers.id) AS total_tr
    FROM bs_transfers
    WHERE bs_transfers.bank ='SCB'";
    $rst = $dbc->Query($sql);
    $transferscb = $dbc->Fetch($rst);
    $total_remain_creditscb = 220000000-$transferscb['unpaid']-$transferscb['value_nonfixed_thb']+$transferscb['paid_usd'];

    $sql = "SELECT
    SUM(bs_transfers.value_thb_net-paid_thb) AS unpaid,
    SUM(value_thb_net) AS net,
    SUM(paid_thb) AS paid,
    SUM(paid_usd * rate_counter) AS paid_usd,
    SUM(value_usd_nonfixed * rate_counter) AS value_nonfixed_thb,
    COUNT(bs_transfers.id) AS total_tr
    FROM bs_transfers
    WHERE bs_transfers.bank ='KBANK'";
    $rst = $dbc->Query($sql);
    $transferkbank = $dbc->Fetch($rst);
    $total_remain_creditkbank = 10000000-$transferkbank['unpaid']-$transferkbank['value_nonfixed_thb']+$transferkbank['paid_usd'];

    $sql = "SELECT
    SUM(bs_transfers.value_thb_net-paid_thb) AS unpaid,
    SUM(value_thb_net) AS net,
    SUM(paid_thb) AS paid,
    SUM(paid_usd * rate_counter) AS paid_usd,
    SUM(value_usd_nonfixed * rate_counter) AS value_nonfixed_thb,
    COUNT(bs_transfers.id) AS total_tr
    FROM bs_transfers
    WHERE bs_transfers.bank ='BBL'";
    $rst = $dbc->Query($sql);
    $transferbbl = $dbc->Fetch($rst);
    $total_remain_creditbbl = 350000000-$transferbbl['unpaid']-$transferbbl['value_nonfixed_thb']+$transferbbl['paid_usd'];

    $sql = "SELECT
    SUM(bs_transfers.value_thb_net-paid_thb) AS unpaid,
    SUM(value_thb_net) AS net,
    SUM(paid_thb) AS paid,
    SUM(paid_usd * rate_counter) AS paid_usd,
    SUM(value_usd_nonfixed * rate_counter) AS value_nonfixed_thb,
    COUNT(bs_transfers.id) AS total_tr
    FROM bs_transfers
    WHERE bs_transfers.bank ='KTB'";
    $rst = $dbc->Query($sql);
    $transferktb = $dbc->Fetch($rst);
    $total_remain_creditktb = 220000000-$transferktb['unpaid']-$transferktb['value_nonfixed_thb']+$transferktb['paid_usd'];

    $sql = "SELECT
    SUM(bs_transfers.value_thb_net-paid_thb) AS unpaid,
    SUM(value_thb_net) AS net,
    SUM(paid_thb) AS paid,
    SUM(paid_usd * rate_counter) AS paid_usd,
    SUM(value_usd_nonfixed * rate_counter) AS value_nonfixed_thb,
    COUNT(bs_transfers.id) AS total_tr
    FROM bs_transfers
    WHERE bs_transfers.bank ='BAY'";
    $rst = $dbc->Query($sql);
    $transferbay = $dbc->Fetch($rst);
    $total_remain_creditbay = 220000000-$transferbay['unpaid']-$transferbay['value_nonfixed_thb']+$transferbay['paid_usd'];

    $sql = "SELECT
    SUM(bs_transfers.value_thb_net-paid_thb) AS unpaid,
    SUM(value_thb_net) AS net,
    SUM(paid_thb) AS paid,
    SUM(paid_usd * rate_counter) AS paid_usd,
    SUM(value_usd_nonfixed * rate_counter) AS value_nonfixed_thb,
    COUNT(bs_transfers.id) AS total_tr
    FROM bs_transfers
    WHERE bs_transfers.bank ='UOB'";
    $rst = $dbc->Query($sql);
    $transferuob = $dbc->Fetch($rst);
    $total_remain_credituob = 220000000-$transferuob['unpaid']-$transferuob['value_nonfixed_thb']+$transferuob['paid_usd'];

    $aSumscb = array(0);
    $sql = "SELECT transfer.bank, 
    ( SELECT SUM(paid) FROM bs_transfer_payments WHERE id = transfer.id AND currency = 'THB' ) AS thaipaid FROM bs_transfers transfer 
    WHERE transfer.bank = 'SCB' GROUP BY transfer.bank , thaipaid";
    $rst = $dbc->Query($sql);
    while($scb = $dbc->Fetch($rst)){
     $aSumscb[0] += $scb['thaipaid'];
    }

    $aSumkbank= array(0);
    $sql = "SELECT transfer.bank, 
    ( SELECT SUM(paid) FROM bs_transfer_payments WHERE id = transfer.id AND currency = 'THB' ) AS thaipaid FROM bs_transfers transfer 
    WHERE transfer.bank = 'KBANK' GROUP BY transfer.bank , thaipaid";
    $rst = $dbc->Query($sql);
    while($kbank = $dbc->Fetch($rst)){
     $aSumkbank[0] += $kbank['thaipaid'];
    }

    $aSumbbl= array(0);
    $sql = "SELECT transfer.bank, 
    ( SELECT SUM(paid) FROM bs_transfer_payments WHERE id = transfer.id AND currency = 'THB' ) AS thaipaid FROM bs_transfers transfer 
    WHERE transfer.bank = 'BBL' GROUP BY transfer.bank , thaipaid";
    $rst = $dbc->Query($sql);
    while($bbl = $dbc->Fetch($rst)){
     $aSumbbl[0] += $bbl['thaipaid'];
    }

    $aSumktb= array(0);
    $sql = "SELECT transfer.bank, 
    ( SELECT SUM(paid) FROM bs_transfer_payments WHERE id = transfer.id AND currency = 'THB' ) AS thaipaid FROM bs_transfers transfer 
    WHERE transfer.bank = 'KTB' GROUP BY transfer.bank , thaipaid";
    $rst = $dbc->Query($sql);
    while($ktb = $dbc->Fetch($rst)){
     $aSumktb[0] += $ktb['thaipaid'];
    }



?>
<table class="table table-striped table-sm table-bordered dt-responsive nowrap">
<tbody>
    <tr>
    <th class="text-center table-dark font-weight-bold"><?php echo $_POST['date'];?></th>
        <th class="text-center table-dark font-weight-bold">SCB</th>
        <th class="text-center table-dark font-weight-bold">KBANK</th>
        <th class="text-center table-dark font-weight-bold">BBL</th>
        <th class="text-center table-dark font-weight-bold">KTB</th>
        <th class="text-center table-dark font-weight-bold">BAY</th>
        <th class="text-center table-dark font-weight-bold">UOB</th>
        <th class="text-center table-dark font-weight-bold">THAI CREDIT</th>
        <th class="text-center table-dark font-weight-bold">เงินสด</th>
        <th class="text-center table-dark font-weight-bold">TOTAL</th>
    </tr>
    <tr>
        <th class="text-left">วงเงิน TR ทั้งหมด</th>
        <td class="text-center"><?php echo number_format(220000000,2);?></td>
        <td class="text-center"><?php echo number_format(10000000,2);?></td>
        <td class="text-center"><?php echo number_format(350000000,2);?></td>
        <td class="text-center"><?php echo number_format(220000000,2);?></td>
        <td class="text-center"><?php echo number_format(220000000,2);?></td>
        <td class="text-center"><?php echo number_format(220000000,2);?></td>
        <td class="text-center"><?php echo number_format(220000000,2);?></td>
        <td class="text-center"><?php echo number_format(220000000,2);?></td>
        <td class="text-center"></td>
    </tr>
    <tr>
        <th class="text-left">TR Fix (B/F)</th>
        <td class="text-center"><?php echo number_format($total_remain_creditscb,2);?></td>
        <td class="text-center"><?php echo number_format($total_remain_creditkbank,2);?></td>
        <td class="text-center"><?php echo number_format($total_remain_creditbbl,2);?></td>
        <td class="text-center"><?php echo number_format($total_remain_creditktb,2);?></td>
        <td class="text-center"><?php echo number_format($total_remain_creditbay,2);?></td>
        <td class="text-center"><?php echo number_format($total_remain_credituob,2);?></td>
        <td class="text-center"><?php echo number_format($total_remain_credituob,2);?></td>
        <td class="text-center"><?php echo number_format($total_remain_credituob,2);?></td>
        <td class="text-center"></td>
    </tr>
    <tr>
        <th class="text-left">TR Paid</th>
        <td class="text-center"><?php echo  number_format($aSumscb[0],2); ?></td>
        <td class="text-center"><?php echo  number_format($aSumkbank[0],2); ?></td>
        <td class="text-center"><?php echo  number_format($aSumbbl[0],2); ?></td>
        <td class="text-center"><?php echo  number_format($aSumktb[0],2); ?></td>
        <td class="text-center"></td>
        <td class="text-center"></td>
        <td class="text-center"></td>
        <td class="text-center"></td>
        <td class="text-center"></td>
    </tr>
    <?php 



        ?>
    <tr>
        <th class="text-left">OPEN TR FIX</th>
        <td class="text-center"></td>
        <td class="text-center"></td>
        <td class="text-center"></td>
        <td class="text-center"></td>
        <td class="text-center"></td>
        <td class="text-center"></td>
        <td class="text-center"></td>
        <td class="text-center"></td>
        <td class="text-center"></td>
    </tr>
    <tr>
        <th class="text-left">วงเงิน TR คงเหลือ</th>
        <td class="text-center"></td>
        <td class="text-center"></td>
        <td class="text-center"></td>
        <td class="text-center"></td>
        <td class="text-center"></td>
        <td class="text-center"></td>
        <td class="text-center"></td>
        <td class="text-center"></td>
        <td class="text-center"></td>
    </tr>
    <tr>
        <th class="text-left">ยอดหนี้ TR</th>
        <td class="text-center"></td>
        <td class="text-center"></td>
        <td class="text-center"></td>
        <td class="text-center"></td>
        <td class="text-center"></td>
        <td class="text-center"></td>
        <td class="text-center"></td>
        <td class="text-center"></td>
        <td class="text-center"></td>                    
    </tr>
    
</tbody>
</table>

