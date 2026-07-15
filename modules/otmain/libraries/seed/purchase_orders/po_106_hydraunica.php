<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Purchase Order — Hydraunica B.V.
 * Source: 2026-PO-106 / Supplier Quote Email do 16-4-2026 09:46
 * Our Ref.: 14 - 2026 - OTMSQ - 114 → tp_otmsq_114
 * Total EUR 211,17 (VAT 0%)
 */
return [
    'key'                  => 'po_106_hydraunica',
    'source_po_number'     => '2026-PO-106',
    'number'               => 106,
    'currency_code'        => 'EUR',
    'supplier_company'     => 'Hydraunica B.V.',
    'related_proposal_key' => 'tp_otmsq_114',
    'save_option'          => 'otmain_seed_po_106_hydraunica',
    'purchase_order'       => [
        'document_title'     => 'Purchase Order',
        'date'               => '2026-05-04',
        'supplier_quote_ref' => 'Email do 16-4-2026 09:46',
        'contact_person'     => "Sha'Iez Ibrahim",
        'email'              => 's.ibrahim@otmain.com',
        'phone'              => '+31618228651',
        'adminnote'          => "Our Ref.: 14 - 2026 - OTMSQ - 114\n\nAfleveren:\nOT-Main\nBajonetstraat 52\n3014ZK, Rotterdam\nNederland",
        'items'              => [
            [
                'description' => 'ML-60-CDW-11A3-11A3-G04-S01',
                'qty'         => 3,
                'unit_price'  => 70.39,
                'taxrate'     => 0,
                'taxname'     => '',
            ],
        ],
    ],
];
