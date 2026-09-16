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
        if (!is_admin() && staff_cant('view', 'otmain_item_tracker') && staff_cant('view', 'invoices') && staff_cant('view', 'proposals')) {
            access_denied('otmain_recycle_bin');
        }

        $items = $this->recycle_bin_model->get_all_bin_items();
        foreach ($items as &$item) {
            $binType = $item['bin_type'] ?? 'file';
            if ($binType === 'file') {
                $item['related_label'] = $this->format_related_label($item);
                $item['display_name']  = $item['file_name'] ?? '-';
                $item['type_label']    = $this->format_type_label($item['rel_type'] ?? 'file');
            } else {
                $item['related_label'] = $item['doc_label'] ?? '-';
                $item['display_name']  = $item['doc_label'] ?? '-';
                $item['type_label']    = $this->format_type_label($item['doc_type'] ?? 'document');
            }
            $item['days_remaining'] = $this->days_remaining($item['deleted_at'] ?? null);
        }
        unset($item);

        $data['title']      = _l('otmain_recycle_bin');
        $data['items']      = $items;
        $data['purge_days'] = Recycle_bin_model::PURGE_DAYS;

        $this->load->view('recycle_bin/manage', $data);
    }

    public function restore($id = '')
    {
        if (!is_admin() && staff_cant('edit', 'otmain_item_tracker') && staff_cant('edit', 'invoices')) {
            access_denied('otmain_recycle_bin');
        }

        $id = (int) $id;

        $binType = $this->input->get('type') ?: 'file';
        $docType = $this->input->get('doc_type') ?: '';

        if ($binType === 'document' && $docType) {
            $success = $this->recycle_bin_model->restore_document($docType, $id);
        } else {
            $success = $this->recycle_bin_model->restore_file($id);
        }

        if ($success) {
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

        $binType = $this->input->get('type') ?: 'file';
        $docType = $this->input->get('doc_type') ?: '';

        if ($binType === 'document' && $docType) {
            $success = $this->recycle_bin_model->permanently_delete_document($docType, $id);
        } else {
            $success = $this->recycle_bin_model->permanently_delete_file($id);
        }

        if ($success) {
            set_alert('success', _l('otmain_recycle_bin_permanently_deleted'));
        } else {
            set_alert('danger', _l('otmain_recycle_bin_delete_failed'));
        }

        redirect(admin_url('otmain/recycle_bin'));
    }

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

    protected function days_remaining($deletedAt)
    {
        if (empty($deletedAt)) {
            return Recycle_bin_model::PURGE_DAYS;
        }

        $expires = strtotime($deletedAt . ' +' . Recycle_bin_model::PURGE_DAYS . ' days');
        $days    = (int) ceil(($expires - time()) / 86400);

        return max(0, $days);
    }

    protected function format_type_label($type)
    {
        $map = [
            'proposal'       => _l('proposal'),
            'invoice'        => _l('invoice'),
            'estimate'       => _l('estimate'),
            'credit_note'    => _l('credit_note'),
            'packing_list'   => _l('otmain_packing_list'),
            'purchase_order' => _l('otmain_purchase_order'),
            'item_tracker'   => _l('otmain_item_tracker'),
        ];

        return $map[$type] ?? ucfirst(str_replace('_', ' ', $type));
    }
}
