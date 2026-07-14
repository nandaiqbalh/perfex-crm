<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Production quotation — TP Company Limited (2025)
 * Source PDF: 2025-PQ-104- Vigor Shipunloader - Service Trip (8-10-2025)
 *
 * Two option lines (both listed on PDF; subtotal = sum of options).
 * Buyer = TP Company Zanzibar; OT-MAIN block = issuer noise.
 */
return [
    'key'                 => 'tp_pq_104_service',
    'source_quote_number' => '2025-PQ-104- Vigor Shipunloader - Service Trip',
    'aliases'             => [
        '2025-PQ-104- Vigor Shipunloader - Service Trip',
        '2025-PQ-104',
        '2025-PQ-104-Vigor Shipunloader - Service Trip',
    ],
    'customer_company'    => 'TP Company Limited',
    'force_status'        => 3,
    'populate_tracker'    => true,
    'save_option'         => 'otmain_seed_proposal_tp_pq_104_service',
    'tracker_updates'     => array_fill(0, 2, ['item_status' => 'pending', 'notes' => '', 'admin_notes' => '']),
    'proposal' => [
        'subject'              => 'Vigor Shipunloader Service Trip - Quotation',
        'date'                 => '2025-10-08',
        'open_till'            => '2025-11-08',
        'status'               => 3,
        'rel_type'             => 'customer',
        'client_ref'           => 'Service Trip - Vigor Shipunloader',
        'quote_title'          => 'Vigor Shipunloader - Service Trip',
        'document_title'       => 'Quotation',
        'terms'                => '',
        'expiry_days'          => 31,
        'availability'         => '',
        'notes'                => 'Vigor Shipunloader Service Trip from 14/15 october to 14/15 november.',
        'contact_person_name'  => 'S.A.Ibrahim',
        'contact_person_email' => 'sales@otmain.com',
        'contact_person_phone' => '+31618228651',
        'payment_terms_text'   => '100% Before departure',
        'shipment_terms'       => '',
        'delivery_time'        => '14/15 October to 14/15 November',
        'total_usd_display'    => '',
        'total_gold_display'   => '',
        'subtotal'             => 16657.00,
        'total_tax'            => 0,
        'total'                => 16657.00,
        'discount_type'        => '',
        'discount_percent'     => 0,
        'discount_total'       => 0,
        'show_quantity_as'     => 1,
        'allow_comments'       => 1,
        'newitems'             => [
            1 => [
                'description'      => 'Option 1: Flexible Service',
                'long_description' => "Vigor Shipunloader Service Trip from 14/15 october to 14/15 november.\n\nStay in Zanzibar for a full month.\nIncludes travel documents, meals, ground transportation, accommodation, and ZIC insurance.\n• Working day: €350 per day\n• Remote assistance from accommodation: €105 for up to 3 hours of service",
                'qty'              => 1,
                'rate'             => 7407.00,
                'unit'             => '',
                'taxname'          => [],
                'order'            => 1,
            ],
            2 => [
                'description'      => 'Option 2: Including 20 Working Days',
                'long_description' => "Stay in Zanzibar for a full month; 20 working days can be used in the month.\nIncludes travel documents, meals, ground transportation, accommodation, and ZIC insurance.",
                'qty'              => 1,
                'rate'             => 9250.00,
                'unit'             => '',
                'taxname'          => [],
                'order'            => 2,
            ],
        ],
    ],
];
