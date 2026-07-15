<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Production invoice — TP Company Limited
 * Source: 2026-INV-103-T1 Vacuum Pump Electrical Panel (50%)
 * Quote Ref: 2025-SQ-109 → 10.2025-SQ-109-vigor-vacuum-panel
 */
return [
    'key'                   => 'tp_inv_103_t1',
    'source_invoice_number' => '2026-INV-103-T1-Vigor Shipunloader-Vacuum Pump Electrical Panel',
    'source_quote_ref'      => '2025-SQ-109-Vacuum Pump Electrical Panel',
    'related_proposal_key'  => '10.2025-SQ-109-vigor-vacuum-panel',
    'customer_company'      => 'TP Company Limited',
    'force_status'          => 1,
    'save_option'           => 'otmain_seed_invoice_tp_inv_103_t1',
    'invoice' => [
        'number'               => 103,
        'date'                 => '2026-01-11',
        'duedate'              => '2026-01-26',
        'expiry_days'          => 15,
        'status'               => 1,
        'document_title'       => 'Invoice',
        'invoice_title'        => 'T1-Vigor Shipunloader-Vacuum Pump Electrical Panel',
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
        'total_usd_display'    => '$ 7.948,32',
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
                'description'      => 'Electrical Panel',
                'long_description' => "First Payment: 50% of €13.498,00 - Vigor Shipunloader -Vacuum Unit Electrical Panel (MCC 3)\n\nInlcudes Electrical diagram limited to MCC3\nTwo revisions Included",
                'qty'              => 1,
                'rate'             => 6749.00,
                'unit'             => '',
                'taxname'          => [],
                'order'            => 1,
            ],
        ],
    ],
];
