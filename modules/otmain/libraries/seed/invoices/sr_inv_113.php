<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Production invoice — Suriname Shiphandling & Services NV
 * Source: 2026-INV-113 ABB Contactor Rudisa
 * Quote Ref → tp_otmsq_111
 */
return [
    'key'                   => 'sr_inv_113',
    'source_invoice_number' => '2026-INV-113 - ABB Contactor - Rudisa',
    'source_quote_ref'      => '11 - 2026 - OTMSQ - 111 - ABB Contactor - Rudisa',
    'related_proposal_key'  => 'tp_otmsq_111',
    'customer_company'      => 'Suriname Shiphandling & Services NV',
    'force_status'          => 1,
    'save_option'           => 'otmain_seed_invoice_sr_inv_113',
    'invoice' => [
        'number'               => 113,
        'date'                 => '2026-03-18',
        'duedate'              => '2026-03-19',
        'expiry_days'          => 1,
        'status'               => 1,
        'document_title'       => 'Commercial Invoice',
        'invoice_title'        => 'ABB Contactor - Rudisa',
        'adminnote'            => '',
        'contact_person_name'  => 'Widia Lestari - Ibrahim',
        'contact_person_email' => 'w.lestari@otmain.com',
        'contact_person_phone' => '+31620020563',
        'payment_terms_text'   => '100% before Shipment',
        'delivery_terms'       => '1 workdays',
        'lead_time'            => '',
        'delivery_address'     => "",
        'availability'         => '',
        'notes'                => 'PLEASE MENTION WITH YOUR TRANSACTION OUR INVOICE NUMBER',
        'total_usd_display'    => '$ 1.507,78',
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
                'description'      => 'ABB Contactor 4 polig AF190-40',
                'long_description' => "",
                'qty'              => 1,
                'rate'             => 1168.70,
                'unit'             => '',
                'taxname'          => [],
                'order'            => 1,
            ],
            2 => [
                'description'      => 'Express Verzendkosten',
                'long_description' => "",
                'qty'              => 1,
                'rate'             => 120.00,
                'unit'             => '',
                'taxname'          => [],
                'order'            => 2,
            ],
        ],
    ],
];
