<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Production quotation — Suriname Shiphandling & Services NV
 * Source PDF: 6 - 2026 - OTMSQ - 106 - Henna-T (09-02-2026)
 *
 * Spare valves seals for Henna-T: Red Silicone + Black EPDM seats.
 * Address / company phone / email diisi orchestrator dari tblclients + primary contact.
 */
return [
    'key'                 => 'tp_otmsq_106',
    'source_quote_number' => '6 - 2026 - OTMSQ - 106 - Henna-T',
    'customer_company'    => 'Suriname Shiphandling & Services NV',
    'force_status'        => 3, // Accepted
    'populate_tracker'    => true,
    'save_option'         => 'otmain_seed_proposal_tp_otmsq_106',
    'tracker_updates'     => [
        0 => ['item_status' => 'pending', 'notes' => '', 'admin_notes' => ''],
        1 => ['item_status' => 'pending', 'notes' => '', 'admin_notes' => ''],
        2 => ['item_status' => 'pending', 'notes' => '', 'admin_notes' => ''],
        3 => ['item_status' => 'pending', 'notes' => '', 'admin_notes' => ''],
        4 => ['item_status' => 'pending', 'notes' => '', 'admin_notes' => ''],
        5 => ['item_status' => 'pending', 'notes' => '', 'admin_notes' => ''],
    ],
    'proposal' => [
        'subject'              => 'Valves Seals - Henna-T - Quotation',
        'date'                 => '2026-02-09',
        'open_till'            => '2026-03-01',
        'status'               => 3,
        'rel_type'             => 'customer',
        'client_ref'           => '',
        'quote_title'          => 'Valves Seals - Henna-T',
        'document_title'       => 'Quotation',
        'terms'                => '',
        'expiry_days'          => 20,
        'availability'         => '',
        'notes'                => '',
        // PDF deal contact (optional override)
        'contact_person_name'  => 'S.A.Ibrahim',
        'contact_person_email' => 's.ibrahim@otmain.com',
        'contact_person_phone' => '+31618228651',
        'payment_terms_text'   => '50% in Advance,<br />50% before Shipment',
        'shipment_terms'       => 'EXW',
        'delivery_time'        => 'Within one week',
        'total_usd_display'    => '$ 9.206,09',
        'total_gold_display'   => '',
        'subtotal'             => 7600.90,
        'total_tax'            => 0,
        'total'                => 7600.90,
        'discount_type'        => '',
        'discount_percent'     => 0,
        'discount_total'       => 0,
        'show_quantity_as'     => 1,
        'allow_comments'       => 1,
        'newitems'             => [
            1 => [
                'description'      => 'Seat for Compressed Air Valve DN200',
                'long_description' => 'Size: DN200
Material: Red Silicone
Seat for Compressed Air Valve

Spare Valves Seals - Henna-T',
                'qty'              => 4,
                'rate'             => 390.83,
                'unit'             => 'unit',
                'taxname'          => [],
                'order'            => 1,
            ],
            2 => [
                'description'      => 'Seat for Compressed Air Regulator Valve DN80',
                'long_description' => 'Size: DN80
Material: Red Silicone
Seat for Compressed Air Regulator Valve

Spare Valves Seals - Henna-T',
                'qty'              => 1,
                'rate'             => 129.47,
                'unit'             => 'unit',
                'taxname'          => [],
                'order'            => 2,
            ],
            3 => [
                'description'      => 'Seat for Suction Transfer Inlet or Discharge Valve DN350',
                'long_description' => 'Size: DN350
Material: Red Silicone
Seat for Suction Transfer Inlet or Discharge Valve

Spare Valves Seals - Henna-T',
                'qty'              => 8,
                'rate'             => 693.33,
                'unit'             => 'unit',
                'taxname'          => [],
                'order'            => 3,
            ],
            4 => [
                'description'      => 'Seat for Vacuum Valve DN250',
                'long_description' => 'Size: DN250
Material: Black EPDM
Seat for Vacuum Valve

Spare Valves Seals - Henna-T',
                'qty'              => 1,
                'rate'             => 205.38,
                'unit'             => 'unit',
                'taxname'          => [],
                'order'            => 4,
            ],
            5 => [
                'description'      => 'Seat for Fluidization Valves Beds DN80',
                'long_description' => 'Size: DN80
Material: Black EPDM
Seat for Fluidization Valves Beds

Spare Valves Seals - Henna-T',
                'qty'              => 1,
                'rate'             => 50.82,
                'unit'             => 'unit',
                'taxname'          => [],
                'order'            => 5,
            ],
            6 => [
                'description'      => 'Seat for Fluidization Valves Beds DN150',
                'long_description' => 'Size: DN150
Material: Black EPDM
Seat for Fluidization Valves Beds

Spare Valves Seals - Henna-T',
                'qty'              => 1,
                'rate'             => 105.27,
                'unit'             => 'unit',
                'taxname'          => [],
                'order'            => 6,
            ],
        ],
    ],
];
