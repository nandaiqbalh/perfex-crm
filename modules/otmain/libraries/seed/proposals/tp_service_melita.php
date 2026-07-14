<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Production quotation — CemFlexX B.V.
 * Source PDF: 5 - 2026 - OTMSQ - 105 - Service Trip Melita Vessel (31-01-2026)
 *
 * Service trip for Self-Discharging Cement System – Melita.
 * Includes VAT 21%.
 * Address / company phone / email diisi orchestrator dari tblclients + primary contact.
 */
return [
    'key'                 => 'tp_service_melita',
    'source_quote_number' => '5 - 2026 - OTMSQ - 105 - Service Trip Melita Vessel',
    'customer_company'    => 'CemFlexX B.V.',
    'force_status'        => 3, // Accepted
    'populate_tracker'    => true,
    'save_option'         => 'otmain_seed_proposal_tp_service_melita',
    'tracker_updates'     => [
        0 => [
            'item_status' => 'pending',
            'notes'       => '',
            'admin_notes' => '',
        ],
    ],
    'proposal' => [
        'subject'              => 'Service Trip Melita Vessel - Quotation',
        'date'                 => '2026-01-31',
        'open_till'            => '2026-02-01',
        'status'               => 3,
        'rel_type'             => 'customer',
        'client_ref'           => '',
        'quote_title'          => 'Service Trip Melita Vessel',
        'document_title'       => 'Quotation',
        'terms'                => '',
        'expiry_days'          => 1,
        'availability'         => '',
        'notes'                => '',
        // PDF deal contact (optional override)
        'contact_person_name'  => 'S.A.Ibrahim',
        'contact_person_email' => 's.ibrahim@otmain.com',
        'contact_person_phone' => '+316****8651',
        'payment_terms_text'   => '30 days after Invoice.',
        'shipment_terms'       => '',
        'delivery_time'        => '',
        'total_usd_display'    => '$ 3.624,48',
        'total_gold_display'   => '999.9 in Gram',
        'subtotal'             => 2490,
        'total_tax'            => 522.90,
        'total'                => 3012.90,
        'discount_type'        => '',
        'discount_percent'     => 0,
        'discount_total'       => 0,
        'show_quantity_as'     => 1,
        'allow_comments'       => 1,
        'newitems'             => [
            1 => [
                'description'      => 'Self-Discharging Cement System – Melita Service Trip',
                'long_description' => 'Scope of Work:
• Providing on-site instructions to the crew regarding operation of the Self-Discharging Cement System
• Sharing practical tips and best practices for an efficient stripping process
• Preparation of a short service report after completion of the visit

The service package includes:
• Meals, Accommodation, Ground transportation

Excluded:
• Flight rescheduling costs, Service Fee
Extra workday: €350 per working day',
                'qty'              => 1,
                'rate'             => 2490,
                'unit'             => 'unit',
                'taxname'          => ['VAT|21'],
                'order'            => 1,
            ],
        ],
    ],
];
