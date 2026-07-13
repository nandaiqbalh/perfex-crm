<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Production quotation — TP Company Limited
 * Source PDF: 11 - 2026 - OTMSQ - 111 (referenced in Packing List & Invoice)
 * Minimal stub — full quotation PDF not available yet.
 */
return [
    'key'                 => 'tp_otmsq_111',
    'source_quote_number' => '11 - 2026 - OTMSQ - 111',
    'customer_company'    => 'TP Company Limited',
    'force_status'        => 3, // Accepted
    'populate_tracker'    => true,
    'save_option'         => 'otmain_seed_proposal_tp_otmsq_111',
    'tracker_updates'     => [
        0 => [
            'item_status' => 'pending',
            'notes'       => '',
            'admin_notes' => '',
        ],
    ],
    'proposal' => [
        'subject'              => 'Quotation 11 - 2026 - OTMSQ - 111',
        'date'                 => '2026-02-15',
        'open_till'            => '2026-02-21',
        'status'               => 3,
        'rel_type'             => 'customer',
        'email'                => 'dheeraj@luckycement.co.tz',
        'phone'                => '+255 777 093 955',
        'address'              => 'Bumbwini',
        'city'                 => 'Zanzibar',
        'state'                => '',
        'zip'                  => 'P.O BOX 271',
        'client_ref'           => '',
        'quote_title'          => '',
        'document_title'       => 'Quotation',
        'terms'                => '',
        'expiry_days'          => 6,
        'availability'         => '',
        'notes'                => '',
        'contact_person_name'  => 'Dheeraj',
        'contact_person_email' => 'dheeraj@luckycement.co.tz',
        'contact_person_phone' => '+255 777 093 955',
        'subtotal'             => 0,
        'total_tax'            => 0,
        'total'                => 0,
        'discount_type'        => '',
        'discount_percent'     => 0,
        'discount_total'       => 0,
        'show_quantity_as'     => 1,
        'allow_comments'       => 1,
        'use_customer_contact' => false,
        'newitems'             => [
            1 => [
                'description'      => 'Quotation 11 - 2026 - OTMSQ - 111 items',
                'long_description' => 'Refer to Packing List & Invoice for item details.',
                'qty'              => 1,
                'rate'             => 0,
                'unit'             => 'unit',
                'taxname'          => [],
                'order'            => 1,
            ],
        ],
    ],
];
