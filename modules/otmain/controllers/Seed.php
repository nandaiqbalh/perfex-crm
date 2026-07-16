<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Seed extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        if (!is_admin()) {
            access_denied('otmain');
        }
    }

    public function index()
    {
        $force     = (bool) $this->input->get('force');
        $repair    = (bool) $this->input->get('repair');
        $customers = (bool) $this->input->get('customers');

        $this->load->library('otmain/otmain_seed');

        if ($customers && !$force && !$repair) {
            $upsert = $this->otmain_seed->upsertCustomers();
            $result = [
                'status'  => 'success',
                'message' => 'Customers upserted from seed/customers.php: ' . (int) $upsert['count'] . ' companies.',
                'stats'   => [
                    'customers_upserted' => (int) $upsert['count'],
                ],
                'summary' => null,
                'links'   => [
                    'seed' => admin_url('otmain/seed'),
                ],
            ];
        } elseif ($repair && !$force) {
            $result = $this->otmain_seed->repairRelations();
        } else {
            $result = $this->otmain_seed->run($force);
        }

        $data['title']  = 'OT-Main Production Seed';
        $data['result'] = $result;
        $this->load->view('seed/result', $data);
    }
}
