<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Production quotation — TP Company Limited
 * Source PDF: 13 - 2026 - OTMSQ - 113 - Spare Part Slew - Vigor Shipunloader (15-04-2026)
 *
 * Spare Parts - Vigor Cement Ship Unloader:
 * Nord-Lock washers, custom suction hose DN400, routing drawing (FOC).
 */
return [
    'key'                 => 'tp_otmsq_113',
    'source_quote_number' => '13 - 2026 - OTMSQ - 113 - Spare Part Slew - Vigor Shipunloader',
    'customer_company'    => 'TP Company Limited',
    'force_status'        => 3, // Accepted
    'populate_tracker'    => true,
    'save_option'         => 'otmain_seed_proposal_tp_otmsq_113',
    'tracker_updates'     => array_fill(0, 4, ['item_status' => 'pending', 'notes' => '', 'admin_notes' => '']),
    'proposal' => [
        'subject'              => 'Spare Parts - Vigor Cement Ship Unloader - Quotation',
        'date'                 => '2026-04-15',
        'open_till'            => '2026-04-14',
        'status'               => 3,
        'rel_type'             => 'customer',
        'client_ref'           => 'Spare Parts',
        'quote_title'          => 'Spare Part Slew - Vigor Shipunloader',
        'document_title'       => 'Quotation',
        'terms'                => '',
        'expiry_days'          => 0,
        'availability'         => '',
        'notes'                => '',
        // PDF deal contact (optional override)
        'contact_person_name'  => 'S.A.Ibrahim',
        'contact_person_email' => 's.ibrahim@otmain.com',
        'contact_person_phone' => '+31618228651',
        'payment_terms_text'   => 'To be agreed.',
        'shipment_terms'       => 'EXW (Ex Works)',
        'delivery_time'        => 'To be agreed.',
        'total_usd_display'    => '$ 9.569,14',
        'total_gold_display'   => '',
        'subtotal'             => 8114.65,
        'total_tax'            => 0,
        'total'                => 8114.65,
        'discount_type'        => '',
        'discount_percent'     => 0,
        'discount_total'       => 0,
        'show_quantity_as'     => 1,
        'allow_comments'       => 1,
        'newitems'             => [
            1 => [
                'description'      => 'NORD-LOCK DELTA PL - M12',
                'long_description' => 'One box consist of 100 pieces

Spare Parts - Vigor Cement Ship Unloader',
                'qty'              => 1,
                'rate'             => 144.75,
                'unit'             => 'box',
                'taxname'          => [],
                'order'            => 1,
            ],
            2 => [
                'description'      => 'NORD-LOCK DELTA PL - M14',
                'long_description' => 'One box consist of 100 pieces

Spare Parts - Vigor Cement Ship Unloader',
                'qty'              => 1,
                'rate'             => 189.90,
                'unit'             => 'box',
                'taxname'          => [],
                'order'            => 2,
            ],
            3 => [
                'description'      => 'Custom Made Suction hose DN400 x L3000mm',
                'long_description' => 'Working pressure: -1 / 10bar
Bursting pressure: 10bar
With one side fixed flange, and one loose flange
Vacuum Reinforced with Vacuum rings
Both side flange are antirust, Bending Angle 90 degree

Spare Parts - Vigor Cement Ship Unloader',
                'qty'              => 2,
                'rate'             => 3890,
                'unit'             => 'unit',
                'taxname'          => [],
                'order'            => 3,
            ],
            4 => [
                'description'      => 'Suction Arm Pipe and Hose Routing Drawing',
                'long_description' => 'Free of Charge

Spare Parts - Vigor Cement Ship Unloader',
                'qty'              => 1,
                'rate'             => 0,
                'unit'             => 'unit',
                'taxname'          => [],
                'order'            => 4,
            ],
        ],
    ],
];
