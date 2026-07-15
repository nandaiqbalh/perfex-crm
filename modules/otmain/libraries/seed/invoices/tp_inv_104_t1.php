<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Production invoice — TP Company Limited
 * Source: 2026-INV-104-T1 Valves (50%)
 * Quote Ref → tp_valves
 */
return [
    'key'                   => 'tp_inv_104_t1',
    'source_invoice_number' => '2026-INV-104-T1-Vigor Shipunloader-Valves',
    'source_quote_ref'      => '2 - 2026 - OTMPQ - 102 - Valves',
    'related_proposal_key'  => 'tp_valves',
    'customer_company'      => 'TP Company Limited',
    'force_status'          => 1,
    'save_option'           => 'otmain_seed_invoice_tp_inv_104_t1',
    'invoice' => [
        'number'               => 104,
        'date'                 => '2026-01-11',
        'duedate'              => '2026-01-26',
        'expiry_days'          => 15,
        'status'               => 1,
        'document_title'       => 'Invoice',
        'invoice_title'        => 'T1-Vigor Shipunloader-Valves',
        'adminnote'            => '',
        'contact_person_name'  => 'Widia Lestari - Ibrahim',
        'contact_person_email' => 'w.lestari@otmain.com',
        'contact_person_phone' => '+31620020563',
        'payment_terms_text'   => 'First Term: 50% by Order<br />50% before delivery',
        'delivery_terms'       => 'EXW - After 30 Days after the first Term Payment',
        'lead_time'            => '',
        'delivery_address'     => "TP Company Limited\nBumbwini\nPO BOX 271 Zanzibar",
        'availability'         => '',
        'notes'                => '',
        'total_usd_display'    => '$ 2.473,67',
        'bank_account'         => 'EUR',
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
                'description'      => 'Valve (without actuator) Size: DN300 / DN350',
                'long_description' => "First Payment: 50% of €4154,00 - Vigor Ship Unloader – Valves\nReference: 2 - 2026 - OTMPQ - 102 -\nValve DN300 including spare parts / Spare Seat EPDM High Temperature\nValve DN350 including spare parts / Spare Seat EPDM High Temperature.",
                'qty'              => 1,
                'rate'             => 2077.00,
                'unit'             => '',
                'taxname'          => [],
                'order'            => 1,
            ],
        ],
    ],
];
