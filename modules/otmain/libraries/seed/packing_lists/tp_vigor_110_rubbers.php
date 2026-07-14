<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Production packing list — TP Company Limited (Vigor Cement Shipunloader)
 * Source PDF: Packing List & Invoice referencing 10 - 2026 - OTMSQ - 110
 * Partial shipment: SET RUBBERS (4 PCS) For Coupling — 3 units.
 */
return [
    'key'                   => 'tp_vigor_110_rubbers',
    'customer_company'      => 'TP Company Limited',
    'related_proposal_keys' => [
        '10 - 2026 - OTMSQ - 110',
    ],
    'save_option'           => 'otmain_seed_packing_vigor_110_rubbers',
    'packing' => [
        'document_title'       => 'Packing List & Invoice',
        'date'                 => '',
        'vessel'               => 'Vigor Cement Shipunloader',
        // contact PDF — ensure di tblcontacts (tanpa replace)
        'contact_person_name'  => 'Dheeraj',
        'contact_person_email' => 'dheeraj@luckycement.co.tz',
        'contact_person_phone' => '+255 777 093 955',
        'adminnote'            => '',
    ],
    'items' => [
        [
            'description'   => 'SET RUBBERS (4 PCS) For Coupling - 1619646704',
            'hs_code'       => '',
            'qty'           => 3,
            'unit_price'    => 1130.00,
            'taxrate'       => 0,
            // Shared box
            'packing_qty'   => 1,
            'unit_type'     => 'other',
            'unit_label'    => 'Box',
            'length'        => 300,   // 30 cm = 300 mm
            'width'         => 200,   // 20 cm = 200 mm
            'height'        => 160,   // 16 cm = 160 mm
            'gross_weight'  => 3.6,
            'net_weight'    => 3,
        ],
    ],
];
