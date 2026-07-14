<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Production quotation — CemFlexX B.V. (2025)
 * Source folder: 1.2025-PQ- 100 - CemflexX - Vigor - Delivery
 * Quote Number: 2025-PQ-100 (30-1-2025)
 *
 * Buyer = CemFlexX; OT-MAIN block = issuer noise.
 * Two options listed; subtotal = sum of options.
 */
return [
    'key'                 => '1.2025-PQ-100-cemflexx-vigor-delivery',
    'source_quote_number' => '1 - 2025 - PQ - 100 - CemflexX - Vigor - Delivery',
    'aliases'             => [
        '1 - 2025 - PQ - 100 - CemflexX - Vigor - Delivery',
        '2025-PQ-100',
        '1.2025-PQ- 100 - CemflexX - Vigor - Delivery',
        '2025-PQ- 100',
    ],
    'customer_company'    => 'CemFlexX B.V.',
    'force_status'        => 3,
    'populate_tracker'    => true,
    'save_option'         => 'otmain_seed_proposal_1_2025_pq_100_cemflexx_vigor_delivery',
    'tracker_updates'     => array_fill(0, 2, ['item_status' => 'pending', 'notes' => '', 'admin_notes' => '']),
    'proposal' => [
        'subject'              => 'Vigor Shipunloader Delivery - Quotation',
        'date'                 => '2025-01-30',
        'open_till'            => '2025-02-13',
        'status'               => 3,
        'rel_type'             => 'customer',
        'client_ref'           => 'Vigor Shipunloader Delivery',
        'quote_title'          => 'PQ-100',
        'document_title'       => 'Quotation',
        'terms'                => '',
        'expiry_days'          => 14,
        'availability'         => '',
        'notes'                => "Agenda: 7 t/m 14 april, 16 mei t/m 23 juni staan al geboekt voor andere werkzaamheden.\nTarief voor een ekstra werkdag: €600 inclusief bovengenoemde kosten.",
        'contact_person_name'  => 'S.A.Ibrahim',
        'contact_person_email' => 'sales@otmain.com',
        'contact_person_phone' => '+31618228651',
        'payment_terms_text'   => "Optie 1: Een aanbetaling van 50% bij opdracht, 50% binnen 30 dagen na oplevering systeem.\nOptie 2: Een aanbetaling van 50% bij opdracht, 50% binnen 30 dagen na elke lossing.",
        'shipment_terms'       => '',
        'delivery_time'        => '',
        'total_usd_display'    => '',
        'total_gold_display'   => '',
        'subtotal'             => 18267.00,
        'total_tax'            => 3836.07,
        'total'                => 22103.07,
        'discount_type'        => '',
        'discount_percent'     => 0,
        'discount_total'       => 0,
        'show_quantity_as'     => 1,
        'allow_comments'       => 1,
        'newitems'             => [
            1 => [
                'description'      => 'Optie 1: Maandverblijf in Zanzibar',
                'long_description' => "Vigor Shipunloader Delivery\nScope van Werkzaamheden:\n• Assistentie en monitoring tijdens de lossingen\n• Fijnregeling en optimalisatie van het systeem\n• Afhandeling van openstaande punten en spesifikke klantwensen\n\nVolledige maand in Zanzibar. Pakket: arbeidskosten, reisdocumenten, maaltijden, grondtransport en overige verblijfskosten.\nDetails: Aantal nachten: 31; Aantal werkdagen: 11 (5 + 3 + 3 bij lossingen).",
                'qty'              => 1,
                'rate'             => 12836.00,
                'unit'             => '',
                'taxname'          => ['VAT|21'],
                'order'            => 1,
            ],
            2 => [
                'description'      => 'Optie 2: Optie Flexibel, aanbieding Eerste Lossing',
                'long_description' => "Voor de eerste lossing. Voor volgende lossingen aparte offertes.\nPakket: arbeidskosten, reisdocumenten, maaltijden, grondtransport en overige verblijfskosten.\nDetails: Aantal nachten: 7; Aantal werkdagen: 5.",
                'qty'              => 1,
                'rate'             => 5431.00,
                'unit'             => '',
                'taxname'          => ['VAT|21'],
                'order'            => 2,
            ],
        ],
    ],
];
