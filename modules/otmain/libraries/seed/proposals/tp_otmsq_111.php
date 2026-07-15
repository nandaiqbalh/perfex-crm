<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Production quotation — Suriname Shiphandling & Services NV
 * Source PDF: 11 - 2026 - OTMSQ - 111 - ABB contactor - Rudisa (18-03-2026)
 *
 * ABB Contactor 4 polig AF190-40 – Rudisa.
 * Address / company phone / email diisi orchestrator dari tblclients + primary contact.
 */
return [
    'key'                 => 'tp_otmsq_111',
    'source_quote_number' => '11 - 2026 - OTMSQ - 111',
    'aliases'             => [
        '11 - 2026 - OTMSQ - 111',
        '11 - 2026 - OTMSQ - 111 - ABB Contactor - Rudisa',
    ],
    'customer_company'    => 'Suriname Shiphandling & Services NV',
    'force_status'        => 3, // Accepted
    'populate_tracker'    => true,
    'save_option'         => 'otmain_seed_proposal_tp_otmsq_111',
    'tracker_updates'     => [
        0 => ['item_status' => 'pending', 'notes' => '', 'admin_notes' => ''],
    ],
    'proposal' => [
        'subject'              => 'ABB Contactor 4 polig AF190-40 - Rudisa - Quotation',
        'date'                 => '2026-03-18',
        'open_till'            => '2026-03-28',
        'status'               => 3,
        'rel_type'             => 'customer',
        'client_ref'           => 'ABB Contactor 4 polig AF190-40',
        'quote_title'          => 'ABB contactor - Rudisa',
        'document_title'       => 'Quotation',
        'terms'                => '',
        'expiry_days'          => 10,
        'availability'         => '',
        'notes'                => 'Airfreight costs will be calculated separately upon confirmation of the order.',
        // PDF deal contact (optional override)
        'contact_person_name'  => 'S.A.Ibrahim',
        'contact_person_email' => 's.ibrahim@otmain.com',
        'contact_person_phone' => '+316****8651',
        'payment_terms_text'   => 'To be agreed.',
        'shipment_terms'       => 'EXW (Ex Works)',
        'delivery_time'        => 'Voor 9:30 uur Surinaamse tijd, melden zodat ik het morgen kan leveren.',
        'total_usd_display'    => '$ 1.376,38',
        'total_gold_display'   => '',
        'subtotal'             => 1168.70,
        'total_tax'            => 0,
        'total'                => 1168.70,
        'discount_type'        => '',
        'discount_percent'     => 0,
        'discount_total'       => 0,
        'show_quantity_as'     => 1,
        'allow_comments'       => 1,
        'newitems'             => [
            1 => [
                'description'      => 'ABB Contactor 4 polig AF190-40 – Rudisa',
                'long_description' => 'The AF190-40 is a 4 pole – 1000 V IEC or 600 V UL contactor with Main Circuit Bars, controlling motors up to 90 kW / 400 V AC (AC-3) / and switching power circuits up to 275 A (AC-1) or 230 A UL general use. Thanks to the AF technology, the contactor has a wide control voltage range (100-250 V 50/60 Hz and DC)',
                'qty'              => 1,
                'rate'             => 1168.70,
                'unit'             => 'unit',
                'taxname'          => [],
                'order'            => 1,
            ],
        ],
    ],
];
