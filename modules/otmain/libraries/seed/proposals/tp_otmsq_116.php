<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Production quotation — TP Company Limited
 * Source PDF: 16 - 2026 - OTMSQ - 116 - Spare Part - Vigor Ship Unloader (09-05-2026)
 *
 * Spare Parts: pneumatic wear protection + SET RUBBERS for coupling.
 */
return [
    'key'                 => 'tp_otmsq_116',
    'source_quote_number' => '16 - 2026 - OTMSQ - 116 - Spare Part - Vigor Ship Unloader',
    'customer_company'    => 'TP Company Limited',
    'force_status'        => 3, // Accepted
    'populate_tracker'    => true,
    'save_option'         => 'otmain_seed_proposal_tp_otmsq_116',
    'tracker_updates'     => array_fill(0, 2, ['item_status' => 'pending', 'notes' => '', 'admin_notes' => '']),
    'proposal' => [
        'subject'              => 'Spare Parts - Vigor Ship Unloader - Quotation',
        'date'                 => '2026-05-09',
        'open_till'            => '2026-06-04',
        'status'               => 3,
        'rel_type'             => 'customer',
        'client_ref'           => 'Spare Parts',
        'quote_title'          => 'Spare Part - Vigor Ship Unloader',
        'document_title'       => 'Quotation',
        'terms'                => '',
        'expiry_days'          => 26,
        'availability'         => '',
        'notes'                => '',
        // PDF deal contact (optional override)
        'contact_person_name'  => 'S.A.Ibrahim',
        'contact_person_email' => 's.ibrahim@otmain.com',
        'contact_person_phone' => '+316****8651',
        'payment_terms_text'   => '50% in advance<br />50% before delivery',
        'shipment_terms'       => 'EXW (Ex Works)',
        'delivery_time'        => '',
        'total_usd_display'    => '$ 6.796,76',
        'total_gold_display'   => '',
        'subtotal'             => 5704,
        'total_tax'            => 0,
        'total'                => 5704,
        'discount_type'        => '',
        'discount_percent'     => 0,
        'discount_total'       => 0,
        'show_quantity_as'     => 1,
        'allow_comments'       => 1,
        'newitems'             => [
            1 => [
                'description'      => 'Pneumatic wear protection solution 10 kg Bucket',
                'long_description' => 'Features:
• High temperature resistance up to 232 °C (450 °F)
• Excellent abrasion resistance against fine particles
• Non-sagging formulation, suitable for vertical and overhead applications
• Easy to apply for maintenance and repair work

Spare Parts - Vigor Ship Unloader',
                'qty'              => 1,
                'rate'             => 1184,
                'unit'             => 'unit',
                'taxname'          => [],
                'order'            => 1,
            ],
            2 => [
                'description'      => 'SET RUBBERS (4 PCS) For Coupling',
                'long_description' => 'Spare Parts - Vigor Ship Unloader',
                'qty'              => 4,
                'rate'             => 1130,
                'unit'             => 'unit',
                'taxname'          => [],
                'order'            => 2,
            ],
        ],
    ],
];
