<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Production invoice — CemFlexX B.V.
 * Source: 2025-INV-111 Kovako Waterford
 * Quote Ref: 2025-PQ-102 rev.1 → 3.2025-PQ-102-cemflexx-kovako-waterford
 */
return [
    'key'                   => 'cx_inv_111',
    'source_invoice_number' => '2025-INV-111',
    'source_quote_ref'      => '2025-PQ-102 rev.1',
    'related_proposal_key'  => '3.2025-PQ-102-cemflexx-kovako-waterford',
    'customer_company'      => 'CemFlexX B.V.',
    'force_status'          => 1,
    'save_option'           => 'otmain_seed_invoice_cx_inv_111',
    'invoice' => [
        'number'               => 111,
        'date'                 => '2025-02-27',
        'duedate'              => '2025-03-26',
        'expiry_days'          => 27,
        'status'               => 1,
        'document_title'       => 'Invoice',
        'invoice_title'        => 'Kovako - Waterford',
        'adminnote'            => 'Kovako inspection - Waterford Ireland',
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
                'description'      => 'Inspectie Kovako M400 en Kovako M275',
                'long_description' => "Scope van Werkzaamheden :\n1. Het uitvoeren van een inspectie van de Kovako-scheepslosser voor herstel\n2. Report maken\n\nHet weergegeven tarief omvat arbeidskosten, maaltijden, grondtransport (autohuur) en overige verblijfskosten",
                'qty'              => 1,
                'rate'             => 2113.00,
                'unit'             => '',
                'taxname'          => ['VAT|21'],
                'order'            => 1,
            ],
        ],
    ],
];
