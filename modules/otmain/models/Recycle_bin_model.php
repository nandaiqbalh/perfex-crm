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

    /**
     * Count files currently in the recycle bin.
     *
     * @return int
     */
    public function count_bin_items()
    {
        if (!otmain_files_soft_delete_enabled()) {
            return 0;
        }

        $this->db->where('deleted_at IS NOT NULL', null, false);
        $this->db->where_in('rel_type', otmain_recycle_bin_rel_types());

        return (int) $this->db->count_all_results($this->table);
    }

    /**
     * List soft-deleted sales / item tracker files.
     *
     * @return array
     */
    public function get_bin_items()
    {
        if (!otmain_files_soft_delete_enabled()) {
            return [];
        }

        $f = $this->table;
        $s = db_prefix() . 'staff';

        $sql = "SELECT {$f}.*,
                       CONCAT({$s}.firstname, ' ', {$s}.lastname) AS deleted_by_name
                FROM {$f}
                LEFT JOIN {$s} ON {$s}.staffid = {$f}.deleted_by
                WHERE {$f}.deleted_at IS NOT NULL
                  AND {$f}.rel_type IN ('invoice','proposal','estimate','credit_note','item_tracker')
                ORDER BY {$f}.deleted_at DESC";

        return $this->db->query($sql)->result_array();
    }

    /**
     * Get a single bin item by id.
     *
     * @param int $id
     * @return object|null
     */
    public function get_bin_item($id)
    {
        if (!otmain_files_soft_delete_enabled()) {
            return null;
        }

        $this->db->where('id', (int) $id);
        $this->db->where('deleted_at IS NOT NULL', null, false);
        $this->db->where_in('rel_type', otmain_recycle_bin_rel_types());

        return $this->db->get($this->table)->row();
    }

    /**
     * Restore file from bin.
     *
     * @param int $id
     * @return bool
     */
    public function restore($id)
    {
        return otmain_restore_file((int) $id);
    }

    /**
     * Permanently delete one bin item.
     *
     * @param int $id
     * @return bool
     */
    public function permanently_delete($id)
    {
        $file = $this->get_bin_item($id);
        if (!$file) {
            return false;
        }

        return otmain_permanently_delete_file($file);
    }

    /**
     * Purge files older than 30 days. Safe for shared hosting (batched).
     *
     * @return int number permanently deleted
     */
    public function purge_expired()
    {
        if (!otmain_files_soft_delete_enabled()) {
            return 0;
        }

        $cutoff = date('Y-m-d H:i:s', strtotime('-' . self::PURGE_DAYS . ' days'));
        $types  = otmain_recycle_bin_rel_types();

        $this->db->where('deleted_at IS NOT NULL', null, false);
        $this->db->where('deleted_at <', $cutoff);
        $this->db->where_in('rel_type', $types);
        $this->db->limit(self::PURGE_BATCH);
        $files = $this->db->get($this->table)->result();

        $count = 0;
        foreach ($files as $file) {
            if (otmain_permanently_delete_file($file)) {
                $count++;
            }
        }

        if ($count > 0) {
            log_activity('OT-Main Recycle Bin: permanently deleted ' . $count . ' expired file(s)');
        }

        return $count;
    }
}
