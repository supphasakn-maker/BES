<?php
    $dt = date($_POST['date']);


    $sql = "SELECT
    SUM(bs_transfers.value_thb_net-paid_thb) AS unpaid,
    SUM(value_thb_net) AS net,
    SUM(paid_thb) AS paid,
    SUM(paid_usd * rate_counter) AS paid_usd,
    SUM(value_usd_nonfixed * rate_counter) AS value_nonfixed_thb,
    COUNT(bs_transfers.id) AS total_tr
    FROM bs_transfers
    WHERE bs_transfers.bank = 'SCB' ";
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
    WHERE bs_transfers.bank = 'KBANK' ";
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
    WHERE bs_transfers.bank = 'BBL' ";
    $rst = $dbc->Query($sql);
    $transferbbl = $dbc->Fetch($rst);
    $total_remain_creditbbl = 350000000-$transferbbl['unpaid']-$transferbbl['value_nonfixed_thb']+$transferbbl['paid_usd'];




    $aSumscb = array(0);
    $sql = "SELECT * FROM bs_transfers WHERE bank = 'SCB' ";
    $rst = $dbc->Query($sql);
    while($transfer = $dbc->Fetch($rst)){
       
        $sql = "SELECT SUM(paid) AS paid FROM bs_transfer_payments WHERE currency = 'THB' AND bs_transfer_payments.date  = '".$dt."' AND transfer_id=".$transfer['id'];
        $rst_usd = $dbc->Query($sql);
        while($scb = $dbc->Fetch($rst_usd)){

            $aSumscb[0] += $scb['paid'];
        }
    }

    $aSumkbank = array(0);
    $sql = "SELECT * FROM bs_transfers WHERE bank = 'KBANK' ";
    $rst = $dbc->Query($sql);
    while($transfer = $dbc->Fetch($rst)){
       
        $sql = "SELECT SUM(paid) AS paid FROM bs_transfer_payments WHERE currency = 'THB' AND bs_transfer_payments.date  = '".$dt."' AND transfer_id=".$transfer['id'];
        $rst_usd = $dbc->Query($sql);
        while($kbank = $dbc->Fetch($rst_usd)){

            $aSumkbank[0] += $kbank['paid'];
        }
    }

    $aSumbbl = array(0);
    $sql = "SELECT * FROM bs_transfers WHERE bank = 'BBL' ";
    $rst = $dbc->Query($sql);
    while($transfer = $dbc->Fetch($rst)){
       
        $sql = "SELECT SUM(paid) AS paid FROM bs_transfer_payments WHERE currency = 'THB' AND bs_transfer_payments.date  = '".$dt."' AND transfer_id=".$transfer['id'];
        $rst_usd = $dbc->Query($sql);
        while($bbl = $dbc->Fetch($rst_usd)){

            $aSumbbl[0] += $bbl['paid'];
        }
    }

    $aSumscbnonfix = array(0,0);
    $sql = "SELECT * FROM bs_transfers WHERE bank = 'SCB' AND  bs_transfers.date = '".$dt."'";
    $rst = $dbc->Query($sql);
    while($scbnonfix = $dbc->Fetch($rst)){

        $total_usd_paid = $scbnonfix['paid_usd']+$scbnonfix['value_usd_paid'];

        $aSumscbnon = $scbnonfix['value_usd_nonfixed']-$total_usd_paid;

        $aSumscbnonfix[0] += $aSumscbnon;

        $valuescb = $scbnonfix['value_usd_fixed'];

        $aascb = $scbnonfix['value_usd_total']+$scbnonfix['value_usd_paid'];

        $aSumscbnonfix[1] += $aascb;

    }



    $aSumkbanknonfix = array(0,0);
    $sql = "SELECT * FROM bs_transfers WHERE bank = 'KBANK' AND  bs_transfers.date = '".$dt."'";
    $rst = $dbc->Query($sql);
    while($kbanknonfix = $dbc->Fetch($rst)){

        $total_usd_paid = $kbanknonfix['paid_usd']+$kbanknonfix['value_usd_paid'];

        $aSumkbanknon = $kbanknonfix['value_usd_nonfixed']-$total_usd_paid;

        $aSumkbanknonfix[0] += $aSumkbanknon;

        $valuekbank= $kbanknonfix['value_usd_fixed'];
        $aakbank = $kbanknonfix['value_usd_total']+$kbanknonfix['value_usd_paid'];
        $aSumkbanknonfix[1] += $aakbank;

    }

    $aSumbblnonfix = array(0,0);
    $sql = "SELECT * FROM bs_transfers WHERE bank = 'BBL' AND  bs_transfers.date = '".$dt."'";
    $rst = $dbc->Query($sql);
    while($bblnonfix = $dbc->Fetch($rst)){

        $total_usd_paid = $bblnonfix['paid_usd']+$bblnonfix['value_usd_paid'];

        $aSumbblnon = $bblnonfix['value_usd_nonfixed']-$total_usd_paid;

        $aSumbblnonfix[0] += $aSumbblnon;
        $valuebbl= $bblnonfix['value_usd_fixed'];
        $aakbbl= $bblnonfix['value_usd_total']+$bblnonfix['value_usd_paid'];
        $aSumbblnonfix[1] += $aakbbl;

    }



    $totaltr = 220000000 + 10000000 + 350000000;


    $total_remain_creditscb1 =  $total_remain_creditscb  - $aSumscb[0];
    $total_remain_creditkbank1 = $total_remain_creditkbank - $aSumkbank[0];
    $total_remain_creditbbl1 = $total_remain_creditbbl - $aSumbbl[0];
    $aSumtrSCB = $total_remain_creditscb1 + $aSumscb[0];
    $aSumtrKBANK = $total_remain_creditkbank1 + $aSumkbank[0];
    $aSumtrBBL= $total_remain_creditbbl1 + $aSumbbl[0];

    $aSubDSCB = $aSumtrSCB - 220000000;
    $aSubDKBANK = $aSumtrKBANK - 10000000;
    $aSubDBBL= $aSumtrBBL - 350000000;

    $SumtotalTR = $aSumtrSCB + $aSumtrKBANK + $aSumtrBBL;
    $SumtotalDept = $aSubDSCB + $aSubDKBANK +  $aSubDBBL;

    $totalnonfix = $aSumscbnonfix[0] + $aSumkbanknonfix[0] +$aSumbblnonfix[0];


    $mont = date("Y-m");

    $aSumINPAIDSCB = array(0);
    $sql = "SELECT * FROM bs_transfers WHERE bank = 'SCB' ";
    $rst = $dbc->Query($sql);
    while($paidscb = $dbc->Fetch($rst)){

        $sql = "SELECT * FROM bs_transfer_payments WHERE DATE_FORMAT(bs_transfer_payments.date,'%Y-%m')   = '".$mont."' AND transfer_id=".$paidscb['id'];
        $rst_usd = $dbc->Query($sql);
        while($payment = $dbc->Fetch($rst_usd)){

                $interest = $payment['interest'];

            $aSumINPAIDSCB[0] += $interest;
        }

    }

    $aSumINPAIDKBANK = array(0);
    $sql = "SELECT * FROM bs_transfers WHERE bank = 'KBANK' ";
    $rst = $dbc->Query($sql);
    while($paidkbank = $dbc->Fetch($rst)){

        $sql = "SELECT * FROM bs_transfer_payments WHERE DATE_FORMAT(bs_transfer_payments.date,'%Y-%m')   = '".$mont."' AND transfer_id=".$paidkbank['id'];
        $rst_usd = $dbc->Query($sql);
        while($payment = $dbc->Fetch($rst_usd)){

                $interest = $payment['interest'];

            $aSumINPAIDKBANK[0] += $interest;
        }

    }

    $aSumINPAIDBBL = array(0);
    $sql = "SELECT * FROM bs_transfers WHERE bank = 'BBL' ";
    $rst = $dbc->Query($sql);
    while($paidbbl = $dbc->Fetch($rst)){

        $sql = "SELECT * FROM bs_transfer_payments WHERE DATE_FORMAT(bs_transfer_payments.date,'%Y-%m')   = '".$mont."' AND transfer_id=".$paidbbl['id'];
        $rst_usd = $dbc->Query($sql);
        while($payment = $dbc->Fetch($rst_usd)){

                $interest = $payment['interest'];

            $aSumINPAIDBBL[0] += $interest;
        }

    }
    $aSumPAIDTOTAL = $aSumINPAIDSCB[0] + $aSumINPAIDKBANK[0] + $aSumINPAIDBBL[0];

    $aSumPAIDSCB = array(0);
    $sql = "SELECT bs_transfer_payments.date AS date, 
    bs_transfer_payments.interest AS interest,
    bs_transfer_payments.currency AS currency 
    FROM bs_transfer_payments 
    LEFT JOIN bs_transfers ON bs_transfer_payments.transfer_id = bs_transfers.id 
    WHERE bs_transfers.bank = 'SCB' AND bs_transfer_payments.date  = '".$dt."' ";
    $rst = $dbc->Query($sql);
    while($paidinscb = $dbc->Fetch($rst)){

        $aSumPAIDSCB[0] += $paidinscb['interest'];

    }

    $aSumPAIDSKBANK= array(0);
    $sql = "SELECT bs_transfer_payments.date AS date, 
    bs_transfer_payments.interest AS interest,
    bs_transfer_payments.currency AS currency 
    FROM bs_transfer_payments 
    LEFT JOIN bs_transfers ON bs_transfer_payments.transfer_id = bs_transfers.id 
    WHERE bs_transfers.bank = 'KBANK'  AND bs_transfer_payments.date = '".$dt."'";
    $rst = $dbc->Query($sql);
    while($paidinkbank = $dbc->Fetch($rst)){

        $aSumPAIDSKBANK[0] += $paidinkbank['interest'];

    }

    $aSumPAIDBBL = array(0);
    $sql = "SELECT bs_transfer_payments.date AS date, 
    bs_transfer_payments.interest AS interest,
    bs_transfer_payments.currency AS currency 
    FROM bs_transfer_payments 
    LEFT JOIN bs_transfers ON bs_transfer_payments.transfer_id = bs_transfers.id 
    WHERE bs_transfers.bank = 'BBL'  AND bs_transfer_payments.date  = '".$dt."'";
    $rst = $dbc->Query($sql);
    while($paidinbbl = $dbc->Fetch($rst)){

        $aSumPAIDBBL[0] += $paidinbbl['interest'];

    }


    $TOTALPAID = $aSumPAIDSCB[0] + $aSumPAIDSKBANK[0] +$aSumPAIDBBL[0];

    $TOTALINSCB = $aSumINPAIDSCB[0] +  $aSumPAIDSCB[0];
    $TOTALINKBANK = $aSumINPAIDKBANK[0] +  $aSumPAIDSKBANK[0];
    $TOTALINBBL = $aSumINPAIDBBL[0] +  $aSumPAIDBBL[0];

    $TOTALALL =  $TOTALINSCB + $TOTALINKBANK +  $TOTALINBBL;
?>
<table class="table table-striped table-sm table-bordered dt-responsive nowrap">
<thead>
    <tr>
        <th class="text-center table-dark font-weight-bold"><?php echo $dt;?></th>
        <th class="text-center table-dark font-weight-bold">SCB</th>
        <th class="text-center table-dark font-weight-bold">KBANK</th>
        <th class="text-center table-dark font-weight-bold">BBL</th>
        <th class="text-center table-dark font-weight-bold"></th>
        <th class="text-center table-dark font-weight-bold">TOTAL</th>
    </tr>
</thead>
<tbody>
    <tr>
        <th class="text-left">วงเงิน TR ทั้งหมด</th>
        <td class="text-center font-weight-bold"><?php echo number_format(220000000,2);?></td>
        <td class="text-center font-weight-bold"><?php echo number_format(10000000,2);?></td>
        <td class="text-center font-weight-bold"><?php echo number_format(350000000,2);?></td>
        <td class="text-center font-weight-bold">Total TR Credit</td>
        <td class="text-center font-weight-bold"><?php echo number_format($totaltr,2);?></td>
    </tr>
    <tr>
        <th class="text-left">TR Fix (B/F)</th>
        <td class="text-center"><?php echo number_format($total_remain_creditscb1,2);?></td>
        <td class="text-center"><?php echo number_format($total_remain_creditkbank1,2);?></td>
        <td class="text-center"><?php echo number_format($total_remain_creditbbl1,2);?></td>
        <td class="text-center"></td>
        <td class="text-center"></td>
    </tr>   <tr>
        <th class="text-left">TR Paid</th>
        <td class="text-center"><?php echo  number_format($aSumscb[0],2); ?></td>
        <td class="text-center"><?php echo  number_format($aSumkbank[0],2); ?></td>
        <td class="text-center"><?php echo  number_format($aSumbbl[0],2); ?></td>
        <td class="text-center"></td>
        <td class="text-center"></td>
    </tr>
    <tr>
        <th class="text-left">OPEN TR FIX</th>
        <td class="text-center"></td>
        <td class="text-center"></td>
        <td class="text-center"></td>
        <td class="text-center"></td>
        <td class="text-center"></td>
    </tr>
    <tr>
        <th class="text-left">วงเงิน TR คงเหลือ</th>
        <td class="text-center"><?php echo  number_format($aSumtrSCB,2); ?></td>
        <td class="text-center"><?php echo  number_format($aSumtrKBANK,2); ?></td>
        <td class="text-center"><?php echo  number_format($aSumtrBBL,2); ?></td>
        <td class="text-center font-weight-bold">วงเงิน TR เหลือทั้งหมด</td>
        <td class="text-center font-weight-bold"><?php echo  number_format($SumtotalTR,2); ?></td>
    </tr>    
    <tr>
        <th class="text-left">ยอดหนี้ TR</th>
        <td class="text-center"><?php echo  number_format($aSubDSCB,2); ?></td>
        <td class="text-center"><?php echo  number_format($aSubDKBANK ,2); ?></td>
        <td class="text-center"><?php echo  number_format($aSubDBBL,2); ?></td>
        <td class="text-center font-weight-bold">ยอดหนี้ TR ทั้งหมด</td>
        <td class="text-center font-weight-bold"><?php echo  number_format($SumtotalDept,2); ?></td>                 
    </tr>
</tbody>
</table>
<table class="table table-striped table-sm table-bordered dt-responsive nowrap">
<thead class="thead-light">
    <tr>
    <th class="text-center table-dark font-weight-bold"><?php echo $dt;?></th>
        <th class="text-center table-dark font-weight-bold">SCB</th>
        <th class="text-center table-dark font-weight-bold">KBANK</th>
        <th class="text-center table-dark font-weight-bold">BBL</th>
        <th class="text-center table-dark font-weight-bold"></th>
        <th class="text-center table-dark font-weight-bold">TOTAL</th>
    </tr>
</thead>
<tbody>
    <tr>
        <th class="text-left">TR NON FIX (B/F)</th>
        <td class="text-center"><?php echo  number_format($aSumscbnonfix[0],2); ?></td>
        <td class="text-center"><?php echo  number_format($aSumkbanknonfix[0],2); ?></td>
        <td class="text-center"><?php echo  number_format($aSumbblnonfix[0],2); ?></td>
        <td class="text-center font-weight-bold">TOTAL TR NON FIX (B/F)</td>
        <td class="text-center font-weight-bold"><?php echo  number_format($totalnonfix,2); ?></td>
    </tr>   
    <tr>
        <th class="text-left">TR NON FIX PAID</th>
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
        <td class="text-center font-weight-bold">TOTAL TR NON FIX PAID</td>
        <td class="text-center"></td>
    </tr> 
    <tr>
        <th class="text-left">หนี้ TR NON FIX คงเหลือ</th>
        <td class="text-center"><?php echo  number_format($aSumscbnonfix[0],2); ?></td>
        <td class="text-center"><?php echo  number_format($aSumkbanknonfix[0],2); ?></td>
        <td class="text-center"><?php echo  number_format($aSumbblnonfix[0],2); ?></td>
        <td class="text-center"></td>
        <td class="text-center font-weight-bold"><?php echo  number_format($totalnonfix,2); ?></td>
    </tr>      
</tbody>
</table>
<table class="table table-striped table-sm table-bordered dt-responsive nowrap">
<thead class="thead-light">
    <tr>
        <th class="text-center table-dark font-weight-bold"><?php echo $mont;?></th>
        <th class="text-center table-dark font-weight-bold">SCB</th>
        <th class="text-center table-dark font-weight-bold">KBANK</th>
        <th class="text-center table-dark font-weight-bold">BBL</th>
        <th class="text-center table-dark font-weight-bold"></th>
        <th class="text-center table-dark font-weight-bold">TOTAL</th>
    </tr>
</thead>
<tbody>
    <tr>
        <th class="text-left">Interest Summary</th>
        <td class="text-center"><?php echo number_format($aSumINPAIDSCB[0],2) ;?></td>
        <td class="text-center"><?php echo number_format($aSumINPAIDKBANK[0],2) ;?></td>
        <td class="text-center"><?php echo number_format($aSumINPAIDBBL[0],2) ;?></td>
        <td class="text-center font-weight-bold">TOTAL SUMMARY</td>
        <td class="text-center font-weight-bold"><?php echo number_format($aSumPAIDTOTAL,2) ;?></td>
    </tr>   
    <tr>
        <th class="text-left">Interest Paid</th>
        <td class="text-center"><?php echo number_format($aSumPAIDSCB[0],2) ;?></td>
        <td class="text-center"><?php echo number_format($aSumPAIDSKBANK[0],2) ;?></td>
        <td class="text-center"><?php echo number_format($aSumPAIDBBL[0],2) ;?></td>
        <td class="text-center font-weight-bold">TOTAL PAID</td>
        <td class="text-center font-weight-bold"><?php echo number_format($TOTALPAID,2) ;?></td>
    </tr>
    <tr>
        <th class="text-left">Total Interest</th>
        <td class="text-center"><?php echo number_format($TOTALINSCB,2) ;?></td>
        <td class="text-center"><?php echo number_format($TOTALINKBANK,2) ;?></td>
        <td class="text-center"><?php echo number_format($TOTALINBBL,2) ;?></td>
        <td class="text-center font-weight-bold">TOTAL INTEREST</td>
        <td class="text-center font-weight-bold"><?php echo number_format($TOTALALL,2) ;?></td>
    </tr>   
</tbody>
</table>


