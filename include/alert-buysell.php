<?php

if (isset($_GET['check_image_status'])) {
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }

    include_once "../../config/define.php";
    include_once "../../include/db.php";
    include_once "../../include/oceanos.php";
    include_once "../../include/datastore.php";

    date_default_timezone_set(DEFAULT_TIMEZONE);

    if (!isset($dbc)) {
        $dbc = new dbc();
        $dbc->Connect();
    }

    $user_id = $_SESSION['auth']['user_id'] ?? 0;

    // Check user for bs_purchase_buyfix (original check)
    $user_check_buyfix = null;
    if (isset($dbc) && ($dbc instanceof datastore || $dbc instanceof dbc)) {
        $user_check_buyfix_result = $dbc->GetRecord("os_users", "gid", "id=" . $user_id . " AND gid IN(1,2,14,17,6,8)");
        $user_check_buyfix = ($user_check_buyfix_result !== false && $user_check_buyfix_result !== null);
    }

    // Check user for bs_purchase_buy (new check)
    $user_check_buy = null;
    if (isset($dbc) && ($dbc instanceof datastore || $dbc instanceof dbc)) {
        $user_check_buy_result = $dbc->GetRecord("os_users", "gid", "id=" . $user_id . " AND gid IN(1,3,14,8)");
        $user_check_buy = ($user_check_buy_result !== false && $user_check_buy_result !== null);
    }

    header('Content-Type: application/json');

    $response = [
        'img_missing' => false,
        'missing_count' => 0,
        'buyfix_missing_count' => 0,
        'buy_missing_count' => 0,
        'debug_user_check_buyfix' => $user_check_buyfix,
        'debug_user_check_buy' => $user_check_buy,
        'debug_user_id' => $user_id,
        'debug_reason' => '',
        'actual_missing_count_from_db' => null,
        'debug_timestamp' => date('Y-m-d H:i:s')
    ];

    $total_missing_count = 0;

    // Check bs_purchase_buyfix if user is authorized
    if ($user_check_buyfix) {
        $raw_result_buyfix = $dbc->GetRecord("bs_purchase_buyfix", "COUNT(*)", "img IS NULL OR img = '' OR img = '*NULL*'");

        if (is_array($raw_result_buyfix) || is_object($raw_result_buyfix)) {
            $missing_img_count_buyfix = (int)($raw_result_buyfix['COUNT(*)'] ?? $raw_result_buyfix[0] ?? 0);
        } else {
            $missing_img_count_buyfix = (int)$raw_result_buyfix;
        }

        $response['buyfix_missing_count'] = $missing_img_count_buyfix;
        $total_missing_count += $missing_img_count_buyfix;
    }

    // Check bs_purchase_buy if user is authorized
    if ($user_check_buy) {
        $raw_result_buy = $dbc->GetRecord("bs_purchase_buy", "COUNT(*)", "img IS NULL OR img = '' OR img = '*NULL*'");

        if (is_array($raw_result_buy) || is_object($raw_result_buy)) {
            $missing_img_count_buy = (int)($raw_result_buy['COUNT(*)'] ?? $raw_result_buy[0] ?? 0);
        } else {
            $missing_img_count_buy = (int)$raw_result_buy;
        }

        $response['buy_missing_count'] = $missing_img_count_buy;
        $total_missing_count += $missing_img_count_buy;
    }

    $response['actual_missing_count_from_db'] = $total_missing_count;
    $response['missing_count'] = $total_missing_count;
    $response['img_missing'] = ($total_missing_count > 0);

    // Build debug reason
    $debug_parts = [];
    if ($user_check_buyfix && $response['buyfix_missing_count'] > 0) {
        $debug_parts[] = "Found {$response['buyfix_missing_count']} records with missing images in bs_purchase_buyfix.";
    }
    if ($user_check_buy && $response['buy_missing_count'] > 0) {
        $debug_parts[] = "Found {$response['buy_missing_count']} records with missing images in bs_purchase_buy.";
    }

    if (!empty($debug_parts)) {
        $response['debug_reason'] = implode(' ', $debug_parts);
    } elseif ($user_check_buyfix || $user_check_buy) {
        $response['debug_reason'] = "No records with missing images found.";
    } else {
        $response['debug_reason'] = "User not authorized for either table. User ID: {$user_id}";
    }

    echo json_encode($response, JSON_PRETTY_PRINT);
    exit();
}

if (!isset($_GET['check_image_status'])) {

    $current_user_id = $_SESSION['auth']['user_id'] ?? 0;
    $current_user_check_buyfix = false;
    $current_user_check_buy = false;

    if (isset($dbc) && ($dbc instanceof datastore || $dbc instanceof dbc)) {
        // Check for buyfix permissions
        $current_user_result_buyfix = $dbc->GetRecord("os_users", "gid", "id=" . $current_user_id . " AND gid IN(1,2,14,17,6,8)");
        $current_user_check_buyfix = ($current_user_result_buyfix !== false && $current_user_result_buyfix !== null);

        // Check for buy permissions
        $current_user_result_buy = $dbc->GetRecord("os_users", "gid", "id=" . $current_user_id . " AND gid IN(1,3,14,8)");
        $current_user_check_buy = ($current_user_result_buy !== false && $current_user_result_buy !== null);
    }

    // Show JavaScript if user has permission for either table
    if ($current_user_check_buyfix || $current_user_check_buy) {
?>
        <script>
            window.alertSystemInitialized = window.alertSystemInitialized || false;

            if (!window.alertSystemInitialized) {
                window.alertSystemInitialized = true;

                document.addEventListener('DOMContentLoaded', function() {
                    initAlertSystemWhenReady();
                });

                if (document.readyState === 'complete' || document.readyState === 'interactive') {
                    setTimeout(initAlertSystemWhenReady, 1000);
                }
            }

            function initAlertSystemWhenReady() {
                let attempts = 0;
                const maxAttempts = 20;

                const checkAndStart = () => {
                    attempts++;

                    if (typeof swal !== 'undefined' || attempts >= maxAttempts) {
                        startAlertSystem();
                    } else {
                        setTimeout(checkAndStart, 500);
                    }
                };

                checkAndStart();
            }

            function startAlertSystem() {
                setTimeout(function() {
                    checkImageStatusGlobal();
                }, 1000);

                setInterval(function() {
                    checkImageStatusGlobal();
                }, 300000);
            }

            async function checkImageStatusGlobal() {
                console.log('🔍 Checking image status...');

                try {
                    const currentPath = window.location.pathname;
                    let fetchUrl;

                    if (currentPath.includes('/apps/buy_fixed/')) {
                        fetchUrl = window.location.pathname + '?check_image_status=1';
                    } else {
                        const basePath = currentPath.includes('/apps/') ?
                            currentPath.substring(0, currentPath.indexOf('/apps/')) :
                            '';
                        fetchUrl = basePath + '/apps/buy_fixed/index.php?check_image_status=1';
                    }

                    const response = await fetch(fetchUrl);

                    if (!response.ok) {
                        console.warn('⚠️ Image status check failed:', response.status);
                        return;
                    }

                    const contentType = response.headers.get('content-type');
                    if (!contentType || !contentType.includes('application/json')) {
                        console.warn('⚠️ Non-JSON response from image check');
                        return;
                    }

                    const data = await response.json();

                    if (data.img_missing && data.missing_count > 0) {
                        showGlobalImageAlert(data);
                    } else {
                        console.log('✅ No missing images');
                    }

                } catch (error) {
                    console.error('❌ Error checking image status:', error);
                }
            }

            function showGlobalImageAlert(data) {
                if (document.getElementById('global-image-alert') || window.alertShowing) {
                    console.log('⚠️ Alert already exists or showing, skipping...');
                    return;
                }

                window.alertShowing = true;

                // Build alert message
                let alertMessage = '';
                let alertParts = [];

                if (data.buyfix_missing_count > 0) {
                    alertParts.push('Buy/Sell Fixed: ' + data.buyfix_missing_count + ' รายการ กรุณาแจ้ง Trader เพื่อทำการอัปโหลดรูปภาพ');
                }
                if (data.buy_missing_count > 0) {
                    alertParts.push('Physical-Adjust: ' + data.buy_missing_count + ' รายการ กรุณาแจ้ง การเงิน เพื่ออัพโหลดรูปจาก Wechat');
                }

                alertMessage = 'พบรูปภาพที่ยังไม่ได้อัปโหลด รวม ' + data.missing_count + ' รายการ\n\n';
                alertMessage += alertParts.join('\n\n');

                if (typeof swal !== 'undefined') {
                    swal({
                        title: 'แจ้งเตือน Order Buy/Sell Fixed',
                        text: alertMessage,
                        type: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'ปิด',
                        cancelButtonText: 'ไปหน้าอัปโหลด',
                        confirmButtonClass: 'btn btn-secondary',
                        cancelButtonClass: 'btn btn-warning',
                        buttonsStyling: false,
                        allowEscapeKey: true,
                        allowOutsideClick: false,
                        timer: 300000
                    }, function(isConfirm) {
                        window.alertShowing = false;

                        if (!isConfirm) {
                            const currentOrigin = window.location.origin;
                            const currentPath = window.location.pathname;
                            const basePath = currentPath.includes('/apps/') ?
                                currentPath.substring(0, currentPath.indexOf('/apps/')) :
                                '';

                            const redirectUrl = currentOrigin + '/#apps/buy_fixed/index.php';
                            window.location.href = redirectUrl;
                        }
                    });

                    setTimeout(function() {
                        applySweetAlertStyling();
                    }, 100);

                } else {
                    const result = confirm(alertMessage.replace(/\n/g, '\n') + '\nต้องการไปหน้าอัปโหลดหรือไม่?');
                    window.alertShowing = false;

                    if (result) {
                        const currentOrigin = window.location.origin;
                        window.location.href = currentOrigin + '/#apps/buy_fixed/index.php';
                    }
                }
            }

            function applySweetAlertStyling() {
                const sweetAlert = document.querySelector('.sweet-alert');
                if (sweetAlert) {
                    sweetAlert.style.borderRadius = '0.5rem';
                    sweetAlert.style.border = '1px solid #dee2e6';
                    sweetAlert.style.boxShadow = '0 0.5rem 1rem rgba(0, 0, 0, 0.15)';
                    sweetAlert.style.padding = '2rem';
                }

                const title = document.querySelector('.sweet-alert h2');
                if (title) {
                    title.style.fontSize = '1.5rem';
                    title.style.fontWeight = '600';
                    title.style.color = '#495057';
                    title.style.marginBottom = '1rem';
                    title.style.fontFamily = '"Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif';
                }

                const text = document.querySelector('.sweet-alert p');
                if (text) {
                    text.style.fontSize = '1.1rem';
                    text.style.color = '#6c757d';
                    text.style.lineHeight = '1.6';
                    text.style.marginBottom = '1.5rem';
                }

                const confirmBtn = document.querySelector('.sweet-alert .confirm');
                const cancelBtn = document.querySelector('.sweet-alert .cancel');

                [confirmBtn, cancelBtn].forEach(btn => {
                    if (btn) {
                        btn.style.padding = '0.5rem 1.5rem';
                        btn.style.fontSize = '1rem';
                        btn.style.fontWeight = '400';
                        btn.style.borderRadius = '0.375rem';
                        btn.style.margin = '0 0.5rem';
                        btn.style.minWidth = '120px';
                        btn.style.transition = 'all 0.2s ease-in-out';
                    }
                });
            }

            window.testBuyFixedAlert = function(count) {
                const testData = {
                    missing_count: count || 8,
                    buyfix_missing_count: Math.floor((count || 8) * 0.625), // 5 รายการ
                    buy_missing_count: Math.floor((count || 8) * 0.375), // 3 รายการ
                    img_missing: true
                };
                showGlobalImageAlert(testData);
            };

            window.testBuyFixedCheck = function() {
                checkImageStatusGlobal();
            };
        </script>
<?php
    }
}
?>