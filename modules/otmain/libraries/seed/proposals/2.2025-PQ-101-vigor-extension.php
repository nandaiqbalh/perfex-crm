<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Production quotation — TP Company Limited (2025)
 * Source folder: 2.2025-PQ- 101-Vigor Extension
 * Quote Number: 2025-PQ-101 (14-5-2025)
 *
 * Buyer = TP Company Zanzibar; OT-MAIN = issuer noise.
 */
return [
    'key'                 => '2.2025-PQ-101-vigor-extension',
    'source_quote_number' => '2 - 2025 - PQ - 101 - Vigor Extension',
    'aliases'             => [
        '2 - 2025 - PQ - 101 - Vigor Extension',
        '2025-PQ-101',
        '2.2025-PQ- 101-Vigor Extension',
        '2025-PQ- 101',
    ],
    'customer_company'    => 'TP Company Limited',
    'force_status'        => 3,
    'populate_tracker'    => true,
    'save_option'         => 'otmain_seed_proposal_2_2025_pq_101_vigor_extension',
    'tracker_updates'     => array_fill(0, 2, ['item_status' => 'pending', 'notes' => '', 'admin_notes' => '']),
    'proposal' => [
        'subject'              => 'Extension Shipunloader Zanzibar - Quotation',
        'date'                 => '2025-05-14',
        'open_till'            => '2025-05-17',
        'status'               => 3,
        'rel_type'             => 'customer',
        'client_ref'           => 'Extension ShipUnloader Zanzibar',
        'quote_title'          => 'PQ-101',
        'document_title'       => 'Quotation',
        'terms'                => '',
        'expiry_days'          => 3,
        'availability'         => '',
        'notes'                => "Period: 19 May 2025 to 27 May 2025.\nOptional Discount: An €800 discount will be applied if hotel accommodations are arranged by Vigor.\nAdditional Workday Rate: €400 per ekstra workday.",
        'contact_person_name'  => 'S.A.Ibrahim',
        'contact_person_email' => 'sales@otmain.com',
        'contact_person_phone' => '+31618228651',
        'payment_terms_text'   => "100% of the Payment of the PLC Spare Parts - What is delivered already\n50% upon placing the Purchase Order\n50% Before leaving Zanzibar",
        'shipment_terms'       => '',
        'delivery_time'        => '26 May 2025',
        'total_usd_display'    => '',
        'total_gold_display'   => '',
        'subtotal'             => 6460.00,
        'total_tax'            => 0,
        'total'                => 6460.00,
        'discount_type'        => '',
        'discount_percent'     => 0,
        'discount_total'       => 0,
        'show_quantity_as'     => 1,
        'allow_comments'       => 1,
        'newitems'             => [
            1 => [
                'description'      => 'Travel and Hotel Rescheduling Costs',
                'long_description' => "Additional costs are expected for rescheduling following travel and accommodation\nZanzibar → Amsterdam\nAmsterdam → Jakarta\nJakarta → Padang\nPadang → Jakarta\nJakarta → Amsterdam\nHotel bookings will also need to be adjusted, which may incur extra charges. Please note that these are estimated costs and subject to change. Any price differences will be recalculated and either refunded or invoiced accordingly.",
                'qty'              => 1,
                'rate'             => 3000.00,
                'unit'             => '',
                'taxname'          => [],
                'order'            => 1,
            ],
            2 => [
                'description'      => 'Extension Package (until 27 May 2025)',
                'long_description' => "This package includes Day Rate, Meals & Accommodation.\nPackage Details:\nTotal duration: 10 nights\nWorking days: 3\nWaiting days: 6",
                'qty'              => 1,
                'rate'             => 3460.00,
                'unit'             => '',
                'taxname'          => [],
                'order'            => 2,
            ],
        ],
    ],
];
