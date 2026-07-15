<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Purchase Order — PT.Agung Buana Sentosa (issuer OT-Project(x))
 * Source: 2026-PO-108 / Supplier Quote 3VA1450-4EF32-0AA0
 * Our Ref.: 10.2025-SQ-109 → 10.2025-SQ-109-vigor-vacuum-panel
 * Subtotal IDR 8.710.000 + VAT 11% → IDR 9.668.100
 */
return [
    'key'                  => 'po_108_agung_mccb',
    'source_po_number'     => '2026-PO-108',
    'number'               => 108,
    'currency_code'        => 'IDR',
    'supplier_company'     => 'PT.Agung Buana Sentosa',
    'related_proposal_key' => '10.2025-SQ-109-vigor-vacuum-panel',
    'save_option'          => 'otmain_seed_po_108_agung_mccb',
    'purchase_order'       => [
        'document_title'         => 'Purchase Order',
        'date'                   => '2026-07-10',
        'supplier_quote_ref'     => '3VA1450-4EF32-0AA0',
        'contact_person'         => "Sha'Iez Ibrahim",
        'email'                  => 's.ibrahim@otmain.com',
        'phone'                  => '+628136375490',
        'company_name'           => 'OT-Project(x)',
        'company_address'        => 'Belleza BSA 1st Floor Jalan Letjen Soepeno Permata Hijau',
        'company_postal_code'    => '',
        'company_city'           => 'South Jakarta',
        'company_country'        => 'Indonesia',
        'company_phone'          => '+628136375490',
        'company_email_invoices' => 'inv@otmain.com',
        'company_website'        => 'www.otprojectx.com',
        'company_vat'            => '10.000.000.0-064.312',
        'company_coc'            => '603250087245',
        'iban'                   => '',
        'adminnote'              => "Delivery Address:\nReceiver: Ma\nContact no.: +86 13600411747\nAddress: Room 401, No. 29-1, Xintangkeng Road,\nSilian Community, Henggang Subdistrict,\nLonggang District, Shenzhen City, P.R.China\n(Shenzhen Rongtai Automation Technology Co., LTD,)\npostcode: 518115",
        'items'                  => [
            [
                'description' => "3VA1450-4EF32-0AA0\nMCCB_IEC_FS630_500A_3P_36KA_TM_ ATAM",
                'qty'         => 1,
                'unit_price'  => 8710000.00,
                'taxrate'     => 11,
                'taxname'     => 'VAT',
            ],
        ],
    ],
];
