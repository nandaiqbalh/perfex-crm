<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Purchase Order — Handelsmij SPT b.v.
 * Source PDF: 2026-OTMPO-105 / Supplier Quote R26132-2
 * DN250 line without price skipped. Subtotal €6.112,00 + VAT 21% → €7.395,52
 */
return [
    'key'              => 'po_105_handelsmij_spt',
    'source_po_number' => '2026-OTMPO-105',
    'aliases'          => ['2026-PO-105', '2026-OTMPO-105'],
    'number'           => 105,
    'prefix'           => 'OTMPO-',
    'currency_code'    => 'EUR',
    'supplier_company' => 'Handelsmij SPT b.v.',
    'save_option'      => 'otmain_seed_po_105_handelsmij_spt',
    'purchase_order'   => [
        'document_title'     => 'Purchase Order',
        'date'               => '2026-02-10',
        'supplier_quote_ref' => 'R26132-2',
        'contact_person'     => "Sha'Iez Ibrahim",
        'email'              => 's.ibrahim@otmain.com',
        'phone'              => '+31618228651',
        'adminnote'          => "Leveringscondities:\nLevering: Af Fabriek\nLevertijd: met enkele dagen\nBetaling: Binnen 30 dagen na factuurdatum",
        'items'              => [
            [
                'description' => "W00106916\nManchet voor DKZ 110/103\nDiameter: DN 200\nMateriaal: Siliconen rood",
                'qty'         => 4,
                'unit_price'  => 323.00,
                'taxrate'     => 21,
                'taxname'     => 'VAT',
            ],
            [
                'description' => "W00106848\nManchet voor DKZ 110/103\nDiameter: DN 80\nMateriaal: Siliconen rood",
                'qty'         => 1,
                'unit_price'  => 107.00,
                'taxrate'     => 21,
                'taxname'     => 'VAT',
            ],
            [
                'description' => "W00106967\nManchet voor DKZ 110/103\nDiameter: DN 350\nMateriaal: Siliconen rood",
                'qty'         => 8,
                'unit_price'  => 573.00,
                'taxrate'     => 21,
                'taxname'     => 'VAT',
            ],
            [
                'description' => "Artikelnr. Aanpassen op de Orderbevestiging\nManchet voor DKZ 110/103\nDiameter: DN 80\nMateriaal: EPDM Zwart",
                'qty'         => 1,
                'unit_price'  => 42.00,
                'taxrate'     => 21,
                'taxname'     => 'VAT',
            ],
            [
                'description' => "Artikelnr. Aanpassen op de Orderbevestiging\nManchet voor DKZ 110/103\nDiameter: DN 150\nMateriaal: EPDM Zwart",
                'qty'         => 1,
                'unit_price'  => 87.00,
                'taxrate'     => 21,
                'taxname'     => 'VAT',
            ],
        ],
    ],
];
