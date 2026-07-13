<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Production packing list — TP Company Limited (Vigor Shipunloader - Valves)
 * Source PDF: Packing List & Invoice referencing 2 - 2026 - OTMPQ - 102 - Valves
 * 8 commercial items + 1 shared wooden box (0.8×0.6×0.5 m, GW 280 / NW 224).
 * Packaging dims only on the line that carries the box — other lines are commercial-only.
 */
return [
    'key'                   => 'tp_valves_packing',
    'customer_company'      => 'TP Company Limited',
    'related_proposal_keys' => [
        '2 - 2026 - OTMPQ - 102 - Valves',
    ],
    'save_option'           => 'otmain_seed_packing_valves_id',
    'packing' => [
        'document_title'       => 'Packing List & Invoice',
        'date'                 => '2026-02-15',
        'vessel'               => 'Vigor Shipunloader',
        'contact_person_name'  => 'Dheeraj',
        'contact_person_email' => 'dheeraj@luckycement.co.tz',
        'contact_person_phone' => '+255 777 093 955',
        // PDF: Subtotal EUR 6.017,40 / Subtotal USD 5.940,00 (OCR often glues EUR onto the USD label)
        'subtotal_usd'         => 5940.00,
        'conversion_rate'      => 0.987137, // 5940 / 6017.40
        'adminnote'            => '',
    ],
    'items' => [
        [
            'description'   => 'Valve (without actuator) Size: DN300',
            'hs_code'       => '',
            'qty'           => 2,
            'unit_price'    => 750.00,
            'taxrate'       => 0,
            // Shared wooden box for all valves (PDF packing section QTY 1)
            'packing_qty'   => 1,
            'unit_type'     => 'other',
            'unit_label'    => 'Wooden box',
            'length'        => 800,
            'width'         => 600,
            'height'        => 500,
            'gross_weight'  => 280,
            'net_weight'    => 224,
        ],
        [
            'description' => 'Spare Seat EPDM High Temp. DN300',
            'hs_code'     => '',
            'qty'         => 4,
            'unit_price'  => 80.00,
            'taxrate'     => 0,
        ],
        [
            'description' => 'Valve (without actuator) Size: DN350',
            'hs_code'     => '',
            'qty'         => 2,
            'unit_price'  => 925.00,
            'taxrate'     => 0,
        ],
        [
            'description' => 'Spare Seat EPDM High Temp. DN350',
            'hs_code'     => '',
            'qty'         => 4,
            'unit_price'  => 121.00,
            'taxrate'     => 0,
        ],
        [
            'description' => 'Valve (without actuator) Size: DN200',
            'hs_code'     => '',
            'qty'         => 2,
            'unit_price'  => 370.10,
            'taxrate'     => 0,
        ],
        [
            'description' => 'Spare Seat High Temp. DN200',
            'hs_code'     => '',
            'qty'         => 4,
            'unit_price'  => 80.00,
            'taxrate'     => 0,
        ],
        [
            'description' => 'Valve (without actuator) Size: DN150',
            'hs_code'     => '',
            'qty'         => 2,
            'unit_price'  => 281.60,
            'taxrate'     => 0,
        ],
        [
            'description' => 'Spare Seat High Temp. DN150',
            'hs_code'     => '',
            'qty'         => 4,
            'unit_price'  => 60.00,
            'taxrate'     => 0,
        ],
    ],
];
