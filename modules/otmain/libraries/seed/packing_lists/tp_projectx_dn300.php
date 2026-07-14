<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Production packing list — TP Company Limited (Vigor Cement Shipunloader)
 * Source PDF: Packing List & Invoice (OT-Project(x) Jakarta)
 * Partial shipment — Suction hose DN300 only, shipped from Jakarta.
 * References quotation 3 - 2026 - OTMSQ - 103 - Suction Hose.
 */
return [
    'key'                   => 'tp_projectx_dn300',
    'customer_company'      => 'TP Company Limited',
    'related_proposal_keys' => [
        '3 - 2026 - OTMSQ - 103 - Suction Hose',
    ],
    'save_option'           => 'otmain_seed_packing_projectx_dn300',
    'packing' => [
        'document_title'       => 'Packing List & Invoice',
        'date'                 => '2026-01-17',
        'vessel'               => 'Vigor Cement Shipunloader',
        // contact PDF — ensure di tblcontacts (tanpa replace)
        'contact_person_name'  => 'Anthuvan',
        'contact_person_email' => 'anthuvan.turkyspetroleum@outlook.com',
        'contact_person_phone' => '+255 773 594 515',
        'adminnote'            => '',
    ],
    'items' => [
        [
            'description'   => 'Custom Made Suction hose: Size: DN300 x L5500mm.',
            'hs_code'       => '400942',
            'qty'           => 1,
            'unit_price'    => 3500.00,
            'taxrate'       => 0,
            // Packing dimensions (converted from cm to mm)
            'packing_qty'   => 1,
            'unit_type'     => 'other',
            'unit_label'    => 'Box',
            'length'        => 5500,
            'width'         => 400,
            'height'        => 400,
            'gross_weight'  => 440,
            'net_weight'    => 440,
        ],
    ],
];
