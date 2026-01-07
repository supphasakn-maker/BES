<?php

/**
 * price_script.php
 * ชุดทดสอบสำหรับระบบ PriceMonitor (อ้างอิงจากไฟล์หลัก price_monitor.php)
 * - ใช้ os_variable สำหรับ pmdc_rate และ change_buy
 * - ปรับเทสการปัดเศษให้ตรงกฎ 0.00–0.24 คงเดิม, 0.25–50.00 +50, >50 +100
 * - ตรวจ No ต่อ "รายวัน" ให้ตรง getNextNo()
 */

require_once 'price_monitor.php';

class PriceMonitorTest extends PriceMonitor
{
    private $test_factor = 32.1507;

    /* ---------- Proxy / Helper ---------- */
    public function isWorkingHours($force = false)
    {
        if ($force) return true;
        return parent::isWorkingHours();
    }

    public function testGetGoldPrice()
    {
        return $this->getGoldPrice();
    }
    public function testGetBBLExchangeRate()
    {
        return $this->getBBLExchangeRate();
    }
    public function testCalculateNewPrices($spot_price, $exchange_rate)
    {
        return $this->calculateNewPrices($spot_price, $exchange_rate);
    }
    public function testRoundToNearestHundred($price)
    {
        return $this->roundToNearestHundred($price);
    }
    public function testCheckPriceDifference($current_prices, $new_prices)
    {
        return $this->checkPriceDifference($current_prices, $new_prices);
    }
    public function testIsPublicHoliday($date = null)
    {
        return $this->isPublicHoliday($date);
    }
    public function testIsSundayPriceAlreadyAnnounced($date = null)
    {
        return $this->isSundayPriceAlreadyAnnounced($date);
    }
    public function testGetLastSaturdayPrice()
    {
        return $this->getLastSaturdayPrice();
    }
    public function testGetNextNo()
    {
        return $this->getNextNo();
    }
    public function testGetPmdcRate()
    {
        return $this->getPmdcRate();
    }

    /* ---------- Tests ---------- */

    public function testDatabaseConnection()
    {
        echo "=== ทดสอบการเชื่อมต่อฐานข้อมูล ===\n";
        try {
            $sql = "SELECT COUNT(*) AS count FROM bs_announce_silver";
            $result = mysqli_query($this->connection, $sql);
            if ($result) {
                $row = mysqli_fetch_assoc($result);
                echo "✓ เชื่อมต่อฐานข้อมูลสำเร็จ\n";
                echo "  จำนวนข้อมูลใน bs_announce_silver: {$row['count']}\n";

                // ใช้ os_variable ตามสคีมาจริง
                $sql2 = "SELECT COUNT(*) AS count FROM os_variable";
                $result2 = mysqli_query($this->connection, $sql2);
                if ($result2) {
                    $row2 = mysqli_fetch_assoc($result2);
                    echo "  จำนวนตัวแปรใน os_variable: {$row2['count']}\n";
                }
                return true;
            }
            echo "✗ เชื่อมต่อฐานข้อมูลไม่สำเร็จ: " . mysqli_error($this->connection) . "\n";
            return false;
        } catch (Exception $e) {
            echo "✗ เกิดข้อผิดพลาด: " . $e->getMessage() . "\n";
            return false;
        }
    }

    public function testPmdcRateFromDatabase()
    {
        echo "\n=== ทดสอบการดึง PMDC Rate จาก os_variable ===\n";
        try {
            $pmdc_rate = $this->testGetPmdcRate();
            if ($pmdc_rate !== null) {
                echo "✓ ดึง PMDC Rate สำเร็จ: {$pmdc_rate}\n";
                if ($pmdc_rate >= 0 && $pmdc_rate <= 10) echo "  ค่าอยู่ในช่วงที่เหมาะสม\n";
                else echo "  ⚠️ ค่าอาจผิดปกติ\n";
                return true;
            }
            echo "✗ ไม่สามารถดึง PMDC Rate ได้\n";
            return false;
        } catch (Exception $e) {
            echo "✗ เกิดข้อผิดพลาด: " . $e->getMessage() . "\n";
            return false;
        }
    }

    public function testGoldAPI()
    {
        echo "\n=== ทดสอบ Gold API (XAG/USD) ===\n";
        try {
            $price = $this->testGetGoldPrice();
            if ($price && is_numeric($price)) {
                echo "✓ ดึงข้อมูลสำเร็จ: {$price}\n";
                if ($price > 10 && $price < 100) echo "  ราคาอยู่ในช่วงปกติ\n";
                else echo "  ⚠️ ราคาอาจผิดปกติ\n";
                return true;
            }
            echo "✗ ดึงข้อมูลไม่สำเร็จ (ตรวจ API key/เครือข่าย)\n";
            return false;
        } catch (Exception $e) {
            echo "✗ เกิดข้อผิดพลาด: " . $e->getMessage() . "\n";
            return false;
        }
    }

    public function testBBLAPI()
    {
        echo "\n=== ทดสอบ BBL API (USD50) ===\n";
        try {
            $rate = $this->testGetBBLExchangeRate();
            if ($rate && is_numeric($rate)) {
                echo "✓ ดึงอัตราแลกเปลี่ยนสำเร็จ: {$rate} THB\n";
                if ($rate > 25 && $rate < 50) echo "  อัตราอยู่ในช่วงปกติ\n";
                else echo "  ⚠️ อาจผิดปกติ\n";
                return true;
            }
            echo "✗ ดึงอัตราแลกเปลี่ยนไม่สำเร็จ (ตรวจ Sub-Key/เครือข่าย)\n";
            return false;
        } catch (Exception $e) {
            echo "✗ เกิดข้อผิดพลาด: " . $e->getMessage() . "\n";
            return false;
        }
    }

    public function testPriceCalculation()
    {
        echo "\n=== ทดสอบการคำนวณราคา (สูตรใหม่) ===\n";
        try {
            $spot_price    = 38.02;
            $exchange_rate = 32.45;
            $pmdc_rate     = $this->testGetPmdcRate();

            $prices = $this->testCalculateNewPrices($spot_price, $exchange_rate);

            echo "ข้อมูลทดสอบ:\n";
            echo "  Spot: {$spot_price}, EX: {$exchange_rate}, PMDC: {$pmdc_rate}, Factor: {$this->test_factor}\n";
            echo "  สูตร: ((Spot + PMDC) × Factor) × Exchange\n\n";

            $step1 = $spot_price + $pmdc_rate;
            $step2 = $step1 * $this->test_factor;
            $step3 = $step2 * $exchange_rate;

            echo "  Step1: {$spot_price} + {$pmdc_rate} = {$step1}\n";
            echo "  Step2: {$step1} × {$this->test_factor} = " . number_format($step2, 4) . "\n";
            echo "  Step3: " . number_format($step2, 4) . " × {$exchange_rate} = " . number_format($step3, 2) . " THB\n\n";

            echo "ผลลัพธ์:\n";
            echo "  Base:      " . number_format($prices['base'], 2) . "\n";
            echo "  Sell(raw): " . number_format($prices['sell_raw'], 2) . " → Sell: " . number_format($prices['sell'], 0) . "\n";
            echo "  Buy(raw):  " . number_format($prices['buy_raw'], 2)  . " → Buy:  " . number_format($prices['buy'], 0)   . "\n";
            echo "  change_buy (จาก os_variable): {$prices['change_buy']}\n";
            return true;
        } catch (Exception $e) {
            echo "✗ เกิดข้อผิดพลาด: " . $e->getMessage() . "\n";
            return false;
        }
    }

    public function testPriceRounding()
    {
        echo "\n=== ทดสอบการปัดราคา (กฎใหม่) ===\n";
        try {
            $test_cases = [
                ['raw' => 40150.25, 'expected' => 40200], // >50 → +100
                ['raw' => 40149.99, 'expected' => 40150], // ≤50 → +50
                ['raw' => 40050.00, 'expected' => 40100], // =50 → +50
                ['raw' => 40049.99, 'expected' => 40050], // ≤50 → +50
                ['raw' => 39950.50, 'expected' => 40000], // >50 → +100
                ['raw' => 39925.00, 'expected' => 39950], // 0.25–50 → +50
                ['raw' => 39900.10, 'expected' => 39900], // <0.25 → คงเดิม
            ];

            $all_ok = true;
            foreach ($test_cases as $i => $t) {
                $rounded = $this->testRoundToNearestHundred($t['raw']);
                $ok = ($rounded == $t['expected']);
                $all_ok = $all_ok && $ok;
                echo "กรณี " . ($i + 1) . ": " . number_format($t['raw'], 2) .
                    " → " . number_format($rounded, 0) .
                    " (คาด: " . number_format($t['expected'], 0) . ") " .
                    ($ok ? "✅" : "❌") . "\n";
            }
            return $all_ok;
        } catch (Exception $e) {
            echo "✗ เกิดข้อผิดพลาด: " . $e->getMessage() . "\n";
            return false;
        }
    }

    public function testPriceDifferenceCheck()
    {
        echo "\n=== ทดสอบการตรวจสอบส่วนต่าง (เทียบ Sell ดิบ vs Sell ก่อนหน้า) ===\n";
        try {
            $change_buy = $this->getVariable('change_buy', -700);
            $current_prices = [
                'sell' => 40050.0000,
                'buy'  => 40050.0000 + $change_buy,
            ];

            $cases = [
                ['name' => 'ใกล้เดิม Δ<100', 'sell_raw' => 40080.00, 'expect' => false],
                ['name' => 'เพิ่มเกิน 100',  'sell_raw' => 40160.00, 'expect' => true],
                ['name' => 'ลดเกิน 100',    'sell_raw' => 39920.00, 'expect' => true],
            ];

            $all_ok = true;
            foreach ($cases as $i => $c) {
                echo "\nกรณี " . ($i + 1) . " ({$c['name']}): Sell ดิบใหม่ = " . number_format($c['sell_raw'], 2) . "\n";
                // ใส่คีย์ตามที่โค้ดจริงใช้ (sell_raw สำคัญ เพราะเทียบกับราคาประกาศก่อนหน้า)
                $new_prices = [
                    'sell_raw' => $c['sell_raw'],
                    'sell'     => $c['sell_raw'], // ไม่ใช้จริง แต่ให้มีค่ากันพลาด
                    'buy'      => $c['sell_raw'] + $change_buy,
                ];
                $trigger = $this->testCheckPriceDifference($current_prices, $new_prices);
                echo $trigger ? "→ จะประกาศ ✅\n" : "→ ไม่ประกาศ ⛔\n";
                if ($trigger !== $c['expect']) $all_ok = false;
            }
            return $all_ok;
        } catch (Exception $e) {
            echo "✗ เกิดข้อผิดพลาด: " . $e->getMessage() . "\n";
            return false;
        }
    }

    public function testWorkingHours()
    {
        echo "\n=== ทดสอบเวลาทำการ ===\n";
        try {
            $now = new DateTime();
            echo "ปัจจุบัน: " . $now->format('Y-m-d H:i:s (l)') . "\n";
            $is_working = $this->isWorkingHours();
            $dow = $now->format('N');
            $date = $now->format('Y-m-d');

            echo "สถานะ: ";
            if ($this->testIsPublicHoliday($date)) {
                echo "❌ วันหยุดนักขัตฤกษ์\n";
            } elseif ($dow == 7) {
                if ($this->testIsSundayPriceAlreadyAnnounced($date)) echo "✅ อาทิตย์: ประกาศแล้ว\n";
                else echo "🔄 อาทิตย์: ต้องประกาศจากวันเสาร์\n";
            } elseif ($dow == 6) {
                echo "❌ เสาร์: หยุด\n";
            } elseif ($is_working) {
                echo "✅ อยู่ในเวลาทำการ\n";
            } else {
                echo "❌ นอกเวลาทำการ\n";
            }
            return true;
        } catch (Exception $e) {
            echo "✗ เกิดข้อผิดพลาด: " . $e->getMessage() . "\n";
            return false;
        }
    }

    public function testHolidayCheck()
    {
        echo "\n=== ทดสอบการตรวจสอบวันหยุด ===\n";
        try {
            $d = date('Y-m-d');
            $is_holiday = $this->testIsPublicHoliday($d);
            echo "วันที่ {$d}: " . ($is_holiday ? "วันหยุด" : "วันปกติ") . "\n";

            $sql = "SELECT COUNT(*) AS count FROM a_public_holiday WHERE PublicHoliday >= CURDATE()";
            $res = mysqli_query($this->connection, $sql);
            if ($res) {
                $row = mysqli_fetch_assoc($res);
                echo "วันหยุดในอนาคต: {$row['count']} วัน\n";
            }
            return true;
        } catch (Exception $e) {
            echo "✗ เกิดข้อผิดพลาด: " . $e->getMessage() . "\n";
            return false;
        }
    }

    public function testNoGeneration()
    {
        echo "\n=== ทดสอบการสร้าง No อัตโนมัติ (รายวัน) ===\n";
        try {
            $next_no = $this->testGetNextNo();
            echo "getNextNo() → {$next_no}\n";

            // ตรวจแบบเดียวกับโค้ดจริง: ต่อวัน (WHERE date = CURDATE())
            $sql = "SELECT COALESCE(MAX(no),0)+1 AS expected_no
                    FROM bs_announce_silver
                    WHERE date = CURDATE()";
            $res = mysqli_query($this->connection, $sql);
            if ($res) {
                $row = mysqli_fetch_assoc($res);
                $expected_no = (int)$row['expected_no'];
                echo "No ที่คาดหวัง (วันนี้): {$expected_no}\n";
                if ($next_no === $expected_no) {
                    echo "✅ ถูกต้อง\n";
                    return true;
                }
                echo "❌ ไม่ตรง (ได้ {$next_no}, คาด {$expected_no})\n";
                return false;
            }
            echo "❌ ตรวจสอบ expected_no ไม่สำเร็จ\n";
            return false;
        } catch (Exception $e) {
            echo "✗ เกิดข้อผิดพลาด: " . $e->getMessage() . "\n";
            return false;
        }
    }

    /* ---------- Runner ---------- */

    public function runAllTests()
    {
        echo "=== เริ่มการทดสอบระบบ Silver Price Monitor ===\n";
        echo "เวลา: " . date('Y-m-d H:i:s') . "\n";
        echo "เวอร์ชัน: 2.0\n\n";

        $tests = [
            'testDatabaseConnection'    => 'การเชื่อมต่อฐานข้อมูล',
            'testPmdcRateFromDatabase'  => 'การดึง PMDC Rate (os_variable)',
            'testGoldAPI'               => 'Gold API (XAG/USD)',
            'testBBLAPI'                => 'BBL API (USD50)',
            'testPriceCalculation'      => 'การคำนวณราคา',
            'testPriceRounding'         => 'การปัดราคา',
            'testPriceDifferenceCheck'  => 'เกณฑ์ประกาศ (Δ≥100)',
            'testWorkingHours'          => 'เวลาทำการ',
            'testHolidayCheck'          => 'วันหยุดนักขัตฤกษ์',
            'testNoGeneration'          => 'เลข No รายวัน',
        ];

        $passed = 0;
        $total = count($tests);
        $failed = [];
        foreach ($tests as $method => $name) {
            try {
                $ok = $this->$method();
                if ($ok) $passed++;
                else $failed[] = $name;
            } catch (Exception $e) {
                echo "❌ ล้มเหลวใน {$name}: " . $e->getMessage() . "\n";
                $failed[] = $name;
            }
        }

        echo "\n" . str_repeat("=", 60) . "\n";
        echo "สรุปผล: ผ่าน {$passed}/{$total}\n";
        if ($passed === $total) echo "🎉 ระบบพร้อมใช้งาน\n";
        else echo "❌ พบเคสล้มเหลว: " . implode(', ', $failed) . "\n";

        return $passed === $total;
    }

    public function testFullMonitoring()
    {
        echo "\n" . str_repeat("=", 60) . "\n";
        echo "=== ทดสอบการทำงานจริง (บังคับรัน) ===\n";
        try {
            $this->monitorPrices();
            echo "✓ เสร็จสิ้น\n";
            return true;
        } catch (Exception $e) {
            echo "❌ เกิดข้อผิดพลาด: " . $e->getMessage() . "\n";
            return false;
        }
    }
}

/* ---------- Entry Points ---------- */

if (php_sapi_name() === 'cli' || isset($_GET['test'])) {
    try {
        $test = new PriceMonitorTest();
        if ((isset($_GET['full']) && $_GET['full'] == 1) || (isset($argv[1]) && $argv[1] === 'full')) {
            $test->runAllTests();
            $test->testFullMonitoring();
        } else {
            $test->runAllTests();
        }
    } catch (Exception $e) {
        echo "❌ เกิดข้อผิดพลาดร้ายแรง: " . $e->getMessage() . "\n";
        echo "กรุณาตรวจสอบการเชื่อมต่อฐานข้อมูลและไฟล์ price_monitor.php\n";
    }
} else {
    // UI หน้าเว็บเล็ก ๆ
    echo "<!DOCTYPE html>
    <html><head><title>Silver Price Monitor - Test Script v2.0</title>
    <meta charset='utf-8'>
    <style>
      body{font-family:Segoe UI,Arial;max-width:900px;margin:50px auto;padding:20px}
      .header{background:linear-gradient(135deg,#667eea,#764ba2);color:#fff;padding:20px;border-radius:10px;margin-bottom:20px}
      .test-btn{background:#28a745;color:#fff;padding:12px 24px;text-decoration:none;border-radius:6px;display:inline-block;margin:10px 5px;font-weight:bold}
      .test-btn:hover{background:#218838}
      .test-btn.danger{background:#dc3545}.test-btn.danger:hover{background:#c82333}
      .warning{background:#fff3cd;border:1px solid #ffeaa7;padding:15px;margin:15px 0;border-radius:6px}
      .info{background:#d1ecf1;border:1px solid #bee5eb;padding:15px;margin:15px 0;border-radius:6px}
      .features{display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:15px;margin:20px 0}
      .feature{background:#f8f9fa;padding:15px;border-radius:6px;border-left:4px solid #28a745}
      .feature h4{margin:0 0 10px;color:#333}.feature p{margin:0;color:#666;font-size:14px}
    </style></head><body>
      <div class='header'><h1>🧪 Silver Price Monitor - Test Script v2.0</h1>
      <p>ระบบทดสอบสำหรับการตรวจสอบราคาเงิน (อิงไฟล์ price_monitor.php)</p></div>
      <div class='warning'><strong>⚠️ คำเตือน:</strong> ควรทดสอบให้ผ่านทุกข้อก่อนใช้งานจริง</div>
      <div class='info'>
        <strong>📊 สูตรคำนวณ:</strong><br>
        <code>((rate_spot + rate_pmdc) × 32.1507) × rate_exchange</code><br>
        • PMDC และ change_buy ดึงจาก <b>os_variable</b><br>
        • ปัดราคา: 0.00–0.24 คงเดิม, 0.25–50.00 ขึ้น 50, &gt;50 ขึ้น 100
      </div>
      <h3>🔬 เริ่มการทดสอบ</h3>
      <a href='?test=1' class='test-btn'>🧪 ทดสอบระบบ (10 ข้อ)</a>
      <a href='?test=1&full=1' class='test-btn danger'>🚀 ทดสอบ + รันจริง</a>
      <a href='price_monitor.php' class='test-btn'>🏠 กลับหน้าหลัก</a>
      <h3>📋 รายการทดสอบ</h3>
      <div class='features'>
        <div class='feature'><h4>🗄️ ฐานข้อมูล</h4><p>ทดสอบเชื่อมต่อ + นับข้อมูล</p></div>
        <div class='feature'><h4>📊 PMDC Rate</h4><p>ดึงจาก os_variable</p></div>
        <div class='feature'><h4>🌐 Gold API</h4><p>ราคา XAG/USD</p></div>
        <div class='feature'><h4>🏦 BBL API</h4><p>อัตราแลกเปลี่ยน USD50</p></div>
        <div class='feature'><h4>🧮 คำนวณ</h4><p>สูตรใหม่พร้อมขั้นตอน</p></div>
        <div class='feature'><h4>🔢 ปัดราคา</h4><p>ตามกฎใหม่</p></div>
        <div class='feature'><h4>📈 ส่วนต่าง</h4><p>ประกาศเมื่อ Δ≥100</p></div>
        <div class='feature'><h4>⏰ เวลาทำการ</h4><p>จ.-ศ. + อาทิตย์พิเศษ</p></div>
        <div class='feature'><h4>📅 วันหยุด</h4><p>ตรวจ a_public_holiday</p></div>
        <div class='feature'><h4>🔢 No รายวัน</h4><p>เลขลำดับต่อวัน</p></div>
      </div>
    </body></html>";
}
