<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Production invoice — CemFlexX B.V.
 * Source: 2025-INV-123-T1 Remote Control Termijn 1
 * Quote Ref: 2025-SQ-102 → 11.2025-SQ-102-cemflexx-remote-control
 */
return [
    'key'                   => 'cx_inv_123_t1',
    'source_invoice_number' => '2025-INV-123-T1',
    'source_quote_ref'      => '2025-SQ-102',
    'related_proposal_key'  => '11.2025-SQ-102-cemflexx-remote-control',
    'customer_company'      => 'CemFlexX B.V.',
    'force_status'          => 1,
    'save_option'           => 'otmain_seed_invoice_cx_inv_123_t1',
    'invoice' => [
        'number'               => 123,
        'date'                 => '2025-11-29',
        'duedate'              => '2025-12-06',
        'expiry_days'          => 7,
        'status'               => 1,
        'document_title'       => 'Invoice',
        'invoice_title'        => 'T1',
        'adminnote'            => 'Vendor Ref.: PO-CF-025-012<br />Factuur Termijn 1',
        'contact_person_name'  => 'Widia Lestari - Ibrahim',
        'contact_person_email' => 'w.lestari@otmain.com',
        'contact_person_phone' => '+31620020563',
        'payment_terms_text'   => '50% upon placing the Purchase Order<br />50% before shipment',
        'delivery_terms'       => 'DAP',
        'lead_time'            => '',
        'delivery_address'     => "CemFlexx BV\nPauvreweg 27\n4879 NK, Etten-Leur\nThe Netherlands",
        'availability'         => '',
        'notes'                => '',
        'total_usd_display'    => '$ 1.595,50',
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
                'description'      => 'Back-up Afstandsbediening voor Vigor Shipunloader',
                'long_description' => "Factuur Termijn 1\n\nOptie 2:\nScanreco afstandsbediening, inclusief de aangepaste functies:\n1x Draadloze afstandsbediening",
                'qty'              => 1,
                'rate'             => 1595.50,
                'unit'             => '',
                'taxname'          => ['VAT|21'],
                'order'            => 1,
            ],
        ],
    ],
];
