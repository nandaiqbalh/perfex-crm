<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Production quotation — TP Company Limited
 * Source PDF: 2 - 2026 - OTMPQ - 102 - Valves (07-01-2026)
 *
 * Address / company phone / email diisi orchestrator dari tblclients + primary contact.
 * contact_person_* di bawah = override dari PDF (deal contact); kosongkan agar pakai primary contact.
 */
return [
    'key'                 => 'tp_valves',
    'source_quote_number' => '2 - 2026 - OTMPQ - 102 - Valves',
    'customer_company'    => 'TP Company Limited',
    'force_status'        => 3, // Accepted
    'populate_tracker'    => true,
    'save_option'         => 'otmain_seed_proposal_tp_valves',
    'tracker_updates'     => [
        0 => [
            'item_status' => 'pending',
            'notes'       => '',
            'admin_notes' => '',
        ],
        1 => [
            'item_status' => 'pending',
            'notes'       => '',
            'admin_notes' => '',
        ],
        2 => [
            'item_status' => 'pending',
            'notes'       => '',
            'admin_notes' => '',
        ],
        3 => [
            'item_status' => 'pending',
            'notes'       => '',
            'admin_notes' => '',
        ],
    ],
    'proposal' => [
        'subject'              => 'DN300 & DN350 Valves - Quotation',
        'date'                 => '2026-01-07',
        'open_till'            => '2026-01-17',
        'status'               => 3,
        'rel_type'             => 'customer',
        'client_ref'           => '',
        'quote_title'          => 'Valves',
        'document_title'       => 'Quotation',
        'terms'                => '',
        'expiry_days'          => 10,
        'availability'         => '',
        'notes'                => '',
        // PDF deal contact (optional override; leave empty to use primary contact)
        'contact_person_name'  => 'S.A.Ibrahim',
        'contact_person_email' => 's.ibrahim@otmain.com',
        'contact_person_phone' => '+31618228651',
        'payment_terms_text'   => '50% in advance<br />50% before delivery',
        'shipment_terms'       => 'EXW - Rotterdam sea transport can be arranged',
        'delivery_time'        => '4 to 6 weeks',
        'total_usd_display'    => '',
        'total_gold_display'   => '',
        'subtotal'             => 4154,
        'total_tax'            => 0,
        'total'                => 4154,
        'discount_type'        => '',
        'discount_percent'     => 0,
        'discount_total'       => 0,
        'show_quantity_as'     => 1,
        'allow_comments'       => 1,
        'newitems'             => [
            1 => [
                'description'      => 'Valve (without actuator) Size: DN300',
                'long_description' => 'Body Ductile Iron With antrust coating
Disc CF8M
Seat EPDM High Temp.
Shaft SS431',
                'qty'              => 2,
                'rate'             => 750,
                'unit'             => 'unit',
                'taxname'          => [],
                'order'            => 1,
            ],
            2 => [
                'description'      => 'Spare Seat EPDM High Temp. (for DN300)',
                'long_description' => 'Spare seat for DN300 valve',
                'qty'              => 4,
                'rate'             => 80,
                'unit'             => 'pcs',
                'taxname'          => [],
                'order'            => 2,
            ],
            3 => [
                'description'      => 'Valve (without actuator) Size: DN350',
                'long_description' => 'Body Ductile Iron With antrust coating
Disc CF8M
Seat EPDM High Temp.
Shaft SS431',
                'qty'              => 2,
                'rate'             => 925,
                'unit'             => 'unit',
                'taxname'          => [],
                'order'            => 3,
            ],
            4 => [
                'description'      => 'Spare Seat EPDM High Temp. (for DN350)',
                'long_description' => 'Spare seat for DN350 valve',
                'qty'              => 4,
                'rate'             => 121,
                'unit'             => 'pcs',
                'taxname'          => [],
                'order'            => 4,
            ],
        ],
    ],
];
