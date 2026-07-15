<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Production invoice — Projectservice Nederland B.V. (2025)
 * Source: 2025-INV-101 — Projectleider-VU - Utrecht
 * Client Ref.: 25.1000266.1.0033
 * No CRM quotation link (Quote Ref = project/job name).
 */
return [
    'key'                   => 'ps_inv_101',
    'source_invoice_number' => '2025-INV-101',
    'source_quote_ref'      => 'Projectleider-VU - Utrecht',
    'customer_company'      => 'Projectservice Nederland B.V.',
    'force_status'          => 1,
    'save_option'           => 'otmain_seed_invoice_ps_inv_101',
    'invoice' => [
        'number'               => 101,
        'date'                 => '2025-01-22',
        'duedate'              => '2025-02-21',
        'expiry_days'          => 30,
        'status'               => 1,
        'document_title'       => 'Invoice',
        'invoice_title'        => 'Projectleider-VU - Utrecht',
        'adminnote'            => 'PDF Invoice Number: 2025-INV-101<br />Quote Ref.: Projectleider-VU - Utrecht<br />Client Ref.: 25.1000266.1.0033',
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
                'description'      => 'Uren tarief Projectleider - VU - Utrecht',
                'long_description' => "Inclusief reistijd minus 30 min\n\nBegintijd Heenreis rijden: 16:15\nStart tijd werkzaamheden: 17:30\nEind tijd werkzaamheden: 22:59\nAankomsttijd Terugreis rijden: 23:45",
                'qty'              => 7,
                'rate'             => 30.00,
                'unit'             => '',
                'taxname'          => ['VAT|21'],
                'order'            => 1,
            ],
            2 => [
                'description'      => 'Parkeerkosten',
                'long_description' => "",
                'qty'              => 1,
                'rate'             => 15.69,
                'unit'             => '',
                'taxname'          => ['VAT|21'],
                'order'            => 2,
            ],
            3 => [
                'description'      => 'Kilometer vergoeding',
                'long_description' => "",
                'qty'              => 125,
                'rate'             => 0.23,
                'unit'             => '',
                'taxname'          => [],
                'order'            => 3,
            ],
        ],
    ],
];
