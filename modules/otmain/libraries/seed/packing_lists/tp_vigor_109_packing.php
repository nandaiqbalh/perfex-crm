<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Production packing list — TP Company Limited (Vigor Cement Shipunloader)
 * Source PDF: Packing List & Invoice referencing 9 - 2026 - OTMSQ - 109
 * and 10 - 2026 - OTMSQ - 110 (not yet seeded).
 * 7 commercial items: compressor element + buffers / O-rings / bearings.
 */
return [
    'key'                   => 'tp_vigor_109_packing',
    'customer_company'      => 'TP Company Limited',
    'related_proposal_keys' => [
        '9 - 2026 - OTMSQ - 109',
        '10 - 2026 - OTMSQ - 110',
    ],
    'save_option'           => 'otmain_seed_packing_vigor_109',
    'packing' => [
        'document_title'       => 'Packing List & Invoice',
        'date'                 => '',
        'vessel'               => 'Vigor Cement Shipunloader',
        // contact PDF — ensure di tblcontacts (tanpa replace)
        'contact_person_name'  => 'Dheeraj',
        'contact_person_email' => 'dheeraj@luckycement.co.tz',
        'contact_person_phone' => '+255 777 093 955',
        // No explicit USD in PDF
        'adminnote'            => '',
    ],
    'items' => [
        [
            'description'   => 'O.F.S. Q-21 elements - 1616635681',
            'hs_code'       => '',
            'qty'           => 1,
            'unit_price'    => 83200.00,
            'taxrate'       => 0,
            // Shared crate — all dims on first item
            'packing_qty'   => 1,
            'unit_type'     => 'other',
            'unit_label'    => 'Crate',
            'length'        => 1300,   // 130 cm = 1300 mm
            'width'         => 790,    // 79 cm = 790 mm
            'height'        => 950,    // 95 cm = 950 mm
            'gross_weight'  => 1021,
            'net_weight'    => 1000,
        ],
        [
            'description'   => 'BUFFER - 1612558400 =1830004330',
            'hs_code'       => '',
            'qty'           => 12,
            'unit_price'    => 127.00,
            'taxrate'       => 0,
        ],
        [
            'description'   => 'O-Ring - 0663210808',
            'hs_code'       => '',
            'qty'           => 2,
            'unit_price'    => 9.00,
            'taxrate'       => 0,
        ],
        [
            'description'   => 'O-Ring - 0663210326',
            'hs_code'       => '',
            'qty'           => 1,
            'unit_price'    => 165.77,
            'taxrate'       => 0,
        ],
        [
            'description'   => 'O-Ring - 0663210858',
            'hs_code'       => '',
            'qty'           => 1,
            'unit_price'    => 15.73,
            'taxrate'       => 0,
        ],
        [
            'description'   => 'BEARING - BA1 7018 A AUSTRIA',
            'hs_code'       => '',
            'qty'           => 1,
            'unit_price'    => 1890.00,
            'taxrate'       => 0,
        ],
        [
            'description'   => 'BEARING - BA1 7018 B AUSTRIA',
            'hs_code'       => '',
            'qty'           => 1,
            'unit_price'    => 1890.00,
            'taxrate'       => 0,
        ],
    ],
];
