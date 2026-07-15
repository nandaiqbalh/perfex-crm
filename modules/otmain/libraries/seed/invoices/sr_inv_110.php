<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Production invoice — Suriname Shiphandling & Services NV
 * Source: 2026-INV-110 Henna T Valves Seals
 * Quote Ref → tp_otmsq_106
 */
return [
    'key'                   => 'sr_inv_110',
    'source_invoice_number' => '2026-INV-110 - Henna T - Valves Seals',
    'source_quote_ref'      => '6 - 2026 - OTMSQ - 106 - Henna-T',
    'related_proposal_key'  => 'tp_otmsq_106',
    'customer_company'      => 'Suriname Shiphandling & Services NV',
    'force_status'          => 1,
    'save_option'           => 'otmain_seed_invoice_sr_inv_110',
    'invoice' => [
        'number'               => 110,
        'date'                 => '2026-03-10',
        'duedate'              => '2026-03-12',
        'expiry_days'          => 2,
        'status'               => 1,
        'document_title'       => 'Invoice',
        'invoice_title'        => 'Henna T - Valves Seals',
        'adminnote'            => '',
        'contact_person_name'  => 'Widia Lestari - Ibrahim',
        'contact_person_email' => 'w.lestari@otmain.com',
        'contact_person_phone' => '+31620020563',
        'payment_terms_text'   => '100% before Shipment',
        'delivery_terms'       => '1 Week After Payment',
        'lead_time'            => '',
        'delivery_address'     => "",
        'availability'         => '',
        'notes'                => 'PLEASE MENTION WITH YOUR TRANSACTION OUR INVOICE NUMBER',
        'total_usd_display'    => '$ 9.718,08',
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
                'description'      => 'First Payment: 100% of € 7.600,90',
                'long_description' => "Reference: 6 - 2026 - OTMSQ - 106 - Henna-T - Spare Valves Seals",
                'qty'              => 1,
                'rate'             => 7600.90,
                'unit'             => '',
                'taxname'          => [],
                'order'            => 1,
            ],
            2 => [
                'description'      => 'Luchtvracht kosten Door to Door',
                'long_description' => "(Inclusief de softstarter van de vorige zending)\nIcoterm: DAP (Delivery At Place)",
                'qty'              => 1,
                'rate'             => 490.00,
                'unit'             => '',
                'taxname'          => [],
                'order'            => 2,
            ],
        ],
    ],
];
