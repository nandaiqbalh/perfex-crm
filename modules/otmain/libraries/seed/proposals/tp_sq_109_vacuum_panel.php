<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Production quotation — TP Company Limited (2025)
 * Source PDF: 2025-SQ-109- Vigor Shipunloader - Vacuum Pump Electrical Panel (22-11-2025)
 */
return [
    'key'                 => 'tp_sq_109_vacuum_panel',
    'source_quote_number' => '2025-SQ-109- Vigor Shipunloader - Vacuum Pump Electrical Panel',
    'aliases'             => [
        '2025-SQ-109- Vigor Shipunloader - Vacuum Pump Electrical Panel',
        '2025-SQ-109',
        '2025-SQ-109-Vacuum Pump Electrical Panel',
        '2025-SQ-109-Vacuum Pump Electrical Panel',
    ],
    'customer_company'    => 'TP Company Limited',
    'force_status'        => 3,
    'populate_tracker'    => true,
    'save_option'         => 'otmain_seed_proposal_tp_sq_109_vacuum_panel',
    'tracker_updates'     => [
        0 => ['item_status' => 'pending', 'notes' => '', 'admin_notes' => ''],
    ],
    'proposal' => [
        'subject'              => 'Vacuum Pump Electrical Panel MCC3 - Quotation',
        'date'                 => '2025-11-22',
        'open_till'            => '2025-12-20',
        'status'               => 3,
        'rel_type'             => 'customer',
        'client_ref'           => 'Vacuum 3 Electrical Panel Shipunloader',
        'quote_title'          => 'Vacuum Pump Electrical Panel',
        'document_title'       => 'Quotation',
        'terms'                => '',
        'expiry_days'          => 28,
        'availability'         => '',
        'notes'                => 'Once the drawing has been confirmed, no further changes can be made. Any additional items or modifications outside the proposed scope will be charged separately.',
        'contact_person_name'  => 'S.A.Ibrahim',
        'contact_person_email' => 's.ibrahim@otmain.com',
        'contact_person_phone' => '+31618228651',
        'payment_terms_text'   => '50% in advance / 50% before delivery',
        'shipment_terms'       => 'EXW - Rotterdam sea transport can be arranged',
        'delivery_time'        => '4 to 6 weeks',
        'total_usd_display'    => '',
        'total_gold_display'   => '',
        'subtotal'             => 13498.00,
        'total_tax'            => 0,
        'total'                => 13498.00,
        'discount_type'        => '',
        'discount_percent'     => 0,
        'discount_total'       => 0,
        'show_quantity_as'     => 1,
        'allow_comments'       => 1,
        'newitems'             => [
            1 => [
                'description'      => 'Electrical Panel',
                'long_description' => "Vacuum Unit Electrical Panel (MCC 3)\n▸ Includes Electrical diagram limited to MCC3\nTwo revisions Included\n\nMCC 3 functions:\n▸ Main ON/OFF switch for cabinet operation\n▸ Start-up power circuit for the vacuum unit, including I/O indication signals to exciting HMI Panel\n▸ Start-up power circuit for the cooling fan, including I/O indication signals to exciting HMI Panel\n▸ Start-up power circuit for the water pump, including I/O indication signals to exciting HMI Panel\n▸ Start-up power circuit for the cooling unit, including I/O indication signals to exciting HMI Panel\n▸ Cooling fan for Electrical Panel\n▸ Automatic cabinet lighting\n▸ Panel indicators",
                'qty'              => 1,
                'rate'             => 13498.00,
                'unit'             => '',
                'taxname'          => [],
                'order'            => 1,
            ],
        ],
    ],
];
