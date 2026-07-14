<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Production packing list — Suriname Shiphandling & Services NV (Henna-T)
 * Source PDF: Packing List & Invoice referencing 4 - 2026 - OTMSQ - 104
 * Partial shipment: VEGABAR 28 pressure transmitters + VEGAPULS 21 radar sensors.
 * Vessel: Henna-T.
 */
return [
    'key'                   => 'tp_suriname_henna_t_sensors',
    'customer_company'      => 'Suriname Shiphandling & Services NV',
    'related_proposal_keys' => [
        '4 - 2026 - OTMSQ - 104',
    ],
    'save_option'           => 'otmain_seed_packing_suriname_henna_t_sensors',
    'packing' => [
        'document_title'       => 'Packing List & Invoice',
        'date'                 => '2026-02-01',
        'vessel'               => 'Henna-T',
        // contact PDF — ensure di tblcontacts (tanpa replace)
        'contact_person_name'  => 'Jerrel',
        'contact_person_email' => 'jerrel@rudisa.net',
        'contact_person_phone' => '+5978532726',
        'adminnote'            => 'No re-export to Russia or Belarus in accordance with Article 12G of EU Regulation 833/2014.',
    ],
    'items' => [
        [
            'description'   => 'VEGABAR 28 - Pressure Transmitter, 4 .... 20 mA, Process Connection: G1/2',
            'hs_code'       => '',
            'qty'           => 4,
            'unit_price'    => 389.84,
            'taxrate'       => 0,
            'packing_qty'   => 1,
            'unit_type'     => 'other',
            'unit_label'    => 'Box',
            'length'        => 585,   // 58.5 cm = 585 mm
            'width'         => 395,   // 39.5 cm = 395 mm
            'height'        => 260,   // 26 cm = 260 mm
            'gross_weight'  => 4.3,
            'net_weight'    => 4.3,
        ],
        [
            'description'   => 'VEGAPULS 21 - Radar sensor for Cargo hold, Process Connection: G1 1/2',
            'hs_code'       => '',
            'qty'           => 2,
            'unit_price'    => 945.00,
            'taxrate'       => 0,
            // Dims/weight already on item 1 (shared box)
        ],
    ],
];
