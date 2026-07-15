<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Production seed load order.
 *
 * 2025 proposal file naming (mirror sales-quotation folders):
 *   {seq}.{YYYY}-{PQ|SQ}-{nnn}-{short_slug}.php
 * Example folder: "3.2025-PQ - 102 - CemflexX Kovako Waterford Inspection"
 *   → 3.2025-PQ-102-cemflexx-kovako-waterford.php
 * key = basename without .php; save_option uses underscored form.
 *
 * 2025 Sales Quotations seq 1–11 complete.
 * Extra spare-parts track: 2025-SQ-100 remote (not in folder seq).
 * Invoices: INV-101..116 (T1 + commercial where both exist; INV-115 = REVISED only).
 */
return [
    'proposals' => [
        // —— 2025 (folder seq 1–11 + spare-parts SQ-100) ——
        'proposals/1.2025-PQ-100-cemflexx-vigor-delivery.php',
        'proposals/2.2025-PQ-101-vigor-extension.php',
        'proposals/3.2025-PQ-102-cemflexx-kovako-waterford.php',
        'proposals/4.2025-PQ-103-cemflexx-service-trip.php',
        'proposals/5.2025-PQ-104-vigor-service-trip.php',
        'proposals/6.2025-SQ-105-vigor-valves.php',
        'proposals/7.2025-PQ-106-vigor-service-trip.php',
        'proposals/8.2025-SQ-107-vigor-spare-parts.php',
        'proposals/9.2025-SQ-108-vigor-backup-remote.php',
        'proposals/10.2025-SQ-109-vigor-vacuum-panel.php',
        'proposals/11.2025-SQ-102-cemflexx-remote-control.php',
        'proposals/2025-SQ-100-vigor-remote-control.php',
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
        'invoices/cx_inv_101_t2.php',
        'invoices/tp_inv_102_t1.php',
        'invoices/tp_inv_102.php',
        'invoices/tp_inv_103_t1.php',
        'invoices/tp_inv_103.php',
        'invoices/tp_inv_104_t1.php',
        'invoices/tp_inv_104.php',
        'invoices/tp_inv_105.php',
        'invoices/tp_inv_106_t1.php',
        'invoices/tp_inv_106.php',
        'invoices/sr_inv_107.php',
        'invoices/tp_inv_108.php',
        'invoices/cx_inv_109.php',
        'invoices/sr_inv_110.php',
        'invoices/tp_inv_111.php',
        'invoices/tp_inv_112.php',
        'invoices/sr_inv_113.php',
        'invoices/tp_inv_114.php',
        'invoices/sr_inv_115.php',
        'invoices/sr_inv_116.php',
    ],
    'purchase_orders' => [
    ],
];
