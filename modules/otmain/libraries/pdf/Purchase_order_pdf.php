<?php

defined('BASEPATH') or exit('No direct script access allowed');

include_once APPPATH . 'libraries/pdf/App_pdf.php';

class Purchase_order_pdf extends App_pdf
{
    protected $purchase_order;

    public function __construct($purchase_order)
    {
        parent::__construct();
        $this->purchase_order = $purchase_order;
        $this->SetTitle(otmain_purchase_order_pdf_filename($purchase_order));
    }

    public function prepare()
    {
        $this->set_view_vars(['po' => $this->purchase_order]);
        return $this->build();
    }

    protected function type()
    {
        return 'purchase_order';
    }

    protected function file_path()
    {
        return module_dir_path('otmain', 'views/purchase_order/pdf.php');
    }
}
