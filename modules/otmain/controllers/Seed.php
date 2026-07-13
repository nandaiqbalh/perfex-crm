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
        $reset = (bool) $this->input->get('reset');
        // reset=1 implies force (wipe documents, then seed)
        if ($reset) {
            $force = true;
        }

        $this->load->library('otmain/otmain_seed');
        $result = $this->otmain_seed->run($force, $reset);

        $data['title']  = 'OT-Main Production Seed';
        $data['result'] = $result;
        $this->load->view('seed/result', $data);
    }
}
