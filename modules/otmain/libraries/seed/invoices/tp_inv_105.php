<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Production invoice — TP Company Limited
 * Source: 2026-INV-105 Service Trip Additional Work Days
 * Quote Ref: 2025-PQ-104/106
 */
return [
    'key'                   => 'tp_inv_105',
    'source_invoice_number' => '2026-INV-105-Vigor Shipunldr.-Service Trip-Additional Work Days',
    'source_quote_ref'      => '2025-PQ-104/106-Vigor Shipunloader-Service Trip',
    'related_proposal_key'  => '5.2025-PQ-104-vigor-service-trip',
    'related_proposal_keys' => ['5.2025-PQ-104-vigor-service-trip', '7.2025-PQ-106-vigor-service-trip'],
    'customer_company'      => 'TP Company Limited',
    'force_status'          => 1,
    'save_option'           => 'otmain_seed_invoice_tp_inv_105',
    'invoice' => [
        'number'               => 105,
        'date'                 => '2026-01-11',
        'duedate'              => '2026-02-11',
        'expiry_days'          => 31,
        'status'               => 1,
        'document_title'       => 'Invoice',
        'invoice_title'        => 'Vigor Shipunldr.-Service Trip-Additional Work Days',
        'adminnote'            => '',
        'contact_person_name'  => 'Widia Lestari - Ibrahim',
        'contact_person_email' => 'w.lestari@otmain.com',
        'contact_person_phone' => '+31620020563',
        'payment_terms_text'   => '-',
        'delivery_terms'       => '-',
        'lead_time'            => '',
        'delivery_address'     => "-",
        'availability'         => '',
        'notes'                => 'Vigor Shipunloader Service Trip- Remaining Working Days (Including Emergency Service Trip) - 10 December until 26 December',
        'total_usd_display'    => '$ 6.912,53',
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
                'description'      => 'Flexible Service (Ref.: 2025-PQ-104-Vigor Shipunloader-Service Trip)',
                'long_description' => "",
                'qty'              => 1,
                'rate'             => 934.00,
                'unit'             => '',
                'taxname'          => [],
                'order'            => 1,
            ],
            2 => [
                'description'      => 'Remaining Working Days (4 days)',
                'long_description' => "12/12, 13/12, 18/12, 19/12",
                'qty'              => 4,
                'rate'             => 350.00,
                'unit'             => '',
                'taxname'          => [],
                'order'            => 2,
            ],
            3 => [
                'description'      => 'Emergency Service Cost',
                'long_description' => "",
                'qty'              => 1,
                'rate'             => 2844.00,
                'unit'             => '',
                'taxname'          => [],
                'order'            => 3,
            ],
            4 => [
                'description'      => 'Emergency Working Days (2 Days)',
                'long_description' => "25/12, 26/12",
                'qty'              => 2,
                'rate'             => 350.00,
                'unit'             => '',
                'taxname'          => [],
                'order'            => 4,
            ],
        ],
    ],
];
