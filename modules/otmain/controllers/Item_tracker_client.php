<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Item_tracker_client extends ClientsController
{
    public function __construct()
    {
        parent::__construct();

        if (!is_client_logged_in()) {
            redirect_after_login_to_current_url();
            redirect(site_url('authentication/login'));
        }

        $this->load->model('otmain/item_tracker_model');
        $this->load->helper('otmain/otmain');
    }

    public function index()
    {
        $clientId = get_client_user_id();
        $trackers = $this->item_tracker_model->get_client_trackers($clientId);

        $data['trackers'] = $trackers;
        $data['title']    = _l('otmain_item_tracker');

        $this->data($data);
        $this->view('themes/' . active_clients_theme() . '/views/item_tracker');
        $this->layout(true);
    }

    public function detail($proposal_id = '')
    {
        $proposal_id = (int) $proposal_id;
        $clientId    = get_client_user_id();

        $proposal = $this->item_tracker_model->get_client_proposal_tracker($proposal_id, $clientId);
        if (!$proposal) {
            set_alert('warning', _l('access_denied'));
            redirect(site_url('otmain/item_tracker_client'));
        }

        $items = $this->item_tracker_model->get($proposal_id);

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

        $data['proposal'] = $proposal;
        $data['items']    = $items;
        $data['invoice']  = $invoice;
        $data['progress'] = $this->item_tracker_model->get_progress($proposal_id);
        $data['title']    = _l('otmain_item_tracker') . ' — ' . format_proposal_number($proposal_id);

        $this->data($data);
        $this->view('themes/' . active_clients_theme() . '/views/item_tracker_detail');
        $this->layout(true);
    }
}
