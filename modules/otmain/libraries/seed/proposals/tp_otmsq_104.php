<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Production quotation — TP Company Limited
 * Source PDF: 4 - 2026 - OTMSQ - 104 (referenced in Packing List & Invoice)
 * Minimal stub — full quotation PDF not available yet.
 * Address/contact company dari tblclients via orchestrator.
 */
return [
    'key'                 => 'tp_otmsq_104',
    'source_quote_number' => '4 - 2026 - OTMSQ - 104',
    'customer_company'    => 'TP Company Limited',
    'force_status'        => 3,
    'populate_tracker'    => true,
    'save_option'         => 'otmain_seed_proposal_tp_otmsq_104',
    'tracker_updates'     => [
        0 => [
            'item_status' => 'pending',
            'notes'       => '',
            'admin_notes' => '',
        ],
    ],
    'proposal' => [
        'subject'            => 'Quotation 4 - 2026 - OTMSQ - 104',
        'date'               => '2026-01-21',
        'open_till'          => '2026-01-27',
        'status'             => 3,
        'rel_type'           => 'customer',
        'client_ref'         => '',
        'quote_title'        => '',
        'document_title'     => 'Quotation',
        'terms'              => '',
        'expiry_days'        => 6,
        'availability'       => '',
        'notes'              => '',
        'subtotal'           => 0,
        'total_tax'          => 0,
        'total'              => 0,
        'discount_type'      => '',
        'discount_percent'   => 0,
        'discount_total'     => 0,
        'show_quantity_as'   => 1,
        'allow_comments'     => 1,
        'newitems'           => [
            1 => [
                'description'      => 'Quotation 4 - 2026 - OTMSQ - 104 items',
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
