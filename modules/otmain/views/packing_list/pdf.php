<?php defined('BASEPATH') or exit('No direct script access allowed');
$CI->load->helper('otmain/otmain');

$pdf->writeHTML(otmain_pdf_packing_list_html($packing), true, false, false, false, '');

// Packing List & Invoice uses the same T&C and account proof pages as Invoice PDF.
$currencyName = !empty($packing->currency_name) ? $packing->currency_name : 'EUR';
otmain_pdf_append_invoice_terms($pdf, $font_name, $font_size, $currencyName);
