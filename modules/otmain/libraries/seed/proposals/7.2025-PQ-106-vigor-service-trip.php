<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Production quotation — TP Company Limited (2025)
 * Source PDF: 2025-PQ-106- Vigor Shipunloader - Service Trip (19-11-2025)
 *
 * PDF prints Expiration 08-11-2025 (before quote date) — OCR/template noise;
 * open_till set to end of service window 15-12-2025.
 */
return [
    'key'                 => '7.2025-PQ-106-vigor-service-trip',
    'source_quote_number' => '7 - 2025 - PQ - 106 - Vigor Shipunloader - Service Trip',
    'aliases'             => [
        '7 - 2025 - PQ - 106 - Vigor Shipunloader - Service Trip',
        '2025-PQ-106- Vigor Shipunloader - Service Trip',
        '2025-PQ-106',
        '2025-PQ-106-Vigor Shipunloader - Service Trip',
        '7.2025 - PQ - 106 - Vigor Shipunloader - Service Trip',
        'tp_pq_106_service',
    ],
    'customer_company'    => 'TP Company Limited',
    'force_status'        => 3,
    'populate_tracker'    => true,
    'save_option'         => 'otmain_seed_proposal_7_2025_pq_106_vigor_service_trip',
    'tracker_updates'     => array_fill(0, 2, ['item_status' => 'pending', 'notes' => '', 'admin_notes' => '']),
    'proposal' => [
        'subject'              => 'Vigor Shipunloader Service Trip Nov-Dec - Quotation',
        'date'                 => '2025-11-19',
        'open_till'            => '2025-12-15',
        'status'               => 3,
        'rel_type'             => 'customer',
        'client_ref'           => 'Service Trip - Vigor Shipunloader',
        'quote_title'          => 'Vigor Shipunloader - Service Trip',
        'document_title'       => 'Quotation',
        'terms'                => '',
        'expiry_days'          => 26,
        'availability'         => '',
        'notes'                => 'Vigor Shipunloader Service Trip from 22 November to 15 December 2025.',
        'contact_person_name'  => "Sha'Iez Ibrahim",
        'contact_person_email' => 's.ibrahim@otmain.com',
        'contact_person_phone' => '+31618228651',
        'payment_terms_text'   => '100% Before departure',
        'shipment_terms'       => '',
        'delivery_time'        => '22 November 2025 until 15 December 2025',
        'total_usd_display'    => '',
        'total_gold_display'   => '',
        'subtotal'             => 10468.20,
        'total_tax'            => 0,
        'total'                => 10468.20,
        'discount_type'        => '',
        'discount_percent'     => 0,
        'discount_total'       => 0,
        'show_quantity_as'     => 1,
        'allow_comments'       => 1,
        'newitems'             => [
            1 => [
                'description'      => 'Option 1: Flexible Service',
                'long_description' => "Stay in Zanzibar from 22 November 2025 until 15 December 2025, flexible working days as per call.\nIncludes meals, accommodation, excluding:\n• Each Working day: €350 per day\n• Remote assistance from accommodation: €105 for up to 3 hours of service",
                'qty'              => 1,
                'rate'             => 4317.60,
                'unit'             => '',
                'taxname'          => [],
                'order'            => 1,
            ],
            2 => [
                'description'      => 'Option 2: Including 18 Working Days',
                'long_description' => "Stay in Zanzibar from 22 November 2025 until 15 December 2025.\nIncludes meals, accommodation.\nIncludes 18 working days until 15 December 2025.",
                'qty'              => 1,
                'rate'             => 6150.60,
                'unit'             => '',
                'taxname'          => [],
                'order'            => 2,
            ],
        ],
    ],
];
