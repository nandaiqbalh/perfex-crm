<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Purchase Order — RR Holland
 * Source: 2026-PO-107 / Supplier Quote 2451625
 * Our Ref.: 14 - 2026 - OTMSQ - 114 - Spare Part → tp_otmsq_114
 * Total EUR 466,00 (VAT 0%)
 */
return [
    'key'                  => 'po_107_rr_holland',
    'source_po_number'     => '2026-PO-107',
    'number'               => 107,
    'currency_code'        => 'EUR',
    'supplier_company'     => 'RR Holland',
    'related_proposal_key' => 'tp_otmsq_114',
    'save_option'          => 'otmain_seed_po_107_rr_holland',
    'purchase_order'       => [
        'document_title'     => 'Purchase Order',
        'date'               => '2026-05-07',
        'supplier_quote_ref' => '2451625',
        'contact_person'     => "Sha'Iez Ibrahim",
        'email'              => 's.ibrahim@otmain.com',
        'phone'              => '+31618228651',
        'adminnote'          => "Our Ref.: 14 - 2026 - OTMSQ - 114 - Spare Part\n\nAfleveren:\nOT-Main\nBajonetstraat 52\n3014ZK, Rotterdam\nNederland",
        'items'              => [
            [
                'description' => "50 - 154N0541 - Valve KPBS-250/1/D/7 (BVS1207-4)\n"
                    . "* Adjusted at 250 Bar, at 5 ltr.min.\n"
                    . "* Max. flow: 60 ltr/min.\n"
                    . "* Pilot ratio: 4.25 : 1\n"
                    . '* 1 rotation of adjustment screw is ± 50 bar',
                'qty'         => 2,
                'unit_price'  => 233.00,
                'taxrate'     => 0,
                'taxname'     => '',
            ],
        ],
    ],
];
