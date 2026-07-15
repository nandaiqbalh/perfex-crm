<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Purchase Order — Nanjing Deers Industrial Co., Ltd
 * Source: 2026-PO-104 / Supplier Quote Q2026011607928011
 * Total USD 3.281,60 (VAT 0%)
 */
return [
    'key'              => 'po_104_nanjing_expansion',
    'source_po_number' => '2026-PO-104',
    'number'           => 104,
    'currency_code'    => 'USD',
    'supplier_company' => 'Nanjing Deers Industrial Co., Ltd',
    'save_option'      => 'otmain_seed_po_104_nanjing_expansion',
    'purchase_order'   => [
        'document_title'     => 'Purchase Order',
        'date'               => '2026-01-29',
        'supplier_quote_ref' => 'Q2026011607928011',
        'contact_person'     => "Sha'Iez Ibrahim",
        'email'              => 's.ibrahim@otmain.com',
        'phone'              => '+31618228651',
        'adminnote'          => '',
        'items'              => [
            [
                'description' => "NDEJ400\n"
                    . "Ceramic expansion joint:\n"
                    . "Size:ID400*L340mm.\n"
                    . "Working pressure:-0.8~10bar.\n"
                    . "Bursting pressure:30bar.\n"
                    . "Flange dimension: OD596.9*PCD540mm, 16holes*φ28mm.\n"
                    . 'One fixed flange and one loose flange',
                'qty'         => 2,
                'unit_price'  => 850.80,
                'taxrate'     => 0,
                'taxname'     => '',
            ],
            [
                'description' => "NDEJ350\n"
                    . "Ceramic expansion joint:\n"
                    . "Size:ID350*L340mm.\n"
                    . "Working pressure:-0.8~10bar.\n"
                    . "Bursting pressure:30bar.\n"
                    . "Flange dimension: OD533*PCD476mm, 12holes*φ28mm.\n"
                    . 'One fixed flange and one loose flange.',
                'qty'         => 2,
                'unit_price'  => 790.00,
                'taxrate'     => 0,
                'taxname'     => '',
            ],
        ],
    ],
];
