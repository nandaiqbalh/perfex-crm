<?php

defined('BASEPATH') or exit('No direct script access allowed');

$CI->load->helper('otmain/otmain');

$pdf->writeHTML(otmain_pdf_quotation_header_html($estimate, $estimate_number), true, false, false, false, '');

$pdf->Ln(hooks()->apply_filters('pdf_info_and_table_separator', 6));

$itemsTable = get_items_table_data($estimate, 'estimate', 'pdf');
$pdf->writeHTML(otmain_pdf_items_table_html($estimate->items, 'estimate', $estimate->id, $estimate->currency_name), true, false, false, false, '');

$pdf->Ln(4);
$pdf->writeHTML(otmain_pdf_quotation_footer_html($estimate, $itemsTable, $estimate->currency_name), true, false, false, false, '');

otmain_pdf_append_quotation_terms($pdf, $font_name, $font_size, $estimate->currency_name);
