<?php
header('Content-Type: application/json; charset=utf-8');

if (!function_exists('apcu_cache_info')) {
    echo json_encode(['error' => 'APCu not available']);
    exit;
}

$info = apcu_cache_info();

// ดู cache ที่เกี่ยวกับ gold price
$goldCache = apcu_fetch('gold_xag_thb_price', $success);

echo json_encode([
    'apcu_enabled' => true,
    'memory_size' => number_format($info['mem_size'] ?? 0),
    'num_entries' => $info['num_entries'] ?? 0,
    'hits' => number_format($info['num_hits'] ?? 0),
    'misses' => number_format($info['num_misses'] ?? 0),
    'hit_rate' => $info['num_hits'] > 0
        ? round(($info['num_hits'] / ($info['num_hits'] + $info['num_misses'])) * 100, 2) . '%'
        : '0%',
    'gold_cache_exists' => $success,
    'gold_cache_data' => $success ? $goldCache : null
], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
