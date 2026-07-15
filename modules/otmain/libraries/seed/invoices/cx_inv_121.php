<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Production invoice — CemFlexX B.V.
 * Source: 2025-INV-121-Service Trip-CemFlexX
 * Quote Ref → 4.2025-PQ-103-cemflexx-service-trip
 * VAT on gross €5.431; discount tickets VAT 0
 */
return [
    'key'                   => 'cx_inv_121',
    'source_invoice_number' => '2025-INV-121-Service Trip-CemFlexX',
    'source_quote_ref'      => '2025-PQ-103-Vigor Shipunloader',
    'related_proposal_key'  => '4.2025-PQ-103-cemflexx-service-trip',
    'customer_company'      => 'CemFlexX B.V.',
    'force_status'          => 1,
    'save_option'           => 'otmain_seed_invoice_cx_inv_121',
    'invoice' => [
        'number'               => 121,
        'date'                 => '2025-08-18',
        'duedate'              => '2025-09-17',
        'expiry_days'          => 30,
        'status'               => 1,
        'document_title'       => 'Invoice',
        'invoice_title'        => 'Service Trip-CemFlexX',
        'adminnote'            => '',
        'contact_person_name'  => 'Widia Lestari - Ibrahim',
        'contact_person_email' => 'w.lestari@otmain.com',
        'contact_person_phone' => '+31620020563',
        'payment_terms_text'   => '30 days',
        'delivery_terms'       => 'Ex Works',
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
                'description'      => 'Vigor Shipunloader – Levering en Ondersteuning',
                'long_description' => "• Assistentie en monitoring tijdens\n• Fijnregeling en optimalisatie van het\nAantal werkdagen: 5\nHet pakket omvat: arbeidskosten, reisdocumenten, maaltijden, lucht en grondtransport en logies en overige verblijfskosten",
                'qty'              => 1,
                'rate'             => 5431.00,
                'unit'             => '',
                'taxname'          => ['VAT|21'],
                'order'            => 1,
            ],
            2 => [
                'description'      => 'Vliegtickets betaald door Vigor',
                'long_description' => "",
                'qty'              => 1,
                'rate'             => -741.52,
                'unit'             => '',
                'taxname'          => [],
                'order'            => 2,
            ],
        ],
    ],
];
