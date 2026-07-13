<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * OT-Main production seed orchestrator.
 *
 * Bulk payloads live under libraries/seed/ — see seed/manifest.php and seed/README.md.
 * Do not grow this class with per-document arrays; add a new file and register it.
 */
class Otmain_seed
{
    protected $CI;

    /** Bump when seed dataset structure changes so force reseed is clearer. */
    protected $marker = 'otmain_prod_v2';

    /** @var string Absolute path to libraries/seed */
    protected $seedPath;

    /** @var array<string,int> source_quote_number|key → proposal id */
    protected $relatedRegistry = [];

    /** @var array<string,int> company → client id */
    protected $clientIdsByCompany = [];

    /**
     * Document IDs created in this run (written to otmain_seed_document_ids).
     *
     * @var array{proposals:int[],packing_lists:int[],purchase_orders:int[],invoices:int[],estimates:int[]}
     */
    protected $seededIds = [
        'proposals'       => [],
        'packing_lists'   => [],
        'purchase_orders' => [],
        'invoices'        => [],
        'estimates'       => [],
    ];

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
        $this->seedPath = dirname(__FILE__) . '/seed';
    }

    public function run($force = false)
    {
        if (!$force && get_option('otmain_seed_marker') === $this->marker) {
            return [
                'status'  => 'skipped',
                'message' => 'Production seed already applied. Add ?force=1 to recreate.',
                'links'   => $this->links(),
            ];
        }

        $this->cleanup();
        $this->relatedRegistry    = [];
        $this->clientIdsByCompany = [];
        $this->seededIds          = [
            'proposals'       => [],
            'packing_lists'   => [],
            'purchase_orders' => [],
            'invoices'        => [],
            'estimates'       => [],
        ];

        $eurId    = $this->getCurrencyId('EUR');
        $usdId    = $this->getCurrencyId('USD');
        $manifest = $this->loadSeedFile('manifest.php');

        foreach ($this->loadSeedFile('customers.php') as $row) {
            $payload = $this->customerToPayload($row, $eurId, $usdId);
            $this->clientIdsByCompany[$row['company']] = $this->ensureClient($payload);
        }

        $proposalIds = [];
        $proposalId  = 0;
        foreach ($manifest['proposals'] ?? [] as $relative) {
            $def = $this->loadSeedFile($relative);
            $id  = $this->seedProposal($def, $eurId, $usdId);
            $proposalIds[] = $id;
            if ($proposalId < 1) {
                $proposalId = $id;
            }
        }

        $packingId = 0;
        foreach ($manifest['packing_lists'] ?? [] as $relative) {
            $packingId = $this->seedPackingList($this->loadSeedFile($relative), $eurId) ?: $packingId;
        }

        $invoiceId = 0;
        foreach ($manifest['invoices'] ?? [] as $relative) {
            $this->loadSeedFile($relative);
        }

        $poId = 0;
        foreach ($manifest['purchase_orders'] ?? [] as $relative) {
            $poId = $this->seedPurchaseOrder($this->loadSeedFile($relative), $eurId) ?: $poId;
        }

        $this->saveSeededRegistry($this->seededIds);

        update_option('otmain_seed_marker', $this->marker);
        update_option('otmain_seed_proposal_id', (int) $proposalId);
        if ($packingId > 0) {
            update_option('otmain_seed_packing_id', (int) $packingId);
        }
        if ($poId > 0) {
            update_option('otmain_seed_po_id', (int) $poId);
        }

        $this->ensureCurrencyDefaults();

        $tpClientId = $this->clientIdsByCompany['TP Company Limited'] ?? 0;
        $estimateId = 0;

        $ids = [
            'estimateId' => (int) $estimateId,
            'proposalId' => (int) $proposalId,
            'invoiceId'  => (int) $invoiceId,
            'packingId'  => (int) $packingId,
            'poId'       => (int) $poId,
        ];

        return [
            'status'  => 'success',
            'message' => 'Production seed applied (' . count($this->clientIdsByCompany) . ' customers upserted, '
                . count($proposalIds) . ' proposals). Only previous seed docs were replaced; other DB data kept.',
            'ids'     => array_merge($ids, [
                'tpClientId'  => (int) $tpClientId,
                'proposalIds' => $proposalIds,
                'seeded'      => $this->seededIds,
            ]),
            'related_registry' => $this->relatedRegistry,
            'links'            => $this->links($ids),
        ];
    }

    /**
     * @param string $relative Path under libraries/seed/
     * @return mixed
     */
    protected function loadSeedFile($relative)
    {
        $path = $this->seedPath . '/' . ltrim(str_replace(['..', '\\'], '', $relative), '/');
        if (!is_file($path)) {
            throw new RuntimeException('Seed file not found: ' . $relative);
        }

        $data = require $path;
        if ($data === 1) {
            throw new RuntimeException('Seed file must return data: ' . $relative);
        }

        return $data;
    }

    /**
     * @param array $def
     * @param int   $eurId
     * @param int   $usdId
     * @return int proposal id
     */
    protected function seedProposal(array $def, $eurId, $usdId)
    {
        $company = $def['customer_company'] ?? '';
        if ($company === '' || !isset($this->clientIdsByCompany[$company])) {
            throw new RuntimeException('Proposal seed customer missing: ' . $company);
        }

        $clientId = $this->clientIdsByCompany[$company];
        $row      = $this->customerByCompany($company);
        $payload  = $this->customerToPayload($row, $eurId, $usdId);
        $proposal = $def['proposal'] ?? [];

        $useCustomerContact = !empty($proposal['use_customer_contact']);
        unset($proposal['use_customer_contact']);

        $proposal['currency'] = $eurId;
        $proposal['assigned'] = get_staff_user_id();
        $proposal['rel_type'] = $proposal['rel_type'] ?? 'customer';
        $proposal['rel_id']   = $clientId;
        $proposal['proposal_to'] = $proposal['proposal_to'] ?? $payload['company'];
        $proposal['country']     = $proposal['country'] ?? $payload['country'];

        if ($useCustomerContact) {
            $proposal['email']   = $proposal['email'] ?? $payload['email'];
            $proposal['phone']   = $proposal['phone'] ?? $payload['phonenumber'];
            $proposal['address'] = $proposal['address'] ?? $payload['address'];
            $proposal['city']    = $proposal['city'] ?? $payload['city'];
            $proposal['zip']     = $proposal['zip'] ?? $payload['zip'];
        }

        if (($proposal['contact_person_phone'] ?? '') === '') {
            $proposal['contact_person_phone'] = get_option('invoice_company_phonenumber') ?: '+31647239658';
        }

        $proposalId = (int) $this->CI->proposals_model->add($proposal);
        if ($proposalId < 1) {
            throw new RuntimeException('Failed to seed proposal: ' . ($def['key'] ?? 'unknown'));
        }

        $forceStatus = isset($def['force_status']) ? (int) $def['force_status'] : null;
        if ($forceStatus !== null) {
            $this->CI->db->where('id', $proposalId)->update(db_prefix() . 'proposals', [
                'status' => $forceStatus,
            ]);
        }

        if (!empty($def['populate_tracker'])) {
            $this->CI->item_tracker_model->populate_from_proposal($proposalId);
            $this->applyTrackerUpdates($proposalId, $def['tracker_updates'] ?? []);
        }

        if (!empty($def['save_option'])) {
            update_option($def['save_option'], $proposalId);
        }

        $this->registerRelated($def, $proposalId);
        $this->trackSeededId('proposals', $proposalId);

        return $proposalId;
    }

    /**
     * @param int   $proposalId
     * @param array $updates
     */
    protected function applyTrackerUpdates($proposalId, array $updates)
    {
        if ($updates === []) {
            return;
        }

        $trackerItems = $this->CI->item_tracker_model->get((int) $proposalId);
        $needsAuto    = false;

        foreach ($updates as $index => $update) {
            if (empty($trackerItems[$index])) {
                continue;
            }

            $itemId = (int) $trackerItems[$index]['id'];
            $etaRaw = $update['eta_date'] ?? null;
            unset($update['eta_date']);

            if ($etaRaw !== null) {
                $etaSql = preg_match('/^\d{4}-\d{2}-\d{2}$/', (string) $etaRaw)
                    ? (string) $etaRaw
                    : date('Y-m-d', strtotime((string) $etaRaw));
                $this->CI->db->where('id', $itemId)->update(db_prefix() . 'otmain_item_tracker', array_merge($update, [
                    'eta_date'    => $etaSql,
                    'updated_by'  => get_staff_user_id(),
                    'dateupdated' => date('Y-m-d H:i:s'),
                ]));
                $needsAuto = true;
                continue;
            }

            $this->CI->item_tracker_model->update_item($itemId, $update);
        }

        if ($needsAuto) {
            $this->CI->item_tracker_model->auto_update_quotation_status((int) $proposalId);
        }
    }

    /**
     * Packing list definition:
     * - customer_company
     * - related_proposal_keys[]  → registry keys / source_quote_number / proposal key
     * - packing                  → packing_list_model->add payload (without quote_ref_ids)
     * - items                    → packing items (merged commercial + dims)
     *
     * @param array $def
     * @param int   $eurId
     * @return int
     */
    protected function seedPackingList(array $def, $eurId)
    {
        $company = $def['customer_company'] ?? '';
        if ($company === '' || !isset($this->clientIdsByCompany[$company])) {
            throw new RuntimeException('Packing seed customer missing: ' . $company);
        }

        $quoteIds = [];
        $quoteRefLines = [];
        foreach ($def['related_proposal_keys'] ?? [] as $key) {
            $id = $this->resolveRelatedId($key);
            if ($id < 1) {
                throw new RuntimeException('Packing related proposal not in registry: ' . $key);
            }
            $quoteIds[] = $id;
            if (function_exists('format_proposal_number')) {
                $quoteRefLines[] = format_proposal_number($id);
            }
        }

        $packing = $def['packing'] ?? [];
        $packing['clientid']      = $this->clientIdsByCompany[$company];
        $packing['currency']      = $packing['currency'] ?? $eurId;
        $packing['quote_ref_ids'] = $quoteIds;
        if (empty($packing['quote_ref']) && $quoteRefLines !== []) {
            $packing['quote_ref'] = implode("\n", $quoteRefLines);
        }
        $packing['items'] = $def['items'] ?? ($packing['items'] ?? []);

        $packingId = (int) $this->CI->packing_list_model->add($packing);
        if ($packingId > 0 && !empty($def['save_option'])) {
            update_option($def['save_option'], $packingId);
        }
        if ($packingId > 0) {
            $this->trackSeededId('packing_lists', $packingId);
        }

        return $packingId;
    }

    /**
     * @param array $def
     * @param int   $eurId
     * @return int
     */
    protected function seedPurchaseOrder(array $def, $eurId)
    {
        $company = $def['supplier_company'] ?? '';
        if ($company === '' || !isset($this->clientIdsByCompany[$company])) {
            throw new RuntimeException('PO seed supplier missing: ' . $company);
        }

        $po = $def['purchase_order'] ?? [];
        $po['supplierid'] = $this->clientIdsByCompany[$company];
        $po['currency']   = $po['currency'] ?? $eurId;
        $po['items']      = $def['items'] ?? ($po['items'] ?? []);

        $poId = (int) $this->CI->purchase_order_model->add($po);
        if ($poId > 0 && !empty($def['save_option'])) {
            update_option($def['save_option'], $poId);
        }
        if ($poId > 0) {
            $this->trackSeededId('purchase_orders', $poId);
        }

        return $poId;
    }

    /**
     * @param array $def
     * @param int   $proposalId
     */
    protected function registerRelated(array $def, $proposalId)
    {
        $proposalId = (int) $proposalId;
        if (!empty($def['key'])) {
            $this->relatedRegistry[(string) $def['key']] = $proposalId;
        }
        if (!empty($def['source_quote_number'])) {
            $this->relatedRegistry[(string) $def['source_quote_number']] = $proposalId;
        }
    }

    /**
     * @param string $key
     * @return int
     */
    protected function resolveRelatedId($key)
    {
        $key = (string) $key;
        if (isset($this->relatedRegistry[$key])) {
            return (int) $this->relatedRegistry[$key];
        }

        return 0;
    }

    protected function ensureCurrencyDefaults()
    {
        if (get_option('otmain_eur_to_usd_rate') === false || get_option('otmain_eur_to_usd_rate') === '') {
            update_option('otmain_eur_to_usd_rate', '1.09');
        }
        if (get_option('otmain_gold_price_eur_per_gram') === false || get_option('otmain_gold_price_eur_per_gram') === '') {
            update_option('otmain_gold_price_eur_per_gram', '75.50');
        }
        if (get_option('otmain_default_conversion_currency') === false || get_option('otmain_default_conversion_currency') === '') {
            $usdCurrency = $this->CI->db->where('name', 'USD')->get(db_prefix() . 'currencies')->row();
            if ($usdCurrency) {
                update_option('otmain_default_conversion_currency', (string) $usdCurrency->id);
            }
        }
    }

    /**
     * @param array $ids
     * @return array
     */
    protected function links(array $ids = [])
    {
        $estimateId = (int) ($ids['estimateId'] ?? get_option('otmain_seed_estimate_id'));
        $proposalId = (int) ($ids['proposalId'] ?? get_option('otmain_seed_proposal_id'));
        $invoiceId  = (int) ($ids['invoiceId'] ?? get_option('otmain_seed_invoice_id'));
        $packingId  = (int) ($ids['packingId'] ?? get_option('otmain_seed_packing_id'));
        $poId       = (int) ($ids['poId'] ?? get_option('otmain_seed_po_id'));

        if ($proposalId < 1) {
            $proposalId = (int) get_option('otmain_seed_proposal_tp_suction_hose');
        }

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

    protected function getCurrencyId($name)
    {
        $currency = $this->CI->db->where('name', $name)->get(db_prefix() . 'currencies')->row();

        return $currency ? (int) $currency->id : 1;
    }

    /**
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
        foreach ($this->loadSeedFile('customers.php') as $row) {
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
        if ($address === '-') {
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
            'password'              => '123Password!',
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
     * Wipe only documents previously created by this seed (otmain_seed_document_ids).
     * Customers and non-seed documents are preserved.
     */
    protected function cleanup()
    {
        $registry = $this->getSeededRegistry();

        $proposalIds = $registry['proposals'];
        $packingIds  = $registry['packing_lists'];
        $poIds       = $registry['purchase_orders'];
        $invoiceIds  = $registry['invoices'];
        $estimateIds = $registry['estimates'];

        if ($this->CI->db->table_exists(db_prefix() . 'otmain_item_tracker') && $proposalIds !== []) {
            $this->CI->db
                ->where('rel_type', 'proposal')
                ->where_in('rel_id', $proposalIds)
                ->delete(db_prefix() . 'otmain_item_tracker');
        }

        foreach ($packingIds as $id) {
            $this->CI->packing_list_model->delete((int) $id);
        }

        foreach ($poIds as $id) {
            $this->CI->purchase_order_model->delete((int) $id);
        }

        foreach ($proposalIds as $id) {
            $this->CI->proposals_model->delete((int) $id);
        }

        foreach ($estimateIds as $id) {
            $this->CI->estimates_model->delete((int) $id, true);
        }

        foreach ($invoiceIds as $id) {
            $this->CI->invoices_model->delete((int) $id, true);
        }

        $optionKeys = [
            'otmain_seed_marker',
            'otmain_seed_document_ids',
            'otmain_seed_estimate_id',
            'otmain_seed_proposal_id',
            'otmain_seed_proposal_tp_suction_hose',
            'otmain_seed_invoice_id',
            'otmain_seed_packing_id',
            'otmain_seed_po_id',
            'otmain_demo_seed_marker',
            'otmain_demo_seed_estimate_id',
            'otmain_demo_seed_proposal_id',
            'otmain_demo_seed_tp_proposal_id',
            'otmain_demo_seed_invoice_id',
            'otmain_demo_seed_packing_id',
            'otmain_demo_seed_po_id',
        ];
        foreach ($optionKeys as $key) {
            delete_option($key);
        }
    }

    /**
     * @param string $type
     * @param int    $id
     */
    protected function trackSeededId($type, $id)
    {
        $id = (int) $id;
        if ($id < 1 || !isset($this->seededIds[$type])) {
            return;
        }
        if (!in_array($id, $this->seededIds[$type], true)) {
            $this->seededIds[$type][] = $id;
        }
    }

    /**
     * @return array{proposals:int[],packing_lists:int[],purchase_orders:int[],invoices:int[],estimates:int[]}
     */
    protected function getSeededRegistry()
    {
        $empty = [
            'proposals'       => [],
            'packing_lists'   => [],
            'purchase_orders' => [],
            'invoices'        => [],
            'estimates'       => [],
        ];

        $raw = get_option('otmain_seed_document_ids');
        if (is_string($raw) && $raw !== '') {
            $decoded = json_decode($raw, true);
            if (is_array($decoded)) {
                foreach ($empty as $key => $_) {
                    if (!empty($decoded[$key]) && is_array($decoded[$key])) {
                        $empty[$key] = array_values(array_unique(array_map('intval', $decoded[$key])));
                        $empty[$key] = array_values(array_filter($empty[$key]));
                    }
                }

                return $empty;
            }
        }

        // One-time fallback: previous single-option IDs from older seed runs.
        foreach ([
            'otmain_seed_proposal_id',
            'otmain_seed_proposal_tp_suction_hose',
            'otmain_demo_seed_proposal_id',
            'otmain_demo_seed_tp_proposal_id',
        ] as $opt) {
            $id = (int) get_option($opt);
            if ($id > 0) {
                $empty['proposals'][] = $id;
            }
        }
        foreach (['otmain_seed_packing_id', 'otmain_demo_seed_packing_id'] as $opt) {
            $id = (int) get_option($opt);
            if ($id > 0) {
                $empty['packing_lists'][] = $id;
            }
        }
        foreach (['otmain_seed_po_id', 'otmain_demo_seed_po_id'] as $opt) {
            $id = (int) get_option($opt);
            if ($id > 0) {
                $empty['purchase_orders'][] = $id;
            }
        }
        foreach (['otmain_seed_invoice_id', 'otmain_demo_seed_invoice_id'] as $opt) {
            $id = (int) get_option($opt);
            if ($id > 0) {
                $empty['invoices'][] = $id;
            }
        }
        foreach (['otmain_seed_estimate_id', 'otmain_demo_seed_estimate_id'] as $opt) {
            $id = (int) get_option($opt);
            if ($id > 0) {
                $empty['estimates'][] = $id;
            }
        }

        foreach ($empty as $key => $ids) {
            $empty[$key] = array_values(array_unique(array_filter($ids)));
        }

        return $empty;
    }

    /**
     * @param array $registry
     */
    protected function saveSeededRegistry(array $registry)
    {
        $clean = [
            'proposals'       => array_values(array_unique(array_map('intval', $registry['proposals'] ?? []))),
            'packing_lists'   => array_values(array_unique(array_map('intval', $registry['packing_lists'] ?? []))),
            'purchase_orders' => array_values(array_unique(array_map('intval', $registry['purchase_orders'] ?? []))),
            'invoices'        => array_values(array_unique(array_map('intval', $registry['invoices'] ?? []))),
            'estimates'       => array_values(array_unique(array_map('intval', $registry['estimates'] ?? []))),
        ];
        foreach ($clean as $key => $ids) {
            $clean[$key] = array_values(array_filter($ids));
        }
        update_option('otmain_seed_document_ids', json_encode($clean));
    }
}
