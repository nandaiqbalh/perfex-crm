<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Production quotation — Suriname Shiphandling & Services NV
 * Source PDF: 19 - 2026 - OTMSQ - 119 - Spare Parts - Henna-T (28-06-2026)
 *
 * Spare Parts - Henna-T: controller, circuit breaker, smoke/heat detectors, MCP.
 */
return [
    'key'                 => 'tp_otmsq_119',
    'source_quote_number' => '19 - 2026 - OTMSQ - 119 - Spare Parts - Henna-T',
    'customer_company'    => 'Suriname Shiphandling & Services NV',
    'force_status'        => 3, // Accepted
    'populate_tracker'    => true,
    'save_option'         => 'otmain_seed_proposal_tp_otmsq_119',
    'tracker_updates'     => array_fill(0, 7, ['item_status' => 'pending', 'notes' => '', 'admin_notes' => '']),
    'proposal' => [
        'subject'              => 'Spare Parts - Henna-T - Quotation',
        'date'                 => '2026-06-28',
        'open_till'            => '2026-07-19',
        'status'               => 3,
        'rel_type'             => 'customer',
        'client_ref'           => 'Spare Parts Henna-T',
        'quote_title'          => 'Spare Parts - Henna-T',
        'document_title'       => 'Quotation',
        'terms'                => '',
        'expiry_days'          => 21,
        'availability'         => '',
        'notes'                => '',
        // PDF deal contact (optional override)
        'contact_person_name'  => 'S.A.Ibrahim',
        'contact_person_email' => 's.ibrahim@otmain.com',
        'contact_person_phone' => '+316****8651',
        'payment_terms_text'   => '50% advance payment<br />50% payable within 30 days after invoice date',
        'shipment_terms'       => 'EXW (Ex Works)',
        'delivery_time'        => 'All On Stock',
        'total_usd_display'    => '$ 5.893,16',
        'total_gold_display'   => '',
        'subtotal'             => 4986.58,
        'total_tax'            => 0,
        'total'                => 4986.58,
        'discount_type'        => '',
        'discount_percent'     => 0,
        'discount_total'       => 0,
        'show_quantity_as'     => 1,
        'allow_comments'       => 1,
        'newitems'             => [
            1 => [
                'description'      => '8610 MKII Autostart Load Share Controller',
                'long_description' => 'This includes software transfer from the old Controller to the new controller remotely. A laptop on board of the Henna-t with an internet connection is required. We will provide the data cable to connect the laptop.

Spare Parts - Henna-T',
                'qty'              => 1,
                'rate'             => 2497.30,
                'unit'             => 'unit',
                'taxname'          => [],
                'order'            => 1,
            ],
            2 => [
                'description'      => 'Schneider Electric Compact NSX 400-630 F/N/H/NA',
                'long_description' => 'U_i 800V, U_imp 8kV, 50/60Hz

Spare Parts - Henna-T',
                'qty'              => 1,
                'rate'             => 1180,
                'unit'             => 'unit',
                'taxname'          => [],
                'order'            => 2,
            ],
            3 => [
                'description'      => 'DHR70 Duplex all round - Red',
                'long_description' => 'Spare Parts - Henna-T',
                'qty'              => 1,
                'rate'             => 520,
                'unit'             => 'unit',
                'taxname'          => [],
                'order'            => 3,
            ],
            4 => [
                'description'      => 'TYCO 601-P-M Smoke Detector / 516.600.201 / White Marine - Marine Approved',
                'long_description' => 'Detector Type: Optical Smoke Detector (Light Scattering Technology)
Application: Marine, Shipboard & Offshore Installations
Series: 600 Series
Operating Voltage: 10.5 – 33 VDC
Quiescent Current: Less than 65 ÂµA
Alarm Current: Maximum 40 mA (depending on detector base)
Operating Temperature: -20Â°C to +70Â°C
Relative Humidity: Up to 95% RH (non-condensing)

Spare Parts - Henna-T',
                'qty'              => 1,
                'rate'             => 166,
                'unit'             => 'unit',
                'taxname'          => [],
                'order'            => 4,
            ],
            5 => [
                'description'      => 'XP95 Optical Smoke Detector including Mounting plate',
                'long_description' => 'The XP95 Optical Smoke Detector uses the light scatter principle to quickly and effectively detect a range of slow burning and smouldering fires.
- Reliable fire detection in areas with a wide temperature range from -20Â°C to +60Â°C
- Ideal for escape routes
- Unaffected by wind and atmospheric pressure

Spare Parts - Henna-T',
                'qty'              => 1,
                'rate'             => 95,
                'unit'             => 'unit',
                'taxname'          => [],
                'order'            => 5,
            ],
            6 => [
                'description'      => 'Tyco 601H-F-H / 516.600.213 / Heat Detector including mounting base',
                'long_description' => 'Alternative version for Tyco 516.014.019

Tyco 601H-F-M marine conventional fixed temperature heat detectors provide a fast response that depends on the absolute temperature reaching 60 deg C.
Key Features:
- EN54 Approved from LPCB
- Marine approved
- Low operational voltage: 10.5 Vdc to 33 Vdc

Spare Parts - Henna-T',
                'qty'              => 1,
                'rate'             => 294.28,
                'unit'             => 'unit',
                'taxname'          => [],
                'order'            => 6,
            ],
            7 => [
                'description'      => 'Tyco MCP250M Marine with indicator - Alternative type',
                'long_description' => 'Key Features:
- Marine Certified: Approved for use on ships and offshore installations.
- Built-in LED Indicator: Clear visual indication for alarm and fault status.
- High Reliability: Based on proven Tyco Minerva MX technology.
- Easy Installation: Designed for quick mounting and straightforward maintenance.
- Rugged Design: Resistant to vibration and temperature variations in marine environments.

Spare Parts - Henna-T',
                'qty'              => 1,
                'rate'             => 234,
                'unit'             => 'unit',
                'taxname'          => [],
                'order'            => 7,
            ],
        ],
    ],
];
