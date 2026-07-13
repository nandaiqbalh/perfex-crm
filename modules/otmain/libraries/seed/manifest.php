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
        'proposals/tp_valves.php',
    ],
    'packing_lists' => [
        'packing_lists/tp_vigor_packing.php',
        'packing_lists/tp_valves_packing.php',
    ],
    'invoices' => [
    ],
    'purchase_orders' => [
    ],
];
