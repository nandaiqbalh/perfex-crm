<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Production invoice — Projectservice Nederland B.V. (2025)
 * Source: 2025-INV-103 — Projectleider-VU - Breda
 * Client Ref.: 25.1000266.1.0041
 * No CRM quotation link (Quote Ref = project/job name).
 */
return [
    'key'                   => 'ps_inv_103',
    'source_invoice_number' => '2025-INV-103',
    'source_quote_ref'      => 'Projectleider-VU - Breda',
    'customer_company'      => 'Projectservice Nederland B.V.',
    'force_status'          => 1,
    'save_option'           => 'otmain_seed_invoice_ps_inv_103',
    'invoice' => [
        'number'               => 103,
        'date'                 => '2025-01-23',
        'duedate'              => '2025-02-22',
        'expiry_days'          => 30,
        'status'               => 1,
        'document_title'       => 'Invoice',
        'invoice_title'        => 'Projectleider-VU - Breda',
        'adminnote'            => 'PDF Invoice Number: 2025-INV-103<br />Quote Ref.: Projectleider-VU - Breda<br />Client Ref.: 25.1000266.1.0041',
        'contact_person_name'  => 'S.A.Ibrahim',
        'contact_person_email' => 'inv@otmain.com',
        'contact_person_phone' => '+31618228651',
        'payment_terms_text'   => '',
        'delivery_terms'       => '',
        'lead_time'            => '',
        'delivery_address'     => "Projectservice Nederland B.V.\nDarwin 20\n7609RL Almelo\nThe Netherlands",
        'availability'         => '',
        'notes'                => '',
        'total_usd_display'    => '',
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
                'description'      => 'Uren tarief Projectleider - VU - Breda',
                'long_description' => "Inclusief reistijd minus 30 min\n\nBegintijd Heenreis rijden: 13:45\nStart tijd werkzaamheden: 18:18\nEind tijd werkzaamheden: 22:59\nAankomsttijd Terugreis rijden: 23:47",
                'qty'              => 9.53,
                'rate'             => 30.00,
                'unit'             => '',
                'taxname'          => ['VAT|21'],
                'order'            => 1,
            ],
            2 => [
                'description'      => 'Parkeerkosten',
                'long_description' => "",
                'qty'              => 1,
                'rate'             => 13.22,
                'unit'             => '',
                'taxname'          => ['VAT|21'],
                'order'            => 2,
            ],
            3 => [
                'description'      => 'Kilometer vergoeding',
                'long_description' => "",
                'qty'              => 346,
                'rate'             => 0.23,
                'unit'             => '',
                'taxname'          => [],
                'order'            => 3,
            ],
        ],
    ],
];
