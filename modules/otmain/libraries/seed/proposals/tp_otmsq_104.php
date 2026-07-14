<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Production quotation — Suriname Shiphandling & Services NV
 * Source PDF: 4 - 2026 - OTMSQ - 104 - Spare Part - Henna-T (25-01-2026)
 *
 * Spare parts for self-discharging cement system (Henna-T):
 * VEGABAR pressure transmitters, VEGAPULS radar sensors, Siemens soft starter.
 * Address / company phone / email diisi orchestrator dari tblclients + primary contact.
 * contact_person_* di bawah = override dari PDF (deal contact); kosongkan agar pakai primary contact.
 */
return [
    'key'                 => 'tp_otmsq_104',
    'source_quote_number' => '4 - 2026 - OTMSQ - 104',
    'customer_company'    => 'Suriname Shiphandling & Services NV',
    'force_status'        => 3, // Accepted
    'populate_tracker'    => true,
    'save_option'         => 'otmain_seed_proposal_tp_otmsq_104',
    'tracker_updates'     => [
        0 => [
            'item_status' => 'pending',
            'notes'       => '',
            'admin_notes' => '',
        ],
        1 => [
            'item_status' => 'pending',
            'notes'       => '',
            'admin_notes' => '',
        ],
        2 => [
            'item_status' => 'pending',
            'notes'       => '',
            'admin_notes' => '',
        ],
    ],
    'proposal' => [
        'subject'              => 'Spare Parts - Henna-T - Quotation',
        'date'                 => '2026-01-25',
        'open_till'            => '2026-02-24',
        'status'               => 3,
        'rel_type'             => 'customer',
        'client_ref'           => '',
        'quote_title'          => 'Spare Parts - Henna - T',
        'document_title'       => 'Quotation',
        'terms'                => '',
        'expiry_days'          => 30,
        'availability'         => '',
        'notes'                => '',
        // PDF deal contact (optional override; leave empty to use primary contact)
        'contact_person_name'  => 'S.A.Ibrahim',
        'contact_person_email' => 's.ibrahim@otmain.com',
        'contact_person_phone' => '+316****8651',
        'payment_terms_text'   => 'Term 1: 50% due upon acceptance of the offer<br />Term 2: 50% due upon receipt of the Bill of Lading copy',
        'shipment_terms'       => 'EXW. Shipment can be arranged by Air or Sea Cargo.',
        'delivery_time'        => '14 days',
        'total_usd_display'    => '$ 9.041,46',
        'total_gold_display'   => '999.9 in Gram',
        'subtotal'             => 7527.05,
        'total_tax'            => 0,
        'total'                => 7527.05,
        'discount_type'        => '',
        'discount_percent'     => 0,
        'discount_total'       => 0,
        'show_quantity_as'     => 1,
        'allow_comments'       => 1,
        'newitems'             => [
            1 => [
                'description'      => 'VEGABAR 28 - Pressure Transmitter',
                'long_description' => '4 .... 20 mA, Process Connection: G1/2

Spare Parts Self Discharging Cement System – Henna-T',
                'qty'              => 4,
                'rate'             => 389.84,
                'unit'             => 'unit',
                'taxname'          => [],
                'order'            => 1,
            ],
            2 => [
                'description'      => 'VEGAPULS 21 - Radar sensor for Cargo hold',
                'long_description' => 'Process Connection: G1 1/2

Spare Parts Self Discharging Cement System – Henna-T',
                'qty'              => 2,
                'rate'             => 945.00,
                'unit'             => 'unit',
                'taxname'          => [],
                'order'            => 2,
            ],
            3 => [
                'description'      => 'Siemens Softstrater for Aerzen Compressor',
                'long_description' => 'Spare Parts Self Discharging Cement System – Henna-T',
                'qty'              => 1,
                'rate'             => 4077.70,
                'unit'             => 'unit',
                'taxname'          => [],
                'order'            => 3,
            ],
        ],
    ],
];
