<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Production invoice — TP Company Limited
 * Source: 2026-INV-104 Commercial Valves
 * Quote Ref → tp_valves
 */
return [
    'key'                   => 'tp_inv_104',
    'source_invoice_number' => '2026-INV-104- Vigor Shipunloader - Valves',
    'source_quote_ref'      => '2 - 2026 - OTMPQ - 102',
    'related_proposal_key'  => 'tp_valves',
    'customer_company'      => 'TP Company Limited',
    'force_status'          => 1,
    'save_option'           => 'otmain_seed_invoice_tp_inv_104',
    'invoice' => [
        'number'               => 104,
        'date'                 => '2026-01-21',
        'duedate'              => '2026-02-11',
        'expiry_days'          => 21,
        'status'               => 1,
        'document_title'       => 'Commercial Invoice',
        'invoice_title'        => 'Vigor Shipunloader - Valves',
        'adminnote'            => '',
        'contact_person_name'  => 'Widia Lestari - Ibrahim',
        'contact_person_email' => 'w.lestari@otmain.com',
        'contact_person_phone' => '+31620020563',
        'payment_terms_text'   => '50% in advance 50% before delivery',
        'delivery_terms'       => '30 Days after the first Term Payment',
        'lead_time'            => '',
        'delivery_address'     => "",
        'availability'         => '',
        'notes'                => '',
        'total_usd_display'    => '$ 4.885,10',
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
                'description'      => 'Valve (without actuator) Size: DN300',
                'long_description' => "Body Ductile Iron With antrust coating\nDisc CF8M\nSeat EPDM High Temp.\nShaft SS431",
                'qty'              => 2,
                'rate'             => 750.00,
                'unit'             => '',
                'taxname'          => [],
                'order'            => 1,
            ],
            2 => [
                'description'      => 'Spare Seat EPDM High Temp.',
                'long_description' => "",
                'qty'              => 4,
                'rate'             => 80.00,
                'unit'             => '',
                'taxname'          => [],
                'order'            => 2,
            ],
            3 => [
                'description'      => 'Valve (without actuator) Size: DN350',
                'long_description' => "Body Ductile Iron With antrust coating\nDisc CF8M\nSeat EPDM High Temp.\nShaft SS431",
                'qty'              => 2,
                'rate'             => 925.00,
                'unit'             => '',
                'taxname'          => [],
                'order'            => 3,
            ],
            4 => [
                'description'      => 'Spare Seat EPDM High Temp.',
                'long_description' => "",
                'qty'              => 4,
                'rate'             => 121.00,
                'unit'             => '',
                'taxname'          => [],
                'order'            => 4,
            ],
        ],
    ],
];
