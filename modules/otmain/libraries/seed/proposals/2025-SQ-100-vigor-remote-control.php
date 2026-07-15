<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Production quotation — TP Company Limited (2025)
 * Source: 2. Spare parts orders / 2025-SQ-100 - Vigor Shipunloader - Remote Control
 * PDF: 2025-SQ-100 -rev.1- Vigor Shipunloader - Remote Control.pdf (27-2-2025)
 *
 * Earlier rev (21-2-2025, €2.941,54) superseded by rev.1 (€2.864,00) — same quote number.
 * Not in Sales Quotations folder seq 1–11 (spare-parts track).
 * Buyer = TP Company Limited; OT-MAIN right block = issuer noise.
 */
return [
    'key'                 => '2025-SQ-100-vigor-remote-control',
    'source_quote_number' => '2025-SQ-100',
    'aliases'             => [
        '2025-SQ-100',
        '2025-SQ-100 - Vigor Shipunloader - Remote Control',
        '2025-SQ-100 -rev.1- Vigor Shipunloader - Remote Control',
        '2025-SQ-100-vigor-remote-control',
        'tp_sq_100_vigor_remote',
    ],
    'customer_company'    => 'TP Company Limited',
    'force_status'        => 3,
    'populate_tracker'    => true,
    'save_option'         => 'otmain_seed_proposal_2025_sq_100_vigor_remote_control',
    'tracker_updates'     => [
        0 => ['item_status' => 'pending', 'notes' => '', 'admin_notes' => ''],
    ],
    'proposal' => [
        'subject'              => 'Back-Up Remote Control - Vigor - Quotation',
        'date'                 => '2025-02-27',
        'open_till'            => '2025-03-10',
        'status'               => 3,
        'rel_type'             => 'customer',
        'client_ref'           => 'Back-Up Remote Control',
        'quote_title'          => 'Back-Up Remote Control',
        'document_title'       => 'Quotation',
        'terms'                => '',
        'expiry_days'          => 11,
        'availability'         => '',
        'notes'                => 'Shipping can be arranged and will be added in the second invoice.',
        'contact_person_name'  => 'S.A.Ibrahim',
        'contact_person_email' => 'sales@otmain.com',
        'contact_person_phone' => '+31618228651',
        'payment_terms_text'   => '50% upon placing the Purchase Order<br />50% before shipment',
        'shipment_terms'       => 'EXW (Ex Works)',
        'delivery_time'        => 'Lead time: 7 weeks',
        'total_usd_display'    => '',
        'total_gold_display'   => '',
        'subtotal'             => 2864.00,
        'total_tax'            => 0,
        'total'                => 2864.00,
        'discount_type'        => '',
        'discount_percent'     => 0,
        'discount_total'       => 0,
        'show_quantity_as'     => 1,
        'allow_comments'       => 1,
        'newitems'             => [
            1 => [
                'description'      => 'Back - Up Remote Control - Vigor',
                'long_description' => "The Scanreco remote control includes the added function.\n• Wirreless Remote control",
                'qty'              => 1,
                'rate'             => 2864.00,
                'unit'             => '',
                'taxname'          => [],
                'order'            => 1,
            ],
        ],
    ],
];
