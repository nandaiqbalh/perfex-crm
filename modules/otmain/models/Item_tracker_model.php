<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Item_tracker_model extends App_Model
{
    public const ITEM_STATUSES = [
        'pending',
        'ordered',
        'eta',
        'quality_check',
        'received',
    ];

    public const QUOTATION_STATUSES = [
        'pending',
        'in_progress',
        'ready_for_shipment',
        'shipped',
    ];

    protected $table;

    public function __construct()
    {
        parent::__construct();
        $this->table = db_prefix() . 'otmain_item_tracker';
    }

    /**
     * Get active tracker items for a proposal.
     *
     * @param int $proposal_id
     * @return array
     */
    public function get($proposal_id)
    {
        $this->db->where('rel_type', 'proposal');
        $this->db->where('rel_id', (int) $proposal_id);
        $this->db->where('deleted_at IS NULL', null, false);
        $this->db->order_by('item_order', 'asc');

        return $this->db->get($this->table)->result_array();
    }

    /**
     * Get a single tracker item by id (active only).
     *
     * @param int $item_id
     * @return object|null
     */
    public function get_item($item_id)
    {
        $this->db->where('id', (int) $item_id);
        $this->db->where('deleted_at IS NULL', null, false);

        return $this->db->get($this->table)->row();
    }

    /**
     * Whether a proposal already has tracker rows (including soft-deleted).
     *
     * @param int $proposal_id
     * @return bool
     */
    public function has_tracker($proposal_id)
    {
        return total_rows($this->table, [
            'rel_type' => 'proposal',
            'rel_id'   => (int) $proposal_id,
        ]) > 0;
    }

    /**
     * Copy proposal items into tracker (idempotent).
     *
     * @param int $proposal_id
     * @return bool true if populated, false if already existed / invalid
     */
    public function populate_from_proposal($proposal_id)
    {
        $proposal_id = (int) $proposal_id;
        if ($proposal_id < 1) {
            return false;
        }

        if ($this->has_tracker($proposal_id)) {
            return false;
        }

        $proposal = $this->db->select('id, invoice_id')
            ->where('id', $proposal_id)
            ->get(db_prefix() . 'proposals')
            ->row();

        if (!$proposal) {
            return false;
        }

        $items = $this->db
            ->where('rel_type', 'proposal')
            ->where('rel_id', $proposal_id)
            ->order_by('item_order', 'asc')
            ->get(db_prefix() . 'itemable')
            ->result_array();

        if (empty($items)) {
            return false;
        }

        $now        = date('Y-m-d H:i:s');
        $invoice_id = !empty($proposal->invoice_id) ? (int) $proposal->invoice_id : null;

        foreach ($items as $item) {
            $row = [
                'rel_type'         => 'proposal',
                'rel_id'           => $proposal_id,
                'invoice_id'       => $invoice_id,
                'item_order'       => (int) ($item['item_order'] ?? 0),
                'description'      => $item['description'] ?? null,
                'long_description' => $item['long_description'] ?? null,
                'qty'              => $item['qty'] ?? 1,
                'unit'             => $item['unit'] ?? null,
                'rate'             => $item['rate'] ?? null,
                'item_status'      => 'pending',
                'eta_date'         => null,
                'notes'            => null,
                'admin_notes'      => null,
                'updated_by'       => null,
                'datecreated'      => $now,
                'dateupdated'      => null,
                'deleted_at'       => null,
            ];

            if ($this->db->field_exists('itemable_id', $this->table)) {
                $row['itemable_id'] = !empty($item['id']) ? (int) $item['id'] : null;
            }

            $this->db->insert($this->table, $row);
        }

        $this->db->where('id', $proposal_id);
        $this->db->update(db_prefix() . 'proposals', [
            'quotation_status' => 'pending',
        ]);

        return true;
    }

    /**
     * Merge current proposal line items into an existing tracker.
     * Updates catalog fields, inserts new lines as pending, soft-deletes removed lines.
     * Preserves tracker-only fields (status, eta, notes, invoice_id).
     *
     * @param int $proposal_id
     * @return bool true if sync ran, false if no tracker / invalid
     */
    public function sync_from_proposal($proposal_id)
    {
        $proposal_id = (int) $proposal_id;
        if ($proposal_id < 1 || !$this->has_tracker($proposal_id)) {
            return false;
        }

        $proposal = $this->db->select('id, invoice_id')
            ->where('id', $proposal_id)
            ->get(db_prefix() . 'proposals')
            ->row();

        if (!$proposal) {
            return false;
        }

        $itemableRows = $this->db
            ->where('rel_type', 'proposal')
            ->where('rel_id', $proposal_id)
            ->order_by('item_order', 'asc')
            ->get(db_prefix() . 'itemable')
            ->result_array();

        // Include soft-deleted so we can revive a row if the line returns.
        $this->db->where('rel_type', 'proposal');
        $this->db->where('rel_id', $proposal_id);
        $trackerRows = $this->db->get($this->table)->result_array();

        $byItemableId = [];
        $unmatched    = [];
        foreach ($trackerRows as $row) {
            $iid = isset($row['itemable_id']) ? (int) $row['itemable_id'] : 0;
            if ($iid > 0) {
                $byItemableId[$iid] = $row;
            } else {
                $unmatched[] = $row;
            }
        }

        $now        = date('Y-m-d H:i:s');
        $invoice_id = !empty($proposal->invoice_id) ? (int) $proposal->invoice_id : null;
        $hasItemableCol = $this->db->field_exists('itemable_id', $this->table);
        $matchedTrackerIds = [];

        foreach ($itemableRows as $item) {
            $itemableId = (int) ($item['id'] ?? 0);
            $match      = null;

            if ($itemableId > 0 && isset($byItemableId[$itemableId])) {
                $match = $byItemableId[$itemableId];
                unset($byItemableId[$itemableId]);
            } else {
                // Legacy fallback: item_order + description
                $order = (int) ($item['item_order'] ?? 0);
                $desc  = (string) ($item['description'] ?? '');
                foreach ($unmatched as $idx => $candidate) {
                    if ((int) ($candidate['item_order'] ?? 0) === $order
                        && (string) ($candidate['description'] ?? '') === $desc
                    ) {
                        $match = $candidate;
                        unset($unmatched[$idx]);
                        break;
                    }
                }
                $unmatched = array_values($unmatched);
            }

            $catalog = [
                'item_order'       => (int) ($item['item_order'] ?? 0),
                'description'      => $item['description'] ?? null,
                'long_description' => $item['long_description'] ?? null,
                'qty'              => $item['qty'] ?? 1,
                'unit'             => $item['unit'] ?? null,
                'rate'             => $item['rate'] ?? null,
                'dateupdated'      => $now,
                'deleted_at'       => null,
            ];
            if ($hasItemableCol) {
                $catalog['itemable_id'] = $itemableId > 0 ? $itemableId : null;
            }

            if ($match) {
                $matchedTrackerIds[(int) $match['id']] = true;
                $this->db->where('id', (int) $match['id']);
                $this->db->update($this->table, $catalog);
            } else {
                $insert = array_merge($catalog, [
                    'rel_type'    => 'proposal',
                    'rel_id'      => $proposal_id,
                    'invoice_id'  => $invoice_id,
                    'item_status' => 'pending',
                    'eta_date'    => null,
                    'notes'       => null,
                    'admin_notes' => null,
                    'updated_by'  => null,
                    'datecreated' => $now,
                ]);
                $insert['dateupdated'] = null;
                $this->db->insert($this->table, $insert);
            }
        }

        // Soft-delete tracker rows whose proposal line was removed.
        $stillActive = array_merge(array_values($byItemableId), $unmatched);
        foreach ($stillActive as $orphan) {
            $oid = (int) ($orphan['id'] ?? 0);
            if ($oid < 1 || isset($matchedTrackerIds[$oid])) {
                continue;
            }
            if (!empty($orphan['deleted_at'])) {
                continue;
            }
            $this->db->where('id', $oid);
            $this->db->update($this->table, [
                'deleted_at'  => $now,
                'dateupdated' => $now,
            ]);
        }

        $this->auto_update_quotation_status($proposal_id);

        return true;
    }

    /**
     * Resync ALL item trackers from their linked proposals (seed + non-seed).
     * Preserves tracker-only fields via sync_from_proposal().
     *
     * @return array{synced:int,skipped:int,proposal_ids:int[]}
     */
    public function sync_all_from_proposals()
    {
        if (!$this->db->table_exists($this->table)) {
            return ['synced' => 0, 'skipped' => 0, 'proposal_ids' => []];
        }

        $rows = $this->db
            ->select('rel_id')
            ->where('rel_type', 'proposal')
            ->group_by('rel_id')
            ->get($this->table)
            ->result_array();

        $synced       = 0;
        $skipped      = 0;
        $proposalIds  = [];

        foreach ($rows as $row) {
            $proposalId = (int) ($row['rel_id'] ?? 0);
            if ($proposalId < 1) {
                $skipped++;
                continue;
            }
            $proposalIds[] = $proposalId;
            if ($this->sync_from_proposal($proposalId)) {
                $synced++;
            } else {
                $skipped++;
            }
        }

        return [
            'synced'       => $synced,
            'skipped'      => $skipped,
            'proposal_ids' => $proposalIds,
        ];
    }

    /**
     * Hard-delete all tracker rows for a proposal (on proposal delete).
     *
     * @param int $proposal_id
     * @return void
     */
    public function delete_for_proposal($proposal_id)
    {
        $proposal_id = (int) $proposal_id;
        if ($proposal_id < 1) {
            return;
        }

        $this->db->where('rel_type', 'proposal');
        $this->db->where('rel_id', $proposal_id);
        $this->db->delete($this->table);
    }

    /**
     * Alias for admin backfill UI.
     *
     * @param int $proposal_id
     * @return bool
     */
    public function backfill_proposal($proposal_id)
    {
        return $this->populate_from_proposal($proposal_id);
    }

    /**
     * Backfill all Accepted proposals that do not yet have a tracker.
     *
     * @return int number of proposals populated
     */
    public function backfill_all()
    {
        $proposals = $this->db
            ->select('id')
            ->where('status', 3)
            ->get(db_prefix() . 'proposals')
            ->result_array();

        $count = 0;
        foreach ($proposals as $proposal) {
            if ($this->populate_from_proposal((int) $proposal['id'])) {
                $count++;
            }
        }

        return $count;
    }

    /**
     * Update a single tracker item.
     *
     * @param int   $item_id
     * @param array $data
     * @return bool|string true on success, error string on validation failure
     */
    public function update_item($item_id, $data)
    {
        $item = $this->get_item($item_id);
        if (!$item) {
            return false;
        }

        $allowedStatuses = self::ITEM_STATUSES;
        $status          = isset($data['item_status']) ? $data['item_status'] : $item->item_status;

        if (!in_array($status, $allowedStatuses, true)) {
            return 'invalid_status';
        }

        $etaDate = array_key_exists('eta_date', $data) ? $data['eta_date'] : $item->eta_date;
        if ($etaDate === '') {
            $etaDate = null;
        }
        if ($etaDate !== null && $etaDate !== '') {
            $etaDate = to_sql_date($etaDate);
        }

        if ($status === 'eta' && empty($etaDate)) {
            return 'eta_date_required';
        }

        $update = [
            'item_status' => $status,
            'eta_date'    => $etaDate,
            'updated_by'  => get_staff_user_id(),
            'dateupdated' => date('Y-m-d H:i:s'),
        ];

        if (array_key_exists('notes', $data)) {
            $update['notes'] = $data['notes'];
        }
        if (array_key_exists('admin_notes', $data)) {
            $update['admin_notes'] = $data['admin_notes'];
        }

        $this->db->where('id', (int) $item_id);
        $this->db->update($this->table, $update);

        $this->auto_update_quotation_status((int) $item->rel_id);

        return true;
    }

    /**
     * Bulk-save tracker items for a proposal.
     *
     * @param int   $proposal_id
     * @param array $items keyed by item id
     * @return true|string
     */
    public function update_items($proposal_id, $items)
    {
        if (!is_array($items) || empty($items)) {
            return true;
        }

        foreach ($items as $itemId => $row) {
            $itemId = (int) $itemId;
            if ($itemId < 1) {
                continue;
            }

            $existing = $this->get_item($itemId);
            if (!$existing || (int) $existing->rel_id !== (int) $proposal_id) {
                continue;
            }

            $result = $this->update_item($itemId, [
                'item_status' => $row['item_status'] ?? $existing->item_status,
                'eta_date'    => $row['eta_date'] ?? $existing->eta_date,
                'notes'       => $row['notes'] ?? $existing->notes,
                'admin_notes' => $row['admin_notes'] ?? $existing->admin_notes,
            ]);

            if ($result !== true) {
                return $result;
            }
        }

        // Ensure quotation status recalculated once after all updates
        $this->auto_update_quotation_status((int) $proposal_id);

        return true;
    }

    /**
     * Manually set quotation_status (admin override).
     *
     * @param int    $proposal_id
     * @param string $status
     * @return bool
     */
    public function update_quotation_status($proposal_id, $status)
    {
        if (!in_array($status, self::QUOTATION_STATUSES, true)) {
            return false;
        }

        $this->db->where('id', (int) $proposal_id);
        $this->db->update(db_prefix() . 'proposals', [
            'quotation_status' => $status,
        ]);

        return $this->db->affected_rows() >= 0;
    }

    /**
     * Auto-upgrade quotation_status based on item statuses.
     * Never downgrades; never overwrites shipped.
     *
     * @param int $proposal_id
     * @return void
     */
    public function auto_update_quotation_status($proposal_id)
    {
        $proposal_id = (int) $proposal_id;

        $proposal = $this->db->select('quotation_status')
            ->where('id', $proposal_id)
            ->get(db_prefix() . 'proposals')
            ->row();

        if (!$proposal) {
            return;
        }

        $current = $proposal->quotation_status ?: 'pending';
        if ($current === 'shipped') {
            return;
        }

        $items = $this->get($proposal_id);
        $total = count($items);
        if ($total === 0) {
            return;
        }

        $received   = 0;
        $nonPending = 0;
        foreach ($items as $item) {
            if ($item['item_status'] === 'received') {
                $received++;
            }
            if ($item['item_status'] !== 'pending') {
                $nonPending++;
            }
        }

        if ($received === $total) {
            $newStatus = 'ready_for_shipment';
        } elseif ($nonPending > 0) {
            $newStatus = 'in_progress';
        } else {
            $newStatus = 'pending';
        }

        $rank = [
            'pending'            => 0,
            'in_progress'        => 1,
            'ready_for_shipment' => 2,
            'shipped'            => 3,
        ];

        $currentRank = $rank[$current] ?? 0;
        $newRank     = $rank[$newStatus] ?? 0;
        if ($newRank <= $currentRank) {
            return;
        }

        $this->db->where('id', $proposal_id);
        $this->db->update(db_prefix() . 'proposals', [
            'quotation_status' => $newStatus,
        ]);
    }

    /**
     * Soft-delete a tracker item.
     *
     * @param int $item_id
     * @return bool
     */
    public function soft_delete($item_id)
    {
        $item = $this->get_item($item_id);
        if (!$item) {
            return false;
        }

        $this->db->where('id', (int) $item_id);
        $this->db->update($this->table, [
            'deleted_at'  => date('Y-m-d H:i:s'),
            'updated_by'  => get_staff_user_id(),
            'dateupdated' => date('Y-m-d H:i:s'),
        ]);

        $this->auto_update_quotation_status((int) $item->rel_id);

        return true;
    }

    /**
     * Link invoice_id on all active tracker rows for a proposal.
     *
     * @param int $proposal_id
     * @param int $invoice_id
     * @return void
     */
    public function link_invoice($proposal_id, $invoice_id)
    {
        $proposal_id = (int) $proposal_id;
        $invoice_id  = (int) $invoice_id;
        if ($proposal_id < 1 || $invoice_id < 1) {
            return;
        }

        $this->db->where('rel_type', 'proposal');
        $this->db->where('rel_id', $proposal_id);
        $this->db->where('deleted_at IS NULL', null, false);
        $this->db->update($this->table, [
            'invoice_id' => $invoice_id,
        ]);
    }

    /**
     * Progress stats for a proposal tracker.
     *
     * @param int $proposal_id
     * @return array{total:int,received:int}
     */
    public function get_progress($proposal_id)
    {
        $items = $this->get($proposal_id);
        $total = count($items);
        $received = 0;
        foreach ($items as $item) {
            if ($item['item_status'] === 'received') {
                $received++;
            }
        }

        return [
            'total'    => $total,
            'received' => $received,
        ];
    }

    /**
     * Client-area: Accepted proposals with trackers for a customer.
     *
     * @param int $client_id
     * @return array
     */
    public function get_client_trackers($client_id)
    {
        $client_id = (int) $client_id;
        $p         = db_prefix() . 'proposals';
        $t         = $this->table;

        $sql = "SELECT {$p}.id, {$p}.subject, {$p}.date, {$p}.status, {$p}.quotation_status,
                       {$p}.invoice_id, {$p}.currency, {$p}.hash,
                       (SELECT COUNT(*) FROM {$t} ti WHERE ti.rel_type = 'proposal' AND ti.rel_id = {$p}.id AND ti.deleted_at IS NULL) AS item_total,
                       (SELECT COUNT(*) FROM {$t} ti WHERE ti.rel_type = 'proposal' AND ti.rel_id = {$p}.id AND ti.deleted_at IS NULL AND ti.item_status = 'received') AS item_received
                FROM {$p}
                WHERE {$p}.rel_type = 'customer'
                  AND {$p}.rel_id = ?
                  AND {$p}.status = 3
                  AND EXISTS (
                      SELECT 1 FROM {$t} tx
                      WHERE tx.rel_type = 'proposal' AND tx.rel_id = {$p}.id AND tx.deleted_at IS NULL
                  )
                ORDER BY {$p}.date DESC, {$p}.id DESC";

        return $this->db->query($sql, [$client_id])->result_array();
    }

    /**
     * Verify client owns the proposal and it has a tracker.
     *
     * @param int $proposal_id
     * @param int $client_id
     * @return object|null proposal row
     */
    public function get_client_proposal_tracker($proposal_id, $client_id)
    {
        $this->db->where('id', (int) $proposal_id);
        $this->db->where('rel_type', 'customer');
        $this->db->where('rel_id', (int) $client_id);
        $this->db->where('status', 3);
        $proposal = $this->db->get(db_prefix() . 'proposals')->row();

        if (!$proposal || !$this->has_tracker($proposal_id)) {
            return null;
        }

        return $proposal;
    }
}
