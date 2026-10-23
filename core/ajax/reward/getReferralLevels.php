<?php
require_once __DIR__ . '/../../system/spex_v4_func.php';

header('Content-Type: application/json');

try {
    global $spexcon;
    
    $settingsQuery = $spexcon->query("SELECT ref_lvl_percentage1, ref_lvl_percentage2, ref_lvl_percentage3, ref_lvl_percentage4, ref_lvl_percentage5 FROM settings WHERE id = 1");
    $settings = $settingsQuery->fetch_assoc();
    
    $levels = [
        [ 'min' => 1, 'max' => 2, 'rate' => $settings['ref_lvl_percentage1'], 'title' => 'Beginner' ],
        [ 'min' => 3, 'max' => 5, 'rate' => $settings['ref_lvl_percentage2'], 'title' => 'Bronze' ],
        [ 'min' => 6, 'max' => 9, 'rate' => $settings['ref_lvl_percentage3'], 'title' => 'Silver' ],
        [ 'min' => 10, 'max' => 12, 'rate' => $settings['ref_lvl_percentage4'], 'title' => 'Gold' ],
        [ 'min' => 13, 'max' => 999, 'rate' => $settings['ref_lvl_percentage5'], 'title' => 'Elite' ]
    ];
    
    echo json_encode([
        'success' => true,
        'data' => $levels
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>