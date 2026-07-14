<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Production quotation — TP Company Limited
 * Source PDF: 12 - 2026 - OTMSQ - 112 - Spare Part Slew - Vigor Shipunloader (18-03-2026)
 *
 * Slew Gear + Hydraulic Motor – two options: complete unit or selected items.
 */
return [
    'key'                 => 'tp_otmsq_112',
    'source_quote_number' => '12 - 2026 - OTMSQ - 112 - Spare Part Slew - Vigor Shipunloader',
    'customer_company'    => 'TP Company Limited',
    'force_status'        => 3, // Accepted
    'populate_tracker'    => true,
    'save_option'         => 'otmain_seed_proposal_tp_otmsq_112',
    'tracker_updates'     => array_fill(0, 2, ['item_status' => 'pending', 'notes' => '', 'admin_notes' => '']),
    'proposal' => [
        'subject'              => 'Slew Gear + Hydraulic Motor - Vigor Shipunloader - Quotation',
        'date'                 => '2026-03-18',
        'open_till'            => '2026-03-28',
        'status'               => 3,
        'rel_type'             => 'customer',
        'client_ref'           => 'Slew Gear + Hydraulic Motor',
        'quote_title'          => 'Spare Part Slew - Vigor Shipunloader',
        'document_title'       => 'Quotation',
        'terms'                => '',
        'expiry_days'          => 10,
        'availability'         => '',
        'notes'                => '',
        // PDF deal contact (optional override)
        'contact_person_name'  => 'S.A.Ibrahim',
        'contact_person_email' => 's.ibrahim@otmain.com',
        'contact_person_phone' => '+316****8651',
        'payment_terms_text'   => 'To be agreed.',
        'shipment_terms'       => 'EXW (Ex Works)',
        'delivery_time'        => '4 weeks',
        'total_usd_display'    => '$ 22.821,66',
        'total_gold_display'   => '',
        'subtotal'             => 19498,
        'total_tax'            => 0,
        'total'                => 19498,
        'discount_type'        => '',
        'discount_percent'     => 0,
        'discount_total'       => 0,
        'show_quantity_as'     => 1,
        'allow_comments'       => 1,
        'newitems'             => [
            1 => [
                'description'      => 'Option 1 - Complete Slew unit',
                'long_description' => 'The complete slew unit includes the following components:
Gear RR 810D
Fixing flange
Brake
OMSS
MSS 160 Hydr. Motor
Valve

Slew Gear + Hydraulic Motor – Vigor Shipunloader',
                'qty'              => 2,
                'rate'             => 5800,
                'unit'             => 'unit',
                'taxname'          => [],
                'order'            => 1,
            ],
            2 => [
                'description'      => 'Option 2 - Selected Items',
                'long_description' => 'This option includes the following:
Gear RR 810D
MSS 160 Hydr. Motor
OMSS

Slew Gear + Hydraulic Motor – Vigor Shipunloader',
                'qty'              => 2,
                'rate'             => 3949,
                'unit'             => 'unit',
                'taxname'          => [],
                'order'            => 2,
            ],
        ],
    ],
];
