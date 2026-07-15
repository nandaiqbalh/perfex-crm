<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Production invoice — TP Company Limited
 * Source: 2026-INV-112 Atlas Copco Element
 * Quote Ref → tp_otmsq_109
 */
return [
    'key'                   => 'tp_inv_112',
    'source_invoice_number' => '2026-INV-112 - Atlas Copco Element - Vigor Shipunloader',
    'source_quote_ref'      => '9 - 2026 - OTMSQ - 109 - Vigor Ship Unloader',
    'related_proposal_key'  => 'tp_otmsq_109',
    'customer_company'      => 'TP Company Limited',
    'force_status'          => 1,
    'save_option'           => 'otmain_seed_invoice_tp_inv_112',
    'invoice' => [
        'number'               => 112,
        'date'                 => '2026-03-17',
        'duedate'              => '2026-03-18',
        'expiry_days'          => 1,
        'status'               => 1,
        'document_title'       => 'Commercial Invoice',
        'invoice_title'        => 'Atlas Copco Element - Vigor Shipunloader',
        'adminnote'            => '',
        'contact_person_name'  => 'Widia Lestari - Ibrahim',
        'contact_person_email' => 'w.lestari@otmain.com',
        'contact_person_phone' => '+31620020563',
        'payment_terms_text'   => '50% in Advance,<br />50% before Shipment',
        'delivery_terms'       => '5-7 workdays',
        'lead_time'            => '',
        'delivery_address'     => "",
        'availability'         => '',
        'notes'                => 'PLEASE MENTION WITH YOUR TRANSACTION OUR INVOICE NUMBER',
        'total_usd_display'    => '$ 97.353,00',
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
                'description'      => 'Atlas Copco Element – PN: 1616635681',
                'long_description' => "Standard Delivery\nCondition: New – OEM\nDelivery Time: 5 to 7 workdays after payment\nEXW. China",
                'qty'              => 1,
                'rate'             => 83200.00,
                'unit'             => '',
                'taxname'          => [],
                'order'            => 1,
            ],
        ],
    ],
];
