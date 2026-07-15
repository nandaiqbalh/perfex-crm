<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Production quotation — TP Company Limited
 * Source PDF: 18 - 2026 - OTMSQ - 118 - Atlas Copco Compressor Spare Part (22-06-2026)
 *
 * Compressor Atlas Copco Spare Parts - Vigor Ship Unloader:
 * oil filter + filter.
 */
return [
    'key'                 => 'tp_otmsq_118',
    'source_quote_number' => '18 - 2026 - OTMSQ - 118 - Atlas Copco Compressor Spare Part',
    'customer_company'    => 'TP Company Limited',
    'force_status'        => 3, // Accepted
    'populate_tracker'    => true,
    'save_option'         => 'otmain_seed_proposal_tp_otmsq_118',
    'tracker_updates'     => array_fill(0, 2, ['item_status' => 'pending', 'notes' => '', 'admin_notes' => '']),
    'proposal' => [
        'subject'              => 'Atlas Copco Compressor Spare Parts - Vigor Ship Unloader - Quotation',
        'date'                 => '2026-06-22',
        'open_till'            => '2026-07-19',
        'status'               => 3,
        'rel_type'             => 'customer',
        'client_ref'           => 'Compressor Atlas Copco Spare Parts',
        'quote_title'          => 'Atlas Copco Compressor Spare Part',
        'document_title'       => 'Quotation',
        'terms'                => '',
        'expiry_days'          => 27,
        'availability'         => '',
        'notes'                => 'From China dispatch today from the Netherlands dispatch by Thursday.',
        // PDF deal contact (optional override)
        'contact_person_name'  => 'S.A.Ibrahim',
        'contact_person_email' => 's.ibrahim@otmain.com',
        'contact_person_phone' => '+31618228651',
        'payment_terms_text'   => '100% After delivery',
        'shipment_terms'       => 'EXW (Ex Works)',
        'delivery_time'        => 'On Stock',
        'total_usd_display'    => '$ 269,78',
        'total_gold_display'   => '',
        'subtotal'             => 221,
        'total_tax'            => 0,
        'total'                => 221,
        'discount_type'        => '',
        'discount_percent'     => 0,
        'discount_total'       => 0,
        'show_quantity_as'     => 1,
        'allow_comments'       => 1,
        'newitems'             => [
            1 => [
                'description'      => 'Oil Filter - Part Number: 1614874799',
                'long_description' => 'Compressor Atlas Copco Spare Parts - Vigor Ship Unloader',
                'qty'              => 1,
                'rate'             => 143,
                'unit'             => 'unit',
                'taxname'          => [],
                'order'            => 1,
            ],
            2 => [
                'description'      => 'Filter - Part Number: 1623507100',
                'long_description' => 'Compressor Atlas Copco Spare Parts - Vigor Ship Unloader',
                'qty'              => 1,
                'rate'             => 78,
                'unit'             => 'unit',
                'taxname'          => [],
                'order'            => 2,
            ],
        ],
    ],
];
