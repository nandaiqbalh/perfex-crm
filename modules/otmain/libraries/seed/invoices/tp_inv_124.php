<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Production invoice — TP Company Limited
 * Source: 2025-INV-124-Spare Parts-R1.1 (supersedes original 1-12-2025)
 * Note: invoice date 2026-01-20 but PDF number still 2025-INV-124
 */
return [
    'key'                   => 'tp_inv_124',
    'source_invoice_number' => '2025-INV-124-Spare Parts-R1.1',
    'source_quote_ref'      => '',
    'customer_company'      => 'TP Company Limited',
    'force_status'          => 1,
    'save_option'           => 'otmain_seed_invoice_tp_inv_124',
    'invoice' => [
        'number'               => 124,
        'date'                 => '2026-01-20',
        'duedate'              => '2026-02-11',
        'expiry_days'          => 22,
        'status'               => 1,
        'document_title'       => 'Invoice',
        'invoice_title'        => 'Spare Parts-R1.1',
        'adminnote'            => '',
        'contact_person_name'  => 'Widia Lestari - Ibrahim',
        'contact_person_email' => 'w.lestari@otmain.com',
        'contact_person_phone' => '+31620020563',
        'payment_terms_text'   => '100% Before Departure',
        'delivery_terms'       => '-',
        'lead_time'            => '',
        'delivery_address'     => "Handover",
        'availability'         => '',
        'notes'                => '',
        'total_usd_display'    => '$ 500,33',
        'bank_account'         => 'EUR',
        'show_quantity_as'     => 1,
        'sale_agent'           => 0,
        'discount_type'        => '',
        'discount_percent'     => 0,
        'discount_total'       => 0,
        'adjustment'           => 0,
        'clientnote'           => 'We kindly request to transfer the amount before the Expiration date, mentioning the invoice number. Our general terms and conditions apply to all services.',
        'terms'                => '',
        'newitems'             => [
            1 => [
                'description'      => 'Siemens Sitop psu8200 20a 120-230vac 110-220V DC 24V/20a DC',
                'long_description' => "",
                'qty'              => 1,
                'rate'             => 389.00,
                'unit'             => '',
                'taxname'          => [],
                'order'            => 1,
            ],
            2 => [
                'description'      => 'RJ45 Female Ethernet Plug',
                'long_description' => "",
                'qty'              => 4,
                'rate'             => 3.24,
                'unit'             => '',
                'taxname'          => [],
                'order'            => 2,
            ],
            3 => [
                'description'      => 'Waterproof Anti Rust Ethernet Coupling RJ45',
                'long_description' => "",
                'qty'              => 1,
                'rate'             => 10.34,
                'unit'             => '',
                'taxname'          => [],
                'order'            => 3,
            ],
            4 => [
                'description'      => 'UK Adaptor',
                'long_description' => "",
                'qty'              => 2,
                'rate'             => 5.19,
                'unit'             => '',
                'taxname'          => [],
                'order'            => 4,
            ],
            5 => [
                'description'      => 'Socket EU Assembly Type Without Cable',
                'long_description' => "",
                'qty'              => 1,
                'rate'             => 2.77,
                'unit'             => '',
                'taxname'          => [],
                'order'            => 5,
            ],
        ],
    ],
];
