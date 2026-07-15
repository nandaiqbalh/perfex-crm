<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Production invoice — CemFlexX B.V.
 * Source: 2026-INV-109 Service Trip Melita
 * Quote Ref PDF OTMSQ-105 → tp_service_melita (OTMPQ-105)
 */
return [
    'key'                   => 'cx_inv_109',
    'source_invoice_number' => '2026-INV-109-Service Trip Melita Vessel - CemflexX',
    'source_quote_ref'      => '5 - 2026 - OTMSQ - 105 - Service Trip Melita Vessel',
    'related_proposal_key'  => 'tp_service_melita',
    'customer_company'      => 'CemFlexX B.V.',
    'force_status'          => 1,
    'save_option'           => 'otmain_seed_invoice_cx_inv_109',
    'invoice' => [
        'number'               => 109,
        'date'                 => '2026-02-07',
        'duedate'              => '2026-03-06',
        'expiry_days'          => 27,
        'status'               => 1,
        'document_title'       => 'Invoice',
        'invoice_title'        => 'Service Trip Melita Vessel - CemflexX',
        'adminnote'            => '',
        'contact_person_name'  => 'Widia Lestari - Ibrahim',
        'contact_person_email' => 'w.lestari@otmain.com',
        'contact_person_phone' => '+31620020563',
        'payment_terms_text'   => '',
        'delivery_terms'       => '',
        'lead_time'            => '',
        'delivery_address'     => "CemFlexX B.V.\nPauvreweg 27\n4879NJ Etten-Leur",
        'availability'         => '',
        'notes'                => '',
        'total_usd_display'    => '$',
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
                'description'      => 'Self-Discharging Cement System – Melita Service Trip',
                'long_description' => "Scope of Work :\nProviding on-site instructions to the crew regarding operation of the Self-Discharging\nSharing practical tips and best practices for an efficient stripping process\nPreparation of a short service report after completion of the visit\n\nThe service package includes:\nMeals, Accommodation, Ground transportation\nExcluded:\nFlight rescheduling costs, Service Fee\nExtra workday: €350 per working day",
                'qty'              => 1,
                'rate'             => 2490.00,
                'unit'             => '',
                'taxname'          => ['VAT|21'],
                'order'            => 1,
            ],
        ],
    ],
];
