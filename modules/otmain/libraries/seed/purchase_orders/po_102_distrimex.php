<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Purchase Order — Distrimex Pompen & Service BV
 * Source: 2026-PO-102 / Supplier Quote 20115896
 * Subtotal €298,67 + VAT 21% €62,72 → total €361,39
 */
return [
    'key'              => 'po_102_distrimex',
    'source_po_number' => '2026-PO-102',
    'number'           => 102,
    'currency_code'    => 'EUR',
    'supplier_company' => 'Distrimex Pompen & Service BV',
    'save_option'      => 'otmain_seed_po_102_distrimex',
    'purchase_order'   => [
        'document_title'     => 'Purchase Order',
        'date'               => '2026-01-29',
        'supplier_quote_ref' => '20115896',
        'contact_person'     => "Sha'Iez Ibrahim",
        'email'              => 's.ibrahim@otmain.com',
        'phone'              => '+31618228651',
        'adminnote'          => '',
        'items'              => [
            [
                'description' => "Calpeda Centrifugaalpomp open waaier Type: C 16/1E\n"
                    . "Vermogen: 0,15 kW\n"
                    . "Spanning: 230/400V\n"
                    . "Frequentie: 50 Hz\n"
                    . "Toerental: 2900 rpm\n"
                    . "Beschermingklasse: IP54\n"
                    . "Materiaal Pomphuis: Gietijzer\n"
                    . "Materiaal Waaier: Messing\n"
                    . "Seal: Kool/Keramiek/NBR\n"
                    . 'Artikel Nummer: CA70630020000',
                'qty'         => 1,
                'unit_price'  => 298.67,
                'taxrate'     => 21,
                'taxname'     => 'VAT',
            ],
        ],
    ],
];
