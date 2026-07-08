<?php

defined('BASEPATH') or exit('No direct script access allowed');

include_once APPPATH . 'libraries/pdf/App_pdf.php';

class Packing_list_pdf extends App_pdf
{
    protected $packing;

    public function __construct($packing)
    {
        parent::__construct();
        $this->packing = $packing;
        $this->SetTitle(otmain_packing_list_pdf_filename($packing));
    }

    public function prepare()
    {
        $this->set_view_vars(['packing' => $this->packing]);
        return $this->build();
    }

    protected function type()
    {
        return 'packing_list';
    }

    protected function file_path()
    {
        return module_dir_path('otmain', 'views/packing_list/pdf.php');
    }
}
