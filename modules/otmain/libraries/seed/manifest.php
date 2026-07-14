<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Production seed load order.
 * Add new PDF-migrated files here — one document per file under proposals/, packing_lists/, etc.
 */
return [
    'proposals' => [
        'proposals/tp_suction_hose.php',
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
