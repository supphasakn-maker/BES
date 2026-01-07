<?php
    $dt = date($_POST['date']);
    $mont = date("Y-m",strtotime($dt));
    $totaltr = 220000000 + 10000000 + 350000000;


    $sql = "SELECT SUM(loader) AS loader , SUM(paid) AS paid , SUM(tr_fix) AS tr_fix , SUM(tr_non_fix) AS tr_non_fix, 
    SUM(load_balance) AS load_balance, SUM(balance) AS balance, SUM(tr_non_fix_usd) AS tr_non_fix_usd,
    SUM(interest_summary) AS interest_summary , SUM(interest_paid) AS interest_paid
    FROM bs_transfer_report WHERE bank = 'SCB' AND  bs_transfer_report.date = '".$dt."' ";
    $rst = $dbc->Query($sql);
    $transferscb = $dbc->Fetch($rst);
    $total_loader_scb = $transferscb['loader'];
    $total_paid_scb = $transferscb['paid'];
    $total_trfix_scb = $transferscb['tr_fix'];
    $total_trnonfix_scb = $transferscb['tr_non_fix'];
    $total_loadbalance_scb = $transferscb['load_balance'];
    $total_balance_scb = $transferscb['balance'];
    $total_trnonfix_usd_scb = $transferscb['tr_non_fix_usd'];
    $total_interest_summary_scb = $transferscb['interest_summary'];
    $total_interest_paid_scb = $transferscb['interest_paid'];

    $sql = "SELECT SUM(loader) AS loader , SUM(paid) AS paid , SUM(tr_fix) AS tr_fix , SUM(tr_non_fix) AS tr_non_fix,
    SUM(load_balance) AS load_balance, SUM(balance) AS balance,SUM(tr_non_fix_usd) AS tr_non_fix_usd,
    SUM(interest_summary) AS interest_summary , SUM(interest_paid) AS interest_paid
    FROM bs_transfer_report WHERE bank = 'KBANK' AND  bs_transfer_report.date = '".$dt."' ";
    $rst = $dbc->Query($sql);
    $transferscb = $dbc->Fetch($rst);
    $total_loader_kbank = $transferscb['loader'];
    $total_paid_kbank = $transferscb['paid'];
    $total_trfix_kbank = $transferscb['tr_fix'];
    $total_trnonfix_kbank = $transferscb['tr_non_fix'];
    $total_loadbalance_kbank = $transferscb['load_balance'];
    $total_balance_kbank = $transferscb['balance'];
    $total_trnonfix_usd_kbank= $transferscb['tr_non_fix_usd'];
    $total_interest_summary_kbank = $transferscb['interest_summary'];
    $total_interest_paid_kbank = $transferscb['interest_paid'];


    $sql = "SELECT SUM(loader) AS loader , SUM(paid) AS paid ,SUM(tr_fix) AS tr_fix , SUM(tr_non_fix) AS tr_non_fix,
    SUM(load_balance) AS load_balance, SUM(balance) AS balance,SUM(tr_non_fix_usd) AS tr_non_fix_usd,
    SUM(interest_summary) AS interest_summary , SUM(interest_paid) AS interest_paid
    FROM bs_transfer_report WHERE bank = 'BBL' AND  bs_transfer_report.date = '".$dt."' ";
    $rst = $dbc->Query($sql);
    $transferscb = $dbc->Fetch($rst);
    $total_loader_bbl = $transferscb['loader'];
    $total_paid_bbl = $transferscb['paid'];
    $total_trfix_bbl = $transferscb['tr_fix'];
    $total_trnonfix_bbl = $transferscb['tr_non_fix'];
    $total_loadbalance_bbl = $transferscb['load_balance'];
    $total_balance_bbl = $transferscb['balance'];
    $total_trnonfix_usd_bbl = $transferscb['tr_non_fix_usd'];
    $total_interest_summary_bbl = $transferscb['interest_summary'];
    $total_interest_paid_bbl = $transferscb['interest_paid'];



    $totalnonfix =  $total_trnonfix_scb+ $total_trnonfix_kbank+ $total_trnonfix_bbl;
    $total_loadbalance =  $total_loadbalance_scb +  $total_loadbalance_kbank +  $total_loadbalance_bbl ;
    $total_balance = $total_balance_scb + $total_balance_kbank + $total_balance_bbl;
    $total_trnonfix_usd = $total_trnonfix_usd_scb + $total_trnonfix_usd_kbank +$total_trnonfix_usd_bbl;
    $total_interest_summary = $total_interest_summary_scb + $total_interest_summary_kbank + $total_interest_summary_bbl;
    $total_interest_paid = $total_interest_paid_scb + $total_interest_paid_kbank + $total_interest_paid_bbl;

    $total_interest = $total_interest_summary_scb+$total_interest_paid_scb+$total_interest_summary_kbank+$total_interest_paid_kbank+$total_interest_summary_bbl+$total_interest_paid_bbl;
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
        <th class="text-left">ภาระ TR ยกมา</th>
        <td class="text-center"><?php echo number_format($total_loader_scb,2);?></td>
        <td class="text-center"><?php echo number_format($total_loader_kbank,2);?></td>
        <td class="text-center"><?php echo number_format($total_loader_bbl,2);?></td>
        <td class="text-center"></td>
        <td class="text-center"></td>
    </tr>   
    <tr>
        <th class="text-left">TR Paid</th>
        <td class="text-center"><?php echo number_format($total_paid_scb,2);?></td>
        <td class="text-center"><?php echo number_format($total_paid_kbank,2);?></td>
        <td class="text-center"><?php echo number_format($total_paid_bbl,2);?></td>
        <td class="text-center"></td>
        <td class="text-center"></td>
    </tr>
    <tr>
        <th class="text-left">OPEN TR FIX</th>
        <td class="text-center"><?php echo  number_format($total_trfix_scb,2); ?></td>
        <td class="text-center"><?php echo  number_format($total_trfix_kbank,2); ?></td>
        <td class="text-center"><?php echo  number_format($total_trfix_bbl,2); ?></td>
        <td class="text-center"></td>
        <td class="text-center"></td>
    </tr>
    <tr>
        <th class="text-left">OPEN TR NON FIX (B/F)</th>
        <td class="text-center"><?php echo  number_format($total_trnonfix_scb,2); ?></td>
        <td class="text-center"><?php echo  number_format($total_trnonfix_kbank,2); ?></td>
        <td class="text-center"><?php echo  number_format($total_trnonfix_bbl,2); ?></td>
        <td class="text-center font-weight-bold">TOTAL TR NON FIX (B/F)</td>
        <td class="text-center font-weight-bold"><?php echo  number_format($totalnonfix,2); ?></td>
    </tr>  
    <tr>
        <th class="text-left">ภาระ TR คงเหลือ</th>
        <td class="text-center"><?php echo  number_format($total_loadbalance_scb,2); ?></td>
        <td class="text-center"><?php echo  number_format($total_loadbalance_kbank,2); ?></td>
        <td class="text-center"><?php echo  number_format($total_loadbalance_bbl,2); ?></td>
        <td class="text-center font-weight-bold">ยอดหนี้ TR ทั้งหมด</td>
        <td class="text-center font-weight-bold"><?php echo  number_format($total_loadbalance,2); ?></td>
    </tr>   
    <tr>
        <th class="text-left">วงเงิน TR คงเหลือ</th>
        <td class="text-center"><?php echo  number_format($total_balance_scb,2); ?></td>
        <td class="text-center"><?php echo  number_format($total_balance_kbank,2); ?></td>
        <td class="text-center"><?php echo  number_format($total_balance_bbl,2); ?></td>
        <td class="text-center font-weight-bold">วงเงิน TR เหลือทั้งหมด</td>
        <td class="text-center font-weight-bold"><?php echo  number_format($total_balance,2); ?></td>                 
    </tr>
</tbody>
</table>
<table class="table table-striped table-sm table-bordered dt-responsive nowrap" >
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
        <td class="text-center"><?php echo  number_format($total_trnonfix_usd_scb,2); ?></td>
        <td class="text-center"><?php echo  number_format($total_trnonfix_usd_kbank,2); ?></td>
        <td class="text-center"><?php echo  number_format($total_trnonfix_usd_bbl,2); ?></td>
        <td class="text-center font-weight-bold">TOTAL TR NON FIX (B/F)</td>
        <td class="text-center font-weight-bold"><?php echo  number_format($total_trnonfix_usd,2); ?></td>
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
        <td class="text-center"><?php echo  number_format($total_trnonfix_usd_scb,2); ?></td>
        <td class="text-center"><?php echo  number_format($total_trnonfix_usd_kbank,2); ?></td>
        <td class="text-center"><?php echo  number_format($total_trnonfix_usd_bbl,2); ?></td>
        <td class="text-center font-weight-bold">TOTAL TR NON FIX (B/F)</td>
        <td class="text-center font-weight-bold"><?php echo  number_format($total_trnonfix_usd,2); ?></td>
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
        <td class="text-center"><?php echo number_format($total_interest_summary_scb,2) ;?></td>
        <td class="text-center"><?php echo number_format($total_interest_summary_kbank,2) ;?></td>
        <td class="text-center"><?php echo number_format($total_interest_summary_bbl,2) ;?></td>
        <td class="text-center font-weight-bold">TOTAL SUMMARY</td>
        <td class="text-center font-weight-bold"><?php echo number_format($total_interest_summary,2) ;?></td>
    </tr>   
    <tr>
        <th class="text-left">Interest Paid</th>
        <td class="text-center"><?php echo number_format($total_interest_paid_scb,2) ;?></td>
        <td class="text-center"><?php echo number_format($total_interest_paid_kbank,2) ;?></td>
        <td class="text-center"><?php echo number_format($total_interest_paid_bbl,2) ;?></td>
        <td class="text-center font-weight-bold">TOTAL PAID</td>
        <td class="text-center font-weight-bold"><?php echo number_format($total_interest_paid,2) ;?></td>
    </tr>
    <tr>
        <th class="text-left">Total Interest</th>
        <td class="text-center"><?php echo number_format($total_interest_summary_scb+$total_interest_paid_scb,2) ;?></td>
        <td class="text-center"><?php echo number_format($total_interest_summary_kbank+$total_interest_paid_kbank,2) ;?></td>
        <td class="text-center"><?php echo number_format($total_interest_summary_bbl+$total_interest_paid_bbl,2) ;?></td>
        <td class="text-center font-weight-bold">TOTAL INTEREST</td>
        <td class="text-center font-weight-bold"><?php echo number_format($total_interest,2) ;?></td>
    </tr>   
</tbody>
</table>




