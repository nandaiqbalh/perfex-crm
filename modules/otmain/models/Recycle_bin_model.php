<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Recycle_bin_model extends App_Model
{
    public const PURGE_DAYS = 30;
    public const PURGE_BATCH = 50;

    protected $table;

    public function __construct()
    {
        parent::__construct();
        $this->table = db_prefix() . 'files';
        $this->load->helper('otmain/otmain');
    }

    // ── File bin methods ────────────────────────────────────────────

    public function count_bin_items()
    {
        return $this->count_bin_files() + $this->count_bin_documents();
    }

    public function count_bin_files()
    {
        if (!otmain_files_soft_delete_enabled()) {
            return 0;
        }

        $this->db->where('deleted_at IS NOT NULL', null, false);
        $this->db->where_in('rel_type', otmain_recycle_bin_rel_types());

        return (int) $this->db->count_all_results($this->table);
    }

    public function get_bin_files()
    {
        if (!otmain_files_soft_delete_enabled()) {
            return [];
        }

        $f = $this->table;
        $s = db_prefix() . 'staff';

        $sql = "SELECT {$f}.*, 'file' AS bin_type,
                       CONCAT({$s}.firstname, ' ', {$s}.lastname) AS deleted_by_name
                FROM {$f}
                LEFT JOIN {$s} ON {$s}.staffid = {$f}.deleted_by
                WHERE {$f}.deleted_at IS NOT NULL
                  AND {$f}.rel_type IN ('invoice','proposal','estimate','credit_note','item_tracker')
                ORDER BY {$f}.deleted_at DESC";

        return $this->db->query($sql)->result_array();
    }

    public function get_bin_file($id)
    {
        if (!otmain_files_soft_delete_enabled()) {
            return null;
        }

        $this->db->where('id', (int) $id);
        $this->db->where('deleted_at IS NOT NULL', null, false);
        $this->db->where_in('rel_type', otmain_recycle_bin_rel_types());

        return $this->db->get($this->table)->row();
    }

    public function restore_file($id)
    {
        return otmain_restore_file((int) $id);
    }

    public function permanently_delete_file($id)
    {
        $file = $this->get_bin_file($id);
        if (!$file) {
            return false;
        }

        return otmain_permanently_delete_file($file);
    }

    // ── Document bin methods ────────────────────────────────────────

    public function count_bin_documents()
    {
        $count = 0;
        foreach (otmain_recycle_bin_doc_types() as $type) {
            $table = otmain_doc_type_to_table($type);
            if (!$table || !otmain_doc_soft_delete_enabled($table)) {
                continue;
            }
            $fullTable = db_prefix() . $table;
            $this->db->where('deleted_at IS NOT NULL', null, false);
            $count += (int) $this->db->count_all_results($fullTable);
        }
        return $count;
    }

    public function get_bin_documents()
    {
        $results = [];
        $s = db_prefix() . 'staff';

        foreach (otmain_recycle_bin_doc_types() as $type) {
            $table = otmain_doc_type_to_table($type);
            if (!$table || !otmain_doc_soft_delete_enabled($table)) {
                continue;
            }
            $fullTable = db_prefix() . $table;

            $sql = "SELECT d.id, d.deleted_at, d.deleted_by, 'document' AS bin_type,
                           '{$type}' AS doc_type,
                           CONCAT(s.firstname, ' ', s.lastname) AS deleted_by_name
                    FROM {$fullTable} d
                    LEFT JOIN {$s} s ON s.staffid = d.deleted_by
                    WHERE d.deleted_at IS NOT NULL
                    ORDER BY d.deleted_at DESC";

            $rows = $this->db->query($sql)->result_array();

            foreach ($rows as &$row) {
                $row['doc_label'] = $this->format_doc_label($type, $row);
            }
            unset($row);

            $results = array_merge($results, $rows);
        }

        // Sort by deleted_at descending
        usort($results, function ($a, $b) {
            return strcmp($b['deleted_at'], $a['deleted_at']);
        });

        return $results;
    }

    public function get_bin_document($type, $id)
    {
        $table = otmain_doc_type_to_table($type);
        if (!$table || !otmain_doc_soft_delete_enabled($table)) {
            return null;
        }

        $fullTable = db_prefix() . $table;
        $this->db->where('id', (int) $id);
        $this->db->where('deleted_at IS NOT NULL', null, false);
        return $this->db->get($fullTable)->row();
    }

    public function restore_document($type, $id)
    {
        return otmain_restore_document($type, (int) $id);
    }

    public function permanently_delete_document($type, $id)
    {
        return otmain_permanently_delete_document($type, (int) $id);
    }

    // ── Combined items for view ─────────────────────────────────────

    public function get_all_bin_items()
    {
        $files = $this->get_bin_files();
        $docs  = $this->get_bin_documents();
        $all   = array_merge($files, $docs);

        usort($all, function ($a, $b) {
            return strcmp($b['deleted_at'], $a['deleted_at']);
        });

        return $all;
    }

    // ── Purge ───────────────────────────────────────────────────────

    public function purge_expired()
    {
        $count = 0;

        // Purge files
        if (otmain_files_soft_delete_enabled()) {
            $cutoff = date('Y-m-d H:i:s', strtotime('-' . self::PURGE_DAYS . ' days'));
            $types  = otmain_recycle_bin_rel_types();

            $this->db->where('deleted_at IS NOT NULL', null, false);
            $this->db->where('deleted_at <', $cutoff);
            $this->db->where_in('rel_type', $types);
            $this->db->limit(self::PURGE_BATCH);
            $files = $this->db->get($this->table)->result();

            foreach ($files as $file) {
                if (otmain_permanently_delete_file($file)) {
                    $count++;
                }
            }
        }

        // Purge documents
        foreach (otmain_recycle_bin_doc_types() as $type) {
            $table = otmain_doc_type_to_table($type);
            if (!$table || !otmain_doc_soft_delete_enabled($table)) {
                continue;
            }

            $fullTable = db_prefix() . $table;
            $cutoff    = date('Y-m-d H:i:s', strtotime('-' . self::PURGE_DAYS . ' days'));

            $this->db->where('deleted_at IS NOT NULL', null, false);
            $this->db->where('deleted_at <', $cutoff);
            $this->db->limit(self::PURGE_BATCH);
            $docs = $this->db->get($fullTable)->result();

            foreach ($docs as $doc) {
                if (otmain_permanently_delete_document($type, $doc->id)) {
                    $count++;
                }
            }
        }

        if ($count > 0) {
            log_activity('OT-Main Recycle Bin: permanently deleted ' . $count . ' expired item(s)');
        }

        return $count;
    }

    // ── Helpers ─────────────────────────────────────────────────────

    protected function format_doc_label($type, $row)
    {
        $id = (int) ($row['id'] ?? 0);
        switch ($type) {
            case 'proposal':
                return _l('proposal') . ': ' . format_proposal_number($id);
            case 'invoice':
                return _l('invoice') . ': ' . format_invoice_number($id);
            case 'estimate':
                return _l('estimate') . ': ' . (function_exists('format_estimate_number') ? format_estimate_number($id) : '#' . $id);
            case 'credit_note':
                return _l('credit_note') . ': ' . (function_exists('format_credit_note_number') ? format_credit_note_number($id) : '#' . $id);
            case 'packing_list':
                return _l('otmain_packing_list') . ': ' . (function_exists('otmain_format_packing_list_number') ? otmain_format_packing_list_number($id) : 'PL-' . $id);
            case 'purchase_order':
                return _l('otmain_purchase_order') . ': ' . (function_exists('otmain_format_purchase_order_number') ? otmain_format_purchase_order_number($id) : 'PO-' . $id);
            default:
                return ucfirst(str_replace('_', ' ', $type)) . ' #' . $id;
        }
    }
}
