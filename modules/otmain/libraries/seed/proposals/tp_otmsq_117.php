<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Production quotation — TP Company Limited
 * Source PDF: 17 - 2026 - OTMSQ - 117 - Marine Crane - Vigor Ship Unloader (19-06-2026)
 *
 * Offshore Marine Crane + Hydraulic power pack for Vigor Ship Unloader.
 */
return [
    'key'                 => 'tp_otmsq_117',
    'source_quote_number' => '17 - 2026 - OTMSQ - 117 - Marine Crane - Vigor Ship Unloader',
    'customer_company'    => 'TP Company Limited',
    'force_status'        => 3, // Accepted
    'populate_tracker'    => true,
    'save_option'         => 'otmain_seed_proposal_tp_otmsq_117',
    'tracker_updates'     => array_fill(0, 3, ['item_status' => 'pending', 'notes' => '', 'admin_notes' => '']),
    'proposal' => [
        'subject'              => 'Offshore Marine Crane - Vigor Ship Unloader - Quotation',
        'date'                 => '2026-06-19',
        'open_till'            => '2026-07-19',
        'status'               => 3,
        'rel_type'             => 'customer',
        'client_ref'           => 'Pontoon Crane',
        'quote_title'          => 'Marine Crane - Vigor Ship Unloader',
        'document_title'       => 'Quotation',
        'terms'                => '',
        'expiry_days'          => 30,
        'availability'         => '',
        'notes'                => 'See attachment 1.1 for Pictures, specifications and load chart.',
        // PDF deal contact (optional override)
        'contact_person_name'  => 'S.A.Ibrahim',
        'contact_person_email' => 's.ibrahim@otmain.com',
        'contact_person_phone' => '+316****8651',
        'payment_terms_text'   => '50% in advance<br />50% before delivery',
        'shipment_terms'       => 'EXW (Ex Works)',
        'delivery_time'        => '50 workdays',
        'total_usd_display'    => '$ 73.731,87',
        'total_gold_display'   => '',
        'subtotal'             => 63011,
        'total_tax'            => 0,
        'total'                => 63011,
        'discount_type'        => '',
        'discount_percent'     => 0,
        'discount_total'       => 0,
        'show_quantity_as'     => 1,
        'allow_comments'       => 1,
        'newitems'             => [
            1 => [
                'description'      => 'Custom Made Offshore Marine 25 ton knuckle marine crane, lifting 2000kg',
                'long_description' => 'Offshore Marine Crane - Shipunloader',
                'qty'              => 1,
                'rate'             => 56000,
                'unit'             => 'unit',
                'taxname'          => [],
                'order'            => 1,
            ],
            2 => [
                'description'      => 'Hydraulic power pack - 400V, 50HZ, 3 PHASE, 350bar, 50KW',
                'long_description' => 'Including:
Winch
5 points remote control

See attachment 1.1 for Pictures, specifications and load chart

Offshore Marine Crane - Shipunloader',
                'qty'              => 1,
                'rate'             => 7511,
                'unit'             => 'unit',
                'taxname'          => [],
                'order'            => 2,
            ],
            3 => [
                'description'      => 'Discount',
                'long_description' => '',
                'qty'              => 1,
                'rate'             => -500,
                'unit'             => 'unit',
                'taxname'          => [],
                'order'            => 3,
            ],
        ],
    ],
];
