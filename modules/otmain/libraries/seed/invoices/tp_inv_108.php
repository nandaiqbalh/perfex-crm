<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Production invoice — TP Company Limited
 * Source: 2026-INV-108 Suction Nozzle
 * Quote Ref → tp_suction_nozzle_101
 */
return [
    'key'                   => 'tp_inv_108',
    'source_invoice_number' => '2026-INV-108-Vigor Shipunloader - Suction Nozzle',
    'source_quote_ref'      => '1 - 2026 - OTMPQ - 101 - Suction Nozzle R1.1',
    'related_proposal_key'  => 'tp_suction_nozzle_101',
    'customer_company'      => 'TP Company Limited',
    'force_status'          => 1,
    'save_option'           => 'otmain_seed_invoice_tp_inv_108',
    'invoice' => [
        'number'               => 108,
        'date'                 => '2026-01-28',
        'duedate'              => '2026-02-04',
        'expiry_days'          => 7,
        'status'               => 1,
        'document_title'       => 'Commercial Invoice',
        'invoice_title'        => 'Vigor Shipunloader - Suction Nozzle',
        'adminnote'            => '',
        'contact_person_name'  => 'Widia Lestari - Ibrahim',
        'contact_person_email' => 'w.lestari@otmain.com',
        'contact_person_phone' => '+31620020563',
        'payment_terms_text'   => '50% in advance 50% before delivery',
        'delivery_terms'       => '30 Days after the first Term Payment',
        'lead_time'            => '',
        'delivery_address'     => "--",
        'availability'         => '',
        'notes'                => '',
        'total_usd_display'    => '$ 28.665,00',
        'bank_account'         => 'USD',
        'show_quantity_as'     => 1,
        'sale_agent'           => 0,
        'discount_type'        => '',
        'discount_percent'     => 0,
        'discount_total'       => 0,
        'adjustment'           => 0,
        'clientnote'           => '',
        'terms'                => '',
        'newitems'             => [
            1 => [
                'description'      => 'Rotating suction nozzle, complete assembly',
                'long_description' => "DN400 suction nozzle with wear-resistant steel to ensure long service life\nHeavy-duty, dust-free slewing bearing for smooth, and well-protected rotation\nRenewed and optimized false air hole design for improved airflow control\nHydraulic pinion drive equipped with an gear system",
                'qty'              => 1,
                'rate'             => 23880.00,
                'unit'             => '',
                'taxname'          => [],
                'order'            => 1,
            ],
        ],
    ],
];
