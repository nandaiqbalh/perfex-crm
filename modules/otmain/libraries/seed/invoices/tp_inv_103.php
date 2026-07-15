<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Production invoice — TP Company Limited
 * Source: 2026-INV-103 Commercial Vac.Pump Electrical Panel
 * Quote Ref OCR PQ-107 wrong → SQ-109 vacuum panel
 */
return [
    'key'                   => 'tp_inv_103',
    'source_invoice_number' => '2026-INV-103 - Vigor Shipunloader - Vac.Pump Electrical Panel',
    'source_quote_ref'      => '2025-SQ-109-Vacuum Pump Electrical Panel',
    'related_proposal_key'  => '10.2025-SQ-109-vigor-vacuum-panel',
    'customer_company'      => 'TP Company Limited',
    'force_status'          => 1,
    'save_option'           => 'otmain_seed_invoice_tp_inv_103',
    'invoice' => [
        'number'               => 103,
        'date'                 => '2026-01-21',
        'duedate'              => '2026-02-11',
        'expiry_days'          => 21,
        'status'               => 1,
        'document_title'       => 'Commercial Invoice',
        'invoice_title'        => 'Vigor Shipunloader - Vac.Pump Electrical Panel',
        'adminnote'            => '',
        'contact_person_name'  => 'Widia Lestari - Ibrahim',
        'contact_person_email' => 'w.lestari@otmain.com',
        'contact_person_phone' => '+31620020563',
        'payment_terms_text'   => '50% in advance 50% before delivery',
        'delivery_terms'       => '30 Days after the first Term Payment',
        'lead_time'            => '',
        'delivery_address'     => "--",
        'availability'         => '',
        'notes'                => '',
        'total_usd_display'    => '$ 15.873,65',
        'bank_account'         => 'USD',
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
                'long_description' => "Inlcudes Electrical diagram limited to MCC3\nTwo revisions Included",
                'qty'              => 1,
                'rate'             => 13498.00,
                'unit'             => '',
                'taxname'          => [],
                'order'            => 1,
            ],
        ],
    ],
];
