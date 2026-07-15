<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Production invoice — Suriname Shiphandling & Services NV
 * Source: 2026-INV-107 Henna T Spare Parts
 * Quote Ref → tp_otmsq_104
 */
return [
    'key'                   => 'sr_inv_107',
    'source_invoice_number' => '2026-INV-107- Henna T - Spare Parts',
    'source_quote_ref'      => '4 - 2026 - OTMSQ - 104 - Spare Part - Henna-T',
    'related_proposal_key'  => 'tp_otmsq_104',
    'customer_company'      => 'Suriname Shiphandling & Services NV',
    'force_status'          => 1,
    'save_option'           => 'otmain_seed_invoice_sr_inv_107',
    'invoice' => [
        'number'               => 107,
        'date'                 => '2026-03-10',
        'duedate'              => '2026-03-12',
        'expiry_days'          => 2,
        'status'               => 1,
        'document_title'       => 'Invoice',
        'invoice_title'        => 'Henna T - Spare Parts',
        'adminnote'            => '',
        'contact_person_name'  => 'Widia Lestari - Ibrahim',
        'contact_person_email' => 'w.lestari@otmain.com',
        'contact_person_phone' => '+31620020563',
        'payment_terms_text'   => '',
        'delivery_terms'       => '',
        'lead_time'            => '',
        'delivery_address'     => "",
        'availability'         => '',
        'notes'                => '',
        'total_usd_display'    => '$ 9.242,46',
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
                'description'      => 'VEGABAR 28 - Pressure Transmitter, 4....20 mA, Process Connection: G1/2',
                'long_description' => "Spare Parts Self Discharging Cement Henna-T\nScope of supply:",
                'qty'              => 4,
                'rate'             => 389.84,
                'unit'             => '',
                'taxname'          => [],
                'order'            => 1,
            ],
            2 => [
                'description'      => 'VEGAPULS 21 - Radar sensor for Cargo Process Connection: G11/2',
                'long_description' => "",
                'qty'              => 2,
                'rate'             => 945.00,
                'unit'             => '',
                'taxname'          => [],
                'order'            => 2,
            ],
            3 => [
                'description'      => 'Siemens Softstrater for Aerzen Compressor',
                'long_description' => "",
                'qty'              => 1,
                'rate'             => 4077.70,
                'unit'             => '',
                'taxname'          => [],
                'order'            => 3,
            ],
            4 => [
                'description'      => 'Freight Cost DHL Express',
                'long_description' => "",
                'qty'              => 1,
                'rate'             => 269.00,
                'unit'             => '',
                'taxname'          => [],
                'order'            => 4,
            ],
        ],
    ],
];
