<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Production quotation — Suriname Shiphandling & Services NV
 * Source PDF: 8 - 2026 - OTMSQ - 108 - Henna-T (11-03-2026)
 *
 * Spare valves - Henna-T: butterfly valves, check valves, OTPX abrasive valves.
 * Deck Order No 26003.
 */
return [
    'key'                 => 'tp_otmsq_108',
    'source_quote_number' => '8 - 2026 - OTMSQ - 108 - Henna-T',
    'customer_company'    => 'Suriname Shiphandling & Services NV',
    'force_status'        => 3, // Accepted
    'populate_tracker'    => true,
    'save_option'         => 'otmain_seed_proposal_tp_otmsq_108',
    'tracker_updates'     => array_fill(0, 6, ['item_status' => 'pending', 'notes' => '', 'admin_notes' => '']),
    'proposal' => [
        'subject'              => 'Spare Valves - Henna-T - Quotation',
        'date'                 => '2026-03-11',
        'open_till'            => '2026-03-11',
        'status'               => 3,
        'rel_type'             => 'customer',
        'client_ref'           => 'Deck Order No 26003',
        'quote_title'          => 'Henna-T',
        'document_title'       => 'Quotation',
        'terms'                => '',
        'expiry_days'          => 0,
        'availability'         => '',
        'notes'                => '',
        // PDF deal contact (optional override)
        'contact_person_name'  => 'S.A.Ibrahim',
        'contact_person_email' => 's.ibrahim@otmain.com',
        'contact_person_phone' => '+316****8651',
        'payment_terms_text'   => '50% advance payment upon order confirmation, 50% balance prior to shipment.',
        'shipment_terms'       => 'EXW (Ex Works)',
        'delivery_time'        => '7-10 weeks after receipt of advance payment and final order confirmation.',
        'total_usd_display'    => '$ 55.983,60',
        'total_gold_display'   => '',
        'subtotal'             => 46260,
        'total_tax'            => 0,
        'total'                => 46260,
        'discount_type'        => '',
        'discount_percent'     => 0,
        'discount_total'       => 0,
        'show_quantity_as'     => 1,
        'allow_comments'       => 1,
        'newitems'             => [
            1 => [
                'description'      => 'Warex Butterfly Valve with Pneumatic Actuator DN200',
                'long_description' => 'Type: DKZ 110 DZ
Size: DN 200 / PN10
Seat Material: Red Silicone
Actuator: AirTorque Pneumatic Actuator
Pilot Valve: Norgren 5/2
Feedback: Signal Micro Switch

Spare Valves - Henna-T',
                'qty'              => 10,
                'rate'             => 1050,
                'unit'             => 'unit',
                'taxname'          => [],
                'order'            => 1,
            ],
            2 => [
                'description'      => 'Warex Butterfly Valve with Pneumatic Actuator DN350',
                'long_description' => 'Type: DKZ 110 DZ
Size: DN 350 / PN10
Seat Material: Red Silicone
Actuator: AirTorque Pneumatic Actuator
Pilot Valve: Norgren 5/2
Feedback: Signal Micro Switch

Spare Valves - Henna-T',
                'qty'              => 10,
                'rate'             => 1530,
                'unit'             => 'unit',
                'taxname'          => [],
                'order'            => 2,
            ],
            3 => [
                'description'      => 'Check Valve – Non-Return Type DN80',
                'long_description' => 'Type: Wafer Dual Plate Check Valve
Size: DN80

Spare Valves - Henna-T',
                'qty'              => 7,
                'rate'             => 180,
                'unit'             => 'unit',
                'taxname'          => [],
                'order'            => 3,
            ],
            4 => [
                'description'      => 'Check Valve – Non-Return Type DN125',
                'long_description' => 'Type: Wafer Dual Plate Check Valve
Size: DN125

Spare Valves - Henna-T',
                'qty'              => 7,
                'rate'             => 320,
                'unit'             => 'unit',
                'taxname'          => [],
                'order'            => 4,
            ],
            5 => [
                'description'      => 'OTPX Abrasive Bulk Solids Valve VEX-200 – Custom Design',
                'long_description' => 'Specially designed for handling abrasive bulk solids applications. Supplied complete with pneumatic actuator.
Type: VEX-200
Size: DN 200 / PN10
Seat Material: EPDM – High Temperature
Actuator: Pneumatic Actuator
Pilot Valve: 5/2 Solenoid Pilot Valve
Position Feedback: Signal Micro Switch

Spare Valves - Henna-T',
                'qty'              => 4,
                'rate'             => 940,
                'unit'             => 'unit',
                'taxname'          => [],
                'order'            => 5,
            ],
            6 => [
                'description'      => 'OTPX Abrasive Bulk Solids Valve VEX-350 – Custom Design',
                'long_description' => 'Specially designed for handling abrasive bulk solids applications. Supplied complete with pneumatic actuator.
Type: VEX-350
Size: DN 350 / PN10
Seat Material: HNBR
Actuator: AirTorque Pneumatic Actuator
Pilot Valve: 5/2 Solenoid Pilot Valve
Position Feedback: Signal Micro Switch

Installation Note (DN350 only):
Minor pipe modification may be required to install the valve. The filling flange may need to be removed. If removal is not possible, a small section of the pipe may need to be cut to allow proper installation of the DN350 valve.

Spare Valves - Henna-T',
                'qty'              => 10,
                'rate'             => 1320,
                'unit'             => 'unit',
                'taxname'          => [],
                'order'            => 6,
            ],
        ],
    ],
];
