<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Production invoice — TP Company Limited
 * Source: 2025-INV-113 Vigor Extension
 * PDF Quote Ref OCR 2025-PQ-100; buyer TP → 2.2025-PQ-101-vigor-extension
 * Extra Workdays: PDF qty 3 / unit €1200 / line €1200 → seed qty 1 @ €1200 (3 days note)
 */
return [
    'key'                   => 'tp_inv_113',
    'source_invoice_number' => '2025-INV-113',
    'source_quote_ref'      => '2025-PQ-101',
    'related_proposal_key'  => '2.2025-PQ-101-vigor-extension',
    'customer_company'      => 'TP Company Limited',
    'force_status'          => 1,
    'save_option'           => 'otmain_seed_invoice_tp_inv_113',
    'invoice' => [
        'number'               => 113,
        'date'                 => '2025-05-29',
        'duedate'              => null,
        'expiry_days'          => 0,
        'status'               => 1,
        'document_title'       => 'Invoice',
        'invoice_title'        => 'Vigor Extension',
        'adminnote'            => 'PDF Quote Ref.: 2025-PQ-100 (buyer TP — linked PQ-101 Extension)<br />Client Ref.: Extension Vigor Shipunloader Delivery<br />Expiration: Before Departure',
        'contact_person_name'  => 'S.A.Ibrahim',
        'contact_person_email' => 'inv@otmain.com',
        'contact_person_phone' => '+31618228651',
        'payment_terms_text'   => 'Before Departure',
        'delivery_terms'       => '',
        'lead_time'            => '',
        'delivery_address'     => "TP Company Limited\nBumbwini\nPO BOX 271 Zanzibar",
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
                'description'      => 'Vigor Shipunloader Delivery Extension',
                'long_description' => "Period: 19 May 2025 - 27 May 2025\n\nThis package includes Day Rate, Meals &Accommodation.\nPackage Details:\nTotal duration: 10 days\nWorking days: 3\nWaiting days: 6",
                'qty'              => 1,
                'rate'             => 3460.00,
                'unit'             => '',
                'taxname'          => [],
                'order'            => 1,
            ],
            2 => [
                'description'      => 'Extra Workdays (3 days)',
                'long_description' => "PDF line: qty 3 / billed total €1.200",
                'qty'              => 1,
                'rate'             => 1200.00,
                'unit'             => '',
                'taxname'          => [],
                'order'            => 2,
            ],
            3 => [
                'description'      => 'Discount',
                'long_description' => "",
                'qty'              => 1,
                'rate'             => -1238.00,
                'unit'             => '',
                'taxname'          => [],
                'order'            => 3,
            ],
        ],
    ],
];
