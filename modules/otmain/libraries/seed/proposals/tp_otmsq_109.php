<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Production quotation — TP Company Limited
 * Source PDF: 9 - 2026 - OTMSQ - 109 - Vigor Ship Unloader (17-03-2026)
 *
 * Element Block ZE6 Compressor – multiple options + complete new compressor.
 */
return [
    'key'                 => 'tp_otmsq_109',
    'source_quote_number' => '9 - 2026 - OTMSQ - 109 - Vigor Ship Unloader',
    'customer_company'    => 'TP Company Limited',
    'force_status'        => 3, // Accepted
    'populate_tracker'    => true,
    'save_option'         => 'otmain_seed_proposal_tp_otmsq_109',
    'tracker_updates'     => array_fill(0, 4, ['item_status' => 'pending', 'notes' => '', 'admin_notes' => '']),
    'proposal' => [
        'subject'              => 'Element Block ZE6 Compressor - Vigor Ship Unloader - Quotation',
        'date'                 => '2026-03-17',
        'open_till'            => '2026-03-19',
        'status'               => 3,
        'rel_type'             => 'customer',
        'client_ref'           => 'Element Block ZE6 Compressor',
        'quote_title'          => 'Vigor Ship Unloader',
        'document_title'       => 'Quotation',
        'terms'                => '',
        'expiry_days'          => 2,
        'availability'         => '',
        'notes'                => 'Airfreight costs will be calculated separately upon confirmation of the order.',
        // PDF deal contact (optional override)
        'contact_person_name'  => 'S.A.Ibrahim',
        'contact_person_email' => 's.ibrahim@otmain.com',
        'contact_person_phone' => '+316****8651',
        'payment_terms_text'   => 'To be agreed.',
        'shipment_terms'       => 'EXW (Ex Works)',
        'delivery_time'        => 'As stated in the quotation items. Airfreight costs will be calculated separately upon confirmation of the order.',
        'total_usd_display'    => '$ 465.169,30',
        'total_gold_display'   => '',
        'subtotal'             => 384430,
        'total_tax'            => 0,
        'total'                => 384430,
        'discount_type'        => '',
        'discount_percent'     => 0,
        'discount_total'       => 0,
        'show_quantity_as'     => 1,
        'allow_comments'       => 1,
        'newitems'             => [
            1 => [
                'description'      => 'Atlas Copco Element PN: 1616635681 – Express Delivery',
                'long_description' => 'Condition: New – OEM with Certificate
Delivery Time: 3 days after payment

Element Block ZE6 Compressor – Vigor Ship Unloader',
                'qty'              => 1,
                'rate'             => 93800,
                'unit'             => 'unit',
                'taxname'          => [],
                'order'            => 1,
            ],
            2 => [
                'description'      => 'Atlas Copco Element PN: 1616635681 – Standard Delivery',
                'long_description' => 'Condition: New – OEM with Certificate
Delivery Time: 2–4 weeks after payment

Element Block ZE6 Compressor – Vigor Ship Unloader',
                'qty'              => 1,
                'rate'             => 83200,
                'unit'             => 'unit',
                'taxname'          => [],
                'order'            => 2,
            ],
            3 => [
                'description'      => 'Atlas Copco Element PN: 1616635681 – Second-Hand Option',
                'long_description' => 'Condition: Used – Tested and in good working condition
Delivery Time: 1 week and 3 days after payment

Element Block ZE6 Compressor – Vigor Ship Unloader',
                'qty'              => 1,
                'rate'             => 42200,
                'unit'             => 'unit',
                'taxname'          => [],
                'order'            => 3,
            ],
            4 => [
                'description'      => 'Complete New Compressor ZE6',
                'long_description' => 'Type: Air Compressor
Model: ZE6
Maximum Working Pressure: 3 bar
Total Rated Power: 317.2 kW
Condition: Brand New
Delivery and pricing: To be discussed

Element Block ZE6 Compressor – Vigor Ship Unloader',
                'qty'              => 1,
                'rate'             => 165230,
                'unit'             => 'unit',
                'taxname'          => [],
                'order'            => 4,
            ],
        ],
    ],
];
