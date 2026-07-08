<?php

defined('BASEPATH') or exit('No direct script access allowed');

$CI->load->helper('otmain/otmain');

$pdf->writeHTML(otmain_pdf_proposal_header_html($proposal, $number), true, false, false, false, '');

$pdf->Ln(hooks()->apply_filters('pdf_info_and_table_separator', 6));

$itemsTable = get_items_table_data($proposal, 'proposal', 'pdf');
$pdf->writeHTML(otmain_pdf_items_table_html($proposal->items, 'proposal', $proposal->id, $proposal->currency_name), true, false, false, false, '');

$pdf->Ln(4);
$pdf->writeHTML(otmain_pdf_proposal_footer_html($proposal, $itemsTable, $proposal->currency_name), true, false, false, false, '');

otmain_pdf_append_quotation_terms($pdf, $font_name, $font_size, $proposal->currency_name);

