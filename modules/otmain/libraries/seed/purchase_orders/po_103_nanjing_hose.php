<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Purchase Order — Nanjing Deers Industrial Co., Ltd
 * Source: 2026-PO-103 / Supplier Quote Q2026011507923011
 * Total USD 10.169,20 (VAT 0%)
 */
return [
    'key'              => 'po_103_nanjing_hose',
    'source_po_number' => '2026-PO-103',
    'number'           => 103,
    'currency_code'    => 'USD',
    'supplier_company' => 'Nanjing Deers Industrial Co., Ltd',
    'save_option'      => 'otmain_seed_po_103_nanjing_hose',
    'purchase_order'   => [
        'document_title'     => 'Purchase Order',
        'date'               => '2026-01-29',
        'supplier_quote_ref' => 'Q2026011507923011',
        'contact_person'     => "Sha'Iez Ibrahim",
        'email'              => 's.ibrahim@otmain.com',
        'phone'              => '+31618228651',
        'adminnote'          => '',
        'items'              => [
            [
                'description' => "NDSH400\n"
                    . "Suction rubber hose:\n"
                    . "Size: ID400*L5500mm.\n"
                    . "Working pressure:-0.8~10bar.\n"
                    . "Bursting pressure:10bar.\n"
                    . "Wear lining:15mm.\n"
                    . 'With one fixed flange, and one loose flange',
                'qty'         => 4,
                'unit_price'  => 2234.80,
                'taxrate'     => 0,
                'taxname'     => '',
            ],
            [
                'description' => 'Sea Freight',
                'qty'         => 1,
                'unit_price'  => 1200.00,
                'taxrate'     => 0,
                'taxname'     => '',
            ],
            [
                'description' => 'Insurance',
                'qty'         => 1,
                'unit_price'  => 30.00,
                'taxrate'     => 0,
                'taxname'     => '',
            ],
        ],
    ],
];
