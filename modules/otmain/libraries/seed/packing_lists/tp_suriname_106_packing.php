<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Production packing list — Suriname Shiphandling & Services NV (Henna-T)
 * Source PDF: Packing List & Invoice referencing 6 - 2026 - OTMSQ - 106 - Henna-T
 * 6 commercial items (valves seals) + 1 shared box packing.
 * Vessel: Henna-T.
 */
return [
    'key'                   => 'tp_suriname_106_packing',
    'customer_company'      => 'Suriname Shiphandling & Services NV',
    'related_proposal_keys' => [
        '6 - 2026 - OTMSQ - 106 - Henna-T',
    ],
    'save_option'           => 'otmain_seed_packing_suriname_106',
    'packing' => [
        'document_title'       => 'Packing List & Invoice',
        'date'                 => '',
        'vessel'               => 'Henna-T',
        // contact PDF — ensure di tblcontacts (tanpa replace)
        'contact_person_name'  => 'Jerrel',
        'contact_person_email' => 'jerrel@rudisa.net',
        'contact_person_phone' => '+597****2726',
        // PDF: Subtotal EUR 7.600,90 / Subtotal USD 9.206,09
        'subtotal_usd'         => 9206.09,
        'conversion_rate'      => 1.211187, // 9206.09 / 7600.90
        'adminnote'            => 'No re-export to Russia or Belarus in accordance with Article 12G of EU Regulation 833/2014.',
    ],
    'items' => [
        [
            'description'   => 'Size: DN200 - Material: Red Silicone - Seat for Compressed Air Valve',
            'hs_code'       => '',
            'qty'           => 4,
            'unit_price'    => 390.83,
            'taxrate'       => 0,
            // Shared box — all dims on first item
            'packing_qty'   => 1,
            'unit_type'     => 'other',
            'unit_label'    => 'Box',
            'length'        => 590,   // 59 cm = 590 mm
            'width'         => 495,   // 49.5 cm = 495 mm
            'height'        => 390,   // 39 cm = 390 mm
            'gross_weight'  => 15.6,
            'net_weight'    => 14,
        ],
        [
            'description'   => 'Size: DN80 - Material: Red Silicone - Seat for Compressed Air Regulator Valve',
            'hs_code'       => '',
            'qty'           => 1,
            'unit_price'    => 129.47,
            'taxrate'       => 0,
        ],
        [
            'description'   => 'Size: DN350 - Material: Red Silicone - Seat for Suction Transfer Inlet or Discharge Valve',
            'hs_code'       => '',
            'qty'           => 8,
            'unit_price'    => 693.33,
            'taxrate'       => 0,
        ],
        [
            'description'   => 'Size: DN250 - Material: Black EPDM - Seat for Vacuum Valve',
            'hs_code'       => '',
            'qty'           => 1,
            'unit_price'    => 205.38,
            'taxrate'       => 0,
        ],
        [
            'description'   => 'Size: DN80 - Material: Black EPDM - Seat for Fluidization Valves Beds',
            'hs_code'       => '',
            'qty'           => 1,
            'unit_price'    => 50.82,
            'taxrate'       => 0,
        ],
        [
            'description'   => 'Size: DN150 - Material: Black EPDM - Seat for Fluidization Valves Beds',
            'hs_code'       => '',
            'qty'           => 1,
            'unit_price'    => 105.27,
            'taxrate'       => 0,
        ],
    ],
];
