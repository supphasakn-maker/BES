<?php
/*
 * 2022-11-03 : Created Function : Pattaragun Junthhomkai.
 * 
 */

class engine
{
    private $dbc = null;
    private $oz = 32.1507;

    function __construct($dbc)
    {
        $this->dbc = $dbc;
    }

    function get_balance($date)
    {
        $dbc = $this->dbc;
        $aBalance = array(0, 0);
        if ($date > strtotime("2025-01-01")) {
            $date_begin = strtotime(date("Y-m-01", $date));
            $date_last_month = $date_begin - (86400 * 5);

            $sqlDate = "BETWEEN '2025-01-01' AND '" . date("Y-m-t", $date_last_month) . "'";
            $line = $dbc->GetRecord("bs_smg_trade", "SUM(amount*(rate_spot+rate_pmdc)*" . $this->oz . ")", "date " . $sqlDate . " AND purchase_type='Buy'");
            $aBalance[0] -= $line[0];
            $line = $dbc->GetRecord("bs_smg_rollover", "SUM(amount*(rate_spot)*" . $this->oz . ")", "date " . $sqlDate . " AND entry='debit' AND type='Buy'");
            $aBalance[0] -= $line[0];
            $line = $dbc->GetRecord("bs_smg_other", "SUM(usd_debit)", "date " . $sqlDate);
            $aBalance[0] -= $line[0];

            $line = $dbc->GetRecord("bs_smg_trade", "SUM(amount*(rate_spot+rate_pmdc)*" . $this->oz . ")", "date " . $sqlDate . " AND purchase_type='Sell'");
            $aBalance[0] += $line[0];
            $line = $dbc->GetRecord("bs_smg_payment", "SUM(amount_usd)", "date " . $sqlDate);
            $aBalance[0] += $line[0];

            $sql = "SELECT SUM((amount * CASE WHEN rate_pmdc < 0 THEN ABS(rate_pmdc) ELSE -rate_pmdc END * 32.1507+ transfer)) AS total_result
            FROM bs_smg_receiving WHERE date BETWEEN '2025-01-01' AND '" . date("Y-m-t", $date_last_month) . "' ";
            $rst = $dbc->Query($sql);
            $line = $dbc->Fetch($rst);
            $aBalance[0] += $line['total_result'];
            // $line = $dbc->GetRecord("bs_smg_receiving", "SUM((amount*rate_pmdc*" . $this->oz . ")+transfer)", "date " . $sqlDate);
            // $aBalance[0] += $line[0];
            $line = $dbc->GetRecord("bs_smg_rollover", "SUM(amount*(rate_spot)*" . $this->oz . ")", "date " . $sqlDate . " AND entry='credit' AND type!='Buy'");
            $aBalance[0] += $line[0];
            $line = $dbc->GetRecord("bs_smg_other", "SUM(usd_credit)", "date " . $sqlDate);
            $aBalance[0] += $line[0];
            $line = $dbc->GetRecord("bs_smg_interest", "SUM(interest)", "date " . $sqlDate);
            $aBalance[0] -= $line[0];

            $line = $dbc->GetRecord("bs_smg_claim", "SUM(amount*(rate_spot)*" . $this->oz . ")", "date " . $sqlDate . " AND purchase_type='Sell'");
            $aBalance[0] += $line[0];
            $line = $dbc->GetRecord("bs_smg_claim", "SUM(amount*(rate_spot)*" . $this->oz . ")", "date " . $sqlDate . " AND purchase_type='Buy'");
            $aBalance[0] -= $line[0];



            $line = $dbc->GetRecord("bs_smg_trade", "SUM(amount*" . $this->oz . ")", "date " . $sqlDate . " AND purchase_type='Sell'");
            $aBalance[1] -= $line[0];
            $line = $dbc->GetRecord("bs_smg_receiving", "SUM(amount*" . $this->oz . ")", "date " . $sqlDate);
            $aBalance[1] -= $line[0];
            $line = $dbc->GetRecord("bs_smg_rollover", "SUM(amount*" . $this->oz . ")", "date " . $sqlDate . " AND entry='credit' AND type!='Buy'");
            $aBalance[1] -= $line[0];
            $line = $dbc->GetRecord("bs_smg_other", "SUM(usd_debit)", "date " . $sqlDate);
            $aBalance[1] += $line[0];

            $line = $dbc->GetRecord("bs_smg_trade", "SUM(amount*" . $this->oz . ")", "date " . $sqlDate . " AND purchase_type='Buy'");
            $aBalance[1] += $line[0];
            $line = $dbc->GetRecord("bs_smg_rollover", "SUM(amount*" . $this->oz . ")", "date " . $sqlDate . " AND type='Buy'");
            $aBalance[1] += $line[0];
            $line = $dbc->GetRecord("bs_smg_claim", "SUM(amount*" . $this->oz . ")", "date " . $sqlDate . " AND purchase_type='Buy'");
            $aBalance[1] += $line[0];
            $line = $dbc->GetRecord("bs_smg_other", "SUM(amount_credit)", "date " . $sqlDate);
            $aBalance[1] += $line[0];
        }

        return $aBalance;
    }

    function get_usd_debit($date_sql)
    {
        $dbc = $this->dbc;
        $total = 0;
        $msgs = array();

        $sql = "SELECT * FROM bs_smg_trade WHERE date ='" . $date_sql . "' AND purchase_type='Buy' ";
        $rst = $dbc->Query($sql);
        while ($line = $dbc->Fetch($rst)) {
            $value = $line['amount'] * ($line['rate_spot'] + $line['rate_pmdc']) * $this->oz;
            $total += $value;
            $msg = $line['amount'] . "*(" . $line['rate_spot'] . "+" . $line['rate_pmdc'] . ")*" . $this->oz;
            array_push($msgs, "<div>bs_smg_trade|" . $line['id'] . "|<span class='text-danger'>" . $value . "</span>|" . $msg . "</div>");
        }

        $sql = "SELECT * FROM bs_smg_rollover WHERE date ='" . $date_sql . "' AND entry='debit' AND type='Buy' ";
        $rst = $dbc->Query($sql);
        while ($line = $dbc->Fetch($rst)) {
            $value = $line['amount'] * ($line['rate_spot']) * $this->oz;
            $total += $value;
            $msg = $line['amount'] . "*(" . $line['rate_spot'] . ")*" . $this->oz;
            array_push($msgs, "<div>bs_smg_rollover|" . $line['id'] . "|" . $value . "|" . $msg . "</div>");
        }



        $sql = "SELECT * FROM bs_smg_other WHERE date ='" . $date_sql . "'";
        $rst = $dbc->Query($sql);
        while ($line = $dbc->Fetch($rst)) {
            $total += $line['usd_debit'];
            array_push($msgs, "<div>bs_smg_other|" . $line['id'] . "|" . $line['usd_debit'] . "</div>");
        }
        $sql = "SELECT * FROM bs_smg_claim WHERE date ='" . $date_sql . "' AND purchase_type = 'Buy' ";
        $rst = $dbc->Query($sql);
        while ($line = $dbc->Fetch($rst)) {
            $value = $line['amount'] * ($line['rate_spot']) * $this->oz;
            $total += $value;
            $msg = $line['amount'] . "*(" . $line['rate_spot'] . ")*" . $this->oz;
            array_push($msgs, "<div>bs_smg_claim|" . $value . "|" . $msg . "</div>");
        }
        return array($total, $msgs);
    }

    function get_usd_credit($date_sql)
    {
        $dbc = $this->dbc;
        $total = 0;
        $msgs = array();

        $sql = "SELECT * FROM bs_smg_trade WHERE date ='" . $date_sql . "' AND purchase_type='Sell' ";
        $rst = $dbc->Query($sql);
        while ($line = $dbc->Fetch($rst)) {
            $value = $line['amount'] * ($line['rate_spot'] + $line['rate_pmdc']) * $this->oz;
            $total += $value;
            $msg = $line['amount'] . "*(" . $line['rate_spot'] . "+" . $line['rate_pmdc'] . ")*" . $this->oz;
            array_push($msgs, "<div>bs_smg_trade|" . $line['id'] . "|" . $value . "|" . $msg . "</div>");
        }

        $sql = "SELECT * FROM bs_smg_payment WHERE date ='" . $date_sql . "'";
        $rst = $dbc->Query($sql);
        while ($line = $dbc->Fetch($rst)) {
            $total += $line['amount_usd'];
            array_push($msgs, "<div>bs_smg_payment|" . $line['id'] . "|" . $line['amount_usd'] . "</div>");
        }

        $sql = "SELECT * FROM bs_smg_receiving WHERE date ='" . $date_sql . "'";
        $rst = $dbc->Query($sql);
        while ($line = $dbc->Fetch($rst)) {
            if ($line['rate_pmdc'] == '-0.0700') {
                $pmdc = abs($line['rate_pmdc']);
            } else {
                $pmdc = -$line['rate_pmdc'];
            }
            $value = $line['amount'] * ($pmdc) * $this->oz + $line['transfer'];
            $total += $value;
            $msg = $line['amount'] . "*" . ($pmdc) . "*" . $this->oz . "+" . $line['transfer'];
            array_push($msgs, "<div>bs_smg_receiving|" . $line['id'] . "|" . $value . "|" . $msg . "</div>");
        }

        $sql = "SELECT * FROM bs_smg_rollover WHERE date ='" . $date_sql . "' AND entry='credit' AND type!='Buy' ";
        $rst = $dbc->Query($sql);
        while ($line = $dbc->Fetch($rst)) {
            $value = $line['amount'] * ($line['rate_spot']) * $this->oz;
            $total += $value;
            $msg = $line['amount'] . "*(" . $line['rate_spot'] . ")*" . $this->oz;
            array_push($msgs, "<div>bs_smg_rollover|" . $line['id'] . "|" . $value . "|" . $msg . "</div>");
        }

        $sql = "SELECT * FROM bs_smg_other WHERE date ='" . $date_sql . "'";
        $rst = $dbc->Query($sql);
        while ($line = $dbc->Fetch($rst)) {
            $total += $line['usd_credit'];
            array_push($msgs, "<div>bs_smg_other|" . $line['id'] . "|" . $line['usd_credit'] . "</div>");
        }

        $sql = "SELECT * FROM bs_smg_interest WHERE date ='" . $date_sql . "'";
        $rst = $dbc->Query($sql);
        while ($line = $dbc->Fetch($rst)) {
            $total -= $line['interest'];
            array_push($msgs, "<div>bs_smg_interest|" . $line['id'] . "|-" . $line['interest'] . "</div>");
        }

        $sql = "SELECT * FROM bs_smg_claim WHERE date ='" . $date_sql . "' AND purchase_type = 'Sell' ";
        $rst = $dbc->Query($sql);
        while ($line = $dbc->Fetch($rst)) {
            $value = $line['amount'] * ($line['rate_spot']) * $this->oz;
            $total += $value;
            $msg = $line['amount'] . "*(" . $line['rate_spot'] . ")*" . $this->oz;
            array_push($msgs, "<div>bs_smg_claim|" . $value . "|" . $msg . "</div>");
        }
        return array($total, $msgs);
    }
    function get_xag_debit($date_sql)
    {
        $dbc = $this->dbc;
        $total = 0;
        $msgs = array();

        $sql = "SELECT * FROM bs_smg_trade WHERE date ='" . $date_sql . "' AND purchase_type='Sell' ";
        $rst = $dbc->Query($sql);
        while ($line = $dbc->Fetch($rst)) {
            $value = $line['amount'] * $this->oz;
            $total += $value;
            $msg = $line['amount'] . "*" . $this->oz;
            array_push($msgs, "<div>bs_smg_trade|" . $line['id'] . "|<span class='text-danger'>-" . $value . "</span>|" . $msg . "</div>");
        }

        $sql = "SELECT * FROM bs_smg_receiving WHERE date ='" . $date_sql . "'";
        $rst = $dbc->Query($sql);
        while ($line = $dbc->Fetch($rst)) {
            $value = $line['amount'] * $this->oz;
            $total += $value;
            $msg = $line['amount'] . "*" . $this->oz;
            array_push($msgs, "<div>bs_smg_receiving|" . $line['id'] . "|<span class='text-danger'>-" . $value . "</span>|" . $msg . "</div>");
        }

        $sql = "SELECT * FROM bs_smg_rollover WHERE date ='" . $date_sql . "' AND entry='credit' AND type!='Buy' ";
        $rst = $dbc->Query($sql);
        while ($line = $dbc->Fetch($rst)) {
            $value = $line['amount'] * $this->oz;
            $total += $value;
            $msg = $line['amount'] . "*" . $this->oz;
            array_push($msgs, "<div>bs_smg_rollover|" . $line['id'] . "|" . $value . "|" . $msg . "</div>");
        }

        $sql = "SELECT * FROM bs_smg_other WHERE date ='" . $date_sql . "'";
        $rst = $dbc->Query($sql);
        while ($line = $dbc->Fetch($rst)) {
            $total -= $line['usd_debit'];
            array_push($msgs, "<div>bs_smg_other|" . $line['id'] . "|-" . $line['usd_debit'] . "</div>");
        }
        $sql = "SELECT * FROM bs_smg_other WHERE date ='" . $date_sql . "'";
        $rst = $dbc->Query($sql);
        while ($line = $dbc->Fetch($rst)) {
            $total -= $line['amount_debit'];
            array_push($msgs, "<div>bs_smg_other|" . $line['id'] . "|" . $line['amount_debit'] . "</div>");
        }
        return array($total, $msgs);
    }
    function get_xag_credit($date_sql)
    {
        $dbc = $this->dbc;
        $total = 0;
        $msgs = array();

        $sql = "SELECT * FROM bs_smg_trade WHERE date ='" . $date_sql . "' AND purchase_type='Buy' ";
        $rst = $dbc->Query($sql);
        while ($line = $dbc->Fetch($rst)) {
            $value = $line['amount'] * $this->oz;
            $total += $value;
            $msg = $line['amount'] . "*" . $this->oz;
            array_push($msgs, "<div>bs_smg_trade|" . $line['id'] . "|<span class='text-danger'>" . $value . "</span>|" . $msg . "</div>");
        }

        $sql = "SELECT * FROM bs_smg_rollover WHERE date ='" . $date_sql . "' AND type='Buy' ";
        $rst = $dbc->Query($sql);
        while ($line = $dbc->Fetch($rst)) {
            $value = $line['amount'] * $this->oz;
            $total += $value;
            $msg = $line['amount'] . "*" . $this->oz;
            array_push($msgs, "<div>bs_smg_rollover|" . $line['id'] . "|" . $value . "|" . $msg . "</div>");
        }

        $sql = "SELECT * FROM bs_smg_other WHERE date ='" . $date_sql . "'";
        $rst = $dbc->Query($sql);
        while ($line = $dbc->Fetch($rst)) {
            $total += $line['amount_credit'];
            array_push($msgs, "<div>bs_smg_other|" . $line['id'] . "|" . $line['amount_credit'] . "</div>");
        }

        $sql = "SELECT * FROM bs_smg_claim WHERE date ='" . $date_sql . "' AND purchase_type = 'Buy' ";
        $rst = $dbc->Query($sql);
        while ($line = $dbc->Fetch($rst)) {
            $value = $line['amount'] * $this->oz;
            $total += $value;
            $msg = $line['amount'] . "*" . $this->oz;
            array_push($msgs, "<div>bs_smg_claim|" . $line['id'] . "|" . $value . "|" . $msg . "</div>");
        }
        return array($total, $msgs);
    }

    function show_tr($value, $decimal, $default_red = false, $msgs = array())
    {
        $tooltip = ' data-toggle="tooltip" data-html="true"';
        $s = '';

        if (!$default_red) {
            if ($value < 0) {
                $class = " text-danger";
                $show_value = '(' . number_format(abs($value), $decimal) . ')';
            } else {
                $class = "";
                $show_value = '' . number_format($value, $decimal) . '';
            }
        } else {
            if ($value <= 0) {
                $class = " text-danger";
                $show_value = '(' . number_format(abs($value), $decimal) . ')';
            } else {
                $class = "";
                $show_value = '' . number_format($value, $decimal) . '';
            }
        }
        if (count($msgs) == 0) $tooltip = "";
        $s .= '<td class="text-right' . $class . '"' . $tooltip . ' title="' . join("\n", $msgs) . '">' . $show_value . '</td>';
        return $s;
    }

    function show_trnew($value, $decimal, $default_red = false, $msgs = array())
    {
        $tooltip = ' data-toggle="tooltip" data-html="true"';
        $s = '';

        if (!$default_red) {
            if ($value < 0) {
                $class = " text-danger";
                $show_value = '(' . number_format(($value), $decimal) . ')';
            } else {
                $class = "";
                $show_value = '' . number_format($value, $decimal) . '';
            }
        } else {
            if ($value <= 0) {
                $class = " text-danger";
                $show_value = '(' . number_format(($value), $decimal) . ')';
            } else {
                $class = "";
                $show_value = '' . number_format($value, $decimal) . '';
            }
        }
        if (count($msgs) == 0) $tooltip = "";
        $s .= '<td class="text-right' . $class . '"' . $tooltip . ' title="' . join("\n", $msgs) . '">' . $show_value . '</td>';
        return $s;
    }
}
