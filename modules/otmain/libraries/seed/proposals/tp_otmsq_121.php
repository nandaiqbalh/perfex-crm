<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Production quotation — Suriname Shiphandling & Services NV
 * Source PDF: 21 - 2026 - OTMSQ - 121 (09-07-2026)
 *
 * Protection Anodes for Hull - Henna-T.
 * OEM + optional variants; customer to confirm welding or bolt-on type.
 */
return [
    'key'                 => 'tp_otmsq_121',
    'source_quote_number' => '21 - 2026 - OTMSQ - 121',
    'customer_company'    => 'Suriname Shiphandling & Services NV',
    'force_status'        => 3, // Accepted
    'populate_tracker'    => true,
    'save_option'         => 'otmain_seed_proposal_tp_otmsq_121',
    'tracker_updates'     => array_fill(0, 4, ['item_status' => 'pending', 'notes' => '', 'admin_notes' => '']),
    'proposal' => [
        'subject'              => 'Protection Anodes for Hull - Henna-T - Quotation',
        'date'                 => '2026-07-09',
        'open_till'            => '2026-08-08',
        'status'               => 3,
        'rel_type'             => 'customer',
        'client_ref'           => 'Protection Anodes for Hull',
        'quote_title'          => '',
        'document_title'       => 'Quotation',
        'terms'                => '',
        'expiry_days'          => 30,
        'availability'         => '10 to 15 days',
        'notes'                => 'Confirm if you require welding type or Bolt - On Type.',
        // PDF deal contact (optional override)
        'contact_person_name'  => 'S.A.Ibrahim',
        'contact_person_email' => 's.ibrahim@otmain.com',
        'contact_person_phone' => '+316****8651',
        'payment_terms_text'   => 'To be agreed.',
        'shipment_terms'       => 'EXW (Ex Works)',
        'delivery_time'        => '10 to 15 days',
        'total_usd_display'    => '$ 19.583,47',
        'total_gold_display'   => '',
        'subtotal'             => 16874.54,
        'total_tax'            => 0,
        'total'                => 16874.54,
        'discount_type'        => '',
        'discount_percent'     => 0,
        'discount_total'       => 0,
        'show_quantity_as'     => 1,
        'allow_comments'       => 1,
        'newitems'             => [
            1 => [
                'description'      => 'OEM Anodes Type MME - 13 - A',
                'long_description' => 'Suitable for: Steel Hulls
Length of Anode Strip: 1075 mm
Material: Aluminum
Product Width: 130 mm
Product Height: 50 mm
Product Length: 920 mm
Strip Length: 1075 mm
Shape: Rectangular

Protection Anodes for Hull - Henna-T',
                'qty'              => 44,
                'rate'             => 214.14,
                'unit'             => 'unit',
                'taxname'          => [],
                'order'            => 1,
            ],
            2 => [
                'description'      => 'Optional for MME - 13 - A (AI-Zn-In-Cd ALLOY, 920x130x50mm)',
                'long_description' => 'Size: 920x130x50MM
Connection type: Welding type or Bolt - On type
Material: AI-Zn-In-Cd ALLOY

Protection Anodes for Hull - Henna-T',
                'qty'              => 44,
                'rate'             => 115,
                'unit'             => 'unit',
                'taxname'          => [],
                'order'            => 2,
            ],
            3 => [
                'description'      => 'OEM Anodes Type MME - 6 - AL',
                'long_description' => 'Suitable For: Steel Hull
Material: Aluminum
Product Width: 130 mm
Product Height: 40 mm
Product Length: 550 mm
Strip Length: 680 mm
Shape: Rectangular

Protection Anodes for Hull - Henna-T',
                'qty'              => 14,
                'rate'             => 112.88,
                'unit'             => 'unit',
                'taxname'          => [],
                'order'            => 3,
            ],
            4 => [
                'description'      => 'Optional for MME - 6 - A (AI-Zn-In-Cd ALLOY, 550x130x40mm)',
                'long_description' => 'Size: 550x130x40MM
Connection type: Welding type or Bolt - On type
Material: AI-Zn-In-Cd ALLOY

Protection Anodes for Hull - Henna-T',
                'qty'              => 14,
                'rate'             => 58,
                'unit'             => 'unit',
                'taxname'          => [],
                'order'            => 4,
            ],
        ],
    ],
];
