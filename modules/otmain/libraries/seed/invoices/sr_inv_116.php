<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Production invoice — Suriname Shiphandling & Services NV
 * Source: 2026-INV-116 Spare Parts detectors
 * Quote Ref → tp_otmsq_115
 */
return [
    'key'                   => 'sr_inv_116',
    'source_invoice_number' => '2026-INV-116 - Spare Parts',
    'source_quote_ref'      => '15 - 2026 - OTMSQ - 115 - Spare Part - Henna-T',
    'related_proposal_key'  => 'tp_otmsq_115',
    'customer_company'      => 'Suriname Shiphandling & Services NV',
    'force_status'          => 1,
    'save_option'           => 'otmain_seed_invoice_sr_inv_116',
    'invoice' => [
        'number'               => 116,
        'date'                 => '2026-07-06',
        'duedate'              => '2026-08-05',
        'expiry_days'          => 30,
        'status'               => 1,
        'document_title'       => 'Commercial Invoice',
        'invoice_title'        => 'Spare Parts',
        'adminnote'            => '',
        'contact_person_name'  => 'Widia Lestari - Ibrahim',
        'contact_person_email' => 'w.lestari@otmain.com',
        'contact_person_phone' => '+31620020563',
        'payment_terms_text'   => '50% advance payment<br />50% before to shipment',
        'delivery_terms'       => 'Lead time: 2 weeks',
        'lead_time'            => '',
        'delivery_address'     => "",
        'availability'         => '',
        'notes'                => 'The price is based on EXW terms; air freight or sea freight costs will be calculated and included in the second invoice.',
        'total_usd_display'    => '$ 3.492,29',
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
                'description'      => 'XP95 Optical Smoke Detector including Mounting plate',
                'long_description' => "Spare Parts - Henna-T",
                'qty'              => 10,
                'rate'             => 95.00,
                'unit'             => '',
                'taxname'          => [],
                'order'            => 1,
            ],
            2 => [
                'description'      => 'Tyco 601H-F-H/ 516.600.213 / Heat Detector',
                'long_description' => "",
                'qty'              => 3,
                'rate'             => 294.28,
                'unit'             => '',
                'taxname'          => [],
                'order'            => 2,
            ],
            3 => [
                'description'      => 'Tyco MCP250M Marine with indicator',
                'long_description' => "",
                'qty'              => 5,
                'rate'             => 234.00,
                'unit'             => '',
                'taxname'          => [],
                'order'            => 3,
            ],
        ],
    ],
];
