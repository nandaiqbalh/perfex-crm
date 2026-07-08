<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Otmain_seed
{
    protected $CI;

    protected $marker = 'otmain_demo_v1';

    public function __construct()
    {
        $this->CI = &get_instance();
        $this->CI->load->helper('otmain/otmain');
        $this->CI->load->model([
            'clients_model',
            'estimates_model',
            'invoices_model',
            'proposals_model',
            'currencies_model',
            'purchase_order_model',
            'packing_list_model',
        ]);
    }

    public function run($force = false)
    {
        if (!$force && get_option('otmain_demo_seed_marker') === $this->marker) {
            return [
                'status'  => 'skipped',
                'message' => 'Demo data already exists. Add ?force=1 to recreate.',
                'links'   => $this->links(),
            ];
        }

        if ($force) {
            $this->cleanup();
        }

        $eurId = $this->getCurrencyId('EUR');
        $usdId = $this->getCurrencyId('USD');

        $buyerPayload = [
            'company'               => 'Handelsmij SPT b.v.',
            'phonenumber'           => '+31618228651',
            'address'               => 'Bajonetstraat 52',
            'city'                  => 'Rotterdam',
            'zip'                   => '3014ZK',
            'country'               => 155,
            'billing_street'        => 'Bajonetstraat 52',
            'billing_city'          => 'Rotterdam',
            'billing_zip'           => '3014ZK',
            'billing_country'       => 155,
            'default_currency'      => $eurId,
            'firstname'             => 'S.A.',
            'lastname'              => 'Ibrahim',
            'email'                 => 's.ibrahim@otmain.com',
            'is_primary'            => 1,
            'donotsendwelcomeemail' => 1,
        ];
        $buyerId = $this->ensureClient($buyerPayload);

        $invoiceClientPayload = [
            'company'               => 'Suriname Shiphandling & Services NV',
            'phonenumber'           => '+31618228651',
            'address'               => 'Ds Martin Luther Kingweg 8-9',
            'city'                  => 'Paramaribo',
            'zip'                   => '',
            'country'               => 103,
            'billing_street'        => 'Ds Martin Luther Kingweg 8-9',
            'billing_city'          => 'Paramaribo',
            'billing_country'       => 103,
            'default_currency'      => $eurId,
            'firstname'             => 'Widia',
            'lastname'              => 'Lestari',
            'email'                 => 'w.lestari@otmain.com',
            'is_primary'            => 1,
            'donotsendwelcomeemail' => 1,
        ];
        $invoiceClientId = $this->ensureClient($invoiceClientPayload);

        $packingClientPayload = [
            'company'               => 'TP Company Limited',
            'phonenumber'           => '+255777093955',
            'address'               => 'Bumbwini, P.O BOX 271',
            'city'                  => 'Zanzibar',
            'zip'                   => '',
            'country'               => 220,
            'billing_street'        => 'Bumbwini, P.O BOX 271',
            'billing_city'          => 'Zanzibar',
            'billing_country'       => 220,
            'default_currency'      => $usdId,
            'firstname'             => 'Dheeraj',
            'lastname'              => 'Patel',
            'email'                 => 'dheeraj@luckycement.co.tz',
            'is_primary'            => 1,
            'donotsendwelcomeemail' => 1,
        ];
        $packingClientId = $this->ensureClient($packingClientPayload);

        $supplierPayload = [
            'company'               => 'RR Holland',
            'phonenumber'           => '+31618228651',
            'address'               => 'Energieweg 34',
            'city'                  => 'Oosterhout',
            'zip'                   => '4906CG',
            'country'               => 155,
            'billing_street'        => 'Energieweg 34',
            'billing_city'          => 'Oosterhout',
            'billing_zip'           => '4906CG',
            'billing_country'       => 155,
            'default_currency'      => $eurId,
            'firstname'             => "Sha'iez",
            'lastname'              => 'Ibrahim',
            'email'                 => 's.ibrahim@otmain.com',
            'is_primary'            => 1,
            'donotsendwelcomeemail' => 1,
        ];
        $supplierId = $this->ensureClient($supplierPayload);

        $estimateId = $this->CI->estimates_model->add([
            'clientid'                  => $buyerId,
            'project_id'                => 0,
            'date'                      => '2026-07-04',
            'currency'                  => $eurId,
            'status'                    => 2,
            'discount_type'             => '',
            'discount_percent'          => 0,
            'discount_total'            => 0,
            'include_shipping'          => 0,
            'show_shipping_on_estimate' => 1,
            'show_quantity_as'          => 1,
            'billing_street'            => 'Bajonetstraat 52',
            'billing_city'              => 'Rotterdam',
            'billing_zip'               => '3014ZK',
            'billing_country'           => 155,
            'client_ref'                => 'Kovako Shipunload M120',
            'quote_title'               => 'Kovako M120',
            'expiry_days'               => 24,
            'shipment_terms'            => 'EXW (Ex Works)',
            'delivery_time'             => 'Immediate availability, subject to prior sale.',
            'availability'              => 'Shipunloader is available for sale.',
            'payment_terms_text'        => 'To be agreed.',
            'total_usd_display'         => '$ 9,00',
            'total_gold_display'        => '999.9 in Gram',
            'adminnote'                 => 'Demo quotation seeded by OT-Main module.',
            'newitems'                  => [
                1 => [
                    'description'      => 'Kovako M120 Shipunloader',
                    'long_description' => 'Complete ship unloader unit, EXW Rotterdam.',
                    'qty'              => 1,
                    'rate'             => 125000,
                    'unit'             => 'unit',
                    'taxname'          => ['VAT|21'],
                    'order'            => 1,
                ],
                2 => [
                    'description'      => 'DN400 Rotating suction nozzle, complete assembly',
                    'long_description' => 'HS Code: 8483409090',
                    'qty'              => 1,
                    'rate'             => 2500,
                    'unit'             => 'pcs',
                    'taxname'          => ['VAT|21'],
                    'order'            => 2,
                ],
                3 => [
                    'description'      => 'Custom Made Suction hose DN400 x L5500mm',
                    'long_description' => 'HS Code: 400942',
                    'qty'              => 4,
                    'rate'             => 1200,
                    'unit'             => 'pcs',
                    'taxname'          => ['VAT|21'],
                    'order'            => 3,
                ],
            ],
        ]);

        $estimate    = $this->CI->estimates_model->get($estimateId);
        $quoteNumber = format_estimate_number($estimate);

        $proposalId = $this->CI->proposals_model->add([
            'subject'          => 'Kovako M120 - Proposal',
            'date'             => '2026-07-04',
            'open_till'        => '2026-07-28',
            'currency'         => $eurId,
            'status'           => 4, // Sent
            'assigned'         => get_staff_user_id(),
            'rel_type'         => 'customer',
            'rel_id'           => $buyerId,
            'proposal_to'      => $buyerPayload['company'],
            'email'            => $buyerPayload['email'],
            'phone'            => $buyerPayload['phonenumber'],
            'address'          => $buyerPayload['address'],
            'city'             => $buyerPayload['city'],
            'state'            => '',
            'zip'              => $buyerPayload['zip'],
            'country'          => $buyerPayload['country'],
            'client_ref'       => 'PO-REF-2026-07',
            'quote_title'      => 'Kovako M120',
            'document_title'   => 'Draft Quotation',
            'expiry_days'      => 24,
            'availability'     => 'Immediate availability, subject to prior sale.',
            'notes'            => 'Demo proposal notes.',
            'contact_person_name'  => 'OT-Main Sales',
            'contact_person_email' => 'sales@otmain.com',
            'contact_person_phone' => get_option('invoice_company_phonenumber') ?: '+31618228651',
            'payment_terms_text'   => 'To be agreed.',
            'shipment_terms'       => 'EXW (Ex Works)',
            'delivery_time'        => 'To be agreed.',
            'total_usd_display'    => '$ 9,00',
            'total_gold_display'   => '999.9 in GR.',
            // Required by tblproposals schema (no defaults)
            'subtotal'         => 132300,
            'total_tax'        => 27783,
            'total'            => 160083,
            'discount_type'    => '',
            'discount_percent' => 0,
            'discount_total'   => 0,
            'newitems'         => [
                1 => [
                    'description'      => 'Kovako M120 Shipunloader',
                    'long_description' => 'Complete ship unloader unit, EXW Rotterdam.',
                    'qty'              => 1,
                    'rate'             => 125000,
                    'unit'             => 'unit',
                    'taxname'          => ['VAT|21'],
                    'order'            => 1,
                ],
                2 => [
                    'description'      => 'DN400 Rotating suction nozzle, complete assembly',
                    'long_description' => 'HS Code: 8483409090',
                    'qty'              => 1,
                    'rate'             => 2500,
                    'unit'             => 'pcs',
                    'taxname'          => ['VAT|21'],
                    'order'            => 2,
                ],
                3 => [
                    'description'      => 'Custom Made Suction hose DN400 x L5500mm',
                    'long_description' => 'HS Code: 400942',
                    'qty'              => 4,
                    'rate'             => 1200,
                    'unit'             => 'pcs',
                    'taxname'          => ['VAT|21'],
                    'order'            => 3,
                ],
            ],
            'show_quantity_as' => 1,
            'allow_comments'   => 1,
        ]);

        $invoiceId = $this->CI->invoices_model->add([
            'clientid'                 => $invoiceClientId,
            'project_id'               => 0,
            'date'                     => '2026-07-04',
            'duedate'                  => '2026-08-03',
            'currency'                 => $eurId,
            'status'                   => 1,
            'discount_type'            => '',
            'discount_percent'         => 0,
            'discount_total'           => 0,
            'include_shipping'         => 0,
            'show_shipping_on_invoice' => 1,
            'show_quantity_as'         => 1,
            'billing_street'           => 'Ds Martin Luther Kingweg 8-9',
            'billing_city'             => 'Paramaribo',
            'billing_country'          => 103,
            'quote_ref'                => $estimateId,
            'invoice_title'            => 'Spare Parts - Henna-T',
            'expiry_days'              => 30,
            'total_usd_display'        => '$ 9,00',
            'total_gold_display'       => '999.9 in GR.',
            'adminnote'                => 'Demo invoice linked to quotation ' . $quoteNumber,
            'newitems'                 => [
                1 => [
                    'description'      => 'DN400 Rotating suction nozzle, complete assembly',
                    'long_description' => 'HS Code: 8483409090',
                    'qty'              => 1,
                    'rate'             => 2500,
                    'unit'             => 'pcs',
                    'taxname'          => ['VAT|21'],
                    'order'            => 1,
                ],
                2 => [
                    'description'      => 'Compensator DN400',
                    'long_description' => 'HS Code: 400942',
                    'qty'              => 2,
                    'rate'             => 450,
                    'unit'             => 'pcs',
                    'taxname'          => ['VAT|21'],
                    'order'            => 2,
                ],
            ],
        ]);

        $packingId = $this->CI->packing_list_model->add([
            'clientid'          => $packingClientId,
            'quote_ref'         => $quoteNumber,
            'date'              => '2026-07-05',
            'vessel'            => 'Vigor Cement Shipunloader',
            'consignee_name'    => 'TP Company Limited',
            'consignee_address' => "Bumbwini\nP.O BOX 271 Zanzibar",
            'consignee_phone'   => '+255 777 093 955',
            'consignee_email'   => 'dheeraj@luckycement.co.tz',
            'purchaser_name'    => 'TP Company Limited',
            'purchaser_address' => "Bumbwini\nP.O BOX 271 Zanzibar",
            'purchaser_phone'   => '+255 777 093 955',
            'purchaser_email'   => 'dheeraj@luckycement.co.tz',
            'currency'          => $eurId,
            'subtotal_usd'      => 5940,
            'adminnote'         => 'Demo packing list for cement shipunloader spare parts.',
            'items'             => [
                [
                    'description'    => 'DN400 Rotating suction nozzle, complete assembly',
                    'hs_code'        => '8483409090',
                    'qty'            => 1,
                    'unit_price'     => 2500,
                    'packing_detail' => '1 Wooden Box: L2630 x W860 x H1000mm',
                    'gross_weight'   => 731,
                    'net_weight'     => 661,
                    'volume'         => '2.262 CBM',
                ],
                [
                    'description'    => 'Custom Made Suction hose DN400 x L5500mm',
                    'hs_code'        => '400942',
                    'qty'            => 4,
                    'unit_price'     => 1200,
                    'packing_detail' => 'Hose DN400: L5500 x W600 x H600mm',
                    'gross_weight'   => 2360,
                    'net_weight'     => 2360,
                    'volume'         => '7.93 CBM',
                ],
                [
                    'description'    => 'Compensator DN400',
                    'hs_code'        => '400942',
                    'qty'            => 2,
                    'unit_price'     => 450,
                    'packing_detail' => 'L340 x W600 x H600mm',
                    'gross_weight'   => 220,
                    'net_weight'     => 220,
                    'volume'         => '0.24 CBM',
                ],
            ],
        ]);

        $poId = $this->CI->purchase_order_model->add([
            'document_title'         => 'Purchase Order',
            'supplierid'             => $supplierId,
            'date'                   => '2026-07-05',
            'supplier_quote_ref'     => '2451625',
            'contact_person'         => 'Shahir Ibrahim',
            'email'                  => 's.ibrahim@otmain.com',
            'phone'                  => '+31618228651',
            'company_name'           => 'OT-MAIN',
            'company_address'        => 'Bajonetstraat 52',
            'company_postal_code'    => '3014ZK',
            'company_city'           => 'Rotterdam',
            'company_country'        => 'The Netherlands',
            'company_phone'          => '+31618228651',
            'company_email_invoices' => 'inv@otmain.com',
            'company_website'        => 'www.otmain.com',
            'company_vat'            => 'NL004830818B51',
            'company_coc'            => '90597427',
            'iban'                   => 'NL34ABNA0548504303',
            'currency'               => $eurId,
            'adminnote'              => 'Demo purchase order to supplier RR Holland.',
            'items'              => [
                [
                    'description' => 'Hydraulic pump assembly for shipunloader',
                    'qty'         => 1,
                    'unit_price'  => 3200,
                    'taxrate'     => 21,
                ],
                [
                    'description' => 'Seal kit DN400 rotating nozzle',
                    'qty'         => 2,
                    'unit_price'  => 185,
                    'taxrate'     => 21,
                ],
            ],
        ]);

        update_option('otmain_demo_seed_marker', $this->marker);
        update_option('otmain_demo_seed_estimate_id', $estimateId);
        update_option('otmain_demo_seed_proposal_id', $proposalId);
        update_option('otmain_demo_seed_invoice_id', $invoiceId);
        update_option('otmain_demo_seed_packing_id', $packingId);
        update_option('otmain_demo_seed_po_id', $poId);

        return [
            'status'  => 'success',
            'message' => 'Demo data created successfully.',
            'ids'     => compact('buyerId', 'invoiceClientId', 'packingClientId', 'supplierId', 'estimateId', 'proposalId', 'invoiceId', 'packingId', 'poId'),
            'links'   => $this->links(),
        ];
    }

    protected function links()
    {
        return [
            'clients'             => admin_url('clients'),
            'estimates'           => admin_url('estimates'),
            'estimate'            => admin_url('estimates/list_estimates/' . get_option('otmain_demo_seed_estimate_id')),
            'proposals'           => admin_url('proposals'),
            'proposal'            => admin_url('proposals/list_proposals/' . get_option('otmain_demo_seed_proposal_id')),
            'invoices'            => admin_url('invoices'),
            'invoice'             => admin_url('invoices/list_invoices/' . get_option('otmain_demo_seed_invoice_id')),
            'packing'             => admin_url('otmain/packing_list'),
            'packing_edit'        => admin_url('otmain/packing_list/packing_list/' . get_option('otmain_demo_seed_packing_id')),
            'purchase_orders'     => admin_url('otmain/purchase_order'),
            'purchase_order_edit' => admin_url('otmain/purchase_order/purchase_order/' . get_option('otmain_demo_seed_po_id')),
        ];
    }

    protected function getCurrencyId($name)
    {
        $currency = $this->CI->db->where('name', $name)->get(db_prefix() . 'currencies')->row();

        return $currency ? (int) $currency->id : 1;
    }

    protected function ensureClient(array $data)
    {
        $company = $data['company'];
        unset($data['company']);

        $existing = $this->CI->db->where('company', $company)->get(db_prefix() . 'clients')->row();
        if ($existing) {
            return (int) $existing->userid;
        }

        $payload = array_merge($data, ['company' => $company]);

        return (int) $this->CI->clients_model->add($payload, true);
    }

    protected function cleanup()
    {
        $estimateId = (int) get_option('otmain_demo_seed_estimate_id');
        $proposalId = (int) get_option('otmain_demo_seed_proposal_id');
        $invoiceId  = (int) get_option('otmain_demo_seed_invoice_id');
        $packingId  = (int) get_option('otmain_demo_seed_packing_id');
        $poId       = (int) get_option('otmain_demo_seed_po_id');

        if ($proposalId > 0) {
            $this->CI->proposals_model->delete($proposalId);
        }
        if ($poId > 0) {
            $this->CI->purchase_order_model->delete($poId);
        }
        if ($packingId > 0) {
            $this->CI->packing_list_model->delete($packingId);
        }
        if ($invoiceId > 0) {
            $this->CI->invoices_model->delete($invoiceId);
        }
        if ($estimateId > 0) {
            $this->CI->estimates_model->delete($estimateId);
        }

        $companies = [
            'Handelsmij SPT b.v.',
            'Suriname Shiphandling & Services NV',
            'TP Company Limited',
            'RR Holland',
        ];

        foreach ($companies as $company) {
            $client = $this->CI->db->where('company', $company)->get(db_prefix() . 'clients')->row();
            if ($client) {
                $this->CI->clients_model->delete($client->userid);
            }
        }

        delete_option('otmain_demo_seed_marker');
        delete_option('otmain_demo_seed_estimate_id');
        delete_option('otmain_demo_seed_proposal_id');
        delete_option('otmain_demo_seed_invoice_id');
        delete_option('otmain_demo_seed_packing_id');
        delete_option('otmain_demo_seed_po_id');
    }
}
