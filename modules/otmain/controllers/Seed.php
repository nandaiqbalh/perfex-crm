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
        $force = (bool) $this->input->get('force');
        $this->load->library('otmain/otmain_seed');
        $result = $this->otmain_seed->run($force);

        $data['title']  = 'OT-Main Production Seed';
        $data['result'] = $result;
        $this->load->view('seed/result', $data);
    }
}
