<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Production quotation — TP Company Limited
 * Source PDF: 14 - 2026 - OTMSQ - 114 - Spare Part - Vigor Shipunloader (19-06-2026)
 *
 * Hydraulics + suction hoses + rotating suction nozzle - Vigor Cement Ship Unloader.
 */
return [
    'key'                 => 'tp_otmsq_114',
    'source_quote_number' => '14 - 2026 - OTMSQ - 114 - Spare Part - Vigor Shipunloader',
    'customer_company'    => 'TP Company Limited',
    'force_status'        => 3, // Accepted
    'populate_tracker'    => true,
    'save_option'         => 'otmain_seed_proposal_tp_otmsq_114',
    'tracker_updates'     => array_fill(0, 8, ['item_status' => 'pending', 'notes' => '', 'admin_notes' => '']),
    'proposal' => [
        'subject'              => 'Hydraulics + Suction Hoses + Rotating Nozzle - Vigor - Quotation',
        'date'                 => '2026-06-19',
        'open_till'            => '2026-07-18',
        'status'               => 3,
        'rel_type'             => 'customer',
        'client_ref'           => 'Spare Parts',
        'quote_title'          => 'Spare Part - Vigor Shipunloader',
        'document_title'       => 'Quotation',
        'terms'                => '',
        'expiry_days'          => 29,
        'availability'         => '',
        'notes'                => '',
        // PDF deal contact (optional override)
        'contact_person_name'  => 'S.A.Ibrahim',
        'contact_person_email' => 's.ibrahim@otmain.com',
        'contact_person_phone' => '+316****8651',
        'payment_terms_text'   => '50% in advance<br />50% before delivery',
        'shipment_terms'       => 'EXW (Ex Works)',
        'delivery_time'        => 'To be agreed.',
        'total_usd_display'    => '$ 32.713,88',
        'total_gold_display'   => '',
        'subtotal'             => 27716,
        'total_tax'            => 0,
        'total'                => 27716,
        'discount_type'        => '',
        'discount_percent'     => 0,
        'discount_total'       => 0,
        'show_quantity_as'     => 1,
        'allow_comments'       => 1,
        'newitems'             => [
            1 => [
                'description'      => 'V21-180 - First Boom Cylinder (one piece)',
                'long_description' => 'Hydraulics - Vigor Cement Ship Unloader',
                'qty'              => 1,
                'rate'             => 6748,
                'unit'             => 'unit',
                'taxname'          => [],
                'order'            => 1,
            ],
            2 => [
                'description'      => 'V21-160 - Second Boom Cylinder (one piece)',
                'long_description' => 'Hydraulics - Vigor Cement Ship Unloader',
                'qty'              => 1,
                'rate'             => 6531,
                'unit'             => 'unit',
                'taxname'          => [],
                'order'            => 2,
            ],
            3 => [
                'description'      => 'V21-100 - Third Boom Cylinder (one piece)',
                'long_description' => 'Hydraulics - Vigor Cement Ship Unloader',
                'qty'              => 1,
                'rate'             => 2632,
                'unit'             => 'unit',
                'taxname'          => [],
                'order'            => 3,
            ],
            4 => [
                'description'      => 'Custom Made Suction hose DN400 x L5500mm',
                'long_description' => 'Working pressure: -1 / 10bar
Bursting pressure: 10bar
With one side fixed flange, and one loose flange
Vacuum Reinforced with Vacuum rings
Both side flange are antirust, Bending Angle 90 degree',
                'qty'              => 1,
                'rate'             => 4300,
                'unit'             => 'unit',
                'taxname'          => [],
                'order'            => 4,
            ],
            5 => [
                'description'      => 'Custom Made Suction hose DN400 x L4000mm',
                'long_description' => 'Working pressure: -1 / 10bar
Bursting pressure: 10bar
With one side fixed flange, and one loose flange
Vacuum Reinforced with Vacuum rings
Both side flange are antirust, Bending Angle 90 degree',
                'qty'              => 1,
                'rate'             => 3890,
                'unit'             => 'unit',
                'taxname'          => [],
                'order'            => 5,
            ],
            6 => [
                'description'      => 'Rotating Suction Nozzle Customized Slew drive Including Danfoss Hydraulic motor',
                'long_description' => '',
                'qty'              => 1,
                'rate'             => 3615,
                'unit'             => 'unit',
                'taxname'          => [],
                'order'            => 6,
            ],
            7 => [
                'description'      => 'Free of Charge - Custom Made Suction hose DN400 x L4000mm',
                'long_description' => 'Working pressure: -1 / 10bar
Bursting pressure: 10bar
With one side fixed flange, and one loose flange
Vacuum Reinforced with Vacuum rings
Both side flange are antirust, Bending Angle 90 degree

Free of Charge',
                'qty'              => 1,
                'rate'             => 0,
                'unit'             => 'unit',
                'taxname'          => [],
                'order'            => 7,
            ],
            8 => [
                'description'      => 'Free of Charge - Suction Arm Pipe and Hose Routing Drawing',
                'long_description' => 'Free of Charge',
                'qty'              => 1,
                'rate'             => 0,
                'unit'             => 'unit',
                'taxname'          => [],
                'order'            => 8,
            ],
        ],
    ],
];
