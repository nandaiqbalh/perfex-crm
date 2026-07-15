<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Production quotation — CemFlexX B.V. (2025)
 * Source folder: 4.2025-PQ-103 - CemflexX - Service Trip - Vigor Shipunloader
 * Quote Number: 2025-PQ-103- Vigor Shipunloader (26-6-2025)
 */
return [
    'key'                 => '4.2025-PQ-103-cemflexx-service-trip',
    'source_quote_number' => '4 - 2025 - PQ - 103 - CemflexX - Service Trip - Vigor Shipunloader',
    'aliases'             => [
        '4 - 2025 - PQ - 103 - CemflexX - Service Trip - Vigor Shipunloader',
        '2025-PQ-103- Vigor Shipunloader',
        '2025-PQ-103-Vigor Shipunloader',
        '2025-PQ-103',
        '4.2025-PQ-103 - CemflexX - Service Trip - Vigor Shipunloader',
    ],
    'customer_company'    => 'CemFlexX B.V.',
    'force_status'        => 3,
    'populate_tracker'    => true,
    'save_option'         => 'otmain_seed_proposal_4_2025_pq_103_cemflexx_service_trip',
    'tracker_updates'     => [
        0 => ['item_status' => 'pending', 'notes' => '', 'admin_notes' => ''],
    ],
    'proposal' => [
        'subject'              => 'Vigor Shipunloader Levering en Ondersteuning - Quotation',
        'date'                 => '2025-06-26',
        'open_till'            => '2025-06-29',
        'status'               => 3,
        'rel_type'             => 'customer',
        'client_ref'           => 'Service Trip - Vigor Shipunloader',
        'quote_title'          => 'PQ-103',
        'document_title'       => 'Quotation',
        'terms'                => '',
        'expiry_days'          => 3,
        'availability'         => '',
        'notes'                => "Tarieven voor ekstra werkzaamheden:\n• Extra werkdag: €400,- per dag\n• Werk tijdens wachtdag per dagdeel: 30% van dagtarief = €120,- per dagdeel",
        'contact_person_name'  => 'S.A.Ibrahim',
        'contact_person_email' => 'sales@otmain.com',
        'contact_person_phone' => '+31618228651',
        'payment_terms_text'   => '100% binnen 30 dagen na oplevering van het systeem',
        'shipment_terms'       => '',
        'delivery_time'        => '5 werkdagen',
        'total_usd_display'    => '',
        'total_gold_display'   => '',
        'subtotal'             => 5431.00,
        'total_tax'            => 1140.51,
        'total'                => 6571.51,
        'discount_type'        => '',
        'discount_percent'     => 0,
        'discount_total'       => 0,
        'show_quantity_as'     => 1,
        'allow_comments'       => 1,
        'newitems'             => [
            1 => [
                'description'      => 'Vigor Shipunloader – Levering en Ondersteuning',
                'long_description' => "• Assistentie en monitoring tijdens losoperaties\n• Fijnregeling en optimalisatie van het systeem\nAantal werkdagen: 5\nHet pakket omvat: arbeidskosten, reisdocumenten, maaltijden, lucht en grondtransport en logies en overige verblijfskosten",
                'qty'              => 1,
                'rate'             => 5431.00,
                'unit'             => '',
                'taxname'          => ['VAT|21'],
                'order'            => 1,
            ],
        ],
    ],
];
