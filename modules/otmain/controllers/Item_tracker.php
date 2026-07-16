<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Item_tracker extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('item_tracker_model');
        $this->load->helper('otmain/otmain');
    }

    public function index()
    {
        if (staff_cant('view', 'otmain_item_tracker')) {
            access_denied('otmain_item_tracker');
        }

        $data['title']              = _l('otmain_item_tracker');
        $data['quotation_statuses'] = otmain_quotation_status_options();
        $this->load->view('item_tracker/manage', $data);
    }

    public function table()
    {
        if (staff_cant('view', 'otmain_item_tracker')) {
            ajax_access_denied();
        }

        $this->app->get_table_data(module_views_path(OTMAIN_MODULE_NAME, 'item_tracker/table'));
    }

    public function detail($proposal_id = '')
    {
        if (staff_cant('view', 'otmain_item_tracker')) {
            access_denied('otmain_item_tracker');
        }

        $proposal_id = (int) $proposal_id;
        if ($proposal_id < 1) {
            show_404();
        }

        $this->load->model('proposals_model');
        $proposal = $this->proposals_model->get($proposal_id);
        if (!$proposal) {
            show_404();
        }

        if ($this->input->post()) {
            if (staff_cant('edit', 'otmain_item_tracker')) {
                access_denied('otmain_item_tracker');
            }

            $items  = $this->input->post('items');
            $result = $this->item_tracker_model->update_items($proposal_id, is_array($items) ? $items : []);

            if ($result === true) {
                $qStatus = $this->input->post('quotation_status');
                if ($qStatus) {
                    $this->item_tracker_model->update_quotation_status($proposal_id, $qStatus);
                }
                set_alert('success', _l('updated_successfully', _l('otmain_item_tracker')));
            } elseif ($result === 'eta_date_required') {
                set_alert('danger', _l('otmain_eta_date_required'));
            } else {
                set_alert('danger', _l('problem_updating', _l('otmain_item_tracker')));
            }

            redirect(admin_url('otmain/item_tracker/detail/' . $proposal_id));
        }

        if (!$this->item_tracker_model->has_tracker($proposal_id)) {
            $data['proposal']           = $proposal;
            $data['items']              = [];
            $data['no_tracker']         = true;
            $data['title']              = _l('otmain_item_tracker') . ' / ' . format_proposal_number($proposal_id);
            $data['item_statuses']      = otmain_item_tracker_status_options();
            $data['quotation_statuses'] = otmain_quotation_status_options();
            $this->load->view('item_tracker/detail', $data);

            return;
        }

        $items = $this->item_tracker_model->get($proposal_id);

        // Enrich with staff names for last updated
        $staffIds = [];
        foreach ($items as $item) {
            if (!empty($item['updated_by'])) {
                $staffIds[] = (int) $item['updated_by'];
            }
        }
        $staffMap = [];
        if (!empty($staffIds)) {
            $staffRows = $this->db->select('staffid, firstname, lastname')
                ->where_in('staffid', array_unique($staffIds))
                ->get(db_prefix() . 'staff')
                ->result_array();
            foreach ($staffRows as $row) {
                $staffMap[(int) $row['staffid']] = trim($row['firstname'] . ' ' . $row['lastname']);
            }
        }
        foreach ($items as &$item) {
            $item['updated_by_name'] = !empty($item['updated_by']) && isset($staffMap[(int) $item['updated_by']])
                ? $staffMap[(int) $item['updated_by']]
                : '';
        }
        unset($item);

        $invoice = null;
        $invoiceId = null;
        foreach ($items as $item) {
            if (!empty($item['invoice_id'])) {
                $invoiceId = (int) $item['invoice_id'];
                break;
            }
        }
        if (!$invoiceId && !empty($proposal->invoice_id)) {
            $invoiceId = (int) $proposal->invoice_id;
        }
        if ($invoiceId) {
            $this->load->model('invoices_model');
            $invoice = $this->invoices_model->get($invoiceId);
        }

        $data['proposal']           = $proposal;
        $data['items']              = $items;
        $data['invoice']            = $invoice;
        $data['no_tracker']         = false;
        $data['progress']           = $this->item_tracker_model->get_progress($proposal_id);
        $data['item_statuses']      = otmain_item_tracker_status_options();
        $data['quotation_statuses'] = otmain_quotation_status_options();
        $data['title']              = _l('otmain_item_tracker') . ' / ' . format_proposal_number($proposal_id);
        $data['currency']           = get_currency($proposal->currency);

        $this->load->view('item_tracker/detail', $data);
    }

    public function update_quotation_status($proposal_id = '')
    {
        if (staff_cant('edit', 'otmain_item_tracker')) {
            ajax_access_denied();
        }

        $proposal_id = (int) $proposal_id;
        $status      = $this->input->post('quotation_status');

        if (!$proposal_id || !$status) {
            echo json_encode(['success' => false]);

            return;
        }

        $ok = $this->item_tracker_model->update_quotation_status($proposal_id, $status);
        echo json_encode([
            'success' => $ok,
            'badge'   => otmain_format_quotation_status($status),
        ]);
    }

    public function delete_item($item_id = '')
    {
        if (staff_cant('delete', 'otmain_item_tracker')) {
            access_denied('otmain_item_tracker');
        }

        $item_id = (int) $item_id;
        $item    = $this->item_tracker_model->get_item($item_id);
        if (!$item) {
            show_404();
        }

        $proposal_id = (int) $item->rel_id;
        $this->item_tracker_model->soft_delete($item_id);
        set_alert('success', _l('deleted', _l('otmain_item_tracker')));
        redirect(admin_url('otmain/item_tracker/detail/' . $proposal_id));
    }

    public function backfill($proposal_id = '')
    {
        if (staff_cant('edit', 'otmain_item_tracker')) {
            access_denied('otmain_item_tracker');
        }

        $proposal_id = (int) $proposal_id;
        if ($proposal_id < 1) {
            show_404();
        }

        $ok = $this->item_tracker_model->backfill_proposal($proposal_id);
        if ($ok) {
            set_alert('success', _l('otmain_tracker_backfill_success'));
        } else {
            set_alert('warning', _l('otmain_tracker_backfill_exists'));
        }

        redirect(admin_url('otmain/item_tracker/detail/' . $proposal_id));
    }

    public function backfill_all()
    {
        if (staff_cant('edit', 'otmain_item_tracker')) {
            access_denied('otmain_item_tracker');
        }

        $count = $this->item_tracker_model->backfill_all();
        set_alert('success', sprintf(_l('otmain_tracker_backfill_all_done'), $count));
        redirect(admin_url('otmain/item_tracker'));
    }

    /**
     * Resync catalog fields for every existing tracker from its proposal (seed + manual).
     */
    public function resync_all()
    {
        if (staff_cant('edit', 'otmain_item_tracker')) {
            access_denied('otmain_item_tracker');
        }

        $result = $this->item_tracker_model->sync_all_from_proposals();
        set_alert(
            'success',
            sprintf(
                _l('otmain_tracker_resync_all_done'),
                (int) ($result['synced'] ?? 0),
                (int) ($result['skipped'] ?? 0)
            )
        );
        redirect(admin_url('otmain/item_tracker'));
    }
}
