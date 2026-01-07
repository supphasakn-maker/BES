<?php
function DateThaiFullNotime($strDate)
{
    $strYear = date("Y", strtotime($strDate)) + 543;
    $strMonth = date("n", strtotime($strDate));
    $strDay = date("j", strtotime($strDate));
    $strMonthCut = array("", "มกราคม", "กุมภาพันธ์", "มีนาคม", "เมษายน", "พฤษภาคม", "มิถุนายน", "กรกฎาคม", "สิงหาคม", "กันยายน", "ตุลาคม", "พฤศจิกายน", "ธันวาคม");
    $strMonthThai = $strMonthCut[$strMonth];
    return "$strDay $strMonthThai $strYear";
}

function TimeNodate($strDate)
{
    $strHour = date("H", strtotime($strDate));
    $strMinute = date("i", strtotime($strDate));
    return "$strHour:$strMinute";
}
$dd = date("Y-m-d");
$aa = date('Y-m-d', strtotime("-1 days"));
$sql = "SELECT * , (LAG(buy) OVER (ORDER BY id)) AS 'PREVIOUS' , (buy +- LAG(buy) OVER (ORDER BY id)) AS 'PREVIOUS_PRICE' FROM bs_announce_silver WHERE status = '1'  ORDER BY id DESC LIMIT 1";
$rst = $dbc->Query($sql);
// $silver = $dbc->Fetch($rst);
if ($rst->num_rows > 0)
    while ($silver = $rst->fetch_assoc()) {

        $allvat = $silver['sell'] * 7 / 100;
        $all = $silver['sell'] + $allvat;

        $created = $silver['created'];
        $timestamp = strtotime($created);
        $new_date = date("d-m-Y H:i", $timestamp);

?>
    <style>
        .bg-bowins {
            background-color: #12284C !important;
            padding: 35px;
        }

        .priceannounce {
            border: solid black 1px;
            background-image: url('../../../img/LOGO-B-Bowins_02.png');
            background-repeat: no-repeat;
            background-position-x: right;
        }

        .price {
            font-size: 100px;
            font-weight: bold;
            position: relative;
            color: #009245;
        }

        .text-price-up {
            font-size: 70px;
            font-weight: bold;
            position: relative;
            color: #FFFFFF;
            -webkit-text-stroke: 2px #009246;
        }

        .text-price-down {
            font-size: 70px;
            font-weight: bold;
            position: relative;
            color: #FFFFFF;
            -webkit-text-stroke: 2px #BA1924;
        }

        .text-vat {
            font-size: 28px;
            font-weight: bold;
            position: relative;
            color: #F9AE40;
        }

        .text-silver {
            font-size: 35px;
            font-weight: bold;
            position: relative;
            color: #FFFFFF;
        }
    </style>
    <div class="card-body">
        <div class="card mb-2">
            <section class="bg-bowins priceannounce">
                <div class="container">
                    <div class="row">
                        <div class="col-lg-6">
                            <h3 class="text-white">วันที่ <?php echo DateThaiFullNotime($created); ?></h3>
                        </div>
                        <div class="col-lg-6">
                            <h3 class="text-white">เวลาประกาศ <?php echo TimeNodate($created); ?> น.</h3>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12 py-3">
                            <h5 class="text-silver text-center">แท่งเงิน 99.99%</h5>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-6 py-5">
                            <h1 class="text-white text-center">ครั้งที่ <?php echo $silver['no']; ?></h1>
                        </div>

                        <div class="col-lg-6">
                            <span class="price">
                                <?php if ($silver['PREVIOUS_PRICE'] < 0) {
                                    echo '<img src="../../../img/ARRED.png" class="img-fluid" alt="..."><font color="#BA1924; -webkit-text-stroke: 1px rgb(182, 179, 179);">' . abs($silver['PREVIOUS_PRICE']) . '</h1>';
                                } else {
                                    echo '<img src="../../../img/ARGREEN.png" class="img-fluid" alt="..."><font color="#009245">' . number_format($silver['PREVIOUS_PRICE'], 0) . '</font>';
                                }
                                ?>
                            </span>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-6">
                            <?php
                            echo '<h2 class="text-white">รับซื้อ ( BUY )<font color="#BA1924">  <i class="fas fa-sort-down"></i></font></h2>';
                            echo '<span class="text-price-down">' . number_format($silver['buy'], 0) . '</span>';

                            ?>
                        </div>
                        <div class="col-lg-6">
                            <?php
                            echo '<h2 class="text-white">ขายออก ( SELL )<font color="#009245">  <i class="fas fa-sort-up"></i></font></h2>';
                            echo '<span class="text-price-up">' . number_format($silver['sell'], 0) . '</span>';

                            ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12">
                            <h5 class="text-vat text-center">ราคารวม VAT+ <?php echo number_format($all, 2); ?></h5>
                        </div>

                    </div>
                </div>
            </section>
        </div>

    <?php } ?>

    <div class="card mb-2">
        <div class="card-body">
            <a href="javascript:;" class="" onclick="$('.hidebyclick').toggle()">Toggle Detail</a>
            <table class="table table-bordered" style="width:100%">
                <thead class="bg-dark">
                    <th class="text-center text-white" style="height: 50px;overflow:hidden; font-weight: bold;">วันที่</th>
                    <th class="text-center text-white" style="height: 50px;overflow:hidden; font-weight: bold;">ครั้งที่</th>
                    <th class="text-center text-white" style="height: 50px; overflow:hidden; font-weight: bold;">ราคาซื้อเข้า</th>
                    <th class="text-center text-white" style="height: 50px; overflow:hidden; font-weight: bold;">ราคาขายออก</th>
                    <th class="text-center text-white" style="height: 50px; overflow:hidden; font-weight: bold;">ราคาเปลี่ยนแปลง</th>
                </thead>
                <?php
                $sql = "select * from bs_announce_silver where date =  '" . $aa . "' ORDER by no DESC limit 1 ";
                // $sql = "select * from bs_announce_silver where date =  '2023-08-25' ORDER by no DESC limit 1 ";
                $rss = $dbc->Query($sql);
                $lastdate = $dbc->Fetch($rss);
                $sql1 = "select *, (lag(buy, 1, '" . $lastdate['buy'] . "') over (order by id)) as previos , (buy +- lag(buy, 1,'" . $lastdate['buy'] . "') over (order by id)) as previosprice from bs_announce_silver WHERE date = '" . $dd . "' ORDER BY id DESC";

                $rsm = $dbc->Query($sql1);
                while ($line = $dbc->Fetch($rsm)) {
                    $created = $line['created'];
                    $timestamp = strtotime($created);
                    $new_date = date("d-m-Y H:i", $timestamp);
                    $price = $line['previosprice'];


                ?>
                    <tbody>
                        <tr class="hidebyclick" style="display: none">
                            <td class="text-center" style="overflow:hidden; vertical-align: middle;"><span style="height: 60px; overflow:hidden;"><?php echo $new_date; ?></span></td>
                            <td class="text-center" style="overflow:hidden; vertical-align: middle;"><span style="height: 60px; overflow:hidden;"><?php echo $line['no']; ?></span></td>
                            <td class="text-center" style="overflow:hidden; vertical-align: middle;"><span style="height: 60px; overflow:hidden;"><?php echo number_format($line['buy'], 2); ?></span></td>
                            <td class="text-center" style="overflow:hidden; vertical-align: middle;"><span style="height: 60px; overflow:hidden;"><?php echo number_format($line['sell'], 2); ?></span></td>
                            <td class="text-center" style="overflow:hidden; vertical-align: middle;"><span style="height: 60px; overflow:hidden;"><?php echo $price; ?></span></td>
                        </tr>
                    </tbody>
                <?php
                }
                ?>
            </table>
        </div>
    </div>
    </div>