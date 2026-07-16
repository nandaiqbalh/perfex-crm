<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Production quotation — OT-Main
 * Source PDF: 7 - 2026 - OTMPQ - 107 - MB Melita - Malta (13-02-2026)
 *
 * Complete System Upgrade – MV MB Melita.
 * Package scope + spare parts + optional pneumatic valves.
 * Quotation to: MB Melita (not OT-Main seller). Address / contact dari tblclients + seed contact.
 */
return [
    'key'                 => 'tp_otmpq_107',
    'source_quote_number' => '7 - 2026 - OTMPQ - 107 - MB Melita - Malta',
    'customer_company'    => 'MB Melita',
    'force_status'        => 3, // Accepted
    'populate_tracker'    => true,
    'save_option'         => 'otmain_seed_proposal_tp_otmpq_107',
    'tracker_updates'     => array_fill(0, 22, ['item_status' => 'pending', 'notes' => '', 'admin_notes' => '']),
    'proposal' => [
        'subject'              => 'Complete System Upgrade – MV MB Melita - Quotation',
        'date'                 => '2026-02-13',
        'open_till'            => '2026-03-12',
        'status'               => 3,
        'rel_type'             => 'customer',
        'client_ref'           => 'Upgrade MB Melita',
        'quote_title'          => 'MB Melita - Malta',
        'document_title'       => 'Quotation',
        'terms'                => '',
        'expiry_days'          => 27,
        'availability'         => '',
        'notes'                => 'The following items are excluded from the base scope of supply of the \'Complete System Upgrade – MV MB Melita Package\':
Fittings and hoses
Bolting materials
Piping / pipeline
Electrical cables',
        // PDF deal contact (optional override)
        'contact_person_name'  => 'S.A.Ibrahim',
        'contact_person_email' => 's.ibrahim@otmain.com',
        'contact_person_phone' => '+31618228651',
        'payment_terms_text'   => 'To be agreed upon order confirmation.',
        'shipment_terms'       => 'EXW (Ex Works – Incoterms 2020). Shipping arrangements can be organized upon request at additional cost.',
        'delivery_time'        => 'Delivery time depends on final order confirmation and scope.',
        'total_usd_display'    => '$ 102.535,33',
        'total_gold_display'   => '',
        'subtotal'             => 84732.50,
        'total_tax'            => 0,
        'total'                => 84732.50,
        'discount_type'        => '',
        'discount_percent'     => 0,
        'discount_total'       => 0,
        'show_quantity_as'     => 1,
        'allow_comments'       => 1,
        'newitems'             => [
            1 => [
                'description'      => 'Complete System Upgrade – MV MB Melita',
                'long_description' => 'Scope of Supply:

Service Air System
1x Automatic Condensate Drain – Service Air
1x Pressure Transmitter – Service Air

Pneumatic Valve System
2x OTPX VEX-80 - Compressed Pneumatic Air Valves, Included actuator
2x DN80 Non-Return Valve
2x Pressure Transmitter – Discharge Line
4x Pressure Regulator – Suction Nozzle
1x OTPX V-80 (Regulator, Actuator and Valve) – Discharge Line
2x OTPX V-80 Manual Operated Valve
2x OTPX VEX-100 Pneumatic Inflatable Valve, including actuator
2x OTPX VEX-50 Pneumatic Inflatable Valve, including actuator
2x OTPX VEX-50 - including actuator

Level & Cargo Monitoring
2x Level Sensor – Batch Tank
4x Radar Sensor – Cargo Hold

Control System
Additional PLC I/O Cards
Software Modifications including new system layout

Documentation & Maintenance
Maintenance Plan Sheets (Daily, Weekly, Monthly, Yearly)

Service Attendance
Including 3 days on-site service visit (Extra days will be additional charge)

* For additional information, please refer to the notes section below.',
                'qty'              => 1,
                'rate'             => 34500,
                'unit'             => 'unit',
                'taxname'          => [],
                'order'            => 1,
            ],
            2 => [
                'description'      => 'Maintenance kit including gear kit - Vacuum Pump',
                'long_description' => 'Spare Parts & Maintenance Components',
                'qty'              => 2,
                'rate'             => 4690,
                'unit'             => 'unit',
                'taxname'          => [],
                'order'            => 2,
            ],
            3 => [
                'description'      => 'Vacuum pump (without motor)',
                'long_description' => 'Spare Parts & Maintenance Components',
                'qty'              => 1,
                'rate'             => 13454,
                'unit'             => 'unit',
                'taxname'          => [],
                'order'            => 3,
            ],
            4 => [
                'description'      => 'Maintenance kit including gear kit - Blower',
                'long_description' => 'Spare Parts & Maintenance Components',
                'qty'              => 1,
                'rate'             => 4200,
                'unit'             => 'unit',
                'taxname'          => [],
                'order'            => 4,
            ],
            5 => [
                'description'      => 'RD100 Filters - Batchtank (Change every 6 months)',
                'long_description' => 'Spare Parts & Maintenance Components',
                'qty'              => 60,
                'rate'             => 81.67,
                'unit'             => 'unit',
                'taxname'          => [],
                'order'            => 5,
            ],
            6 => [
                'description'      => 'Warex DN80 Valve Seal - EPDM (existing)',
                'long_description' => 'Spare Parts & Maintenance Components',
                'qty'              => 1,
                'rate'             => 52.50,
                'unit'             => 'unit',
                'taxname'          => [],
                'order'            => 6,
            ],
            7 => [
                'description'      => 'Warex DN80 Valve Seal - Silicon (existing)',
                'long_description' => 'Spare Parts & Maintenance Components',
                'qty'              => 1,
                'rate'             => 133.75,
                'unit'             => 'unit',
                'taxname'          => [],
                'order'            => 7,
            ],
            8 => [
                'description'      => 'Warex DN125 Valve Seal - Silicon (existing)',
                'long_description' => 'Spare Parts & Maintenance Components',
                'qty'              => 1,
                'rate'             => 263.50,
                'unit'             => 'unit',
                'taxname'          => [],
                'order'            => 8,
            ],
            9 => [
                'description'      => 'Warex DN150 Valve Seal - EPDM (existing)',
                'long_description' => 'Spare Parts & Maintenance Components',
                'qty'              => 1,
                'rate'             => 108.75,
                'unit'             => 'unit',
                'taxname'          => [],
                'order'            => 9,
            ],
            10 => [
                'description'      => 'Warex DN200 Valve Seal - EPDM (existing)',
                'long_description' => 'Spare Parts & Maintenance Components',
                'qty'              => 1,
                'rate'             => 181.25,
                'unit'             => 'unit',
                'taxname'          => [],
                'order'            => 10,
            ],
            11 => [
                'description'      => 'Warex DN250 Valve Seal - Silicon (existing)',
                'long_description' => 'Spare Parts & Maintenance Components',
                'qty'              => 1,
                'rate'             => 562.50,
                'unit'             => 'unit',
                'taxname'          => [],
                'order'            => 11,
            ],
            12 => [
                'description'      => 'Warex DN300 Valve Seal - Silicon (existing)',
                'long_description' => 'Spare Parts & Maintenance Components',
                'qty'              => 1,
                'rate'             => 615,
                'unit'             => 'unit',
                'taxname'          => [],
                'order'            => 12,
            ],
            13 => [
                'description'      => 'Warex DN350 Valve Seal - Silicon (existing)',
                'long_description' => 'Spare Parts & Maintenance Components',
                'qty'              => 1,
                'rate'             => 716.25,
                'unit'             => 'unit',
                'taxname'          => [],
                'order'            => 13,
            ],
            14 => [
                'description'      => 'DN350 – OTPX VEX-350 Pneumatic Valve Expansion, including actuator – Abrasive',
                'long_description' => 'Optional Upgrade Valves',
                'qty'              => 4,
                'rate'             => 2200,
                'unit'             => 'unit',
                'taxname'          => [],
                'order'            => 14,
            ],
            15 => [
                'description'      => 'Spare - VEX-350 - Valve Seal - Abrasive',
                'long_description' => 'Optional Upgrade Valves',
                'qty'              => 1,
                'rate'             => 920,
                'unit'             => 'unit',
                'taxname'          => [],
                'order'            => 15,
            ],
            16 => [
                'description'      => 'DN300 – OTPX VEX-300 Pneumatic Valve Expansion, including actuator – Abrasive',
                'long_description' => 'Optional Upgrade Valves',
                'qty'              => 2,
                'rate'             => 1650,
                'unit'             => 'unit',
                'taxname'          => [],
                'order'            => 16,
            ],
            17 => [
                'description'      => 'Spare - VEX-300 - Valve Seal - Abrasive',
                'long_description' => 'Optional Upgrade Valves',
                'qty'              => 1,
                'rate'             => 560,
                'unit'             => 'unit',
                'taxname'          => [],
                'order'            => 17,
            ],
            18 => [
                'description'      => 'DN250 – OTPX VEX-250 Pneumatic Valve Expansion, including actuator',
                'long_description' => 'Optional Upgrade Valves',
                'qty'              => 2,
                'rate'             => 750,
                'unit'             => 'unit',
                'taxname'          => [],
                'order'            => 18,
            ],
            19 => [
                'description'      => 'Spare - VEX-250 - Valve Seal',
                'long_description' => 'Optional Upgrade Valves',
                'qty'              => 1,
                'rate'             => 210,
                'unit'             => 'unit',
                'taxname'          => [],
                'order'            => 19,
            ],
            20 => [
                'description'      => 'Spare - VEX-80 - Valve Seal',
                'long_description' => 'Optional Upgrade Valves',
                'qty'              => 1,
                'rate'             => 120,
                'unit'             => 'unit',
                'taxname'          => [],
                'order'            => 20,
            ],
            21 => [
                'description'      => 'Spare - VEX-100 - Valve Seal',
                'long_description' => 'Optional Upgrade Valves',
                'qty'              => 1,
                'rate'             => 150,
                'unit'             => 'unit',
                'taxname'          => [],
                'order'            => 21,
            ],
            22 => [
                'description'      => 'Spare - VEX-50 - Valve Seal',
                'long_description' => 'Optional Upgrade Valves',
                'qty'              => 1,
                'rate'             => 105,
                'unit'             => 'unit',
                'taxname'          => [],
                'order'            => 22,
            ],
        ],
    ],
];
