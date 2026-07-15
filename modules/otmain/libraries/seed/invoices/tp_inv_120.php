<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Production invoice — TP Company Limited
 * Source: 2025-INV-120 Reimbursement Costs (REV.1 26-9-2025)
 * Waiting days: PDF qty 5 / unit €510 / line €510 → qty 1 @ €510
 */
return [
    'key'                   => 'tp_inv_120',
    'source_invoice_number' => '2025-INV-120',
    'source_quote_ref'      => '2025-PQ-101',
    'related_proposal_key'  => '2.2025-PQ-101-vigor-extension',
    'customer_company'      => 'TP Company Limited',
    'force_status'          => 1,
    'save_option'           => 'otmain_seed_invoice_tp_inv_120',
    'invoice' => [
        'number'               => 120,
        'date'                 => '2025-09-26',
        'duedate'              => null,
        'expiry_days'          => 0,
        'status'               => 1,
        'document_title'       => 'Invoice',
        'invoice_title'        => 'Reimbursement Costs Vigor Shipunloader Delivery',
        'adminnote'            => '',
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
                'description'      => 'Flight Costs',
                'long_description' => "Zanzibar - Amsterdam €1.034,98\nAmsterdam - Jakarta €831,42\nJakarta - Padang €162,00\nPadang - Jakarta €196,99\nJakarta - Amsterdam €816,34\n(price increased due to high season)",
                'qty'              => 1,
                'rate'             => 3041.73,
                'unit'             => '',
                'taxname'          => [],
                'order'            => 1,
            ],
            2 => [
                'description'      => 'Accomodation Cost Padang/Jakarta',
                'long_description' => "",
                'qty'              => 1,
                'rate'             => 535.00,
                'unit'             => '',
                'taxname'          => [],
                'order'            => 2,
            ],
            3 => [
                'description'      => 'Accomodation Cost Zanzibar (1 to 11 July)',
                'long_description' => "",
                'qty'              => 1,
                'rate'             => 686.34,
                'unit'             => '',
                'taxname'          => [],
                'order'            => 3,
            ],
            4 => [
                'description'      => 'Waiting days (5,6,7,9,10 July)',
                'long_description' => "5 waiting days — billed as package €510",
                'qty'              => 1,
                'rate'             => 510.00,
                'unit'             => '',
                'taxname'          => [],
                'order'            => 4,
            ],
        ],
    ],
];
