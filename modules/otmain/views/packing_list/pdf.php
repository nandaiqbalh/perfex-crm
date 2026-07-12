<?php defined('BASEPATH') or exit('No direct script access allowed');
$CI->load->helper('otmain/otmain');

$pdf->writeHTML(otmain_pdf_packing_list_html($packing), true, false, false, false, '');

// Terms & Conditions page dihapus sesuai permintaan.
// otmain_pdf_append_invoice_tc_page($pdf);
