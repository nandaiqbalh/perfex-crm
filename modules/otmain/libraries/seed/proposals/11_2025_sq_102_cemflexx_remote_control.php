<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Production quotation — CemFlexX B.V. (2025)
 * Source folder: 11.2025-SQ-102-CemflexX-Remote Control
 * Source PDF: 2025-SQ-102 (29-4-2025)
 *
 * Back-up Afstandsbediening / Scanreco — Optie 1 + Optie 2 listed (subtotal = sum).
 * Distinct from 2025-PQ-102 (Kovako inspection).
 */
return [
    'key'                 => '11_2025_sq_102_cemflexx_remote_control',
    'source_quote_number' => '2025-SQ-102',
    'aliases'             => [
        '2025-SQ-102',
        '11.2025-SQ-102-CemflexX-Remote Control',
        'cx_sq_102',
    ],
    'customer_company'    => 'CemFlexX B.V.',
    'force_status'        => 3,
    'populate_tracker'    => true,
    'save_option'         => 'otmain_seed_proposal_11_2025_sq_102_cemflexx_remote_control',
    'tracker_updates'     => array_fill(0, 2, ['item_status' => 'pending', 'notes' => '', 'admin_notes' => '']),
    'proposal' => [
        'subject'              => 'Back-up Afstandsbediening Vigor Shipunloader - Quotation',
        'date'                 => '2025-04-29',
        'open_till'            => '2025-05-29',
        'status'               => 3,
        'rel_type'             => 'customer',
        'client_ref'           => 'Afstandsbediening voor Vigor Shipunloader',
        'quote_title'          => 'SQ-102',
        'document_title'       => 'Quotation',
        'terms'                => '',
        'expiry_days'          => 30,
        'availability'         => '',
        'notes'                => 'Shipping included in the price (Only in The Netherlands). Shipping costs for orders outside the Netherlands will be charged separately.',
        'contact_person_name'  => 'S.A.Ibrahim',
        'contact_person_email' => 'sales@otmain.com',
        'contact_person_phone' => '+31618228651',
        'payment_terms_text'   => '50% upon placing the Purchase Order / 50% before shipment',
        'shipment_terms'       => 'Included in the price (Only in The Netherlands)',
        'delivery_time'        => 'Lead time: 6 a 7 weeks',
        'total_usd_display'    => '',
        'total_gold_display'   => '',
        'subtotal'             => 6766.60,
        'total_tax'            => 1420.99,
        'total'                => 8187.59,
        'discount_type'        => '',
        'discount_percent'     => 0,
        'discount_total'       => 0,
        'show_quantity_as'     => 1,
        'allow_comments'       => 1,
        'newitems'             => [
            1 => [
                'description'      => 'Optie 1: Scanreco afstandsbediening (full set)',
                'long_description' => "Back-up Afstandsbediening voor Vigor Shipunloader\n\nScanreco afstandsbediening, inclusief:\n1. 1x Draadloze afstandsbediening\n2. 1x Nekriem\n3. 1x Snellader voor batterijen (110–230 VAC, laadtijd 3-4 uur)\n4. 2x Batterijen van 2000 mAh\n5. Inclusief Aangepaste Software",
                'qty'              => 1,
                'rate'             => 3575.60,
                'unit'             => '',
                'taxname'          => ['VAT|21'],
                'order'            => 1,
            ],
            2 => [
                'description'      => 'Optie 2: Scanreco afstandsbediening (basic)',
                'long_description' => "Scanreco afstandsbediening, inclusief:\n1. 1x Draadloze afstandsbediening\n2. Inclusief Aangepaste Software",
                'qty'              => 1,
                'rate'             => 3191.00,
                'unit'             => '',
                'taxname'          => ['VAT|21'],
                'order'            => 2,
            ],
        ],
    ],
];
