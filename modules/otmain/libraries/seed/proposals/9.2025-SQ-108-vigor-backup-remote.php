<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Production quotation — TP Company Limited (2025)
 * Source PDF: 2. Spare parts orders / 2025-SQ-108- Back-Up Remote Control - Vigor.pdf (13-10-2025)
 *
 * Fills Sales Quotations folder seq 9 (folder name said “Flow Meter” but that folder has no
 * quotation PDF — only process-info sheets). Official quote number/title = Back-Up Remote Control.
 *
 * Option 1 + Option 2 listed; subtotal = sum. Freight line is note only (excluded from totals).
 * Buyer = TP Company Limited; OT-MAIN right block = issuer noise.
 */
return [
    'key'                 => '9.2025-SQ-108-vigor-backup-remote',
    'source_quote_number' => '9 - 2025 - SQ - 108 - Back-Up Remote Control',
    'aliases'             => [
        '9 - 2025 - SQ - 108 - Back-Up Remote Control',
        '2025-SQ-108- Back - Up Remote Control',
        '2025-SQ-108- Back-Up Remote Control',
        '2025-SQ-108- Back-Up Remote Control - Vigor',
        '2025-SQ-108',
        '9.2025-SQ-108- Vigor Shipunloader - Flow Meter', // folder name (no quote PDF there)
        '9.2025-SQ-108-vigor-backup-remote',
        'tp_sq_108_backup_remote',
    ],
    'customer_company'    => 'TP Company Limited',
    'force_status'        => 3,
    'populate_tracker'    => true,
    'save_option'         => 'otmain_seed_proposal_9_2025_sq_108_vigor_backup_remote',
    'tracker_updates'     => array_fill(0, 2, ['item_status' => 'pending', 'notes' => '', 'admin_notes' => '']),
    'proposal' => [
        'subject'              => 'Back-Up Remote Control - Vigor - Quotation',
        'date'                 => '2025-10-13',
        'open_till'            => '2025-11-08',
        'status'               => 3,
        'rel_type'             => 'customer',
        'client_ref'           => 'Back - Up Remote Control',
        'quote_title'          => 'Back-Up Remote Control',
        'document_title'       => 'Quotation',
        'terms'                => '',
        'expiry_days'          => 26,
        'availability'         => '',
        'notes'                => 'Freight charges are excluded.',
        'contact_person_name'  => 'S.A.Ibrahim',
        'contact_person_email' => 'sales@otmain.com',
        'contact_person_phone' => '+31618228651',
        'payment_terms_text'   => '50% by order<br />50% by delivery',
        'shipment_terms'       => 'EXW - We can arrange the shipment by DHL',
        'delivery_time'        => '6 - 8 weeks',
        'total_usd_display'    => '',
        'total_gold_display'   => '',
        'subtotal'             => 6539.66,
        'total_tax'            => 0,
        'total'                => 6539.66,
        'discount_type'        => '',
        'discount_percent'     => 0,
        'discount_total'       => 0,
        'show_quantity_as'     => 1,
        'allow_comments'       => 1,
        'newitems'             => [
            1 => [
                'description'      => 'Option 1: Back - Up Remote Control - Vigor',
                'long_description' => "The Scanreco remote control includes the added function.\nIncluded in this option:\n • Wirreless Remote control",
                'qty'              => 1,
                'rate'             => 2941.54,
                'unit'             => '',
                'taxname'          => [],
                'order'            => 1,
            ],
            2 => [
                'description'      => 'Option 2: Back - Up Remote Control - Vigor',
                'long_description' => "The Scanreco remote control includes the added function.\nIncluded in this option:\n • Wirreless Remote control\n • 2x batteries\n • Neck Belt\n • Fast Charger (3-4 hours) 110-230VAC",
                'qty'              => 1,
                'rate'             => 3598.12,
                'unit'             => '',
                'taxname'          => [],
                'order'            => 2,
            ],
        ],
    ],
];
