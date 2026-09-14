<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Recycle_bin extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('recycle_bin_model');
        $this->load->helper('otmain/otmain');
    }

    public function index()
    {
        if (!is_admin() && staff_cant('view', 'otmain_item_tracker') && staff_cant('delete', 'invoices')) {
            access_denied('otmain_recycle_bin');
        }

        $items = $this->recycle_bin_model->get_bin_items();
        foreach ($items as &$item) {
            $item['related_label'] = $this->format_related_label($item);
            $item['days_remaining'] = $this->days_remaining($item['deleted_at'] ?? null);
        }
        unset($item);

        $data['title'] = _l('otmain_recycle_bin');
        $data['items'] = $items;
        $data['purge_days'] = Recycle_bin_model::PURGE_DAYS;

        $this->load->view('recycle_bin/manage', $data);
    }

    public function restore($id = '')
    {
        if (!is_admin() && staff_cant('edit', 'otmain_item_tracker') && staff_cant('edit', 'invoices')) {
            access_denied('otmain_recycle_bin');
        }

        $id = (int) $id;
        if ($this->recycle_bin_model->restore($id)) {
            set_alert('success', _l('otmain_recycle_bin_restored'));
        } else {
            set_alert('danger', _l('otmain_recycle_bin_restore_failed'));
        }

        redirect(admin_url('otmain/recycle_bin'));
    }

    public function permanently_delete($id = '')
    {
        if (!is_admin() && staff_cant('delete', 'otmain_item_tracker') && staff_cant('delete', 'invoices')) {
            access_denied('otmain_recycle_bin');
        }

        $id = (int) $id;
        if ($this->recycle_bin_model->permanently_delete($id)) {
            set_alert('success', _l('otmain_recycle_bin_permanently_deleted'));
        } else {
            set_alert('danger', _l('otmain_recycle_bin_delete_failed'));
        }

        redirect(admin_url('otmain/recycle_bin'));
    }

    /**
     * @param array $item
     * @return string
     */
    protected function format_related_label(array $item)
    {
        $type  = $item['rel_type'] ?? '';
        $relId = (int) ($item['rel_id'] ?? 0);

        switch ($type) {
            case 'invoice':
                return _l('invoice') . ': ' . format_invoice_number($relId);
            case 'proposal':
                return _l('proposal') . ': ' . format_proposal_number($relId);
            case 'estimate':
                return _l('estimate') . ': ' . (function_exists('format_estimate_number') ? format_estimate_number($relId) : '#' . $relId);
            case 'credit_note':
                return _l('credit_note') . ': ' . (function_exists('format_credit_note_number') ? format_credit_note_number($relId) : '#' . $relId);
            case 'item_tracker':
                return _l('otmain_item_tracker') . ': ' . format_proposal_number($relId);
            default:
                return ucfirst($type) . ' #' . $relId;
        }
    }

    /**
     * @param string|null $deletedAt
     * @return int
     */
    protected function days_remaining($deletedAt)
    {
        if (empty($deletedAt)) {
            return Recycle_bin_model::PURGE_DAYS;
        }

        $expires = strtotime($deletedAt . ' +' . Recycle_bin_model::PURGE_DAYS . ' days');
        $days    = (int) ceil(($expires - time()) / 86400);

        return max(0, $days);
    }
}
