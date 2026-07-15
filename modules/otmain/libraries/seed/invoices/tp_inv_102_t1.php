<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Production invoice — TP Company Limited
 * Source: 2026-INV-102-T1 Spare Parts (First Payment 50%)
 * Quote Ref: 2025-PQ-107 → 8.2025-SQ-107-vigor-spare-parts
 */
return [
    'key'                   => 'tp_inv_102_t1',
    'source_invoice_number' => '2026-INV-102-T1-Vigor Shipunloader-Spare Parts',
    'source_quote_ref'      => '2025-PQ-107-Vigor Shipunloader - Spare Parts',
    'related_proposal_key'  => '8.2025-SQ-107-vigor-spare-parts',
    'customer_company'      => 'TP Company Limited',
    'force_status'          => 1,
    'save_option'           => 'otmain_seed_invoice_tp_inv_102_t1',
    'invoice' => [
        'number'               => 102,
        'date'                 => '2026-01-11',
        'duedate'              => '2026-01-26',
        'expiry_days'          => 15,
        'status'               => 1,
        'document_title'       => 'Invoice',
        'invoice_title'        => 'T1-Vigor Shipunloader-Spare Parts',
        'adminnote'            => '',
        'contact_person_name'  => 'Widia Lestari - Ibrahim',
        'contact_person_email' => 'w.lestari@otmain.com',
        'contact_person_phone' => '+31620020563',
        'payment_terms_text'   => 'First Term: 50% by Order',
        'delivery_terms'       => 'EXW',
        'lead_time'            => '',
        'delivery_address'     => "TP Company Limited\nBumbwini\nPO BOX 271 Zanzibar",
        'availability'         => '',
        'notes'                => '',
        'total_usd_display'    => '$ 5.285,43',
        'bank_account'         => 'EUR',
        'show_quantity_as'     => 1,
        'sale_agent'           => 0,
        'discount_type'        => '',
        'discount_percent'     => 0,
        'discount_total'       => 0,
        'adjustment'           => 0,
        'clientnote'           => '',
        'terms'                => '',
        'newitems'             => [
            1 => [
                'description'      => 'Valves , Hydraulics & Vacuum Parts',
                'long_description' => "First Payment: 50% of €10.570,86 - Vigor Shipunloader - Spare Parts\nReference: 2025-PQ-107-Vigor Shipunloader - Spare Parts",
                'qty'              => 1,
                'rate'             => 5285.43,
                'unit'             => '',
                'taxname'          => [],
                'order'            => 1,
            ],
        ],
    ],
];
