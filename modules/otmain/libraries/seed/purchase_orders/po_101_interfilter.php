<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Purchase Order — Interfilter Industries B.V
 * Source: 2026-PO-101 / Supplier Quote OF25011724
 * Subtotal €114,35 + VAT 21% on adsorber only → total €134,16
 */
return [
    'key'              => 'po_101_interfilter',
    'source_po_number' => '2026-PO-101',
    'number'           => 101,
    'currency_code'    => 'EUR',
    'supplier_company' => 'Interfilter Industries B.V',
    'save_option'      => 'otmain_seed_po_101_interfilter',
    'purchase_order'   => [
        'document_title'     => 'Purchase Order',
        'date'               => '2026-01-29',
        'supplier_quote_ref' => 'OF25011724',
        'contact_person'     => "Sha'Iez Ibrahim",
        'email'              => 's.ibrahim@otmain.com',
        'phone'              => '+31618228651',
        'adminnote'          => '',
        'items'              => [
            [
                'description' => "F Adsorber VV-D 3S G1''m (Article Nummer: 256460 )",
                'qty'         => 1,
                'unit_price'  => 94.35,
                'taxrate'     => 21,
                'taxname'     => 'VAT',
            ],
            [
                'description' => 'Transportkosten',
                'qty'         => 1,
                'unit_price'  => 20.00,
                'taxrate'     => 0,
                'taxname'     => '',
            ],
        ],
    ],
];
