<?php
/*
 * 2024-08-13 : Created Function : Pattaragun J.
 * 2025-07-29 : Improved Code - Compatible with existing DBC methods
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
        $aBalance = array(765.17, -332.924);

        if ($date > strtotime("2025-07-19")) {
            $date_begin = strtotime(date("Y-m-01", $date));
            $date_last_month = $date_begin - (86400 * 5);

            $sqlDate = "BETWEEN '2025-07-19' AND '" . date("Y-m-t", $date_last_month) . "'";

            // USD Balance calculations (deductions)
            $line = $dbc->GetRecord("bs_smg_stx_trade", "SUM(amount*(rate_spot+rate_pmdc)*" . $this->oz . ")", "date " . $sqlDate . " AND purchase_type='Buy'");
            $aBalance[0] -= $this->safeValue($line);

            $line = $dbc->GetRecord("bs_smg_stx_rollover", "SUM(amount*(rate_spot)*" . $this->oz . ")", "date " . $sqlDate . " AND entry='debit' AND type='Buy'");
            $aBalance[0] -= $this->safeValue($line);

            $line = $dbc->GetRecord("bs_smg_stx_other", "SUM(usd_debit)", "date " . $sqlDate);
            $aBalance[0] -= $this->safeValue($line);

            $line = $dbc->GetRecord("bs_smg_stx_interest", "SUM(interest)", "date " . $sqlDate);
            $aBalance[0] -= $this->safeValue($line);

            $line = $dbc->GetRecord("bs_smg_stx_claim", "SUM(amount*(rate_spot)*" . $this->oz . ")", "date " . $sqlDate . " AND purchase_type='Buy'");
            $aBalance[0] -= $this->safeValue($line);

            // USD Balance calculations (additions)
            $line = $dbc->GetRecord("bs_smg_stx_trade", "SUM(amount*(rate_spot+rate_pmdc)*" . $this->oz . ")", "date " . $sqlDate . " AND purchase_type='Sell'");
            $aBalance[0] += $this->safeValue($line);

            $line = $dbc->GetRecord("bs_smg_stx_payment", "SUM(amount_usd)", "date " . $sqlDate);
            $aBalance[0] += $this->safeValue($line);

            // Special handling for receiving with PMDC calculation
            $sql = "SELECT SUM((amount * CASE WHEN rate_pmdc < 0 THEN ABS(rate_pmdc) ELSE -rate_pmdc END * 32.1507 + transfer)) AS total_result
                    FROM bs_smg_stx_receiving WHERE date BETWEEN '2025-07-01' AND '" . date("Y-m-t", $date_last_month) . "' ";
            $rst = $dbc->Query($sql);
            $line = $dbc->Fetch($rst);
            $aBalance[0] += $this->safeValue($line['total_result']);

            $line = $dbc->GetRecord("bs_smg_stx_rollover", "SUM(amount*(rate_spot)*" . $this->oz . ")", "date " . $sqlDate . " AND entry='credit' AND type!='Buy'");
            $aBalance[0] += $this->safeValue($line);

            $line = $dbc->GetRecord("bs_smg_stx_other", "SUM(usd_credit)", "date " . $sqlDate);
            $aBalance[0] += $this->safeValue($line);

            $line = $dbc->GetRecord("bs_smg_stx_claim", "SUM(amount*(rate_spot)*" . $this->oz . ")", "date " . $sqlDate . " AND purchase_type='Sell'");
            $aBalance[0] += $this->safeValue($line);

            // XAG Balance calculations (deductions)
            $line = $dbc->GetRecord("bs_smg_stx_trade", "SUM(amount*" . $this->oz . ")", "date " . $sqlDate . " AND purchase_type='Sell'");
            $aBalance[1] -= $this->safeValue($line);

            $line = $dbc->GetRecord("bs_smg_stx_receiving", "SUM(amount*" . $this->oz . ")", "date " . $sqlDate);
            $aBalance[1] -= $this->safeValue($line);

            $line = $dbc->GetRecord("bs_smg_stx_rollover", "SUM(amount*" . $this->oz . ")", "date " . $sqlDate . " AND entry='credit' AND type!='Buy'");
            $aBalance[1] -= $this->safeValue($line);

            $line = $dbc->GetRecord("bs_smg_stx_other", "SUM(usd_debit)", "date " . $sqlDate);
            $aBalance[1] += $this->safeValue($line);

            // XAG Balance calculations (additions)
            $line = $dbc->GetRecord("bs_smg_stx_trade", "SUM(amount*" . $this->oz . ")", "date " . $sqlDate . " AND purchase_type='Buy'");
            $aBalance[1] += $this->safeValue($line);

            $line = $dbc->GetRecord("bs_smg_stx_rollover", "SUM(amount*" . $this->oz . ")", "date " . $sqlDate . " AND type='Buy'");
            $aBalance[1] += $this->safeValue($line);

            $line = $dbc->GetRecord("bs_smg_stx_claim", "SUM(amount*" . $this->oz . ")", "date " . $sqlDate . " AND purchase_type='Buy'");
            $aBalance[1] += $this->safeValue($line);

            $line = $dbc->GetRecord("bs_smg_stx_other", "SUM(amount_credit)", "date " . $sqlDate);
            $aBalance[1] += $this->safeValue($line);
        }

        return $aBalance;
    }

    // Helper function to safely handle null values and array access
    private function safeValue($value)
    {
        if ($value === null) {
            return 0;
        }

        // If it's an array, return the first element
        if (is_array($value)) {
            return isset($value[0]) ? $value[0] : 0;
        }

        // If it's already a number, return it
        return $value;
    }

    function get_usd_debit($date_sql)
    {
        $dbc = $this->dbc;
        $total = 0;
        $msgs = array();

        // Trade purchases
        $sql = "SELECT * FROM bs_smg_stx_trade WHERE date ='" . $date_sql . "' AND purchase_type='Buy' ";
        $rst = $dbc->Query($sql);
        while ($line = $dbc->Fetch($rst)) {
            $value = $line['amount'] * ($line['rate_spot'] + $line['rate_pmdc']) * $this->oz;
            $total += $value;
            $msg = $line['amount'] . "*(" . $line['rate_spot'] . "+" . $line['rate_pmdc'] . ")*" . $this->oz;
            array_push($msgs, "<div>bs_smg_stx_trade|" . $line['id'] . "|<span class='text-danger'>" . number_format($value, 2) . "</span>|" . $msg . "</div>");
        }

        // Rollover debits
        $sql = "SELECT * FROM bs_smg_stx_rollover WHERE date ='" . $date_sql . "' AND entry='debit' AND type='Buy' ";
        $rst = $dbc->Query($sql);
        while ($line = $dbc->Fetch($rst)) {
            $value = $line['amount'] * ($line['rate_spot']) * $this->oz;
            $total += $value;
            $msg = $line['amount'] . "*(" . $line['rate_spot'] . ")*" . $this->oz;
            array_push($msgs, "<div>bs_smg_stx_rollover|" . $line['id'] . "|" . number_format($value, 2) . "|" . $msg . "</div>");
        }

        // Other debits and Claims (Buy)
        $sql = "SELECT * FROM bs_smg_stx_other WHERE date ='" . $date_sql . "'";
        $rst = $dbc->Query($sql);
        while ($line = $dbc->Fetch($rst)) {
            if (isset($line['usd_debit']) && $line['usd_debit'] > 0) {
                $total += $line['usd_debit'];
                array_push($msgs, "<div>bs_smg_stx_other|" . $line['id'] . "|" . number_format($line['usd_debit'], 2) . "</div>");
            }
        }

        $sql = "SELECT * FROM bs_smg_stx_claim WHERE date ='" . $date_sql . "' AND purchase_type = 'Buy' ";
        $rst = $dbc->Query($sql);
        while ($line = $dbc->Fetch($rst)) {
            $value = $line['amount'] * ($line['rate_spot']) * $this->oz;
            $total += $value;
            $msg = $line['amount'] . "*(" . $line['rate_spot'] . ")*" . $this->oz;
            array_push($msgs, "<div>bs_smg_stx_claim|" . $line['id'] . "|" . number_format($value, 2) . "|" . $msg . "</div>");
        }

        return array($total, $msgs);
    }

    function get_usd_credit($date_sql)
    {
        $dbc = $this->dbc;
        $total = 0;
        $msgs = array();

        // Trade sales
        $sql = "SELECT * FROM bs_smg_stx_trade WHERE date ='" . $date_sql . "' AND purchase_type='Sell' ";
        $rst = $dbc->Query($sql);
        while ($line = $dbc->Fetch($rst)) {
            $value = $line['amount'] * ($line['rate_spot'] + $line['rate_pmdc']) * $this->oz;
            $total += $value;
            $msg = $line['amount'] . "*(" . $line['rate_spot'] . "+" . $line['rate_pmdc'] . ")*" . $this->oz;
            array_push($msgs, "<div>bs_smg_stx_trade|" . $line['id'] . "|" . number_format($value, 2) . "|" . $msg . "</div>");
        }

        // Payments
        $sql = "SELECT * FROM bs_smg_stx_payment WHERE date ='" . $date_sql . "'";
        $rst = $dbc->Query($sql);
        while ($line = $dbc->Fetch($rst)) {
            $total += $line['amount_usd'];
            array_push($msgs, "<div>bs_smg_stx_payment|" . $line['id'] . "|" . number_format($line['amount_usd'], 2) . "</div>");
        }

        // Receiving with special PMDC handling
        $sql = "SELECT * FROM bs_smg_stx_receiving WHERE date ='" . $date_sql . "'";
        $rst = $dbc->Query($sql);
        while ($line = $dbc->Fetch($rst)) {
            if ($line['rate_pmdc'] == '-0.0800') {
                $pmdc = abs($line['rate_pmdc']);
            } else {
                $pmdc = -$line['rate_pmdc'];
            }
            $value = $line['amount'] * ($pmdc) * $this->oz + $line['transfer'];
            $total += $value;
            $msg = $line['amount'] . "*" . ($pmdc) . "*" . $this->oz . "+" . $line['transfer'];
            array_push($msgs, "<div>bs_smg_receiving|" . $line['id'] . "|" . number_format($value, 2) . "|" . $msg . "</div>");
        }

        // Rollover credits
        $sql = "SELECT * FROM bs_smg_stx_rollover WHERE date ='" . $date_sql . "' AND entry='credit' AND type!='Buy' ";
        $rst = $dbc->Query($sql);
        while ($line = $dbc->Fetch($rst)) {
            $value = $line['amount'] * ($line['rate_spot']) * $this->oz;
            $total += $value;
            $msg = $line['amount'] . "*(" . $line['rate_spot'] . ")*" . $this->oz;
            array_push($msgs, "<div>bs_smg_stx_rollover|" . $line['id'] . "|" . number_format($value, 2) . "|" . $msg . "</div>");
        }

        // Other credits
        $sql = "SELECT * FROM bs_smg_stx_other WHERE date ='" . $date_sql . "'";
        $rst = $dbc->Query($sql);
        while ($line = $dbc->Fetch($rst)) {
            if (isset($line['usd_credit']) && $line['usd_credit'] > 0) {
                $total += $line['usd_credit'];
                array_push($msgs, "<div>bs_smg_stx_other|" . $line['id'] . "|" . number_format($line['usd_credit'], 2) . "</div>");
            }
        }

        // Interest (subtract from credit)
        $sql = "SELECT * FROM bs_smg_stx_interest WHERE date ='" . $date_sql . "'";
        $rst = $dbc->Query($sql);
        while ($line = $dbc->Fetch($rst)) {
            $total -= $line['interest'];
            array_push($msgs, "<div>bs_smg_stx_interest|" . $line['id'] . "|-" . number_format($line['interest'], 2) . "</div>");
        }

        // Claims (Sell)
        $sql = "SELECT * FROM bs_smg_stx_claim WHERE date ='" . $date_sql . "' AND purchase_type = 'Sell' ";
        $rst = $dbc->Query($sql);
        $claim_count = 0;
        while ($line = $dbc->Fetch($rst)) {
            $claim_count++;
            $value = $line['amount'] * ($line['rate_spot']) * $this->oz;
            $total += $value;
            $msg = $line['amount'] . "*(" . $line['rate_spot'] . ")*" . $this->oz;
            array_push($msgs, "<div>bs_smg_stx_claim|" . $line['id'] . "|" . number_format($value, 2) . "|" . $msg . "</div>");
        }

        // Debug: Add info if no claims found
        if ($claim_count == 0) {
            array_push($msgs, "<div class='text-muted'>bs_smg_stx_claim|No Sell records found for " . $date_sql . "</div>");
        }

        return array($total, $msgs);
    }

    function get_xag_debit($date_sql)
    {
        $dbc = $this->dbc;
        $total = 0;
        $msgs = array();

        // Trade sales (XAG debit)
        $sql = "SELECT * FROM bs_smg_stx_trade WHERE date ='" . $date_sql . "' AND purchase_type='Sell' ";
        $rst = $dbc->Query($sql);
        while ($line = $dbc->Fetch($rst)) {
            $value = $line['amount'] * $this->oz;
            $total += $value;
            $msg = $line['amount'] . "*" . $this->oz;
            array_push($msgs, "<div>bs_smg_stx_trade|" . $line['id'] . "|<span class='text-danger'>-" . number_format($value, 2) . "</span>|" . $msg . "</div>");
        }

        // Receiving (XAG debit)
        $sql = "SELECT * FROM bs_smg_stx_receiving WHERE date ='" . $date_sql . "'";
        $rst = $dbc->Query($sql);
        while ($line = $dbc->Fetch($rst)) {
            $value = $line['amount'] * $this->oz;
            $total += $value;
            $msg = $line['amount'] . "*" . $this->oz;
            array_push($msgs, "<div>bs_smg_stx_receiving|" . $line['id'] . "|<span class='text-danger'>-" . number_format($value, 2) . "</span>|" . $msg . "</div>");
        }

        // Rollover credits (XAG debit)
        $sql = "SELECT * FROM bs_smg_stx_rollover WHERE date ='" . $date_sql . "' AND entry='credit' AND type!='Buy' ";
        $rst = $dbc->Query($sql);
        while ($line = $dbc->Fetch($rst)) {
            $value = $line['amount'] * $this->oz;
            $total += $value;
            $msg = $line['amount'] . "*" . $this->oz;
            array_push($msgs, "<div>bs_smg_stx_rollover|" . $line['id'] . "|" . number_format($value, 2) . "|" . $msg . "</div>");
        }

        // Other debits
        $sql = "SELECT * FROM bs_smg_stx_other WHERE date ='" . $date_sql . "'";
        $rst = $dbc->Query($sql);
        while ($line = $dbc->Fetch($rst)) {
            if (isset($line['usd_debit']) && $line['usd_debit'] > 0) {
                $total -= $line['usd_debit'];
                array_push($msgs, "<div>bs_smg_stx_other|" . $line['id'] . "|-" . number_format($line['usd_debit'], 2) . "</div>");
            }
            if (isset($line['amount_debit']) && $line['amount_debit'] > 0) {
                $total -= $line['amount_debit'];
                array_push($msgs, "<div>bs_smg_stx_other|" . $line['id'] . "|" . number_format($line['amount_debit'], 2) . "</div>");
            }
        }

        return array($total, $msgs);
    }

    function get_xag_credit($date_sql)
    {
        $dbc = $this->dbc;
        $total = 0;
        $msgs = array();

        // Trade purchases (XAG credit)
        $sql = "SELECT * FROM bs_smg_stx_trade WHERE date ='" . $date_sql . "' AND purchase_type='Buy' ";
        $rst = $dbc->Query($sql);
        while ($line = $dbc->Fetch($rst)) {
            $value = $line['amount'] * $this->oz;
            $total += $value;
            $msg = $line['amount'] . "*" . $this->oz;
            array_push($msgs, "<div>bs_smg_stx_trade|" . $line['id'] . "|<span class='text-success'>" . number_format($value, 2) . "</span>|" . $msg . "</div>");
        }

        // Rollover (Buy type)
        $sql = "SELECT * FROM bs_smg_stx_rollover WHERE date ='" . $date_sql . "' AND type='Buy' ";
        $rst = $dbc->Query($sql);
        while ($line = $dbc->Fetch($rst)) {
            $value = $line['amount'] * $this->oz;
            $total += $value;
            $msg = $line['amount'] . "*" . $this->oz;
            array_push($msgs, "<div>bs_smg_stx_rollover|" . $line['id'] . "|" . number_format($value, 2) . "|" . $msg . "</div>");
        }

        // Other amount credits
        $sql = "SELECT * FROM bs_smg_stx_other WHERE date ='" . $date_sql . "'";
        $rst = $dbc->Query($sql);
        while ($line = $dbc->Fetch($rst)) {
            if (isset($line['amount_credit']) && $line['amount_credit'] > 0) {
                $total += $line['amount_credit'];
                array_push($msgs, "<div>bs_smg_stx_other|" . $line['id'] . "|" . number_format($line['amount_credit'], 2) . "</div>");
            }
        }

        // Claims (Buy)
        $sql = "SELECT * FROM bs_smg_stx_claim WHERE date ='" . $date_sql . "' AND purchase_type = 'Buy' ";
        $rst = $dbc->Query($sql);
        while ($line = $dbc->Fetch($rst)) {
            $value = $line['amount'] * $this->oz;
            $total += $value;
            $msg = $line['amount'] . "*" . $this->oz;
            array_push($msgs, "<div>bs_smg_stx_claim|" . $line['id'] . "|" . number_format($value, 2) . "|" . $msg . "</div>");
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
        $s .= '<td class="text-right' . $class . '"' . $tooltip . ' title="' . htmlspecialchars(join("\n", $msgs)) . '">' . $show_value . '</td>';
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
        $s .= '<td class="text-right' . $class . '"' . $tooltip . ' title="' . htmlspecialchars(join("\n", $msgs)) . '">' . $show_value . '</td>';
        return $s;
    }
}
