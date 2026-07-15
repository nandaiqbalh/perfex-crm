<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Production invoice — TP Company Limited
 * Source: 2025-INV-125 R1.1 Remote Control Accessories (supersedes original)
 * Related accessories for remote — link SQ-108 backup remote
 */
return [
    'key'                   => 'tp_inv_125',
    'source_invoice_number' => '2025-INV-125 R1.1',
    'source_quote_ref'      => 'Spare Accessories Remote Control - Vigor Shipunloader',
    'related_proposal_key'  => '9.2025-SQ-108-vigor-backup-remote',
    'customer_company'      => 'TP Company Limited',
    'force_status'          => 1,
    'save_option'           => 'otmain_seed_invoice_tp_inv_125',
    'invoice' => [
        'number'               => 125,
        'date'                 => '2026-01-17',
        'duedate'              => '2026-02-11',
        'expiry_days'          => 25,
        'status'               => 1,
        'document_title'       => 'Invoice',
        'invoice_title'        => 'R1.1',
        'adminnote'            => '',
        'contact_person_name'  => 'Widia Lestari - Ibrahim',
        'contact_person_email' => 'w.lestari@otmain.com',
        'contact_person_phone' => '+31620020563',
        'payment_terms_text'   => '',
        'delivery_terms'       => '',
        'lead_time'            => '',
        'delivery_address'     => "-",
        'availability'         => '',
        'notes'                => '',
        'total_usd_display'    => '$ 738,38',
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
                'description'      => 'Remote Control Accessories Packages:',
                'long_description' => "1 x Neck Belt\n1 x Battery Charger 230VAC - Fast Charger (3-4hr)\n2 x Batteries 2000MA",
                'qty'              => 1,
                'rate'             => 626.08,
                'unit'             => '',
                'taxname'          => [],
                'order'            => 1,
            ],
        ],
    ],
];
