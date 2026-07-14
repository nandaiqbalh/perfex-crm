<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Production quotation — TP Company Limited
 * Source PDF: 10 - 2026 - OTMSQ - 110 - Vigor Ship Unloader (18-03-2026)
 *
 * Spare Parts ZE6 Compressor – Vigor Ship Unloader:
 * bearings, antivibration pads, rubbers, O-rings.
 */
return [
    'key'                 => 'tp_otmsq_110',
    'source_quote_number' => '10 - 2026 - OTMSQ - 110 - Vigor Ship Unloader',
    'customer_company'    => 'TP Company Limited',
    'force_status'        => 3, // Accepted
    'populate_tracker'    => true,
    'save_option'         => 'otmain_seed_proposal_tp_otmsq_110',
    'tracker_updates'     => array_fill(0, 7, ['item_status' => 'pending', 'notes' => '', 'admin_notes' => '']),
    'proposal' => [
        'subject'              => 'Spare Parts ZE6 Compressor - Vigor Ship Unloader - Quotation',
        'date'                 => '2026-03-18',
        'open_till'            => '2026-03-19',
        'status'               => 3,
        'rel_type'             => 'customer',
        'client_ref'           => 'Spare Parts ZE6 Compressor',
        'quote_title'          => 'Vigor Ship Unloader',
        'document_title'       => 'Quotation',
        'terms'                => '',
        'expiry_days'          => 1,
        'availability'         => '',
        'notes'                => 'Airfreight costs will be calculated separately upon confirmation of the order.',
        // PDF deal contact (optional override)
        'contact_person_name'  => 'S.A.Ibrahim',
        'contact_person_email' => 's.ibrahim@otmain.com',
        'contact_person_phone' => '+316****8651',
        'payment_terms_text'   => 'To be agreed.',
        'shipment_terms'       => 'EXW (Ex Works)',
        'delivery_time'        => 'As stated in the quotation items.',
        'total_usd_display'    => '$ 7.770,90',
        'total_gold_display'   => '',
        'subtotal'             => 6634.10,
        'total_tax'            => 0,
        'total'                => 6634.10,
        'discount_type'        => '',
        'discount_percent'     => 0,
        'discount_total'       => 0,
        'show_quantity_as'     => 1,
        'allow_comments'       => 1,
        'newitems'             => [
            1 => [
                'description'      => 'SKF BA1 7018 A AUSTRIA',
                'long_description' => 'Spare Parts ZE6 Compressor – Vigor Ship Unloader',
                'qty'              => 1,
                'rate'             => 1890,
                'unit'             => 'unit',
                'taxname'          => [],
                'order'            => 1,
            ],
            2 => [
                'description'      => 'SKF BA1 7018 B AUSTRIA',
                'long_description' => 'Spare Parts ZE6 Compressor – Vigor Ship Unloader',
                'qty'              => 1,
                'rate'             => 1890,
                'unit'             => 'unit',
                'taxname'          => [],
                'order'            => 2,
            ],
            3 => [
                'description'      => 'Antivibration Pads (for 2 compressors)',
                'long_description' => 'Spare Parts ZE6 Compressor – Vigor Ship Unloader',
                'qty'              => 12,
                'rate'             => 127.05,
                'unit'             => 'unit',
                'taxname'          => [],
                'order'            => 3,
            ],
            4 => [
                'description'      => 'SET RUBBERS (4 PCS) For Coupling',
                'long_description' => 'Spare Parts ZE6 Compressor – Vigor Ship Unloader',
                'qty'              => 1,
                'rate'             => 1130,
                'unit'             => 'unit',
                'taxname'          => [],
                'order'            => 4,
            ],
            5 => [
                'description'      => '0663210808 - O-RING',
                'long_description' => 'Spare Parts ZE6 Compressor – Vigor Ship Unloader',
                'qty'              => 2,
                'rate'             => 9,
                'unit'             => 'unit',
                'taxname'          => [],
                'order'            => 5,
            ],
            6 => [
                'description'      => '0663210326 - O-RING',
                'long_description' => 'Spare Parts ZE6 Compressor – Vigor Ship Unloader',
                'qty'              => 1,
                'rate'             => 165.77,
                'unit'             => 'unit',
                'taxname'          => [],
                'order'            => 6,
            ],
            7 => [
                'description'      => '0663210858 - O-RING',
                'long_description' => 'Spare Parts ZE6 Compressor – Vigor Ship Unloader',
                'qty'              => 1,
                'rate'             => 15.73,
                'unit'             => 'unit',
                'taxname'          => [],
                'order'            => 7,
            ],
        ],
    ],
];
