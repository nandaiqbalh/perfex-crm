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

    /** Bump when seed dataset structure changes so next /admin/otmain/seed recreates. */
    protected $marker = 'otmain_prod_v23';

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

    /** Default client-area password for contacts created/synced by seed. */
    protected $seedContactPassword = '123Password!';

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

    /**
     * @param bool $force Recreate when marker already applied (tracked seed docs only)
     * @return array
     */
    public function run($force = false)
    {
        if (!$force && get_option('otmain_seed_marker') === $this->marker) {
            return [
                'status'  => 'skipped',
                'message' => 'Production seed already applied (marker ' . $this->marker . '). '
                    . 'Use ?force=1 to recreate seed docs, or ?repair=1 to re-link packing/invoice/PO → proposal without wipe.',
                'links'   => $this->links(),
            ];
        }

        // Only remove seed-stamped / previously tracked docs — never wipe manual proposals.
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
            $invoiceId = $this->seedInvoice($this->loadSeedFile($relative), $eurId) ?: $invoiceId;
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
        if ($invoiceId > 0) {
            update_option('otmain_seed_invoice_id', (int) $invoiceId);
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
                . count($proposalIds) . ' proposals'
                . ($invoiceId > 0 ? ', invoice(s) seeded' : '')
                . '). Replaced seed-stamped docs only; manual proposals kept.',
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
        $master   = $this->getClientMasterData($clientId);
        $proposal = $def['proposal'] ?? [];
        unset($proposal['use_customer_contact']);

        // Company / address block always from tblclients (never overwrite client master from PDF).
        $proposal['currency']    = $eurId;
        $proposal['assigned']    = get_staff_user_id();
        $proposal['rel_type']    = $proposal['rel_type'] ?? 'customer';
        $proposal['rel_id']      = $clientId;
        $proposal['proposal_to'] = $master['company'];
        $proposal['address']     = $master['address'];
        $proposal['city']        = $master['city'];
        $proposal['state']       = $proposal['state'] ?? '';
        $proposal['zip']         = $master['zip'];
        $proposal['country']     = $master['country'];
        $proposal['phone']       = $master['phonenumber'];
        $proposal['email']       = $master['email'];

        // PDF contact → ensure in tblcontacts (add if new, never replace existing), link otmain_contact_id.
        $contact = $this->resolveDocumentContact($clientId, [
            'name'  => $proposal['contact_person_name'] ?? '',
            'email' => $proposal['contact_person_email'] ?? '',
            'phone' => $proposal['contact_person_phone'] ?? '',
        ], $master);
        $proposal['otmain_contact_id']      = $contact['id'];
        $proposal['contact_person_name']    = $contact['doc_name'];
        $proposal['contact_person_email']   = $contact['doc_email'];
        $proposal['contact_person_phone']   = $contact['doc_phone'];

        // Persist original PDF quotation number for list/PDF display (not DB auto-id format).
        if ($this->CI->db->field_exists('source_quote_number', db_prefix() . 'proposals')
            && !empty($def['source_quote_number'])) {
            $proposal['source_quote_number'] = trim((string) $def['source_quote_number']);
        }

        $proposalId = (int) $this->CI->proposals_model->add($proposal);
        if ($proposalId < 1) {
            throw new RuntimeException('Failed to seed proposal: ' . ($def['key'] ?? 'unknown'));
        }

        $postUpdate = [];
        $forceStatus = isset($def['force_status']) ? (int) $def['force_status'] : null;
        if ($forceStatus !== null) {
            $postUpdate['status'] = $forceStatus;
        }
        // Safety: ensure source number is set even if insert stripped an unknown column on older schema.
        if ($this->CI->db->field_exists('source_quote_number', db_prefix() . 'proposals')
            && !empty($def['source_quote_number'])) {
            $postUpdate['source_quote_number'] = trim((string) $def['source_quote_number']);
        }
        if ($postUpdate !== []) {
            $this->CI->db->where('id', $proposalId)->update(db_prefix() . 'proposals', $postUpdate);
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

        // Consignee / purchaser / contact from tblclients (+ primary contact).
        $master = $this->getClientMasterData($packing['clientid']);
        $addrBlock = function_exists('otmain_format_client_address_lines')
            ? otmain_format_client_address_lines($packing['clientid'])
            : $this->formatClientAddressBlock($master);
        if (trim($addrBlock) === '') {
            $addrBlock = $this->formatClientAddressBlock($master);
        }
        $packing['consignee_name']    = $master['company'];
        $packing['consignee_address'] = $addrBlock;
        $packing['consignee_phone']   = $master['phonenumber'];
        $packing['consignee_email']   = $master['email'];
        $packing['purchaser_name']    = $master['company'];
        $packing['purchaser_address'] = $addrBlock;
        $packing['purchaser_phone']   = $master['phonenumber'];
        $packing['purchaser_email']   = $master['email'];

        $contact = $this->resolveDocumentContact($packing['clientid'], [
            'name'  => $packing['contact_person_name'] ?? '',
            'email' => $packing['contact_person_email'] ?? '',
            'phone' => $packing['contact_person_phone'] ?? '',
        ], $master);
        $packing['otmain_contact_id']    = $contact['id'];
        $packing['contact_person_name']  = $contact['doc_name'];
        $packing['contact_person_email'] = $contact['doc_email'];
        $packing['contact_person_phone'] = $contact['doc_phone'];

        $packing['items'] = $def['items'] ?? ($packing['items'] ?? []);

        // Explicit PDF USD — model save_items recalculates subtotal_usd from rate; re-apply after add.
        $explicitUsd = null;
        if (array_key_exists('subtotal_usd', $packing) && $packing['subtotal_usd'] !== '' && $packing['subtotal_usd'] !== null) {
            $explicitUsd = (float) $packing['subtotal_usd'];
        }
        unset($packing['subtotal_usd']);

        $packingId = (int) $this->CI->packing_list_model->add($packing);
        if ($packingId > 0 && $explicitUsd !== null) {
            $this->applyPackingSubtotalUsd($packingId, $explicitUsd);
        }
        if ($packingId > 0 && !empty($def['save_option'])) {
            update_option($def['save_option'], $packingId);
        }
        if ($packingId > 0) {
            $this->trackSeededId('packing_lists', $packingId);
        }

        return $packingId;
    }

    /**
     * Force packing Subtotal in USD to the PDF value and keep conversion_rate in sync.
     *
     * @param int   $packingId
     * @param float $subtotalUsd
     */
    protected function applyPackingSubtotalUsd($packingId, $subtotalUsd)
    {
        $table = db_prefix() . 'otmain_packing_lists';
        $row   = $this->CI->db->select('subtotal, conversion_rate')->where('id', (int) $packingId)->get($table)->row();
        if (!$row) {
            return;
        }

        $update = ['subtotal_usd' => round((float) $subtotalUsd, 2)];
        $eur    = (float) ($row->subtotal ?? 0);
        if ($eur > 0 && $this->CI->db->field_exists('conversion_rate', $table)) {
            $hasRate = isset($row->conversion_rate) && $row->conversion_rate !== null && $row->conversion_rate !== '';
            if (!$hasRate) {
                $update['conversion_rate'] = round(((float) $subtotalUsd) / $eur, 6);
            }
        }

        $this->CI->db->where('id', (int) $packingId)->update($table, $update);
    }

    /**
     * Sum qty×rate (+ VAT from taxname "NAME|RATE") for Perfex sales documents.
     * Required for invoice seed: model add() does not recalculate subtotal/total from items.
     *
     * @param array $items newitems[] from seed
     * @return array{subtotal:float,total_tax:float,total:float}
     */
    protected function calculateSalesTotalsFromItems(array $items)
    {
        $subtotal = 0.0;
        $totalTax = 0.0;

        foreach ($items as $item) {
            if (!is_array($item)) {
                continue;
            }
            $line = (float) ($item['qty'] ?? 0) * (float) ($item['rate'] ?? 0);
            $subtotal += $line;

            $taxnames = $item['taxname'] ?? [];
            if (!is_array($taxnames)) {
                continue;
            }
            foreach ($taxnames as $taxname) {
                $parts = explode('|', (string) $taxname);
                $rate  = isset($parts[1]) ? (float) $parts[1] : 0.0;
                if ($rate > 0) {
                    $totalTax += ($line * $rate) / 100;
                }
            }
        }

        $subtotal = round($subtotal, 2);
        $totalTax = round($totalTax, 2);

        return [
            'subtotal'  => $subtotal,
            'total_tax' => $totalTax,
            'total'     => round($subtotal + $totalTax, 2),
        ];
    }

    /**
     * @param array $def
     * @param int   $eurId
     * @return int invoice id
     */
    protected function seedInvoice(array $def, $eurId)
    {
        $company = $def['customer_company'] ?? '';
        if ($company === '' || !isset($this->clientIdsByCompany[$company])) {
            throw new RuntimeException('Invoice seed customer missing: ' . $company);
        }

        $clientId = $this->clientIdsByCompany[$company];
        $master   = $this->getClientMasterData($clientId);
        $invoice  = $def['invoice'] ?? [];

        $invoice['clientid']  = $clientId;
        $invoice['currency']  = $invoice['currency'] ?? $eurId;
        $invoice['number']    = isset($invoice['number']) ? (int) $invoice['number'] : (int) get_option('next_invoice_number');
        $invoice['date']      = $invoice['date'] ?? date('Y-m-d');
        $invoice['duedate']   = $invoice['duedate'] ?? null;
        $invoice['status']    = isset($invoice['status']) ? (int) $invoice['status'] : 1;

        // Billing from client master (never overwrite company address from PDF OCR mess).
        $invoice['billing_street']  = $master['address'];
        $invoice['billing_city']    = $master['city'];
        $invoice['billing_state']   = $invoice['billing_state'] ?? '';
        $invoice['billing_zip']     = $master['zip'];
        $invoice['billing_country'] = $master['country'];

        if (!empty($invoice['include_shipping'])) {
            $invoice['include_shipping'] = 1;
            $invoice['show_shipping_on_invoice'] = $invoice['show_shipping_on_invoice'] ?? 1;
        }

        $contact = $this->resolveDocumentContact($clientId, [
            'name'  => $invoice['contact_person_name'] ?? '',
            'email' => $invoice['contact_person_email'] ?? '',
            'phone' => $invoice['contact_person_phone'] ?? '',
        ], $master);
        $invoice['otmain_contact_id']      = $contact['id'];
        $invoice['contact_person_name']    = $contact['doc_name'];
        $invoice['contact_person_email']   = $contact['doc_email'];
        $invoice['contact_person_phone']   = $contact['doc_phone'];

        if (array_key_exists('quote_ref', $invoice) && ($invoice['quote_ref'] === '' || $invoice['quote_ref'] === null)) {
            unset($invoice['quote_ref']);
        }

        // Resolve related proposal → invoices.proposal_id (Quote Ref).
        $proposalId = 0;
        $relatedKeys = [];
        if (!empty($def['related_proposal_key'])) {
            $relatedKeys[] = (string) $def['related_proposal_key'];
        }
        if (!empty($def['related_proposal_keys']) && is_array($def['related_proposal_keys'])) {
            foreach ($def['related_proposal_keys'] as $k) {
                $relatedKeys[] = (string) $k;
            }
        }
        if (!empty($def['source_quote_ref'])) {
            $relatedKeys[] = (string) $def['source_quote_ref'];
        }
        foreach (array_unique($relatedKeys) as $key) {
            $resolved = $this->resolveRelatedId($key);
            if ($resolved > 0) {
                $proposalId = $resolved;
                break;
            }
        }
        if ($proposalId > 0 && $this->CI->db->field_exists('proposal_id', db_prefix() . 'invoices')) {
            $invoice['proposal_id'] = $proposalId;
        } elseif (array_key_exists('proposal_id', $invoice) && empty($invoice['proposal_id'])) {
            unset($invoice['proposal_id']);
        }

        // Perfex Invoices_model::add() stores subtotal/total from POST (form JS).
        // update_sales_total_tax_column() only refreshes total_tax — so seed must set totals.
        $totals = $this->calculateSalesTotalsFromItems($invoice['newitems'] ?? []);
        $invoice['subtotal']  = $totals['subtotal'];
        $invoice['total_tax'] = $totals['total_tax'];
        $invoice['total']     = $totals['total'];
        if (!isset($invoice['discount_percent'])) {
            $invoice['discount_percent'] = 0;
        }
        if (!isset($invoice['discount_total'])) {
            $invoice['discount_total'] = 0;
        }
        if (!isset($invoice['adjustment'])) {
            $invoice['adjustment'] = 0;
        }

        $invoiceId = (int) $this->CI->invoices_model->add($invoice);
        if ($invoiceId < 1) {
            throw new RuntimeException('Failed to seed invoice: ' . ($def['key'] ?? 'unknown'));
        }

        $postUpdate = [
            // Re-assert after add (hooks / default columns can leave 0).
            'subtotal'  => $totals['subtotal'],
            'total_tax' => $totals['total_tax'],
            'total'     => $totals['total'],
        ];
        if (isset($def['force_status'])) {
            $postUpdate['status'] = (int) $def['force_status'];
        }
        // Keep PDF number / title consistent after add recalculation.
        if (!empty($def['source_invoice_number']) && empty($invoice['invoice_title'])) {
            $postUpdate['invoice_title'] = trim((string) $def['source_invoice_number']);
        }
        if ($proposalId > 0 && $this->CI->db->field_exists('proposal_id', db_prefix() . 'invoices')) {
            $postUpdate['proposal_id'] = $proposalId;
        }
        $this->CI->db->where('id', $invoiceId)->update(db_prefix() . 'invoices', $postUpdate);

        // Back-link proposal + item tracker (same as convert path).
        if ($proposalId > 0) {
            $this->CI->db->where('id', $proposalId)->update(db_prefix() . 'proposals', [
                'invoice_id' => $invoiceId,
            ]);
            if ($this->CI->db->table_exists(db_prefix() . 'otmain_item_tracker')) {
                $this->CI->item_tracker_model->link_invoice($proposalId, $invoiceId);
            }
        }

        if (!empty($def['save_option'])) {
            update_option($def['save_option'], $invoiceId);
        }

        if (!empty($def['key'])) {
            $this->relatedRegistry[(string) $def['key']] = $invoiceId;
        }
        if (!empty($def['source_invoice_number'])) {
            $this->relatedRegistry[(string) $def['source_invoice_number']] = $invoiceId;
        }
        if (!empty($def['source_quote_ref'])) {
            $this->relatedRegistry['invoice_quote_ref:' . (string) $def['source_quote_ref']] = $invoiceId;
        }

        $this->trackSeededId('invoices', $invoiceId);

        // Advance next number past seeded sequence when needed.
        $usedNumber = (int) ($invoice['number'] ?? 0);
        $next       = (int) get_option('next_invoice_number');
        if ($usedNumber >= $next) {
            update_option('next_invoice_number', $usedNumber + 1);
        }

        return $invoiceId;
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

        $currencyCode = strtoupper((string) ($def['currency_code'] ?? $po['currency_code'] ?? 'EUR'));
        unset($po['currency_code']);
        $po['currency'] = $this->getCurrencyId($currencyCode) ?: $eurId;

        $po['items'] = $def['items'] ?? ($po['items'] ?? []);
        if (!empty($def['number']) && empty($po['number'])) {
            $po['number'] = (int) $def['number'];
        }
        if (!empty($def['prefix']) && empty($po['prefix'])) {
            $po['prefix'] = (string) $def['prefix'];
        }

        $master = $this->getClientMasterData($po['supplierid']);
        $contact = $this->resolveDocumentContact($po['supplierid'], [
            'name'  => $po['contact_person'] ?? '',
            'email' => $po['email'] ?? '',
            'phone' => $po['phone'] ?? '',
        ], $master);
        $po['otmain_contact_id'] = $contact['id'];
        $po['contact_person']    = $contact['doc_name'];
        $po['email']             = $contact['doc_email'];
        $po['phone']             = $contact['doc_phone'];

        $po['supplier_address'] = function_exists('otmain_format_client_address_lines')
            ? otmain_format_client_address_lines($po['supplierid'])
            : $this->formatClientAddressBlock($master);
        if (trim((string) $po['supplier_address']) === '') {
            $po['supplier_address'] = $this->formatClientAddressBlock($master);
        }

        // Optional proposal link (FK). Do not overwrite supplier_quote_ref (supplier quote ≠ our quote).
        $proposalId = 0;
        $relatedKeys = [];
        if (!empty($def['related_proposal_key'])) {
            $relatedKeys[] = (string) $def['related_proposal_key'];
        }
        if (!empty($def['related_proposal_keys']) && is_array($def['related_proposal_keys'])) {
            foreach ($def['related_proposal_keys'] as $k) {
                $relatedKeys[] = (string) $k;
            }
        }
        foreach (array_unique($relatedKeys) as $key) {
            $resolved = $this->resolveRelatedId($key);
            if ($resolved > 0) {
                $proposalId = $resolved;
                break;
            }
        }
        if ($proposalId > 0) {
            $po['proposal_id'] = $proposalId;
        } elseif (array_key_exists('proposal_id', $po) && empty($po['proposal_id'])) {
            unset($po['proposal_id']);
        }

        $poId = (int) $this->CI->purchase_order_model->add($po);
        if ($poId > 0 && $proposalId > 0) {
            $this->CI->db->where('id', $poId)->update(db_prefix() . 'otmain_purchase_orders', [
                'proposal_id' => $proposalId,
            ]);
        }
        if ($poId > 0 && !empty($def['save_option'])) {
            update_option($def['save_option'], $poId);
        }
        if ($poId > 0) {
            $this->trackSeededId('purchase_orders', $poId);
            $this->registerRelated($def, $poId);
        }

        return $poId;
    }

    /**
     * Re-link packing / invoice / PO FK to proposals without deleting documents.
     * Uses seed manifest keys + existing DB `source_quote_number` / save_option IDs.
     *
     * @return array
     */
    public function repairRelations()
    {
        $this->relatedRegistry    = [];
        $this->clientIdsByCompany = [];
        $eurId                    = $this->getCurrencyId('EUR');
        $usdId                    = $this->getCurrencyId('USD');

        foreach ($this->loadSeedFile('customers.php') as $row) {
            $payload = $this->customerToPayload($row, $eurId, $usdId);
            $this->clientIdsByCompany[$row['company']] = $this->ensureClient($payload);
        }

        $this->hydrateRelatedRegistryFromDatabase();

        $manifest = $this->loadSeedFile('manifest.php');
        foreach ($manifest['proposals'] ?? [] as $relative) {
            $def = $this->loadSeedFile($relative);
            $id  = $this->resolveRelatedId($def['key'] ?? '');
            if ($id < 1 && !empty($def['source_quote_number'])) {
                $id = $this->resolveRelatedId((string) $def['source_quote_number']);
            }
            if ($id < 1 && !empty($def['save_option'])) {
                $id = (int) get_option($def['save_option']);
            }
            if ($id > 0) {
                $this->registerRelated($def, $id);
            }
        }

        $stats = [
            'packing_updated'  => 0,
            'invoice_updated'  => 0,
            'po_updated'       => 0,
            'missing_proposals'=> [],
        ];

        foreach ($manifest['packing_lists'] ?? [] as $relative) {
            $def = $this->loadSeedFile($relative);
            $quoteIds = [];
            $quoteRefLines = [];
            foreach ($def['related_proposal_keys'] ?? [] as $key) {
                $id = $this->resolveRelatedId($key);
                if ($id < 1) {
                    $stats['missing_proposals'][] = (string) $key;
                    continue;
                }
                $quoteIds[] = $id;
                if (function_exists('format_proposal_number')) {
                    $quoteRefLines[] = format_proposal_number($id);
                }
            }
            $packingId = $this->findExistingPackingId($def);
            if ($packingId > 0 && $quoteIds !== []) {
                $this->CI->db->where('id', $packingId)->update(db_prefix() . 'otmain_packing_lists', [
                    'quote_ref_ids' => implode(',', $quoteIds),
                    'quote_ref'     => implode("\n", $quoteRefLines),
                ]);
                $stats['packing_updated']++;
            }
        }

        foreach ($manifest['invoices'] ?? [] as $relative) {
            $def = $this->loadSeedFile($relative);
            $proposalId = 0;
            foreach ($this->invoiceRelatedKeys($def) as $key) {
                $proposalId = $this->resolveRelatedId($key);
                if ($proposalId > 0) {
                    break;
                }
            }
            $invoiceId = $this->findExistingInvoiceId($def);
            if ($invoiceId > 0 && $proposalId > 0 && $this->CI->db->field_exists('proposal_id', db_prefix() . 'invoices')) {
                $this->CI->db->where('id', $invoiceId)->update(db_prefix() . 'invoices', [
                    'proposal_id' => $proposalId,
                ]);
                $this->CI->db->where('id', $proposalId)->update(db_prefix() . 'proposals', [
                    'invoice_id' => $invoiceId,
                ]);
                if ($this->CI->db->table_exists(db_prefix() . 'otmain_item_tracker')) {
                    $this->CI->item_tracker_model->link_invoice($proposalId, $invoiceId);
                }
                $stats['invoice_updated']++;
            } elseif ($proposalId < 1) {
                foreach ($this->invoiceRelatedKeys($def) as $key) {
                    $stats['missing_proposals'][] = (string) $key;
                }
            }
        }

        foreach ($manifest['purchase_orders'] ?? [] as $relative) {
            $def = $this->loadSeedFile($relative);
            $proposalId = 0;
            if (!empty($def['related_proposal_key'])) {
                $proposalId = $this->resolveRelatedId((string) $def['related_proposal_key']);
            }
            if ($proposalId < 1) {
                foreach ($def['related_proposal_keys'] ?? [] as $key) {
                    $proposalId = $this->resolveRelatedId((string) $key);
                    if ($proposalId > 0) {
                        break;
                    }
                }
            }
            $poId = 0;
            if (!empty($def['save_option'])) {
                $poId = (int) get_option($def['save_option']);
            }
            if ($poId > 0 && $proposalId > 0 && $this->CI->db->field_exists('proposal_id', db_prefix() . 'otmain_purchase_orders')) {
                $this->CI->db->where('id', $poId)->update(db_prefix() . 'otmain_purchase_orders', [
                    'proposal_id' => $proposalId,
                ]);
                $stats['po_updated']++;
            }
        }

        $stats['missing_proposals'] = array_values(array_unique($stats['missing_proposals']));

        return [
            'status'  => 'success',
            'message' => 'Relations repaired: packing ' . $stats['packing_updated']
                . ', invoice ' . $stats['invoice_updated']
                . ', PO ' . $stats['po_updated']
                . (empty($stats['missing_proposals'])
                    ? '.'
                    : '. Missing proposals (seed ?force=1 if needed): ' . implode(', ', $stats['missing_proposals'])),
            'stats'   => $stats,
            'related_registry' => $this->relatedRegistry,
            'links'   => $this->links(),
        ];
    }

    /**
     * @param array $def
     * @param int   $proposalId
     */
    protected function registerRelated(array $def, $proposalId)
    {
        $proposalId = (int) $proposalId;
        if ($proposalId < 1) {
            return;
        }
        if (!empty($def['key'])) {
            $this->relatedRegistry[(string) $def['key']] = $proposalId;
        }
        if (!empty($def['source_quote_number'])) {
            $this->relatedRegistry[trim((string) $def['source_quote_number'])] = $proposalId;
        }
        if (!empty($def['source_po_number'])) {
            $this->relatedRegistry[trim((string) $def['source_po_number'])] = $proposalId;
        }
        if (!empty($def['source_invoice_number'])) {
            $this->relatedRegistry[trim((string) $def['source_invoice_number'])] = $proposalId;
        }
        foreach ($def['aliases'] ?? [] as $alias) {
            $alias = trim((string) $alias);
            if ($alias !== '') {
                $this->relatedRegistry[$alias] = $proposalId;
            }
        }
    }

    /**
     * @param string $key
     * @return int
     */
    protected function resolveRelatedId($key)
    {
        $key = trim((string) $key);
        if ($key === '') {
            return 0;
        }
        if (isset($this->relatedRegistry[$key])) {
            return (int) $this->relatedRegistry[$key];
        }

        // DB fallback (repair / partial seed runs).
        $table = db_prefix() . 'proposals';
        if ($this->CI->db->field_exists('source_quote_number', $table)) {
            $row = $this->CI->db
                ->select('id')
                ->where('source_quote_number', $key)
                ->order_by('id', 'DESC')
                ->limit(1)
                ->get($table)
                ->row();
            if ($row) {
                $this->relatedRegistry[$key] = (int) $row->id;

                return (int) $row->id;
            }
        }

        return 0;
    }

    /**
     * Prefill registry from existing proposals.source_quote_number.
     */
    protected function hydrateRelatedRegistryFromDatabase()
    {
        $table = db_prefix() . 'proposals';
        if (!$this->CI->db->field_exists('source_quote_number', $table)) {
            return;
        }
        $rows = $this->CI->db
            ->select('id, source_quote_number')
            ->where('source_quote_number IS NOT NULL', null, false)
            ->where('source_quote_number !=', '')
            ->get($table)
            ->result_array();
        foreach ($rows as $row) {
            $num = trim((string) ($row['source_quote_number'] ?? ''));
            if ($num !== '') {
                $this->relatedRegistry[$num] = (int) $row['id'];
            }
        }
    }

    /**
     * @param array $def
     * @return string[]
     */
    protected function invoiceRelatedKeys(array $def)
    {
        $keys = [];
        if (!empty($def['related_proposal_key'])) {
            $keys[] = (string) $def['related_proposal_key'];
        }
        foreach ($def['related_proposal_keys'] ?? [] as $k) {
            $keys[] = (string) $k;
        }
        if (!empty($def['source_quote_ref'])) {
            $keys[] = (string) $def['source_quote_ref'];
        }

        return array_values(array_unique(array_filter(array_map('trim', $keys))));
    }

    /**
     * @param array $def
     * @return int
     */
    protected function findExistingPackingId(array $def)
    {
        if (!empty($def['save_option'])) {
            $id = (int) get_option($def['save_option']);
            if ($id > 0) {
                return $id;
            }
        }

        return 0;
    }

    /**
     * Prefer save_option, then number + invoice_title (+ year from date when present)
     * so 2025-INV-101 and 2026-INV-101 can coexist (same number, different year/title).
     *
     * @param array $def
     * @return int
     */
    protected function findExistingInvoiceId(array $def)
    {
        if (!empty($def['save_option'])) {
            $id = (int) get_option($def['save_option']);
            if ($id > 0) {
                return $id;
            }
        }

        $number = (int) ($def['invoice']['number'] ?? 0);
        $title  = (string) ($def['invoice']['invoice_title'] ?? '');
        if ($number < 1) {
            return 0;
        }

        $this->CI->db->select('id')->where('number', $number);
        if ($title !== '' && $this->CI->db->field_exists('invoice_title', db_prefix() . 'invoices')) {
            $this->CI->db->where('invoice_title', $title);
        }
        $date = (string) ($def['invoice']['date'] ?? '');
        if ($date !== '' && preg_match('/^(\d{4})-/', $date, $m)) {
            $this->CI->db->where('YEAR(' . db_prefix() . 'invoices.date) =', (int) $m[1], false);
        }
        $row = $this->CI->db->order_by('id', 'DESC')->limit(1)->get(db_prefix() . 'invoices')->row();

        return $row ? (int) $row->id : 0;
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
        $name = strtoupper(trim((string) $name));
        if ($name === '') {
            $name = 'EUR';
        }

        $currency = $this->CI->db->where('name', $name)->get(db_prefix() . 'currencies')->row();
        if ($currency) {
            return (int) $currency->id;
        }

        $fields = $this->CI->db->list_fields(db_prefix() . 'currencies');
        $row    = ['name' => $name];
        if (in_array('symbol', $fields, true)) {
            $symbols = ['EUR' => '€', 'USD' => '$', 'IDR' => 'Rp'];
            $row['symbol'] = $symbols[$name] ?? $name;
        }
        if (in_array('decimal_separator', $fields, true)) {
            $row['decimal_separator'] = ($name === 'IDR') ? ',' : '.';
        }
        if (in_array('thousand_separator', $fields, true)) {
            $row['thousand_separator'] = ($name === 'IDR') ? '.' : ',';
        }
        if (in_array('placement', $fields, true)) {
            $row['placement'] = 'before';
        }
        if (in_array('isdefault', $fields, true)) {
            $row['isdefault'] = 0;
        }

        $this->CI->db->insert(db_prefix() . 'currencies', $row);
        $id = (int) $this->CI->db->insert_id();
        if ($id > 0) {
            return $id;
        }

        $fallback = $this->CI->db->where('name', 'EUR')->get(db_prefix() . 'currencies')->row();

        return $fallback ? (int) $fallback->id : 1;
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
            // Fill empty fields only — never replace existing client master data.
            $fill = [
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
                $fill['default_currency'] = $data['default_currency'];
            }

            $update = [];
            foreach ($fill as $col => $newVal) {
                $cur = $existing->{$col} ?? null;
                $curEmpty = ($cur === null || $cur === '' || $cur === 0 || $cur === '0');
                $newEmpty = ($newVal === null || $newVal === '' || $newVal === 0 || $newVal === '0');
                if ($curEmpty && !$newEmpty) {
                    $update[$col] = $newVal;
                }
            }
            if ($update !== []) {
                $this->CI->db->where('userid', $existing->userid)->update(db_prefix() . 'clients', $update);
            }

            // Existing clients keep master data, but seed password must stay predictable for portal login.
            $primaryId = $this->getPrimaryContactId((int) $existing->userid);
            if ($primaryId > 0) {
                $this->syncSeedContactPassword($primaryId);
            }

            return (int) $existing->userid;
        }

        $payload = array_merge($data, ['company' => $company]);

        return (int) $this->CI->clients_model->add($payload, true);
    }

    /**
     * Resolve PDF contact against tblcontacts for a client.
     * - Match existing by email (same client) → reuse, do NOT update row
     * - Else create new non-primary contact
     * - Document keeps PDF contact fields (doc_*); empty PDF → primary contact
     *
     * @param int   $clientId
     * @param array $pdf {name,email,phone}
     * @param array $master from getClientMasterData
     * @return array{id:int,doc_name:string,doc_email:string,doc_phone:string}
     */
    protected function resolveDocumentContact($clientId, array $pdf, array $master)
    {
        $clientId = (int) $clientId;
        $name     = trim((string) ($pdf['name'] ?? ''));
        $email    = strtolower(trim((string) ($pdf['email'] ?? '')));
        $phone    = trim((string) ($pdf['phone'] ?? ''));

        $pdfEmpty = ($name === '' && $email === '' && $phone === '');
        if ($pdfEmpty) {
            $primaryId = $this->getPrimaryContactId($clientId);

            return [
                'id'        => $primaryId,
                'doc_name'  => $master['contact_name'],
                'doc_email' => $master['email'],
                'doc_phone' => $master['contact_phone'] !== '' ? $master['contact_phone'] : $master['phonenumber'],
            ];
        }

        $contactId = $this->ensureClientContact($clientId, $name, $email, $phone);

        return [
            'id'        => $contactId,
            'doc_name'  => $name !== '' ? $name : $master['contact_name'],
            'doc_email' => $email !== '' ? $email : $master['email'],
            'doc_phone' => $phone !== '' ? $phone : ($master['contact_phone'] !== '' ? $master['contact_phone'] : $master['phonenumber']),
        ];
    }

    /**
     * @param int $clientId
     * @return int
     */
    protected function getPrimaryContactId($clientId)
    {
        $row = $this->CI->db
            ->select('id')
            ->where('userid', (int) $clientId)
            ->where('is_primary', 1)
            ->get(db_prefix() . 'contacts')
            ->row();
        if ($row) {
            return (int) $row->id;
        }
        $row = $this->CI->db
            ->select('id')
            ->where('userid', (int) $clientId)
            ->order_by('id', 'ASC')
            ->limit(1)
            ->get(db_prefix() . 'contacts')
            ->row();

        return $row ? (int) $row->id : 0;
    }

    /**
     * Find or create contact under client. Never updates name/email/phone on existing rows.
     * Always syncs seed portal password so login stays predictable after re-seed.
     *
     * @param int    $clientId
     * @param string $name
     * @param string $email
     * @param string $phone
     * @return int contact id
     */
    protected function ensureClientContact($clientId, $name, $email, $phone)
    {
        $clientId = (int) $clientId;
        $email    = strtolower(trim((string) $email));
        $name     = trim((string) $name);
        $phone    = trim((string) $phone);

        if ($email !== '') {
            $existing = $this->CI->db
                ->where('userid', $clientId)
                ->where('email', $email)
                ->get(db_prefix() . 'contacts')
                ->row();
            if ($existing) {
                $this->syncSeedContactPassword((int) $existing->id);

                return (int) $existing->id;
            }
        }

        if ($name !== '' && $email === '') {
            $contacts = $this->CI->db
                ->where('userid', $clientId)
                ->get(db_prefix() . 'contacts')
                ->result_array();
            $needle = strtolower(preg_replace('/\s+/', ' ', $name));
            foreach ($contacts as $c) {
                $full = strtolower(preg_replace('/\s+/', ' ', trim(($c['firstname'] ?? '') . ' ' . ($c['lastname'] ?? ''))));
                if ($full === $needle) {
                    $this->syncSeedContactPassword((int) $c['id']);

                    return (int) $c['id'];
                }
            }
        }

        $emailForDb = $email;
        if ($emailForDb === '') {
            $emailForDb = 'seed.contact.' . $clientId . '.' . substr(md5($name . '|' . $phone), 0, 10) . '@otmain.local';
        } else {
            // Globally unique email constraint — if owned by another client, use local alias (PDF email stays on document).
            $other = $this->CI->clients_model->get_contact_by_email($emailForDb);
            if ($other && (int) $other->userid !== $clientId) {
                $emailForDb = 'seed.contact.' . $clientId . '.' . substr(md5($email), 0, 10) . '@otmain.local';
            }
        }

        $parts = preg_split('/\s+/', $name !== '' ? $name : 'Contact', 2);
        $first = $parts[0] !== '' ? $parts[0] : 'Contact';
        $last  = $parts[1] ?? '';

        $contactId = (int) $this->CI->clients_model->add_contact([
            'firstname'             => $first,
            'lastname'              => $last,
            'email'                 => $emailForDb,
            'phonenumber'           => $phone,
            'password'              => $this->seedContactPassword,
            'donotsendwelcomeemail' => true,
            'invoice_emails'        => 0,
            'estimate_emails'       => 0,
            'credit_note_emails'    => 0,
            'contract_emails'       => 0,
            'task_emails'           => 0,
            'project_emails'        => 0,
            'ticket_emails'         => 0,
        ], $clientId, true);

        if ($contactId < 1) {
            throw new RuntimeException('Failed to add contact for client ' . $clientId . ': ' . $name);
        }

        return $contactId;
    }

    /**
     * Force contact portal password to the seed default (hashed). Does not change other fields.
     *
     * @param int $contactId
     */
    protected function syncSeedContactPassword($contactId)
    {
        $contactId = (int) $contactId;
        if ($contactId < 1) {
            return;
        }

        $this->CI->db->where('id', $contactId)->update(db_prefix() . 'contacts', [
            'password'             => app_hash_password($this->seedContactPassword),
            'last_password_change' => date('Y-m-d H:i:s'),
            'active'               => 1,
        ]);
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
            'password'              => $this->seedContactPassword,
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
     * Master customer data from tblclients + primary tblcontacts.
     *
     * @param int $clientId
     * @return array{company:string,address:string,city:string,zip:string,country:int,phonenumber:string,email:string,contact_name:string,contact_phone:string}
     */
    protected function getClientMasterData($clientId)
    {
        $clientId = (int) $clientId;
        $client   = $this->CI->db->where('userid', $clientId)->get(db_prefix() . 'clients')->row_array();
        if (!$client) {
            throw new RuntimeException('Client not found for seed master data: ' . $clientId);
        }

        $contact = $this->CI->db
            ->where('userid', $clientId)
            ->where('is_primary', 1)
            ->get(db_prefix() . 'contacts')
            ->row_array();
        if (!$contact) {
            $contact = $this->CI->db
                ->where('userid', $clientId)
                ->order_by('id', 'ASC')
                ->limit(1)
                ->get(db_prefix() . 'contacts')
                ->row_array();
        }

        $contactName = '';
        if ($contact) {
            $contactName = trim(($contact['firstname'] ?? '') . ' ' . ($contact['lastname'] ?? ''));
        }

        return [
            'company'       => (string) ($client['company'] ?? ''),
            'address'       => (string) ($client['address'] ?? ''),
            'city'          => (string) ($client['city'] ?? ''),
            'zip'           => (string) ($client['zip'] ?? ''),
            'country'       => (int) ($client['country'] ?? 0),
            'phonenumber'   => (string) ($client['phonenumber'] ?? ''),
            'email'         => (string) ($contact['email'] ?? ''),
            'contact_name'  => $contactName,
            'contact_phone' => (string) ($contact['phonenumber'] ?? ''),
        ];
    }

    /**
     * @param array $master
     * @return string
     */
    protected function formatClientAddressBlock(array $master)
    {
        $lines = array_filter([
            trim((string) ($master['address'] ?? '')),
            trim(implode(' ', array_filter([
                trim((string) ($master['zip'] ?? '')),
                trim((string) ($master['city'] ?? '')),
            ]))),
        ], static function ($line) {
            return $line !== '';
        });

        return implode("\n", $lines);
    }

    /**
     * Wipe seed documents only. Manual / client proposals stay untouched.
     *
     * Safety rules (any match → considered seed, eligible for delete):
     * 1. ID listed in otmain_seed_document_ids (last successful seed run)
     * 2. source_quote_number matches a number from the seed catalog (explicit stamp)
     * 3. Legacy orphans: empty source_quote_number AND subject exact-match a seed subject
     *
     * Manual proposals: no source_quote_number + subject not in seed catalog → never deleted.
     */
    protected function cleanup()
    {
        $registry = $this->getSeededRegistry();
        $catalog  = $this->loadSeedCatalog();

        $proposalIds = $this->resolveSeedProposalIdsToDelete($registry['proposals'], $catalog);
        $packingIds  = $this->resolveSeedPackingIdsToDelete($registry['packing_lists'], $proposalIds);
        $poIds       = $registry['purchase_orders'];
        $invoiceIds  = $this->resolveSeedInvoiceIdsToDelete($registry['invoices']);
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

        $this->clearSeedOptions();
    }

    /**
     * @param int[] $trackedIds
     * @param int[] $proposalIdsBeingDeleted
     * @return int[]
     */
    protected function resolveSeedPackingIdsToDelete(array $trackedIds, array $proposalIdsBeingDeleted)
    {
        $ids = array_map('intval', $trackedIds);
        $manifest = $this->loadSeedFile('manifest.php');
        foreach ($manifest['packing_lists'] ?? [] as $relative) {
            $def = $this->loadSeedFile($relative);
            if (!empty($def['save_option'])) {
                $optId = (int) get_option($def['save_option']);
                if ($optId > 0) {
                    $ids[] = $optId;
                }
            }
        }

        $table = db_prefix() . 'otmain_packing_lists';
        if ($this->CI->db->table_exists($table) && $proposalIdsBeingDeleted !== []) {
            $propSet = array_fill_keys(array_map('intval', $proposalIdsBeingDeleted), true);
            $rows = $this->CI->db->select('id, quote_ref_ids')->get($table)->result_array();
            foreach ($rows as $row) {
                $raw = trim((string) ($row['quote_ref_ids'] ?? ''));
                if ($raw === '') {
                    continue;
                }
                foreach (preg_split('/\s*,\s*/', $raw) as $piece) {
                    $pid = (int) $piece;
                    if ($pid > 0 && isset($propSet[$pid])) {
                        $ids[] = (int) $row['id'];
                        break;
                    }
                }
            }
        }

        $ids = array_values(array_unique(array_filter($ids)));
        sort($ids);

        return $ids;
    }

    /**
     * @param int[] $trackedIds
     * @return int[]
     */
    protected function resolveSeedInvoiceIdsToDelete(array $trackedIds)
    {
        $ids = array_map('intval', $trackedIds);
        $manifest = $this->loadSeedFile('manifest.php');
        foreach ($manifest['invoices'] ?? [] as $relative) {
            $def = $this->loadSeedFile($relative);
            if (!empty($def['save_option'])) {
                $optId = (int) get_option($def['save_option']);
                if ($optId > 0) {
                    $ids[] = $optId;
                }
            }
            $found = $this->findExistingInvoiceId($def);
            if ($found > 0) {
                $ids[] = $found;
            }
        }

        $ids = array_values(array_unique(array_filter($ids)));
        sort($ids);

        return $ids;
    }

    /**
     * Collect identifiers from seed PHP defs used to recognize seed rows in DB.
     *
     * @return array{source_quote_numbers:string[],subjects:string[]}
     */
    protected function loadSeedCatalog()
    {
        $catalog = [
            'source_quote_numbers' => [],
            'subjects'             => [],
        ];

        $manifest = $this->loadSeedFile('manifest.php');
        foreach ($manifest['proposals'] ?? [] as $relative) {
            $def = $this->loadSeedFile($relative);
            if (!empty($def['source_quote_number'])) {
                $catalog['source_quote_numbers'][] = trim((string) $def['source_quote_number']);
            }
            // Aliases / prior stamps so force-reseed still finds renamed source_quote_number rows.
            foreach ($def['aliases'] ?? [] as $alias) {
                $alias = trim((string) $alias);
                if ($alias !== '') {
                    $catalog['source_quote_numbers'][] = $alias;
                }
            }
            $subject = trim((string) ($def['proposal']['subject'] ?? ''));
            if ($subject !== '') {
                $catalog['subjects'][] = $subject;
            }
        }

        $catalog['source_quote_numbers'] = array_values(array_unique($catalog['source_quote_numbers']));
        $catalog['subjects']             = array_values(array_unique($catalog['subjects']));

        return $catalog;
    }

    /**
     * @param int[] $trackedIds
     * @param array{source_quote_numbers:string[],subjects:string[]} $catalog
     * @return int[]
     */
    protected function resolveSeedProposalIdsToDelete(array $trackedIds, array $catalog)
    {
        $ids = array_map('intval', $trackedIds);
        $table = db_prefix() . 'proposals';

        // Stamp: source_quote_number set by seed (manual UI never writes this column).
        if ($this->CI->db->field_exists('source_quote_number', $table)
            && !empty($catalog['source_quote_numbers'])) {
            $rows = $this->CI->db
                ->select('id')
                ->where_in('source_quote_number', $catalog['source_quote_numbers'])
                ->get($table)
                ->result_array();
            foreach ($rows as $row) {
                $ids[] = (int) $row['id'];
            }
        }

        // Legacy orphans from older force-reseeds (before source_quote_number existed).
        // Only exact subject match from seed catalog — manual quotes with other subjects stay.
        if (!empty($catalog['subjects'])) {
            $this->CI->db->select('id')->where_in('subject', $catalog['subjects']);
            if ($this->CI->db->field_exists('source_quote_number', $table)) {
                $this->CI->db->group_start()
                    ->where('source_quote_number IS NULL', null, false)
                    ->or_where('source_quote_number', '')
                    ->group_end();
            }
            $rows = $this->CI->db->get($table)->result_array();
            foreach ($rows as $row) {
                $ids[] = (int) $row['id'];
            }
        }

        $ids = array_values(array_unique(array_filter($ids)));
        sort($ids);

        return $ids;
    }

    /**
     * Clear seed marker / registry / legacy option keys.
     */
    protected function clearSeedOptions()
    {
        $optionKeys = [
            'otmain_seed_marker',
            'otmain_seed_document_ids',
            'otmain_seed_estimate_id',
            'otmain_seed_proposal_id',
            'otmain_seed_proposal_tp_suction_hose',
            'otmain_seed_invoice_id',
            'otmain_seed_packing_id',
            'otmain_seed_packing_valves_id',
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

        // Dynamic save_option keys from seed files (otmain_seed_*)
        $rows = $this->CI->db
            ->select('name')
            ->like('name', 'otmain_seed_', 'after')
            ->get(db_prefix() . 'options')
            ->result_array();
        foreach ($rows as $row) {
            delete_option($row['name']);
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
