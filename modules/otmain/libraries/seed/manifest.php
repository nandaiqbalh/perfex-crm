<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Production seed load order.
 *
 * 2025 proposal file naming (mirror sales-quotation folders):
 *   {seq}_2025_{pq|sq}_{nnn}_{short_slug}.php
 * Example folder: "3.2025-PQ - 102 - CemflexX Kovako Waterford Inspection"
 *   → 3_2025_pq_102_cemflexx_kovako_waterford.php
 *
 * Missing seq 9 only (SQ-108 Flow Meter) — add when PDF seeded.
 * Invoices empty until 2025/2026 quotations complete.
 */
return [
    'proposals' => [
        // —— 2025 (by folder sequence 1–11; missing only 9 SQ-108 Flow Meter) ——
        'proposals/1_2025_pq_100_cemflexx_vigor_delivery.php',
        'proposals/2_2025_pq_101_vigor_extension.php',
        'proposals/3_2025_pq_102_cemflexx_kovako_waterford.php',
        'proposals/4_2025_pq_103_cemflexx_service_trip.php',
        'proposals/5_2025_pq_104_vigor_service_trip.php',
        'proposals/6_2025_sq_105_vigor_valves.php',
        'proposals/7_2025_pq_106_vigor_service_trip.php',
        'proposals/8_2025_sq_107_vigor_spare_parts.php',
        'proposals/10_2025_sq_109_vigor_vacuum_panel.php',
        'proposals/11_2025_sq_102_cemflexx_remote_control.php',
        // —— 2026 catalog ——
        'proposals/tp_suction_hose.php',
        'proposals/tp_suction_nozzle_101.php',
        'proposals/tp_otmsq_104.php',
        'proposals/tp_otmsq_111.php',
        'proposals/tp_otmsq_112.php',
        'proposals/tp_valves.php',
        'proposals/tp_otmsq_113.php',
        'proposals/tp_service_melita.php',
        'proposals/tp_otmsq_106.php',
        'proposals/tp_otmpq_107.php',
        'proposals/tp_otmsq_108.php',
        'proposals/tp_otmsq_109.php',
        'proposals/tp_otmsq_110.php',
        'proposals/tp_otmsq_114.php',
        'proposals/tp_otmsq_115.php',
        'proposals/tp_otmsq_116.php',
        'proposals/tp_otmsq_117.php',
        'proposals/tp_otmsq_118.php',
        'proposals/tp_otmsq_119.php',
        'proposals/tp_otpsq_120.php',
        'proposals/tp_otmsq_121.php',
    ],
    'packing_lists' => [
        'packing_lists/tp_vigor_packing.php',
        'packing_lists/tp_valves_packing.php',
        'packing_lists/tp_projectx_dn300.php',
        'packing_lists/tp_suriname_104_packing.php',
        'packing_lists/tp_suriname_henna_t_sensors.php',
        'packing_lists/tp_suriname_106_packing.php',
        'packing_lists/tp_vigor_109_packing.php',
        'packing_lists/tp_vigor_110_rubbers.php',
    ],
    'invoices' => [
    ],
    'purchase_orders' => [
    ],
];
