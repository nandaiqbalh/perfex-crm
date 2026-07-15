<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Production invoice — TP Company Limited
 * Source: 2025-INV-115 Vigor Extension
 * Related: 2.2025-PQ-101-vigor-extension
 */
return [
    'key'                   => 'tp_inv_115',
    'source_invoice_number' => '2025-INV-115',
    'source_quote_ref'      => '2025-PQ-101',
    'related_proposal_key'  => '2.2025-PQ-101-vigor-extension',
    'customer_company'      => 'TP Company Limited',
    'force_status'          => 1,
    'save_option'           => 'otmain_seed_invoice_tp_inv_115',
    'invoice' => [
        'number'               => 115,
        'date'                 => '2025-06-08',
        'duedate'              => null,
        'expiry_days'          => 0,
        'status'               => 1,
        'document_title'       => 'Invoice',
        'invoice_title'        => 'Vigor Extension',
        'adminnote'            => 'PDF Quote Ref.: 2025-PQ-100 (linked PQ-101)<br />Client Ref.: Extension Vigor Shipunloader Delivery',
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
                'long_description' => "Period: 28 May 2025 - 07 June 2025\nTotal duration: 11 day\nWorking days: 2\nWaiting days: 9",
                'qty'              => 1,
                'rate'             => 1849.00,
                'unit'             => '',
                'taxname'          => [],
                'order'            => 1,
            ],
            2 => [
                'description'      => '30% of Day Rate for Programming as per Request',
                'long_description' => "",
                'qty'              => 3,
                'rate'             => 120.00,
                'unit'             => '',
                'taxname'          => [],
                'order'            => 2,
            ],
        ],
    ],
];
