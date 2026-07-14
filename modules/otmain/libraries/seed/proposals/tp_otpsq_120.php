<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Production quotation — Meuth
 * Source PDF: 20 - 2026 - OTPSQ - 120 - Kovako M120 (29-06-2026)
 *
 * Pre-owned KOVAKO MD120 Mobile Pneumatic Ship Unloader, sold as-is.
 */
return [
    'key'                 => 'tp_otpsq_120',
    'source_quote_number' => '20 - 2026 - OTPSQ - 120 - Kovako M120',
    'customer_company'    => 'Meuth',
    'force_status'        => 3, // Accepted
    'populate_tracker'    => true,
    'save_option'         => 'otmain_seed_proposal_tp_otpsq_120',
    'tracker_updates'     => array_fill(0, 1, ['item_status' => 'pending', 'notes' => '', 'admin_notes' => '']),
    'proposal' => [
        'subject'              => 'KOVAKO MD120 Mobile Pneumatic Ship Unloader - Quotation',
        'date'                 => '2026-06-29',
        'open_till'            => '2026-07-28',
        'status'               => 3,
        'rel_type'             => 'customer',
        'client_ref'           => 'Kovako Shipunload M120',
        'quote_title'          => 'Kovako M120',
        'document_title'       => 'Draft Quotation',
        'terms'                => '',
        'expiry_days'          => 29,
        'availability'         => 'Shipunloader is available for sale.',
        'notes'                => 'The machine is sold as-is, where-is under EXW terms. See Inspection report about the finding of the machine.

Commercial Terms
Incoterms: EXW (Ex Works). Loading assistance can be provided upon request. No refurbishment or commissioning is included.

Optional Services (upon request):
- Complete mechanical and electrical refurbishment
- Supply of all required spare parts and replacement components
- Installation of the refurbished vacuum unit
- Replacement of filters, hoses, sensors, bearings and other wear parts
- Generator inspection, repair or replacement
- Commissioning and start-up supervision
- Operator and maintenance training
- On-site refurbishment at customer facility with assistance of Mechanics of Meuth
- Technical support during installation

Complete logistics, freight forwarding except customs duty (Sea, Road or Air Freight). Delivery under DAP conditions. Delivery under Incoterms other than EXW can be arranged upon request (FCA, FOB, CFR, CIF, DAP).

Remarks: Any refurbishment, commissioning, testing, transportation or installation services are not included in the EXW selling price unless agreed separately.',
        // PDF deal contact (optional override)
        'contact_person_name'  => 'S.A.Ibrahim',
        'contact_person_email' => 's.ibrahim@otmain.com',
        'contact_person_phone' => '+316****8651',
        'payment_terms_text'   => 'To be agreed.',
        'shipment_terms'       => 'EXW (Ex Works)',
        'delivery_time'        => 'Immediate availability, subject to prior sale.',
        'total_usd_display'    => '$ 98.609,00',
        'total_gold_display'   => '',
        'subtotal'             => 85000,
        'total_tax'            => 0,
        'total'                => 85000,
        'discount_type'        => '',
        'discount_percent'     => 0,
        'discount_total'       => 0,
        'show_quantity_as'     => 1,
        'allow_comments'       => 1,
        'newitems'             => [
            1 => [
                'description'      => 'KOVAKO MD120 Mobile Pneumatic Ship Unloader',
                'long_description' => 'The offered equipment is a pre-owned KOVAKO MD120 Mobile Pneumatic Ship Unloader, designed for the unloading of cement and other dry bulk powder materials. The machine features a robust steel construction and proven pneumatic conveying technology, making it a reliable solution for cement terminals and bulk handling facilities.

Based on the latest technical inspection, the machine is generally in good structural condition. Due to a prolonged period of storage, several maintenance and replacement activities are recommended before the unit is returned to full operational service. The inspection confirmed that the main structure, hydraulic system, compressed air system, and several mechanical components remain in good condition. Certain components, including filters, hoses, generator, instrumentation, and consumables, require maintenance or replacement. The vacuum pump has been overhauled previously but has not yet been re-installed.

Key Features:
- Mobile pneumatic ship unloader for cement and dry bulk materials
- Nominal unloading capacity up to 110 TPH (depending on material and conveying distance)
- Suitable for vessels up to approximately maximum 5000 DWT
- Heavy-duty steel construction
- Hydraulic boom system in good operating condition

Scope of Supply:
The machine is supplied as inspected and in its current condition ("AS IS").',
                'qty'              => 1,
                'rate'             => 85000,
                'unit'             => 'unit',
                'taxname'          => [],
                'order'            => 1,
            ],
        ],
    ],
];
