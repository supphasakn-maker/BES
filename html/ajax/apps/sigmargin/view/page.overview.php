
<?php


	$dbc = new dbc;
	$dbc->Connect();

    $today = date("Y-m-d");  
    if(empty($_GET['date'])){
        $date_select = $today;
    }else{
        $date_select = $_GET['date'];
    }

    $sql = "
        SELECT * FROM `bs_smg_payment` a INNER JOIN bs_smg_receiving b ON a.date = b.date
        WHERE  a.type = 'โอนมัดจำ' ORDER BY a.date DESC LIMIT 1
    ";

    if ($result = $dbc->query($sql)) {
        while ($row = $result->fetch_row()) {
            // echo "row => ".print_r($row,true);
            $rs_bs_smg_trade = $dbc->GetRecord("bs_smg_trade","*","date='".$row[1]."' AND purchase_type = 'Buy' ORDER BY id DESC");
            $s = ((($row[4]+$row[3])-($row[8]+$row[9])) * 0.02);
            $usd_bal = ((($row[4]+$row[3])-($row[8]+$row[9])) * 0.02) - $rs_bs_smg_trade['amount'] * $s * $rs_bs_smg_trade['rate_spot'];

        }
    }


?>
<div class="row gutters-sm">
	<div class="col-xl-12 mb-3">
		<table class="table table-bordered table-sm">
			<tbody>
				<tr height="21">
                    <td height="21"  align="left" width="88" style="height:15.75pt; width:66pt">Name:</td>
                    <td width="95" style="border-left:none;width:71pt">BOWIN</td>
                    <td colspan="3"  width="287" style="border-right:.5pt solid black; border-left:none;width:216pt">(Bowins Silver Limited Partnership)</td>
                    <td width="106" style="border-left:none;width:80pt">Date:</td>
                    <td colspan="2"  width="140" style="border-right:.5pt solid black; border-left:none;width:105pt">
                        <?php //echo date("d/m/Y H:i");?>
                        <input type="date" class="form-control" name="date" id="date" placeholder="Purchase Date" value="<?php echo date('Y-m-d');?>">
                    </td>
                </tr>
                <tr height="21" style="height:15.75pt">
                    <td height="21"  style="height:15.75pt;border-top:none">&nbsp;</td>
                    <td style="border-top:none;border-left:none" align=center>B</td>
                    <td style="border-top:none;border-left:none" align=center>S</td>
                    <td style="border-top:none;border-left:none">&nbsp;</td>
                    <td style="border-top:none;border-left:none" align=center>S</td>
                    <td style="border-top:none;border-left:none" align=center>B</td>
                    <td style="border-top:none;border-left:none">&nbsp;</td>
                    <td style="border-top:none;border-left:none">&nbsp;</td>
                </tr>
                <tr height="22" style="height:16.5pt">
                    <td height="22"  align="left" style="height:16.5pt;border-top:none">Products</td>
                    <td style="border-top:none;border-left:none" align=center>Balances</td>
                    <td style="border-top:none;border-left:none" align=center>Ex rate</td>
                    <td style="border-top:none;border-left:none" align=center>in USD equiv</td>
                    <td style="border-top:none;border-left:none">&nbsp;</td>
                    <td style="border-top:none;border-left:none" align=center>Initial Margin</td>
                    <td style="border-top:none;border-left:none">&nbsp;</td>
                    <td style="border-top:none;border-left:none" align=center>Cut_loss<span style="mso-spacerun:yes">&nbsp;</span></td>
                </tr>
                <tr height="22" style="height:16.5pt">
                    <td height="22"  style="height:16.5pt;border-top:none">&nbsp;</td>
                    <td style="border-top:none;border-left:none">&nbsp;</td>
                    <td style="border-top:none;border-left:none">&nbsp;</td>
                    <td style="border-top:none;border-left:none">&nbsp;</td>
                    <td style="border-top:none;border-left:none">&nbsp;</td>
                    <td align=center>10%</td>
                    <td style="border-left:none">&nbsp;</td>
                    <td align=center style="border-left:none">
                        <input style="text-align: right; border:none; width: 40px;" value="5"></input>
                        <span>%</span>
                    </td>
                </tr>
                <tr height="21" style="height:15.75pt">
                    <td height="21"  style="height:15.75pt;border-top:none">&nbsp;</td>
                    <td style="border-top:none;border-left:none">&nbsp;</td>
                    <td style="border-top:none;border-left:none">&nbsp;</td>
                    <td style="border-top:none;border-left:none">&nbsp;</td>
                    <td style="border-top:none;border-left:none">&nbsp;</td>
                    <td style="border-left:none">&nbsp;</td>
                    <td style="border-left:none">&nbsp;</td>
                    <td style="border-left:none">&nbsp;</td>
                </tr>
                <tr height="21" style="height:15.75pt">
                    <td height="21"  align="left" style="height:15.75pt;border-top:none">USD</td>
                    <td align="right" style="border-top:none;border-left:none"><?php echo $usd_bal;?></td>
                    <td align="right" style="border-top:none;border-left:none">1.0000</td>
                    <td align="right" style="border-top:none;border-left:none"><?php echo $usd_bal;?></td>
                    <td align="right" style="border-top:none;border-left:none">&nbsp;</td>
                    <td align="right" style="border-top:none;border-left:none"><?php echo $usd_bal/2;?></td>
                    <td align="right" style="border-top:none;border-left:none">0.00</td>
                </tr>
                <tr height="21" style="height:15.75pt">
                    <td height="21"  style="height:15.75pt;border-top:none">&nbsp;</td>
                    <td style="border-top:none;border-left:none">&nbsp;</td>
                    <td align="right" style="border-top:none;border-left:none">1.0000</td>
                    <td align="right" style="border-top:none;border-left:none">0.00</td>
                    <td style="border-top:none;border-left:none">&nbsp;</td>
                    <td align="right" style="border-top:none;border-left:none"><?php echo $usd_bal;?></td>
                    <td align="right" style="border-top:none;border-left:none">0.00</td>
                    <td align="right" style="border-top:none;border-left:none">0.00</td>
                </tr>
                <tr height="22" style="height:16.5pt">
                    <td height="22"  align="left" style="height:16.5pt;border-top:none">USD/ACL</td>
                    <td style="border-top:none;border-left:none">&nbsp;</td>
                    <td align="right" style="border-top:none;border-left:none">1.0000</td>
                    <td align="right" style="border-top:none;border-left:none">0.00</td>
                    <td style="border-top:none;border-left:none">&nbsp;</td>
                    <td align="right" style="border-top:none;border-left:none">0.00</td>
                    <td align="right" style="border-top:none;border-left:none">0.00</td>
                    <td align="right" style="border-top:none;border-left:none">0.00</td>
                </tr>
                <tr height="22" style="height:16.5pt">
                    <td height="22" align="left" style="height:16.5pt;border-top:none">SIGHT L/C</td>
                    <td align="right"><input style="text-align: right; border: none;" id="dynamic_input" value="0"></input></td>
                    <td align="right" style="border-top:none">1.0000</td>
                    <td align="right" style="border-top:none;border-left:none" id="dynamic_input_1">0</td>
                    <td style="border-top:none;border-left:none">&nbsp;</td>
                    <td align="right" style="border-top:none;border-left:none" id="dynamic_input_2">0</td>
                    <td align="right" style="border-top:none;border-left:none">0.00</td>
                    <td align="right" style="border-top:none;border-left:none">0.00</td>
                </tr>
                <tr height="22" style="height:16.5pt">
                    <td height="22"  align="left" style="height:16.5pt;border-top:none">NAG</td>
                    <td style="border-left:none">&nbsp;</td>
                    <?php
                        $current_spot = $dbc->GetRecord("bs_smg_trade","*"," 1 ORDER BY id DESC LIMIT 1");
                        echo "<td align='right'>".number_format($current_spot['rate_spot'])."</td>";
                    ?>
                    <td align="right" style="border-top:none">0.00 </td>
                    <td align="right" style="border-top:none;border-left:none">
                    <?php
                        $rs_trade = $dbc->GetRecord("bs_smg_trade","*"," 1 ORDER BY id DESC LIMIT 1");
                        echo number_format($rs_trade['amount'])." kg";
                    ?>
                    </td>
                    <td align="right" style="border-top:none;border-left:none">0.00</td>
                    <td align="right" style="border-top:none;border-left:none">0.00</td>
                    <td align="right" style="border-top:none;border-left:none">0.00</td>
                </tr>
                <tr height="22" style="height:16.5pt">
                    <td height="22"  align="left" style="height:16.5pt;border-top:none">XAG</td>
                    <td align="right" style="border-top:none;border-left:none">0.0000</td>
                    <td align="right" style="border-left:none">15.1000</td>
                    <td align="right" style="border-top:none;border-left:none">0.00</td>
                    <td align="right">0.000 kg</td>
                    <td align="right" style="border-top:none">0.00 </td>
                    <td align="right" style="border-top:none;border-left:none">0.00</td>
                    <td align="right" style="border-top:none;border-left:none">0.00</td>
                </tr>
                <tr height="22" style="height:16.5pt">
                    <td height="22"  style="height:16.5pt;border-top:none">&nbsp;</td>
                    <td style="border-top:none;border-left:none">&nbsp;</td>
                    <td style="border-top:none;border-left:none">&nbsp;</td>
                    <td style="border-top:none;border-left:none">&nbsp;</td>
                    <td style="border-left:none">&nbsp;</td>
                    <td style="border-top:none;border-left:none">&nbsp;</td>
                    <td style="border-top:none;border-left:none">&nbsp;</td>
                    <td style="border-top:none;border-left:none">&nbsp;</td>
                </tr>
                <tr height="22" style="height:16.5pt">
                    <td height="22"  align="left" style="height:16.5pt;border-top:none">Net Avail.</td>
                    <td style="border-top:none;border-left:none">&nbsp;</td>
                    <td style="border-top:none;border-left:none">&nbsp;</td>
                    <td align="right" style="border-top:none;border-left:none">0.00</td>
                    <td style="border-top:none;border-left:none">&nbsp;</td>
                    <td align="right">0.00 </td>
                    <td style="border-top:none">0.00 </td>
                    <td align="right" style="border-top:none;border-left:none">0.00</td>
                </tr>
                <tr height="21" style="height:15.75pt">
                    <td height="21"  align="left" style="height:15.75pt;border-top:none">Rate XAU</td>
                    <td style="border-top:none;border-left:none">&nbsp;</td>
                    <td style="border-top:none;border-left:none">&nbsp;</td>
                    <td style="border-top:none;border-left:none">&nbsp;</td>
                    <td style="border-top:none;border-left:none">&nbsp;</td>
                    <td style="border-left:none">&nbsp;</td>
                    <td style="border-top:none;border-left:none">&nbsp;</td>
                    <td style="border-top:none;border-left:none">&nbsp;</td>
                </tr>
                <tr height="21" style="height:15.75pt">
                    <td height="21"  align="left" style="height:15.75pt;border-top:none">Rate XAG</td>
                    <td style="border-top:none;border-left:none">&nbsp;</td>
                    <td style="border-top:none;border-left:none">&nbsp;</td>
                    <td style="border-top:none;border-left:none">&nbsp;</td>
                    <td style="border-top:none;border-left:none">&nbsp;</td>
                    <td style="border-top:none;border-left:none">&nbsp;</td>
                    <td style="border-top:none;border-left:none">&nbsp;</td>
                    <td style="border-top:none;border-left:none">&nbsp;</td>
                </tr>
                    <tr height="0" style="display:none">
                    <td width="88" style="width:66pt"></td>
                    <td width="95" style="width:71pt"></td>
                    <td width="106" style="width:80pt"></td>
                    <td width="109" style="width:82pt"></td>
                    <td width="72" style="width:54pt"></td>
                    <td width="106" style="width:80pt"></td>
                    <td width="45" style="width:34pt"></td>
                    <td width="95" style="width:71pt"></td>
                </tr>
            </tbody>
        </table>
        <table class="table table-sm table-bordered " >
			<tbody>
                <tr>
                    <td colspan="7" align="center">Silver Pending Collection</td>
                    <td colspan="5" align="center">INCLUDED in available funds</td>
                </tr>
                <tr>
                    <td rowspan="2" width=150 align="center"  style="padding-top: 35px;" colspan="1">DATE</td>
                    <td colspan="3" align="center">USD</td>
                    <td colspan="3" align="center">Silver</td>
                </tr>
                <tr>
                    <td align="center">DR</td>
                    <td align="center">CR</td>
                    <td align="center">BAL</td>
                    <td align="center">DR</td>
                    <td align="center">CR</td>
                    <td align="center">BAL</td>
                    <td width=70 align="center">Rollover</td>
                    <td width=70 align="center">SPOT Sell</td>
                    <td width=70 align="center">SPOT Buy</td>
                    <td width=70 align="center">Cashจาก MetalsWeb</td>
                </tr> 
                <tr>
                <?php
                        $sql = "
                            SELECT * FROM `bs_smg_payment` a INNER JOIN bs_smg_receiving b ON a.date = b.date
                            WHERE  MONTH(a.date) = MONTH('$date_select') AND a.type = 'โอนมัดจำ' ORDER BY a.date ASC
                        ";

                        if ($result = $dbc->query($sql)) {
                            while ($row = $result->fetch_row()) {
                                // echo "row => ".print_r($row[1]);
                                $rs_bs_smg_trade = $dbc->GetRecord("bs_smg_trade","*","date='".$row[1]."' AND purchase_type = 'Buy' ORDER BY id DESC");
                                $rs_bs_smg_rollover = $dbc->GetRecord("bs_smg_rollover","*","date='".$row[1]."' AND type = 'Buy' ORDER BY id DESC");
                                $rs_bs_smg_cash = $dbc->GetRecord("bs_smg_cash","*","date='".$row[1]."' ORDER BY id DESC");
                                // echo "rs_bs_smg_rollover => ".print_r($rs_bs_smg_rollover,true);
                                
                                $s = ((($row[4]+$row[3])-($row[8]+$row[9])) * 0.02);
                                $usd_bal = ((($row[4]+$row[3])-($row[8]+$row[9])) * 0.02) - $rs_bs_smg_trade['amount'] * $s * $rs_bs_smg_trade['rate_spot'];
                                $silver_bal = ($row[8]-$row[9]) - ($rs_bs_smg_trade['amount'] * $s);
                                
                                $Rollover = 0;
                                $SPOT_Sell = 0;
                                $SPOT_Buy = 0;
                                $Cashจาก_MetalsWeb = 0;
                                if($rs_bs_smg_rollover != 0){
                                    $Rollover = $rs_bs_smg_rollover['amount'];
                                    $SPOT_Sell = $rs_bs_smg_rollover['rate_spot'];
                                    $SPOT_Buy = ($rs_bs_smg_rollover['amount']+$rs_bs_smg_rollover['rate_spot']);
                                    if($rs_bs_smg_cash != 0){
                                        $Cashจาก_MetalsWeb = $rs_bs_smg_cash['amount'];
                                    }
                                }

                                echo "<tr>";
                                echo "<td align=center>".date("d-M-Y", strtotime($row[1]))."</td>";
                                echo "<td align=center width=120>".$rs_bs_smg_trade['amount'] * $s * $rs_bs_smg_trade['rate_spot']."</td>";
                                echo "<td align=center width=120>".((($row[4]+$row[3])-($row[8]+$row[9])) * 0.02)."</td>";
                                echo "<td align=center width=120>".$usd_bal."</td>";
                                echo "<td align=center width=120>".($row[8]-$row[9])."</td>";
                                echo "<td align=center width=120>".$rs_bs_smg_trade['amount'] * $s."</td>";
                                echo "<td align=center width=120>".$silver_bal."</td>";

                                echo "<td align=center>".$Rollover."</td>";
                                echo "<td align=center>".$SPOT_Sell."</td>";
                                echo "<td align=center>".$SPOT_Buy."</td>";
                                echo "<td align=center>".$Cashจาก_MetalsWeb."</td>";
                                echo "</tr>";
                            }
                            $result -> free_result();
                        }
                    
                    ?> 
                </tr>
            </tbody>
        </table>
    </div>
</div>
<?php $dbc->Close();?>