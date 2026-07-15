<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Production invoice — TP Company Limited
 * Source: 2026-INV-106 Commercial Suction Hose
 * Quote Ref → tp_suction_hose
 */
return [
    'key'                   => 'tp_inv_106',
    'source_invoice_number' => '2026-INV-106- Vigor Shipunloader - Suction Hose',
    'source_quote_ref'      => '3 - 2026 - OTMSQ - 103 - Suction Hose rev.1.1',
    'related_proposal_key'  => 'tp_suction_hose',
    'customer_company'      => 'TP Company Limited',
    'force_status'          => 1,
    'save_option'           => 'otmain_seed_invoice_tp_inv_106',
    'invoice' => [
        'number'               => 106,
        'date'                 => '2026-01-21',
        'duedate'              => '2026-02-11',
        'expiry_days'          => 21,
        'status'               => 1,
        'document_title'       => 'Commercial Invoice',
        'invoice_title'        => 'Vigor Shipunloader - Suction Hose',
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
        'total_usd_display'    => '$ 19.286,00',
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
                'description'      => 'Custom Made Suction hose: Size: DN400 x L5500mm.',
                'long_description' => "Working pressure: -0.8 / 10bar.\nBursting pressure:10bar\nWith one side fixed flange, and one side Split flange. Vacuum Reinforced with Vacuum rings, Both side flange are antirust",
                'qty'              => 4,
                'rate'             => 4100.00,
                'unit'             => '',
                'taxname'          => [],
                'order'            => 1,
            ],
        ],
    ],
];
