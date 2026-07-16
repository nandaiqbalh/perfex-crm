<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Production quotation — TP Company Limited
 * Source PDF: 3 - 2026 - OTMSQ - 103 - Suction Hose (17-01-2026 rev.1.1)
 *
 * Custom Made Suction hose DN400 x L5500mm (4 pcs) for Vigor Ship Unloader.
 * Address / company phone / email diisi orchestrator dari tblclients + primary contact.
 */
return [
    'key'                 => 'tp_suction_hose',
    'source_quote_number' => '3 - 2026 - OTMSQ - 103 - Suction Hose',
    'aliases'             => [
        '3 - 2026 - OTMSQ - 103 - Suction Hose',
        '3 - 2026 - OTMSQ - 103 - Suction Hose rev.1.1',
    ],
    'customer_company'    => 'TP Company Limited',
    'force_status'        => 3, // Accepted
    'populate_tracker'    => true,
    'save_option'         => 'otmain_seed_proposal_tp_suction_hose',
    'tracker_updates'     => [
        0 => [
            'item_status' => 'pending',
            'notes'       => '',
            'admin_notes' => '',
        ],
    ],
    'proposal' => [
        'subject'              => 'Suction Hose DN400 - Quotation',
        'date'                 => '2026-01-17',
        'open_till'            => '2026-01-27',
        'status'               => 3,
        'rel_type'             => 'customer',
        'client_ref'           => '',
        'quote_title'          => 'Suction Hose',
        'document_title'       => 'Quotation',
        'terms'                => '',
        'expiry_days'          => 10,
        'availability'         => '',
        'notes'                => '',
        'contact_person_name'  => 'S.A.Ibrahim',
        'contact_person_email' => 's.ibrahim@otmain.com',
        'contact_person_phone' => '+31618228651',
        'payment_terms_text'   => '50% in advance<br />50% before delivery',
        'shipment_terms'       => 'EXW Jakarta',
        'delivery_time'        => '30 days',
        'total_usd_display'    => '$ 19.115,00',
        'total_gold_display'   => '',
        'subtotal'             => 16400,
        'total_tax'            => 0,
        'total'                => 16400,
        'discount_type'        => '',
        'discount_percent'     => 0,
        'discount_total'       => 0,
        'show_quantity_as'     => 1,
        'allow_comments'       => 1,
        'newitems'             => [
            1 => [
                'description'      => 'Custom Made Suction hose DN400 x L5500mm',
                'long_description' => 'Size: DN400 x L5500mm
Working pressure: -0.8 / 10bar
Bursting pressure: 10bar
With one side fixed flange, and one side Split flange
Vacuum Reinforced with Vacuum rings
Both side flange are antirust',
                'qty'              => 4,
                'rate'             => 4100,
                'unit'             => 'unit',
                'taxname'          => [],
                'order'            => 1,
            ],
        ],
    ],
];
