<?php defined('BASEPATH') or exit('No direct script access allowed');
$CI->load->helper('otmain/otmain');

$pdf->writeHTML(otmain_pdf_packing_list_html($packing), true, false, false, false, '');

// Packing List uses the same T&C page as Invoice PDF (no account detail proof).
otmain_pdf_append_invoice_tc_page($pdf);
