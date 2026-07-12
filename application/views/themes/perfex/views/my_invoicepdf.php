<?php

defined('BASEPATH') or exit('No direct script access allowed');

$CI->load->helper('otmain/otmain');

$pdf->writeHTML(otmain_pdf_invoice_header_html($invoice, $invoice_number), true, false, false, false, '');

$pdf->Ln(hooks()->apply_filters('pdf_info_and_table_separator', 6));

$itemsTable = get_items_table_data($invoice, 'invoice', 'pdf');
$pdf->writeHTML(otmain_pdf_items_table_html($invoice->items, 'invoice', $invoice->id, $invoice->currency_name), true, false, false, false, '');

$pdf->Ln(4);
$pdf->writeHTML(otmain_pdf_invoice_footer_html($invoice, $itemsTable, $invoice->currency_name), true, false, false, false, '');

otmain_pdf_append_invoice_terms($pdf, $font_name, $font_size, $invoice->currency_name, $invoice);
