<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Production invoice — TP Company Limited
 * Source: 2026-INV-111 Suction Hose DN300
 * Quote Ref → tp_suction_hose
 */
return [
    'key'                   => 'tp_inv_111',
    'source_invoice_number' => '2026-INV-111 - Suction Hose - Vigor Shipunloader',
    'source_quote_ref'      => '3 - 2026 - OTMSQ - 103 - Suction Hose',
    'related_proposal_key'  => 'tp_suction_hose',
    'customer_company'      => 'TP Company Limited',
    'force_status'          => 1,
    'save_option'           => 'otmain_seed_invoice_tp_inv_111',
    'invoice' => [
        'number'               => 111,
        'date'                 => '2026-02-20',
        'duedate'              => '2026-02-23',
        'expiry_days'          => 3,
        'status'               => 1,
        'document_title'       => 'Invoice',
        'invoice_title'        => 'Suction Hose - Vigor Shipunloader',
        'adminnote'            => '',
        'contact_person_name'  => 'Widia Lestari - Ibrahim',
        'contact_person_email' => 'w.lestari@otmain.com',
        'contact_person_phone' => '+31620020563',
        'payment_terms_text'   => '100% in Advance',
        'delivery_terms'       => 'EXW. Ready in 2 Weeks After Payment. We will push to manufacture it faster.',
        'lead_time'            => '',
        'delivery_address'     => "--",
        'availability'         => '',
        'notes'                => '',
        'total_usd_display'    => '$ 4.060,00',
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
                'description'      => 'Custom Made Suction hose: Size: DN300 x L5500mm.',
                'long_description' => "Working pressure: -0.8 / 10bar.\nBursting pressure:10bar\nWith one side fixed flange, and one side Split flange. Vacuum Reinforced with Vacuum",
                'qty'              => 1,
                'rate'             => 3500.00,
                'unit'             => '',
                'taxname'          => [],
                'order'            => 1,
            ],
        ],
    ],
];
