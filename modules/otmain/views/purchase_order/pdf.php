<?php defined('BASEPATH') or exit('No direct script access allowed');
$CI->load->helper('otmain/otmain');

$currencyName = otmain_po_currency_name($po);

$pdf->writeHTML(otmain_pdf_purchase_order_header_html($po), true, false, false, false, '');

$pdf->Ln(hooks()->apply_filters('pdf_info_and_table_separator', 6));

$pdf->writeHTML(otmain_pdf_po_items_table_html($po, $currencyName), true, false, false, false, '');

$pdf->Ln(4);
$pdf->writeHTML(otmain_pdf_purchase_order_footer_html($po, $currencyName), true, false, false, false, '');
