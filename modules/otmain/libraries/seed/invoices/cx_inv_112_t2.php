<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Production invoice — CemFlexX B.V.
 * Source: 2025-INV-112-T2 (Second Termijn + extras)
 * Verzendkosten + sensor = VAT 0%; delivery + remote = VAT 21%
 */
return [
    'key'                   => 'cx_inv_112_t2',
    'source_invoice_number' => '2025-INV-112-T2',
    'source_quote_ref'      => '2025-PQ-100',
    'related_proposal_key'  => '1.2025-PQ-100-cemflexx-vigor-delivery',
    'customer_company'      => 'CemFlexX B.V.',
    'force_status'          => 1,
    'save_option'           => 'otmain_seed_invoice_cx_inv_112_t2',
    'invoice' => [
        'number'               => 112,
        'date'                 => '2025-05-29',
        'duedate'              => '2025-05-24',
        'expiry_days'          => 0,
        'status'               => 1,
        'document_title'       => 'Invoice',
        'invoice_title'        => 'T2',
        'adminnote'            => '',
        'contact_person_name'  => 'S.A.Ibrahim',
        'contact_person_email' => 'inv@otmain.com',
        'contact_person_phone' => '+31618228651',
        'payment_terms_text'   => '',
        'delivery_terms'       => '',
        'lead_time'            => '',
        'delivery_address'     => "CemFlexx BV\nPauvreweg 27\n4879 NK, Etten-Leur\nThe Netherlands",
        'availability'         => '',
        'notes'                => '',
        'total_usd_display'    => '',
        'bank_account'         => 'EUR',
        'show_quantity_as'     => 1,
        'sale_agent'           => 0,
        'discount_type'        => '',
        'discount_percent'     => 0,
        'discount_total'       => 0,
        'adjustment'           => 0,
        'clientnote'           => 'We kindly request to transfer the amount before the Expiration date, mentioning the invoice number. Our general terms and conditions apply to all services.',
        'terms'                => '',
        'newitems'             => [
            1 => [
                'description'      => 'Vigor Shipunloader Delivery',
                'long_description' => "Tweede Termijn van 50% van €12,836\n\nScope van Werkzaamheden:\n1. Assistentie en monitoring tijdens de aflevering\n2. Fijnafstelling en optimalisatie van het system\n3. Afhandeling van openstaande punten en",
                'qty'              => 1,
                'rate'             => 6418.00,
                'unit'             => '',
                'taxname'          => ['VAT|21'],
                'order'            => 1,
            ],
            2 => [
                'description'      => 'Uitbereiding Remote Control met 1 Joystick en 1 Reset knop',
                'long_description' => "",
                'qty'              => 1,
                'rate'             => 406.00,
                'unit'             => '',
                'taxname'          => ['VAT|21'],
                'order'            => 2,
            ],
            3 => [
                'description'      => 'Verzendkosten - Versturen van onderdelen naar Zanzibar',
                'long_description' => "",
                'qty'              => 1,
                'rate'             => 282.47,
                'unit'             => '',
                'taxname'          => [],
                'order'            => 3,
            ],
            4 => [
                'description'      => 'Regen en Wind sensor - 4-20 mA',
                'long_description' => "",
                'qty'              => 1,
                'rate'             => 214.68,
                'unit'             => '',
                'taxname'          => [],
                'order'            => 4,
            ],
        ],
    ],
];
