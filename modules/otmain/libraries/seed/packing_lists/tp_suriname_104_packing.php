<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Production packing list — Suriname Shiphandling & Services NV
 * Source PDF: Packing List & Invoice referencing 2 quotes (104, 111)
 * 2 commercial items + 1 packing dimension row (1 box).
 */
return [
    'key'                   => 'tp_suriname_104_packing',
    'customer_company'      => 'Suriname Shiphandling & Services NV',
    'related_proposal_keys' => [
        '4 - 2026 - OTMSQ - 104',
        '11 - 2026 - OTMSQ - 111',
    ],
    'save_option'           => 'otmain_seed_packing_suriname_104',
    'packing' => [
        'document_title'       => 'Packing List & Invoice',
        'date'                 => '',
        'vessel'               => '',
        // consignee/purchaser dari tblclients; contact PDF tidak jelas — biar orchestrator pakai primary contact
        'contact_person_name'  => '',
        'contact_person_email' => '',
        'contact_person_phone' => '',
        // PDF: Subtotal EUR 5.246,40 / Subtotal USD 5.940,00
        'subtotal_usd'         => 5940.00,
        'conversion_rate'      => 1.132145, // 5940 / 5246.40
        'adminnote'            => 'No re-export to Russia or Belarus in accordance with Article 12G of EU Regulation 833/2014.',
    ],
    'items' => [
        [
            'description'   => 'Siemens Softstrater for Aerzen Compressor',
            'hs_code'       => '',
            'qty'           => 1,
            'unit_price'    => 4077.70,
            'taxrate'       => 0,
            'packing_qty'   => 1,
            'unit_type'     => 'other',
            'unit_label'    => 'Box',
            'length'        => 580,   // 58 cm = 580 mm
            'width'         => 385,   // 38.5 cm = 385 mm
            'height'        => 370,   // 37 cm = 370 mm
            'gross_weight'  => 15,
            'net_weight'    => 14,
        ],
        [
            'description'   => 'ABB Contactor 4 polig AF190-40',
            'hs_code'       => '',
            'qty'           => 1,
            'unit_price'    => 1168.70,
            'taxrate'       => 0,
        ],
    ],
];
