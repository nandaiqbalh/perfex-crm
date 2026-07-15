<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Production invoice — Projectservice Nederland B.V. (2025)
 * Source: 2025-INV-110 — Afgifte-VU- Gouda
 * Client Ref.: VU Gouda
 * No CRM quotation link (Quote Ref = project/job name).
 */
return [
    'key'                   => 'ps_inv_110',
    'source_invoice_number' => '2025-INV-110',
    'source_quote_ref'      => 'Afgifte-VU- Gouda',
    'customer_company'      => 'Projectservice Nederland B.V.',
    'force_status'          => 1,
    'save_option'           => 'otmain_seed_invoice_ps_inv_110',
    'invoice' => [
        'number'               => 110,
        'date'                 => '2025-02-10',
        'duedate'              => '2025-03-09',
        'expiry_days'          => 27,
        'status'               => 1,
        'document_title'       => 'Invoice',
        'invoice_title'        => 'Afgifte-VU- Gouda',
        'adminnote'            => 'PDF Invoice Number: 2025-INV-110<br />Quote Ref.: Afgifte-VU- Gouda<br />Client Ref.: VU Gouda',
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
                'description'      => 'Afgifte scanners, netwerk materiaal en stickers - VU - Gouda',
                'long_description' => "Geregistreerd: 1.86 uur\n\nBegintijd Heenreis rijden: 16:40\nStart tijd werkzaamheden: -\nEind tijd werkzaamheden: -\nAankomsttijd Terugreis rijden: 18:32",
                'qty'              => 1,
                'rate'             => 30.00,
                'unit'             => '',
                'taxname'          => ['VAT|21'],
                'order'            => 1,
            ],
            2 => [
                'description'      => 'Kilometer vergoeding',
                'long_description' => "",
                'qty'              => 54,
                'rate'             => 0.23,
                'unit'             => '',
                'taxname'          => [],
                'order'            => 2,
            ],
        ],
    ],
];
