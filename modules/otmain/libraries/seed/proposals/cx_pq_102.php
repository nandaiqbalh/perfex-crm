<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Production quotation — CemFlexX B.V. (2025)
 * Source PDF: Quote Number 2025-PQ-102 rev.1 (14-2-2025)
 *
 * Kovako inspection - Waterford Ireland (Kovako M400 ship unloader).
 * Issuer OT-MAIN block on PDF right is noise; buyer = CemFlexX.
 */
return [
    'key'                 => 'cx_pq_102',
    'source_quote_number' => '2025-PQ-102 rev.1',
    'aliases'             => [
        '2025-PQ-102 rev.1',
        '2025-PQ-102',
    ],
    'customer_company'    => 'CemFlexX B.V.',
    'force_status'        => 3, // Accepted
    'populate_tracker'    => true,
    'save_option'         => 'otmain_seed_proposal_cx_pq_102',
    'tracker_updates'     => [
        0 => [
            'item_status' => 'pending',
            'notes'       => '',
            'admin_notes' => '',
        ],
    ],
    'proposal' => [
        'subject'              => 'Kovako inspection - Waterford Ireland - Quotation',
        'date'                 => '2025-02-14',
        'open_till'            => '2025-02-17',
        'status'               => 3,
        'rel_type'             => 'customer',
        'client_ref'           => 'Kovako inspection - Waterford Ireland',
        'quote_title'          => 'PQ-102 rev.1',
        'document_title'       => 'Quotation',
        'terms'                => '',
        'expiry_days'          => 3,
        'availability'         => '',
        'notes'                => "Details van het pakket:\nAantal nachten: 2\nAantal werkdagen: 2 dagen\nEventuele extra kosten (bijv. wijziging vluchtschema, extra overnachtingen) worden apart gefactureerd.\nTarief voor een extra werkdag: €450 inclusief bovengenoemde kosten.\nHet weergegeven tarief omvat arbeidskosten, maaltijden, grondtransport (autohuur), vliegtickets en overige verblijfskosten.\nTarief is gebaseerd voor werkzaamheden van volgend week 17/02/2025 t/m 19/02/2025",
        'contact_person_name'  => 'S.A.Ibrahim',
        'contact_person_email' => 'sales@otmain.com',
        'contact_person_phone' => '+31618228651',
        'payment_terms_text'   => 'Binnen 30 dagen na plaatsen van inkooporder.',
        'shipment_terms'       => '',
        'delivery_time'        => '17/02/2025 t/m 19/02/2025',
        'total_usd_display'    => '',
        'total_gold_display'   => '',
        'subtotal'             => 2113.00,
        'total_tax'            => 443.73,
        'total'                => 2556.73,
        'discount_type'        => '',
        'discount_percent'     => 0,
        'discount_total'       => 0,
        'show_quantity_as'     => 1,
        'allow_comments'       => 1,
        'newitems'             => [
            1 => [
                'description'      => 'Kovako inspection - Waterford Ireland',
                'long_description' => "Scope van Werkzaamheden:\n• Uitvoeren van inspecties van de Kovako-scheepslosser M400\n• Inspectie rapport\n\nDetails van het pakket:\nAantal nachten: 2\nAantal werkdagen: 2 dagen\nTarief extra werkdag: €450\nInclusief: arbeidskosten, maaltijden, grondtransport (autohuur), vliegtickets en overige verblijfskosten.\nPeriode: 17/02/2025 t/m 19/02/2025",
                'qty'              => 1,
                'rate'             => 2113.00,
                'unit'             => '',
                'taxname'          => ['VAT|21'],
                'order'            => 1,
            ],
        ],
    ],
];
