<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Production quotation — TP Company Limited
 * Source PDF: 1 - 2026 - OTMPQ - 101 - Suction Nozzle R1.1 (21-01-2026)
 *
 * Vigor Ship Unloader – Suction Nozzle DN400 with slew drive assembly.
 * Address / company phone / email diisi orchestrator dari tblclients + primary contact.
 */
return [
    'key'                 => 'tp_suction_nozzle_101',
    'source_quote_number' => '1 - 2026 - OTMPQ - 101 - Suction Nozzle R1.1',
    'customer_company'    => 'TP Company Limited',
    'force_status'        => 3, // Accepted
    'populate_tracker'    => true,
    'save_option'         => 'otmain_seed_proposal_tp_suction_nozzle_101',
    'tracker_updates'     => [
        0 => [
            'item_status' => 'pending',
            'notes'       => '',
            'admin_notes' => '',
        ],
    ],
    'proposal' => [
        'subject'              => 'Suction Nozzle DN400 - Quotation',
        'date'                 => '2026-01-21',
        'open_till'            => '2026-01-27',
        'status'               => 3,
        'rel_type'             => 'customer',
        'client_ref'           => '',
        'quote_title'          => 'Suction Nozzle R1.1',
        'document_title'       => 'Quotation',
        'terms'                => '',
        'expiry_days'          => 6,
        'availability'         => '',
        'notes'                => '',
        'contact_person_name'  => 'S.A.Ibrahim',
        'contact_person_email' => 's.ibrahim@otmain.com',
        'contact_person_phone' => '+31618228651',
        'payment_terms_text'   => '50% in advance<br />50% before delivery',
        'shipment_terms'       => 'EXW Jakarta',
        'delivery_time'        => '45 days',
        'total_usd_display'    => '$ 28.665,00',
        'total_gold_display'   => '',
        'subtotal'             => 23880,
        'total_tax'            => 0,
        'total'                => 23880,
        'discount_type'        => '',
        'discount_percent'     => 0,
        'discount_total'       => 0,
        'show_quantity_as'     => 1,
        'allow_comments'       => 1,
        'newitems'             => [
            1 => [
                'description'      => 'Vigor Ship Unloader – Suction Nozzle DN400',
                'long_description' => 'Complete assembled

Scope of supply:
Rotating suction nozzle, complete assembly
DN400 suction nozzle with wear-resistant steel to ensure long service life
Heavy-duty, dust-free slewing bearing for smooth and well-protected rotation
Renewed and optimized false air hole design for improved airflow control

Item: slew drive assembly 5613-20641-25-8.20-A-2026.03.10
1 wooden box packing size: 2630X860X1000(mm)=2.262CBM GW: 731kg
Hydraulic pinion drive equipped with an improved gear system',
                'qty'              => 1,
                'rate'             => 23880,
                'unit'             => 'unit',
                'taxname'          => [],
                'order'            => 1,
            ],
        ],
    ],
];
