<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Otmain_seed
{
    protected $CI;

    /** Bump when seed dataset structure changes so force reseed is clearer. */
    protected $marker = 'otmain_demo_v2';

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
            'item_tracker_model',
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

        // Seed all relation customers from the provided list.
        $clientIdsByCompany = [];
        foreach ($this->customerCatalog() as $row) {
            $payload = $this->customerToPayload($row, $eurId, $usdId);
            $clientIdsByCompany[$row['company']] = $this->ensureClient($payload);
        }

        $buyer     = $this->customerByCompany('Handelsmij SPT b.v.');
        $invoiceC  = $this->customerByCompany('Suriname Shiphandling & Services NV');
        $packingC  = $this->customerByCompany('TP Company Limited');
        $supplierC = $this->customerByCompany('RR Holland');

        $buyerPayload     = $this->customerToPayload($buyer, $eurId, $usdId);
        $invoicePayload   = $this->customerToPayload($invoiceC, $eurId, $usdId);
        $packingPayload   = $this->customerToPayload($packingC, $eurId, $usdId);
        $supplierPayload  = $this->customerToPayload($supplierC, $eurId, $usdId);

        $buyerId         = $clientIdsByCompany[$buyer['company']];
        $invoiceClientId = $clientIdsByCompany[$invoiceC['company']];
        $packingClientId = $clientIdsByCompany[$packingC['company']];
        $supplierId      = $clientIdsByCompany[$supplierC['company']];

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
            'billing_street'            => $buyerPayload['address'],
            'billing_city'              => $buyerPayload['city'],
            'billing_zip'               => $buyerPayload['zip'],
            'billing_country'           => $buyerPayload['country'],
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
            'newitems'                  => $this->demoLineItems(),
        ]);

        $estimate    = $this->CI->estimates_model->get($estimateId);
        $quoteNumber = format_estimate_number($estimate);

        $proposalId = $this->CI->proposals_model->add([
            'subject'              => 'Kovako M120 - Proposal',
            'date'                 => '2026-07-04',
            'open_till'            => '2026-07-28',
            'currency'             => $eurId,
            'status'               => 4,
            'assigned'             => get_staff_user_id(),
            'rel_type'             => 'customer',
            'rel_id'               => $buyerId,
            'proposal_to'          => $buyerPayload['company'],
            'email'                => $buyerPayload['email'],
            'phone'                => $buyerPayload['phonenumber'],
            'address'              => $buyerPayload['address'],
            'city'                 => $buyerPayload['city'],
            'state'                => '',
            'zip'                  => $buyerPayload['zip'],
            'country'              => $buyerPayload['country'],
            'client_ref'           => 'PO-REF-2026-07',
            'quote_title'          => 'Kovako M120',
            'document_title'       => 'Quotation',
            'expiry_days'          => 24,
            'availability'         => 'Immediate availability, subject to prior sale.',
            'notes'                => 'Demo proposal notes.',
            'contact_person_name'  => 'OT-Main Sales',
            'contact_person_email' => 'sales@otmain.com',
            'contact_person_phone' => get_option('invoice_company_phonenumber') ?: '+31647239658',
            'payment_terms_text'   => 'To be agreed.',
            'shipment_terms'       => 'EXW (Ex Works)',
            'delivery_time'        => 'To be agreed.',
            'total_usd_display'    => '$ 9,00',
            'total_gold_display'   => '999.9 in GR.',
            'subtotal'             => 132300,
            'total_tax'            => 27783,
            'total'                => 160083,
            'discount_type'        => '',
            'discount_percent'     => 0,
            'discount_total'       => 0,
            'newitems'             => $this->demoLineItems(),
            'show_quantity_as'     => 1,
            'allow_comments'       => 1,
        ]);

        if ($proposalId) {
            $this->CI->db->where('id', $proposalId);
            $this->CI->db->update(db_prefix() . 'proposals', [
                'status' => 3,
            ]);

            $this->CI->item_tracker_model->populate_from_proposal((int) $proposalId);

            $trackerItems = $this->CI->item_tracker_model->get((int) $proposalId);
            if (!empty($trackerItems[0])) {
                $this->CI->item_tracker_model->update_item((int) $trackerItems[0]['id'], [
                    'item_status' => 'received',
                    'notes'       => 'Received and QC passed.',
                    'admin_notes' => 'Demo seed — item 1 complete.',
                ]);
            }
            if (!empty($trackerItems[1])) {
                $this->CI->item_tracker_model->update_item((int) $trackerItems[1]['id'], [
                    'item_status' => 'quality_check',
                    'notes'       => 'Arrived, under quality check.',
                    'admin_notes' => 'Demo seed — awaiting QC sign-off.',
                ]);
            }
            if (!empty($trackerItems[2])) {
                $etaSql = date('Y-m-d', strtotime('+14 days'));
                $this->CI->db->where('id', (int) $trackerItems[2]['id']);
                $this->CI->db->update(db_prefix() . 'otmain_item_tracker', [
                    'item_status' => 'eta',
                    'eta_date'    => $etaSql,
                    'notes'       => 'Supplier confirmed ETA.',
                    'admin_notes' => 'Demo seed — ETA set.',
                    'updated_by'  => get_staff_user_id(),
                    'dateupdated' => date('Y-m-d H:i:s'),
                ]);
                $this->CI->item_tracker_model->auto_update_quotation_status((int) $proposalId);
            }
        }

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
            'billing_street'           => $invoicePayload['address'],
            'billing_city'             => $invoicePayload['city'],
            'billing_zip'              => $invoicePayload['zip'],
            'billing_country'          => $invoicePayload['country'],
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
            'consignee_name'    => $packingPayload['company'],
            'consignee_address' => trim($packingPayload['address'] . "\n" . $packingPayload['city']),
            'consignee_phone'   => $packingPayload['phonenumber'],
            'consignee_email'   => $packingPayload['email'],
            'purchaser_name'    => $packingPayload['company'],
            'purchaser_address' => trim($packingPayload['address'] . "\n" . $packingPayload['city']),
            'purchaser_phone'   => $packingPayload['phonenumber'],
            'purchaser_email'   => $packingPayload['email'],
            'currency'          => $eurId,
            'subtotal_usd'      => 5940,
            'adminnote'         => 'Demo packing list for cement shipunloader spare parts.',
            'items'             => [
                [
                    'description'    => 'DN400 Rotating suction nozzle, complete assembly',
                    'hs_code'        => '8483409090',
                    'qty'            => 1,
                    'packing_qty'    => 1,
                    'unit_type'      => 'box',
                    'length'         => 2630,
                    'width'          => 860,
                    'height'         => 1000,
                    'unit_price'     => 2500,
                    'packing_detail' => '1 Box: L2630 x W860 x H1000mm',
                    'gross_weight'   => 731,
                    'net_weight'     => 661,
                    'volume'         => '2.262 CBM',
                ],
                [
                    'description'    => 'Custom Made Suction hose DN400 x L5500mm',
                    'hs_code'        => '400942',
                    'qty'            => 4,
                    'packing_qty'    => 4,
                    'unit_type'      => 'box',
                    'length'         => 5500,
                    'width'          => 600,
                    'height'         => 600,
                    'unit_price'     => 1200,
                    'packing_detail' => '4 Box: L5500 x W600 x H600mm',
                    'gross_weight'   => 2360,
                    'net_weight'     => 2360,
                    'volume'         => '7.920 CBM',
                ],
                [
                    'description'    => 'Compensator DN400',
                    'hs_code'        => '400942',
                    'qty'            => 2,
                    'packing_qty'    => 1,
                    'unit_type'      => 'box',
                    'length'         => 340,
                    'width'          => 600,
                    'height'         => 600,
                    'unit_price'     => 450,
                    'packing_detail' => '1 Box: L340 x W600 x H600mm',
                    'gross_weight'   => 220,
                    'net_weight'     => 220,
                    'volume'         => '0.122 CBM',
                ],
            ],
        ]);

        $poId = $this->CI->purchase_order_model->add([
            'document_title'         => 'Purchase Order',
            'supplierid'             => $supplierId,
            'date'                   => '2026-07-05',
            'supplier_quote_ref'     => '2451625',
            'contact_person'         => 'RR Holland Sales',
            'email'                  => $supplierPayload['email'],
            'phone'                  => $supplierPayload['phonenumber'],
            'company_name'           => 'OT-MAIN',
            'company_address'        => 'Bajonetstraat 52',
            'company_postal_code'    => '3014ZK',
            'company_city'           => 'Rotterdam',
            'company_country'        => 'The Netherlands',
            'company_phone'          => '+31647239658',
            'company_email_invoices' => 'inv@otmain.com',
            'company_website'        => 'www.otmain.com',
            'company_vat'            => 'NL004830818B51',
            'company_coc'            => '90597427',
            'iban'                   => 'NL34ABNA0548504303',
            'currency'               => $eurId,
            'adminnote'              => 'Demo purchase order to supplier RR Holland.',
            'items'                  => [
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
        update_option('otmain_demo_seed_estimate_id', (int) $estimateId);
        update_option('otmain_demo_seed_proposal_id', (int) $proposalId);
        update_option('otmain_demo_seed_invoice_id', (int) $invoiceId);
        update_option('otmain_demo_seed_packing_id', (int) $packingId);
        update_option('otmain_demo_seed_po_id', (int) $poId);

        $ids = [
            'estimateId' => (int) $estimateId,
            'proposalId' => (int) $proposalId,
            'invoiceId'  => (int) $invoiceId,
            'packingId'  => (int) $packingId,
            'poId'       => (int) $poId,
        ];

        return [
            'status'  => 'success',
            'message' => 'Demo data created successfully (' . count($clientIdsByCompany) . ' customers).',
            'ids'     => compact('buyerId', 'invoiceClientId', 'packingClientId', 'supplierId', 'estimateId', 'proposalId', 'invoiceId', 'packingId', 'poId'),
            'links'   => $this->links($ids),
        ];
    }

    /**
     * @param array $ids
     * @return array
     */
    protected function links(array $ids = [])
    {
        $estimateId = (int) ($ids['estimateId'] ?? get_option('otmain_demo_seed_estimate_id'));
        $proposalId = (int) ($ids['proposalId'] ?? get_option('otmain_demo_seed_proposal_id'));
        $invoiceId  = (int) ($ids['invoiceId'] ?? get_option('otmain_demo_seed_invoice_id'));
        $packingId  = (int) ($ids['packingId'] ?? get_option('otmain_demo_seed_packing_id'));
        $poId       = (int) ($ids['poId'] ?? get_option('otmain_demo_seed_po_id'));

        if ($proposalId < 1) {
            $row = $this->CI->db
                ->select(db_prefix() . 'proposals.id')
                ->from(db_prefix() . 'proposals')
                ->join(
                    db_prefix() . 'otmain_item_tracker',
                    db_prefix() . 'otmain_item_tracker.rel_type = \'proposal\' AND '
                    . db_prefix() . 'otmain_item_tracker.rel_id = ' . db_prefix() . 'proposals.id',
                    'inner'
                )
                ->where(db_prefix() . 'proposals.status', 3)
                ->order_by(db_prefix() . 'proposals.id', 'DESC')
                ->limit(1)
                ->get()
                ->row();
            if ($row) {
                $proposalId = (int) $row->id;
            }
        }

        return [
            'clients'             => admin_url('clients'),
            'estimates'           => admin_url('estimates'),
            'estimate'            => $estimateId > 0 ? admin_url('estimates/list_estimates/' . $estimateId) : admin_url('estimates'),
            'proposals'           => admin_url('proposals'),
            'proposal'            => $proposalId > 0 ? admin_url('proposals/list_proposals/' . $proposalId) : admin_url('proposals'),
            'invoices'            => admin_url('invoices'),
            'invoice'             => $invoiceId > 0 ? admin_url('invoices/list_invoices/' . $invoiceId) : admin_url('invoices'),
            'packing'             => admin_url('otmain/packing_list'),
            'packing_edit'        => $packingId > 0 ? admin_url('otmain/packing_list/packing_list/' . $packingId) : admin_url('otmain/packing_list'),
            'purchase_orders'     => admin_url('otmain/purchase_order'),
            'purchase_order_edit' => $poId > 0 ? admin_url('otmain/purchase_order/purchase_order/' . $poId) : admin_url('otmain/purchase_order'),
            'item_tracker'        => admin_url('otmain/item_tracker'),
            'item_tracker_detail' => $proposalId > 0
                ? admin_url('otmain/item_tracker/detail/' . $proposalId)
                : admin_url('otmain/item_tracker'),
        ];
    }

    protected function demoLineItems()
    {
        return [
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
        ];
    }

    protected function getCurrencyId($name)
    {
        $currency = $this->CI->db->where('name', $name)->get(db_prefix() . 'currencies')->row();

        return $currency ? (int) $currency->id : 1;
    }

    /**
     * Resolve country id by ISO2 or short name.
     *
     * @param string $iso2
     * @return int
     */
    protected function getCountryId($iso2)
    {
        $iso2 = strtoupper(trim($iso2));
        if ($iso2 === '') {
            return 0;
        }

        static $cache = [];
        if (isset($cache[$iso2])) {
            return $cache[$iso2];
        }

        $row = $this->CI->db
            ->group_start()
            ->where('iso2', $iso2)
            ->or_where('short_name', $iso2)
            ->group_end()
            ->get(db_prefix() . 'countries')
            ->row();

        $cache[$iso2] = $row ? (int) $row->country_id : 0;

        return $cache[$iso2];
    }

    protected function ensureClient(array $data)
    {
        $company = $data['company'];
        unset($data['company']);

        $existing = $this->CI->db->where('company', $company)->get(db_prefix() . 'clients')->row();
        if ($existing) {
            // Keep address/contact fresh on reseed when company already exists.
            $update = [
                'phonenumber'     => $data['phonenumber'] ?? '',
                'address'         => $data['address'] ?? '',
                'city'            => $data['city'] ?? '',
                'zip'             => $data['zip'] ?? '',
                'country'         => $data['country'] ?? 0,
                'billing_street'  => $data['billing_street'] ?? ($data['address'] ?? ''),
                'billing_city'    => $data['billing_city'] ?? ($data['city'] ?? ''),
                'billing_zip'     => $data['billing_zip'] ?? ($data['zip'] ?? ''),
                'billing_country' => $data['billing_country'] ?? ($data['country'] ?? 0),
            ];
            if (isset($data['default_currency'])) {
                $update['default_currency'] = $data['default_currency'];
            }
            $this->CI->db->where('userid', $existing->userid)->update(db_prefix() . 'clients', $update);

            return (int) $existing->userid;
        }

        $payload = array_merge($data, ['company' => $company]);

        return (int) $this->CI->clients_model->add($payload, true);
    }

    protected function customerByCompany($company)
    {
        foreach ($this->customerCatalog() as $row) {
            if ($row['company'] === $company) {
                return $row;
            }
        }

        throw new RuntimeException('Seed customer not found: ' . $company);
    }

    /**
     * @param array $row
     * @param int   $eurId
     * @param int   $usdId
     * @return array
     */
    protected function customerToPayload(array $row, $eurId, $usdId)
    {
        $countryId = $this->getCountryId($row['country_iso']);
        $email     = $this->normalizeEmail($row['email'], $row['nr']);
        $phone     = trim((string) ($row['phone'] ?? ''));
        $address   = trim((string) ($row['address'] ?? ''));
        if ($address === '-' ) {
            $address = '';
        }
        $zip = trim((string) ($row['zip'] ?? ''));
        if ($zip === '-') {
            $zip = '';
        }
        $city = trim((string) ($row['city'] ?? ''));
        if ($city === '-') {
            $city = '';
        }

        $currency = in_array($row['country_iso'], ['TZ', 'CN', 'HK', 'US'], true) ? $usdId : $eurId;

        return [
            'company'               => $row['company'],
            'phonenumber'           => $phone,
            'address'               => $address,
            'city'                  => $city,
            'zip'                   => $zip,
            'country'               => $countryId,
            'billing_street'        => $address,
            'billing_city'          => $city,
            'billing_zip'           => $zip,
            'billing_country'       => $countryId,
            'default_currency'      => $currency,
            'firstname'             => 'Primary',
            'lastname'              => 'Contact',
            'email'                 => $email,
            'is_primary'            => 1,
            'donotsendwelcomeemail' => 1,
        ];
    }

    protected function normalizeEmail($email, $nr)
    {
        $email = trim((string) $email);
        $email = preg_replace('/\s+/', '', $email);
        if ($email === '' || $email === '-' || $email[0] === '@' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return 'seed' . (int) $nr . '@otmain.demo';
        }

        return $email;
    }

    /**
     * Relation list provided for OT-Main demo seed.
     *
     * @return array<int, array{nr:int,company:string,address:string,zip:string,city:string,phone:string,email:string,country_iso:string}>
     */
    protected function customerCatalog()
    {
        return [
            ['nr' => 1,  'company' => 'CemFlexX B.V.', 'address' => 'Pauvreweg 27', 'zip' => '4879NJ', 'city' => 'Etten-Leur', 'phone' => '+31 (0)76 850 39 04', 'email' => 'finance@cemflexx-int.org', 'country_iso' => 'NL'],
            ['nr' => 2,  'company' => 'Suriname Shiphandling & Services NV', 'address' => 'Ds Martin Luther Kingweg 8-9', 'zip' => '', 'city' => 'Paramaribo', 'phone' => '+597-8532726', 'email' => 'jerrel@rudisa.net', 'country_iso' => 'SR'],
            ['nr' => 3,  'company' => 'Belastingdienst', 'address' => '', 'zip' => '', 'city' => '', 'phone' => '', 'email' => '', 'country_iso' => 'NL'],
            ['nr' => 4,  'company' => 'Yoyo Transport', 'address' => '32 Av. Villemain', 'zip' => '75014', 'city' => 'Paris', 'phone' => '', 'email' => 'yoyotransport@yahoo.com', 'country_iso' => 'FR'],
            ['nr' => 5,  'company' => 'Bol.com', 'address' => 'Papendorpseweg 100', 'zip' => '3528 BJ', 'city' => 'Utrecht', 'phone' => '088 712 60 00', 'email' => '', 'country_iso' => 'NL'],
            ['nr' => 6,  'company' => 'OT-Main', 'address' => 'Bajonetstraat 52', 'zip' => '3014 ZK', 'city' => 'Rotterdam', 'phone' => '+31647239658', 'email' => 'info@otmain.com', 'country_iso' => 'NL'],
            ['nr' => 7,  'company' => 'TP Company Limited', 'address' => 'Bumbwini, P.O BOX 271', 'zip' => '', 'city' => 'Zanzibar', 'phone' => '+255242230722', 'email' => 'procurementmanager@turkyspetroleum.co.tz', 'country_iso' => 'TZ'],
            ['nr' => 8,  'company' => 'WM Industrietechnik Isaak Öztürk & Oliver Schmidt GbR', 'address' => 'Südlohner Weg 34a', 'zip' => 'D-48703', 'city' => 'Stadtlohn', 'phone' => '+49 2563 2098550', 'email' => 'info@wm-industrietechnik.de', 'country_iso' => 'DE'],
            ['nr' => 9,  'company' => 'Boettcher Conveying Systems & Service GmbH', 'address' => 'Theodor-Marwitz-Street 2a', 'zip' => '21337', 'city' => 'Lüneburg', 'phone' => '+49 4131 2213 100', 'email' => 'boe@power-in-motion.net', 'country_iso' => 'DE'],
            ['nr' => 10, 'company' => 'Handelsmij SPT b.v.', 'address' => 'Rudonk 21', 'zip' => '4824 AJ', 'city' => 'Breda', 'phone' => '+31850660100', 'email' => 'info@smitspt.nl', 'country_iso' => 'NL'],
            ['nr' => 11, 'company' => 'Doedijns b.v.', 'address' => 'Bleiswijkseweg 51', 'zip' => '2712 PB', 'city' => 'Zoetermeer', 'phone' => '+31880912600', 'email' => 'info@doedijns.com', 'country_iso' => 'NL'],
            ['nr' => 12, 'company' => 'Projectservice Nederland B.V.', 'address' => 'Darwin 20', 'zip' => '7609RL', 'city' => 'Almelo', 'phone' => '+31854012499', 'email' => 'info@projectservice.nl', 'country_iso' => 'NL'],
            ['nr' => 13, 'company' => 'Remote Control Parts B.V.', 'address' => 'Industrieweg 20', 'zip' => '4794 SX', 'city' => 'Heijningen', 'phone' => '+31167521228', 'email' => 'info@remotecontrolparts.nl', 'country_iso' => 'NL'],
            ['nr' => 14, 'company' => 'Ayushman Freelancer', 'address' => 'Santoshi Mishra, E-233, Pariwar Passion apartment', 'zip' => '', 'city' => 'Bangalore', 'phone' => '+918587006726', 'email' => '', 'country_iso' => 'IN'],
            ['nr' => 15, 'company' => 'Interfilter Industries B.V', 'address' => 'Seggeweg 2', 'zip' => '3237 MK', 'city' => 'Vierpolders', 'phone' => '+31181 - 31 11 87', 'email' => 'info@interfilter.nl', 'country_iso' => 'NL'],
            ['nr' => 16, 'company' => 'Distrimex Pompen & Service BV', 'address' => 'Edisonstraat 12', 'zip' => '7006 RD', 'city' => 'Doetinchem', 'phone' => '+31 (0)314 36 84 44', 'email' => 'info@distrimex.nl', 'country_iso' => 'NL'],
            ['nr' => 17, 'company' => 'Nanjing Deers Industrial Co., Ltd', 'address' => 'Hanzhong Road No. 185, Qinhuai Dist, Nanjing City', 'zip' => '', 'city' => 'Jiangsu Province', 'phone' => '+86 25 8450 7790', 'email' => 'sellers1@chinarubberfender.com', 'country_iso' => 'CN'],
            ['nr' => 18, 'company' => 'Pov Fluid Control Technology (Wuhu) Co., Ltd', 'address' => 'No.6 Weishier Road, Yijiang Dist. Wuhu City', 'zip' => '', 'city' => 'Anhui Province', 'phone' => '+86 18616895255', 'email' => 'pov@povvalve.com', 'country_iso' => 'CN'],
            ['nr' => 19, 'company' => 'Tanjung Agus Fastwork', 'address' => 'Karanganyar', 'zip' => '', 'city' => 'Jawa Tengah', 'phone' => '+6286921693226', 'email' => 'tanjungagus999@gmail.com', 'country_iso' => 'ID'],
            ['nr' => 20, 'company' => 'Sylvano Fastwork', 'address' => 'Bekasi', 'zip' => '', 'city' => 'Jawa Barat', 'phone' => '+62895399399932', 'email' => '', 'country_iso' => 'ID'],
            ['nr' => 21, 'company' => 'DHL Nederlands', 'address' => 'Amsterdam', 'zip' => '', 'city' => 'Amsterdam', 'phone' => '+3188-055 2000', 'email' => '', 'country_iso' => 'NL'],
            ['nr' => 22, 'company' => 'B.V. VEGA', 'address' => 'Arnhemseweg-Zuid 213-2', 'zip' => '3817 CG', 'city' => 'Amersfoort', 'phone' => '(033) 450 25 02', 'email' => 'info.nl@vega.com', 'country_iso' => 'NL'],
            ['nr' => 23, 'company' => 'Parcop s.r.l.', 'address' => 'Via filomarino III trav N 13', 'zip' => '80070', 'city' => 'Monte di Procida Napoli', 'phone' => '39 081 868 2064', 'email' => '', 'country_iso' => 'IT'],
            ['nr' => 24, 'company' => 'LabelsDirect BV', 'address' => 'Trasmolenlaan 12', 'zip' => '3447 GZ', 'city' => 'Woerden', 'phone' => '0348 342 186', 'email' => '', 'country_iso' => 'NL'],
            ['nr' => 25, 'company' => 'Verpakgigant.nl', 'address' => 'De Schrepel 24 B1', 'zip' => '1648 GC', 'city' => 'De Goorn', 'phone' => '', 'email' => 'support@verpakgigant.nl', 'country_iso' => 'NL'],
            ['nr' => 26, 'company' => 'Amazon EU S.à r.l', 'address' => 'Mr. Treublaan 7', 'zip' => '1097 DP', 'city' => 'Amsterdam', 'phone' => '', 'email' => '', 'country_iso' => 'NL'],
            ['nr' => 27, 'company' => 'Fastwork.id', 'address' => '', 'zip' => '', 'city' => '', 'phone' => '+62 821-6747-1450', 'email' => 'support@fastwork.id', 'country_iso' => 'ID'],
            ['nr' => 28, 'company' => 'Dongguan Dxseals Technology Co.,Ltd', 'address' => '56 Dongcheng Road, Guancheng District, Dongguan', 'zip' => '', 'city' => 'Dongguan', 'phone' => '+86 15992798689', 'email' => 'sales1@dxtseals.com', 'country_iso' => 'CN'],
            ['nr' => 29, 'company' => 'Unique Transmission Equipment (Luoyang) Co., Ltd.', 'address' => 'No.22 Binhe Road, New & High Tech Industry Development Zone', 'zip' => '471000', 'city' => 'Luoyang', 'phone' => '+86 0379 64915181', 'email' => '', 'country_iso' => 'CN'],
            ['nr' => 30, 'company' => 'SHENZHEN WETAC TECHNOLOGY CO.,LTD', 'address' => 'ROOM 106, BUILDING 1, NO. 5 NIUXING ROAD', 'zip' => '', 'city' => 'Dongguan, Guangdong', 'phone' => '86-13530046228', 'email' => 'atlastrade@163.com', 'country_iso' => 'CN'],
            ['nr' => 31, 'company' => 'V-Trust Inspection Service Group', 'address' => '', 'zip' => '', 'city' => '', 'phone' => '+86-20-89089938', 'email' => 'cathy.xiao@v-trust.com', 'country_iso' => 'CN'],
            ['nr' => 32, 'company' => 'FS International Limited Cargo', 'address' => 'I/F Block C Sea View Estate, No.8 Watson Road, North Point', 'zip' => '', 'city' => 'Hong Kong', 'phone' => '85228400824', 'email' => '', 'country_iso' => 'HK'],
            ['nr' => 33, 'company' => 'PT. Trinity Konsultan Group', 'address' => 'Jl. Gn Saputan No.1A, Pemecutan Kelod, Denpasar Barat', 'zip' => '', 'city' => 'Bali', 'phone' => '082341878520', 'email' => 'trinity.konsultangroup@gmail.com', 'country_iso' => 'ID'],
            ['nr' => 34, 'company' => 'Rubix B.V', 'address' => '', 'zip' => '', 'city' => '', 'phone' => '', 'email' => '', 'country_iso' => 'NL'],
            ['nr' => 35, 'company' => 'Hydrotechnik24.de', 'address' => '', 'zip' => '', 'city' => '', 'phone' => '', 'email' => '', 'country_iso' => 'DE'],
            ['nr' => 36, 'company' => 'Automation24 GmbH', 'address' => 'Keurenplein 41', 'zip' => '1069CD', 'city' => 'Amsterdam', 'phone' => '', 'email' => '', 'country_iso' => 'NL'],
            ['nr' => 37, 'company' => 'Jorny Product B.V', 'address' => 'Philipshoofjesweg 90', 'zip' => '3247 XS', 'city' => 'Dirksland', 'phone' => '+31 (0)6 51 95 10 96', 'email' => '', 'country_iso' => 'NL'],
            ['nr' => 38, 'company' => 'Hydraunica B.V.', 'address' => 'Biesbosweg 2', 'zip' => '5145 PZ', 'city' => 'Waalwijk', 'phone' => '+31 318 519 837', 'email' => 'j.denhertog@hydraunica.nl', 'country_iso' => 'NL'],
            ['nr' => 39, 'company' => 'RR Holland', 'address' => 'Energieweg 34', 'zip' => '4906CG', 'city' => 'Oosterhout', 'phone' => '+31162456397', 'email' => 'sales@rrholland.nl', 'country_iso' => 'NL'],
            ['nr' => 40, 'company' => 'Witway Webshops B.V', 'address' => 'Tussendiepen 48', 'zip' => '9206AE', 'city' => 'Drachten', 'phone' => '0850020030', 'email' => 'klantenservice@witway.nl', 'country_iso' => 'NL'],
            ['nr' => 41, 'company' => 'Klium N.V', 'address' => 'Ekkelgaarden 26', 'zip' => '3500', 'city' => 'Hasselt', 'phone' => '', 'email' => '', 'country_iso' => 'BE'],
            ['nr' => 42, 'company' => 'Meuth', 'address' => '', 'zip' => '', 'city' => '', 'phone' => '', 'email' => '', 'country_iso' => 'DE'],
            ['nr' => 43, 'company' => 'Scheepvaartcenter', 'address' => 'Krammer 8', 'zip' => '3232 HE', 'city' => 'Brielle', 'phone' => '', 'email' => '', 'country_iso' => 'NL'],
            ['nr' => 44, 'company' => 'Shenzen Rongtai Automation Technology Co.Ltd', 'address' => 'Room 401, No. 29-1, Xintangkeng Road, Silian Community, Longgang District', 'zip' => '', 'city' => 'Shenzhen', 'phone' => '', 'email' => '', 'country_iso' => 'CN'],
            ['nr' => 45, 'company' => 'Outletspecialist BV', 'address' => 'Zuidhollandsedijk 179', 'zip' => '5171 TM', 'city' => 'Kaatsheuvel', 'phone' => '', 'email' => '', 'country_iso' => 'NL'],
            ['nr' => 46, 'company' => 'RS Components BV', 'address' => 'Bingerweg 19', 'zip' => '2001 HN', 'city' => 'Haarlem', 'phone' => '0235166555', 'email' => '', 'country_iso' => 'NL'],
            ['nr' => 47, 'company' => 'PT.Agung Buana Sentosa', 'address' => 'Komp.Pengampon Square B12,B15,Jl. Semut Baru, Pabean Cantikan', 'zip' => '', 'city' => 'Surabaya', 'phone' => '(031)3550081', 'email' => '', 'country_iso' => 'ID'],
        ];
    }

    protected function cleanup()
    {
        $estimateId = (int) get_option('otmain_demo_seed_estimate_id');
        $proposalId = (int) get_option('otmain_demo_seed_proposal_id');
        $invoiceId  = (int) get_option('otmain_demo_seed_invoice_id');
        $packingId  = (int) get_option('otmain_demo_seed_packing_id');
        $poId       = (int) get_option('otmain_demo_seed_po_id');

        if ($proposalId > 0) {
            $this->CI->db->where('rel_type', 'proposal');
            $this->CI->db->where('rel_id', $proposalId);
            $this->CI->db->delete(db_prefix() . 'otmain_item_tracker');

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

        // Also remove legacy v1 companies if still present.
        $companies = array_map(static function ($row) {
            return $row['company'];
        }, $this->customerCatalog());

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
