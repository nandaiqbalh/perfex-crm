<?php

defined('BASEPATH') or exit('No direct script access allowed');

function otmain_sales_number_format($number, $data)
{
    $format         = $data['format'];
    $date           = $data['date'];
    $originalNumber = $data['number'];
    $prefixPadding  = $data['prefix_padding'];
    $prefix         = '';

    if ($format == 5) {
        $prefix = get_option('estimate_prefix');
        $number = date('d', strtotime($date)) . '-' . date('Y', strtotime($date)) . '-' . $prefix . str_pad($originalNumber, 3, '0', STR_PAD_LEFT);
    } elseif ($format == 6) {
        $prefix = get_option('invoice_prefix');
        $number = date('Y', strtotime($date)) . '-' . $prefix . str_pad($originalNumber, 3, '0', STR_PAD_LEFT);
    } elseif ($format == 7) {
        $prefix = get_option('otmain_purchase_order_prefix');
        $number = date('Y', strtotime($date)) . '-' . $prefix . str_pad($originalNumber, 3, '0', STR_PAD_LEFT);
    }

    return $number;
}

function otmain_format_estimate_number($number, $data)
{
    $CI = &get_instance();
    $CI->db->select('quote_title')->where('id', $data['id']);
    $row = $CI->db->get(db_prefix() . 'estimates')->row();

    if ($row && !empty($row->quote_title)) {
        $number .= ' - ' . $row->quote_title;
    }

    return $number;
}

function otmain_format_invoice_number($number, $data)
{
    $CI = &get_instance();
    $CI->db->select('invoice_title')->where('id', $data['id']);
    $row = $CI->db->get(db_prefix() . 'invoices')->row();

    if ($row && !empty($row->invoice_title)) {
        $number .= ' - ' . $row->invoice_title;
    }

    return $number;
}

function otmain_get_quotation_terms()
{
    return 'These Terms & Conditions apply to all quotations, offers, and agreements issued by OT-Main ("Seller") unless otherwise agreed in writing.

1. Validity of Quotation
All quotations are valid for 30 days from the date of issue, unless stated otherwise in writing. After this period, prices, delivery times, and conditions may be subject to change.

2. Prices
All prices are quoted excluding VAT, import duties, customs fees, withholding taxes, bank charges, and any other applicable local taxes, unless explicitly stated otherwise.
Prices are based on current material, labor, and logistics costs and may be adjusted in case of significant cost increases.

3. Payment Terms
Unless otherwise agreed in writing, payment terms are:
- Advance payment required before production or shipment, or
- As specified in the quotation (e.g. 50% down payment / 50% before shipment).
All payments must be made in full and without any deductions or set-offs.
Late payments may be subject to interest and may result in suspension of work or delivery.

4. Delivery & Lead Time
All delivery times are estimates only and are based on information available at the time of quotation. The Seller shall not be held liable for any delay in delivery resulting from, but not limited to:
- Force majeure events
- Delays caused by suppliers or manufacturers
- Transportation, logistics, or customs clearance issues
- Any changes or variations requested by the Buyer after order placement
- Public holidays, company shutdowns, or holiday periods, during which the Seller has no control over production, logistics, or delivery schedules once the order has been confirmed.
Any delivery delay does not entitle the Buyer to cancel the order or claim damages, unless otherwise agreed in writing.

5. Scope of Supply
Only the items and services specifically listed in the quotation are included. Any additional work, materials, or services requested by the Buyer will be treated as variation orders and charged separately.

6. Technical Information & Drawings
All drawings, technical documents, custom made software and specifications remain the intellectual property of OT-Main unless otherwise agreed. These may not be copied, shared, or used for third-party manufacturing without written consent. OT-Main is not required to share their intellectual property to the buyer. Custom made software will always be in ownership of OT-Main and will be not allowed to share.

7. Warranty
Unless otherwise stated, the Seller provides a limited warranty of 12 months from date of delivery against manufacturing defects under normal operating conditions.
The warranty does not cover:
- Normal wear and tear
- Improper installation or use
- Lack of maintenance
- Modifications by third parties
The Seller\'s liability is limited to repair or replacement of defective parts only.

8. Limitation of Liability
The Seller shall not be liable for any indirect, incidental, or consequential damages, including but not limited to loss of profit, production loss, or business interruption.
The maximum liability of the Seller shall in all cases be limited to the total value of the relevant quotation or order.

9. Cancellation
Order cancellation by the Buyer is only possible with written approval of the Seller. Any costs already incurred (engineering, materials, production, logistics, etc.) will be charged to the Buyer.

10. Force Majeure
The Seller shall not be held responsible for failure or delay in performance due to circumstances beyond reasonable control, including but not limited to natural disasters, war, strikes, government actions, pandemics, or supply chain disruptions.

11. Governing Law & Jurisdiction
Unless otherwise agreed, this quotation and any resulting agreement shall be governed by the laws of Indonesia and/or the Netherlands, at the Seller\'s discretion. Any disputes shall be settled in a competent court chosen by the Seller.

12. Acceptance
By accepting this quotation or placing an order, the Buyer confirms acceptance of these Terms & Conditions in full.';
}

function otmain_get_invoice_terms()
{
    return '1. Payment Term
Unless otherwise agreed in writing, all invoices are payable within 30 days from invoice date.

2. Currency & Bank Charges
All payments shall be made in the currency stated on the invoice. All bank charges, intermediary bank fees, and currency conversion costs are for the account of the Buyer. Any exchange rate fluctuation occurring between the invoice date and the payment date shall be entirely borne by the Buyer, and no adjustment, deduction, or claim may be made against the Seller arising from or in connection with currency movements.

3. Bank details
Unless otherwise agreed in writing, all payments can be transfer to the below account:
EURO CURRENCY:
Account Holder Name: OT-Main
IBAN: BE46 9675 4582 6036
Swift/BIC: TRWIBEB1XXX
Bank& Adress: Wise, Rue du Trône 100, 3rd floor, Brussels, 1050, Belgium

USD CURRENCY:
Account Holder Name: OT-Main
Account Number: 192552059816660
Routing number: 084009519
Swift/BIC: TRWIUS35XXX
Bank& Adress: Wise US Inc, 108 W 13th St, Wilmington, DE, 19801, United States

4. Late Payment
In case of late payment, the Seller reserves the right to charge interest of 2% per month or the maximum rate permitted by law, whichever is lower. The Seller may also suspend further deliveries and services.

5. Retention of Title
All delivered goods remain the property of the Seller until full and final payment of all outstanding invoices has been received.

6. Complaints
Any complaints regarding the invoice or delivered goods and/or services must be submitted in writing within 7 days of invoice date. After this period, the invoice shall be deemed accepted.

7. No Set-Off
The Buyer is not entitled to withhold, deduct, or set off any amounts from the invoiced amount unless expressly agreed in writing by the Seller.

8. Suspension of Work
In case of overdue payment, the Seller reserves the right to suspend any ongoing work, deliveries, and services without any liability for resulting delays or damages.

9. Collection Costs
All reasonable costs incurred for the collection of overdue payments, including legal fees, collection agency fees, daily storage cost, and administrative costs, shall be fully borne by the Buyer.

10. Limitation of Liability
The Seller shall not be liable for any indirect or consequential damages arising from late or disputed payments.

11. Governing Law & Jurisdiction
All invoices shall be governed by the laws of Indonesia and/or the Netherlands, at the Seller\'s discretion. Any disputes shall be submitted to a competent court chosen by the Seller.';
}

function otmain_pdf_append_quotation_terms($pdf, $font_name, $font_size, $currencyName = '')
{
    otmain_pdf_append_customize_image_page($pdf, 'generated/Term and Condition Qutation_page-0001.jpg', true);
}

function otmain_pdf_append_invoice_tc_page($pdf)
{
    otmain_pdf_append_customize_image_page($pdf, 'generated/Terms & Conditions – Invoices_page-0001.jpg', true);
}

function otmain_pdf_append_invoice_terms($pdf, $font_name, $font_size, $currencyName = '')
{
    otmain_pdf_append_invoice_tc_page($pdf);
    otmain_pdf_append_account_detail_proof($pdf);
}

function otmain_customize_file_path($relativePath)
{
    $relativePath = ltrim(str_replace('\\', '/', (string) $relativePath), '/');

    $candidates = [];
    $fcRoot       = realpath(FCPATH);
    if ($fcRoot !== false) {
        $candidates[] = dirname($fcRoot) . '/customize/' . $relativePath;
    }
    $candidates[] = dirname(FCPATH) . '/customize/' . $relativePath;
    $candidates[] = module_dir_path('otmain', 'assets/customize/' . $relativePath);

    foreach ($candidates as $path) {
        if (is_string($path) && $path !== '' && file_exists($path)) {
            return $path;
        }
    }

    return '';
}

function otmain_pdf_append_customize_image_page($pdf, $relativePath, $reuseBlankPage = false)
{
    $imagePath = otmain_customize_file_path($relativePath);
    if ($imagePath === '') {
        return false;
    }

    $printHeader   = isset($pdf->print_header) ? $pdf->print_header : true;
    $printFooter   = isset($pdf->print_footer) ? $pdf->print_footer : true;
    $autoPageBreak = $pdf->getAutoPageBreak();
    $breakMargin   = $pdf->getBreakMargin();

    $pdf->setPrintHeader(false);
    $pdf->setPrintFooter(false);
    $pdf->SetAutoPageBreak(false, 0);

    $currentY = $pdf->GetY();
    if ($reuseBlankPage && $currentY <= 10) {
        $pdf->setPage($pdf->getNumPages());
    } else {
        $pdf->AddPage();
    }

    $fullWidth  = $pdf->getPageWidth();
    $fullHeight = $pdf->getPageHeight();

    $ext = strtolower(pathinfo($imagePath, PATHINFO_EXTENSION));
    $imgType = ($ext === 'jpg' || $ext === 'jpeg') ? 'JPEG' : 'PNG';

    $pdf->Image(
        $imagePath,
        0,
        0,
        $fullWidth,
        $fullHeight,
        $imgType,
        '',
        '',
        true,
        300,
        '',
        false,
        false,
        0,
        '',
        false,
        false
    );

    $pdf->setPrintHeader($printHeader);
    $pdf->setPrintFooter($printFooter);
    $pdf->SetAutoPageBreak($autoPageBreak, $breakMargin);

    return true;
}

function otmain_pdf_append_customize_image_sequence($pdf, $basenameWithoutPage, $reuseFirstBlankPage = true)
{
    $basenameWithoutPage = trim((string) $basenameWithoutPage, '/');

    for ($page = 1; $page <= 20; $page++) {
        $relativePath = $basenameWithoutPage . '-' . $page . '.png';
        $reuseBlankPage = ($page === 1 && $reuseFirstBlankPage);
        if (!otmain_pdf_append_customize_image_page($pdf, $relativePath, $reuseBlankPage)) {
            break;
        }
    }
}

function otmain_pdf_append_account_detail_proof($pdf)
{
    otmain_pdf_append_customize_image_sequence($pdf, 'generated/account_details_proof_eur', false);
    otmain_pdf_append_customize_image_sequence($pdf, 'generated/account_details_proof_usd', false);
}

function otmain_get_bank_details($currencyName)
{
    $currencyName = strtoupper(trim($currencyName));

    if ($currencyName === 'USD' || $currencyName === 'US DOLLAR' || strpos($currencyName, 'USD') !== false) {
        $json = get_option('otmain_bank_details_usd');
    } else {
        $json = get_option('otmain_bank_details_eur');
    }

    $details = json_decode($json, true);

    return is_array($details) ? $details : [];
}

function otmain_bank_detail_fields()
{
    return ['account_holder', 'iban', 'account_number', 'routing_number', 'swift', 'bank', 'address'];
}

function otmain_format_bank_details_html($currencyName)
{
    $details = otmain_get_bank_details($currencyName);
    if (empty($details)) {
        return '';
    }

    $html = '<strong>' . e($details['account_holder'] ?? 'OT-Main') . '</strong><br />';

    if (!empty($details['iban'])) {
        $html .= 'IBAN: ' . e($details['iban']) . '<br />';
    }
    if (!empty($details['account_number'])) {
        $html .= 'Account Number: ' . e($details['account_number']) . '<br />';
    }
    if (!empty($details['routing_number'])) {
        $html .= 'Routing Number: ' . e($details['routing_number']) . '<br />';
    }
    if (!empty($details['swift'])) {
        $html .= 'Swift/BIC: ' . e($details['swift']) . '<br />';
    }
    if (!empty($details['bank'])) {
        $html .= 'Bank: ' . e($details['bank']) . '<br />';
    }
    if (!empty($details['address'])) {
        $html .= e($details['address']);
    }

    return $html;
}

function otmain_format_packing_list_number($id)
{
    $CI = &get_instance();
    $CI->db->where('id', $id);
    $row = $CI->db->get(db_prefix() . 'otmain_packing_lists')->row();
    if (!$row) {
        return '';
    }

    if (!empty($row->formatted_number) && preg_match('/^\d{4}-/', $row->formatted_number)) {
        return $row->formatted_number;
    }

    $prefix = $row->prefix ?: (get_option('otmain_packing_list_prefix') ?: 'PL-');
    $year   = !empty($row->date) && $row->date !== '0000-00-00' ? date('Y', strtotime($row->date)) : date('Y');

    return $year . '-' . $prefix . str_pad($row->number, 3, '0', STR_PAD_LEFT);
}

function otmain_packing_list_pdf_filename($packing)
{
    $number = '';
    if (is_object($packing) && !empty($packing->id)) {
        $number = otmain_format_packing_list_number($packing->id);
    } elseif (is_object($packing) && !empty($packing->formatted_number)) {
        $number = $packing->formatted_number;
    }

    $title = 'Packing-List-and-Invoice';
    if (is_object($packing) && !empty($packing->document_title)) {
        $title = $packing->document_title;
    }

    if ($number !== '') {
        return slug_it($number . '-' . $title);
    }

    return slug_it($title);
}

function otmain_format_purchase_order_number($id)
{
    $CI = &get_instance();
    $CI->db->where('id', $id);
    $row = $CI->db->get(db_prefix() . 'otmain_purchase_orders')->row();
    if (!$row) {
        return '';
    }

    if ($row->formatted_number) {
        return $row->formatted_number;
    }

    return date('Y', strtotime($row->date)) . '-' . get_option('otmain_purchase_order_prefix') . str_pad($row->number, 3, '0', STR_PAD_LEFT);
}

function otmain_purchase_order_pdf_filename($po)
{
    $number = '';
    if (is_object($po) && !empty($po->formatted_number)) {
        $number = $po->formatted_number;
    } elseif (is_object($po) && !empty($po->id)) {
        $number = otmain_format_purchase_order_number($po->id);
    }

    $title = 'Purchase-Order';
    if (is_object($po) && !empty($po->document_title)) {
        $title = $po->document_title;
    }

    if ($number !== '') {
        return slug_it($number . '-' . $title);
    }

    return slug_it($title);
}

function otmain_preview_purchase_order_number($date = null)
{
    $date   = $date ?: date('Y-m-d');
    $prefix = get_option('otmain_purchase_order_prefix');
    $number = get_option('next_otmain_purchase_order_number');

    return date('Y', strtotime($date)) . '-' . $prefix . str_pad($number, 3, '0', STR_PAD_LEFT);
}

function otmain_get_po_company_defaults()
{
    $bankDetails = otmain_get_bank_details('EUR');
    $iban        = $bankDetails['iban'] ?? 'NL34ABNA0548504303';
    $iban        = str_replace(' ', '', $iban);

    return [
        'document_title'         => 'Purchase Order',
        'company_name'           => get_option('companyname') ?: 'OT-MAIN',
        'company_address'        => get_option('invoice_company_address') ?: 'Bajonetstraat 52',
        'company_postal_code'    => get_option('invoice_company_postal_code') ?: '3014ZK',
        'company_city'           => get_option('invoice_company_city') ?: 'Rotterdam',
        'company_country'        => get_option('invoice_company_country_code') ?: 'The Netherlands',
        'company_phone'          => get_option('invoice_company_phonenumber') ?: '+31618228651',
        'company_email_invoices' => 'inv@otmain.com',
        'company_website'        => get_option('companywebsite') ?: 'www.otmain.com',
        'company_vat'            => get_option('company_vat') ?: 'NL004830818B51',
        'company_coc'            => get_option('company_registration_number') ?: '90597427',
        'iban'                   => $iban,
    ];
}

function otmain_format_client_address_lines($clientid)
{
    $CI = &get_instance();
    $CI->load->model('clients_model');
    $client = $CI->clients_model->get($clientid);

    if (!$client) {
        return '';
    }

    $lines = array_filter([
        $client->billing_street ?? '',
        trim(($client->billing_zip ?? '') . ' ' . ($client->billing_city ?? '')),
        $client->billing_state ?? '',
    ]);

    if (!empty($client->billing_country)) {
        $countryName = get_country_short_name($client->billing_country);
        if ($countryName) {
            $lines[] = $countryName;
        }
    }

    return implode("\n", $lines);
}

function otmain_po_apply_missing_defaults($po)
{
    if (!$po) {
        return $po;
    }

    foreach (otmain_get_po_company_defaults() as $field => $value) {
        if (!isset($po->$field) || $po->$field === '' || $po->$field === null) {
            $po->$field = $value;
        }
    }

    if (empty($po->document_title)) {
        $po->document_title = 'Purchase Order';
    }

    if (empty($po->supplier_address) && !empty($po->supplierid)) {
        $po->supplier_address = nl2br_save_html(otmain_format_client_address_lines($po->supplierid));
    }

    return $po;
}

function otmain_pdf_po_calculate_vat_summary($items)
{
    $subtotal = 0;
    $vat21    = 0;
    $vat0     = 0;

    foreach ($items as $item) {
        $line = (float) ($item['qty'] ?? 0) * (float) ($item['unit_price'] ?? 0);
        $rate = (float) ($item['taxrate'] ?? 0);
        $subtotal += $line;

        if ($rate == 0.0) {
            continue;
        }

        $taxAmount = $line * ($rate / 100);
        if ($rate == 21.0) {
            $vat21 += $taxAmount;
        } else {
            $vat21 += $taxAmount;
        }
    }

    return [
        'subtotal' => $subtotal,
        'vat21'    => $vat21,
        'vat0'     => $vat0,
        'total'    => $subtotal + $vat21 + $vat0,
    ];
}

function otmain_po_currency_name($po)
{
    if (empty($po->currency)) {
        return 'EUR';
    }

    $CI = &get_instance();
    $CI->load->model('currencies_model');
    $currency = $CI->currencies_model->get($po->currency);

    return $currency ? $currency->name : 'EUR';
}

function otmain_pdf_po_left_block_html($po)
{
    $poNumber = $po->formatted_number ?: otmain_format_purchase_order_number($po->id);

    return '<div style="' . otmain_pdf_meta_text_style() . '">'
        . '<strong>P.O. to:</strong><br />'
        . e(get_company_name($po->supplierid)) . '<br />'
        . process_text_content_for_display($po->supplier_address ?? '')
        . '<br />'
        . '<strong>Order Date:</strong> <span style="font-weight:bold;">' . e(otmain_pdf_format_document_date($po->date ?? '')) . '</span><br />'
        . '<strong>P.O. Number:</strong> <span style="font-weight:bold;">' . e($poNumber) . '</span><br />'
        . '<strong>Supplier Quote Ref.:</strong> ' . e($po->supplier_quote_ref ?? '-') . '<br />'
        . '<br />'
        . '<strong>Contact Person:</strong> ' . e($po->contact_person ?? '-') . '<br />'
        . '<strong>Email Address:</strong> ' . e($po->email ?? '-') . '<br />'
        . '<strong>Phone Number:</strong> ' . e($po->phone ?? '-')
        . '</div>';
}

function otmain_pdf_po_right_column_html($po)
{
    $style = otmain_pdf_meta_text_style();

    $addressHtml = '<strong>' . e($po->company_name ?? 'OT-MAIN') . '</strong><br />'
        . e($po->company_address ?? '') . '<br />'
        . e($po->company_postal_code ?? '') . '<br />'
        . e($po->company_city ?? '') . '<br />'
        . e($po->company_country ?? '') . '<br />';

    $html = '<table cellpadding="1" cellspacing="0" width="100%" align="right" style="' . $style . '">'
        . '<tr><td colspan="2" valign="top" align="right" style="' . $style . 'text-align:right;">'
        . $addressHtml
        . '</td></tr>';

    $rows = [
        ['Phone:', e($po->company_phone ?? '')],
        ['Email Invoices:', e($po->company_email_invoices ?? '')],
        ['Website:', e($po->company_website ?? '')],
        null,
        ['VAT N.:', e($po->company_vat ?? '')],
        ['COC N.:', e($po->company_coc ?? '')],
        ['IBAN:', e($po->iban ?? '')],
    ];

    foreach ($rows as $row) {
        if ($row === null) {
            $html .= '<tr><td colspan="2"><br /></td></tr>';
            continue;
        }

        $html .= otmain_pdf_meta_kv_table_row_html($row[0], $row[1], '40%', true);
    }

    $html .= '</table>';

    return $html;
}

function otmain_pdf_purchase_order_header_html($po)
{
    $logo  = otmain_pdf_logo_url(130);
    $title = !empty($po->document_title) ? $po->document_title : 'Purchase Order';

    return '<table cellpadding="2" cellspacing="0" width="100%">'
        . '<tr>'
        . '<td width="52%" valign="top">'
        . '<span style="font-weight:bold;font-size:18px;color:#00205B;">' . e($title) . '</span>'
        . '</td>'
        . '<td width="48%" valign="top" align="right">' . $logo . '</td>'
        . '</tr>'
        . '<tr>'
        . '<td valign="top">' . otmain_pdf_po_left_block_html($po) . '</td>'
        . '<td valign="top" align="right">' . otmain_pdf_right_column_wrap_html(otmain_pdf_po_right_column_html($po)) . '</td>'
        . '</tr>'
        . '</table>';
}

function otmain_pdf_po_items_table_html($po, $currencyName = 'EUR')
{
    $html = '<table cellpadding="6" border="1" cellspacing="0" width="100%" style="border-collapse:collapse;font-size:10px;">';
    $html .= '<tr style="background-color:#00205B;color:#ffffff;">'
        . '<th width="8%" align="center"><strong>QTY</strong></th>'
        . '<th width="52%"><strong>Description</strong></th>'
        . '<th width="15%" align="right"><strong>Unit Price</strong></th>'
        . '<th width="10%" align="center"><strong>VAT %</strong></th>'
        . '<th width="15%" align="right"><strong>Total</strong></th>'
        . '</tr>';

    foreach ($po->items as $item) {
        $lineTotal  = (float) ($item['qty'] ?? 0) * (float) ($item['unit_price'] ?? 0);
        $unitPrice  = app_format_money($item['unit_price'], $currencyName);
        $lineAmount = app_format_money($lineTotal, $currencyName);
        $taxLabel   = otmain_pdf_format_tax_rate($item['taxrate'] ?? 0);

        $html .= '<tr>'
            . '<td align="center">' . app_format_number($item['qty']) . '</td>'
            . '<td>' . e($item['description']) . '</td>'
            . '<td align="right">' . $unitPrice . '</td>'
            . '<td align="center">' . e($taxLabel) . '</td>'
            . '<td align="right">' . $lineAmount . '</td>'
            . '</tr>';
    }

    $html .= '</table>';

    return $html;
}

function otmain_pdf_po_totals_column_html($po, $currencyName = 'EUR')
{
    $summary = otmain_pdf_po_calculate_vat_summary($po->items);

    $html = '<table cellpadding="3" cellspacing="0" width="100%" style="font-size:10px;color:#424242;">';
    $html .= '<tr><td align="right" width="70%"><strong>Subtotal EUR</strong></td><td align="right" width="30%">' . otmain_pdf_format_total_amount($summary['subtotal'], $currencyName) . '</td></tr>';
    $html .= '<tr><td align="right"><strong>VAT 21%</strong></td><td align="right">' . otmain_pdf_format_total_amount($summary['vat21'], $currencyName) . '</td></tr>';
    $html .= '<tr><td align="right"><strong>VAT 0%</strong></td><td align="right">' . otmain_pdf_format_total_amount($summary['vat0'], $currencyName) . '</td></tr>';
    $html .= '<tr><td align="right"><strong>TOTAAL EUR</strong></td><td align="right"><strong>' . otmain_pdf_format_total_amount($summary['total'], $currencyName) . '</strong></td></tr>';
    $html .= '</table>';

    return $html;
}

function otmain_pdf_purchase_order_footer_html($po, $currencyName = 'EUR')
{
    return '<table cellpadding="4" cellspacing="0" width="100%">'
        . '<tr>'
        . '<td width="52%" valign="top">&nbsp;</td>'
        . '<td width="48%" valign="top" align="right">' . otmain_pdf_po_totals_column_html($po, $currencyName) . '</td>'
        . '</tr>'
        . '</table>';
}

function otmain_pdf_purchase_order_html($po)
{
    $currencyName = otmain_po_currency_name($po);

    return otmain_pdf_purchase_order_header_html($po)
        . '<br />'
        . otmain_pdf_po_items_table_html($po, $currencyName)
        . '<br />'
        . otmain_pdf_purchase_order_footer_html($po, $currencyName);
}

function otmain_pdf_logo_url($width = 160)
{
    $paths = array_filter([
        FCPATH . 'uploads/company/otmain_logo.jpeg',
        otmain_customize_file_path('logo_otmain.jpeg'),
    ]);

    foreach ($paths as $path) {
        if (file_exists($path)) {
            return '<img width="' . (int) $width . 'px" src="' . $path . '">';
        }
    }

    return pdf_logo_url();
}

function otmain_get_primary_contact($clientid)
{
    if (!$clientid) {
        return null;
    }

    $CI = &get_instance();
    $CI->load->model('clients_model');
    $contacts = $CI->clients_model->get_contacts($clientid);

    if (empty($contacts)) {
        return null;
    }

    foreach ($contacts as $contact) {
        if (!empty($contact['is_primary'])) {
            return $contact;
        }
    }

    return $contacts[0];
}

function otmain_pdf_recipient_address_html($name, $address, $zip = '', $city = '')
{
    $lines = '';

    if (!empty($name)) {
        $lines .= e($name) . '<br />';
    }
    if (!empty($address)) {
        $lines .= process_text_content_for_display($address) . '<br />';
    }

    $zipCity = trim(trim((string) $zip) . ' ' . trim((string) $city));
    if ($zipCity !== '') {
        $lines .= e($zipCity) . '<br />';
    }

    return $lines !== '' ? $lines : '-<br />';
}

function otmain_pdf_format_document_date($dateValue)
{
    if (empty($dateValue) || $dateValue === '0000-00-00') {
        return '-';
    }

    $ts = strtotime($dateValue);

    return (int) date('j', $ts) . '-' . (int) date('n', $ts) . '-' . date('Y', $ts);
}

function otmain_pdf_format_expiry_date($dateValue)
{
    if (empty($dateValue) || $dateValue === '0000-00-00') {
        return '-';
    }

    $ts = strtotime($dateValue);

    return str_pad((string) (int) date('j', $ts), 2, '0', STR_PAD_LEFT)
        . '-' . str_pad((string) (int) date('n', $ts), 2, '0', STR_PAD_LEFT)
        . '-' . date('Y', $ts);
}

function otmain_pdf_invoice_left_block_html($invoice, $invoiceNumber)
{
    $companyName = '';
    if (!empty($invoice->deleted_customer_name)) {
        $companyName = $invoice->deleted_customer_name;
    } elseif (!empty($invoice->client->company)) {
        $companyName = $invoice->client->company;
    }

    $contactName  = trim((string) ($invoice->contact_person_name ?? ''));
    $contactEmail = trim((string) ($invoice->contact_person_email ?? ''));
    $contactPhone = trim((string) ($invoice->contact_person_phone ?? ''));

    if ($contactName === '') {
        $contact = otmain_get_primary_contact($invoice->clientid);
        $contactName  = $contact ? trim(($contact['firstname'] ?? '') . ' ' . ($contact['lastname'] ?? '')) : get_staff_full_name($invoice->sale_agent);
        $contactEmail = $contact['email'] ?? '-';
        $contactPhone = $contact['phonenumber'] ?? '-';
    }

    if ($contactEmail === '') {
        $contactEmail = '-';
    }
    if ($contactPhone === '') {
        $contactPhone = '-';
    }

    $quoteRef = !empty($invoice->quote_ref) ? format_estimate_number($invoice->quote_ref) : '-';

    return '<div style="font-size:10px;color:#424242;line-height:1.6;">'
        . '<strong>Invoice to:</strong><br />'
        . otmain_pdf_recipient_address_html(
            $companyName,
            $invoice->billing_street ?? '',
            $invoice->billing_zip ?? '',
            $invoice->billing_city ?? ''
        )
        . '<br />'
        . '<strong>Invoice Date:</strong> <span style="font-weight:bold;">' . e(otmain_pdf_format_document_date($invoice->date ?? '')) . '</span><br />'
        . '<strong>Expiration Date:</strong> <span style="font-weight:bold;">' . e(otmain_pdf_format_expiry_date($invoice->duedate ?? '')) . '</span><br />'
        . '<strong>Invoice Number:</strong> <span style="font-weight:bold;">' . e($invoiceNumber) . '</span><br />'
        . '<strong>Quote Ref.:</strong> ' . e($quoteRef) . '<br />'
        . '<br />'
        . '<strong>Contact Person:</strong> ' . e($contactName ?: '-') . '<br />'
        . '<strong>Email Address:</strong> ' . e($contactEmail) . '<br />'
        . '<strong>Phone Number:</strong> ' . e($contactPhone)
        . '</div>';
}

function otmain_pdf_invoice_to_html($invoice)
{
    return otmain_pdf_invoice_left_block_html($invoice, format_invoice_number($invoice->id));
}

function otmain_pdf_meta_text_style()
{
    return 'font-size:10px;color:#424242;line-height:1.6;';
}

function otmain_pdf_meta_kv_table_row_html($label, $value, $labelWidth = '40%', $alignRight = false)
{
    $cellStyle = otmain_pdf_meta_text_style();

    if ($alignRight) {
        $labelColWidth = is_numeric($labelWidth) ? (string) $labelWidth : '40%';
        $valueColWidth = '60%';

        return '<tr>'
            . '<td width="' . $labelColWidth . '" valign="top" align="right" style="' . $cellStyle . '"><strong>' . e($label) . '</strong></td>'
            . '<td width="' . $valueColWidth . '" valign="top" align="right" style="' . $cellStyle . '">' . $value . '</td>'
            . '</tr>';
    }

    return '<tr>'
        . '<td width="' . $labelWidth . '" valign="top" style="' . $cellStyle . 'white-space:nowrap;"><strong>' . e($label) . '</strong></td>'
        . '<td valign="top" style="' . $cellStyle . '"><span style="' . $cellStyle . '">' . $value . '</span></td>'
        . '</tr>';
}

function otmain_pdf_meta_kv_table_row_value_only_html($value, $labelWidth = '40%', $alignRight = false)
{
    $cellStyle = otmain_pdf_meta_text_style();

    if ($alignRight) {
        $labelColWidth = is_numeric($labelWidth) ? (string) $labelWidth : '40%';
        $valueColWidth = '60%';

        return '<tr>'
            . '<td width="' . $labelColWidth . '" valign="top" align="right" style="' . $cellStyle . '">&nbsp;</td>'
            . '<td width="' . $valueColWidth . '" valign="top" align="right" style="' . $cellStyle . '">' . $value . '</td>'
            . '</tr>';
    }

    return '<tr>'
        . '<td width="' . $labelWidth . '" valign="top" style="' . $cellStyle . '">&nbsp;</td>'
        . '<td valign="top" style="' . $cellStyle . '"><span style="' . $cellStyle . '">' . $value . '</span></td>'
        . '</tr>';
}

function otmain_pdf_meta_kv_table_html(array $rows, $labelWidth = '40%', $alignRight = false)
{
    $html = '';

    foreach ($rows as $row) {
        if ($row === null) {
            $html .= '<tr><td colspan="2" style="line-height:4px;">&nbsp;</td></tr>';
            continue;
        }

        if ($row[0] === '') {
            $html .= otmain_pdf_meta_kv_table_row_value_only_html($row[1], $labelWidth, $alignRight);
            continue;
        }

        $html .= otmain_pdf_meta_kv_table_row_html($row[0], $row[1], $labelWidth, $alignRight);
    }

    if ($alignRight) {
        return '<table cellpadding="1" cellspacing="0" width="100%" align="right" style="' . otmain_pdf_meta_text_style() . '">' . $html . '</table>';
    }

    return '<table cellpadding="1" cellspacing="0" width="100%" style="' . otmain_pdf_meta_text_style() . '">' . $html . '</table>';
}

function otmain_pdf_company_contact_block_html($alignRight = false, $includeBankDetails = false, $currencyName = '')
{
    $phone   = get_option('invoice_company_phonenumber') ?: '+31618228651';
    $email   = get_option('smtp_email') ?: 'sales@otmain.com';
    $website = get_option('companywebsite') ?: 'www.otmain.com';
    $vat     = get_option('company_vat') ?: 'NL004830818B51';
    $coc     = get_option('company_registration_number') ?: '90597427';
    $address = get_option('invoice_company_address') ?: 'Bajonetstraat 52';
    $city    = get_option('invoice_company_city') ?: 'Rotterdam';
    $postal  = get_option('invoice_company_postal_code') ?: '3014ZK';
    $country = get_option('invoice_company_country_code') ?: 'The Netherlands';
    $style   = otmain_pdf_meta_text_style();
    $labelW  = $alignRight ? '40%' : '40%';
    $tableW  = $alignRight ? '100%' : '100%';
    $tableAlign = $alignRight ? ' align="right"' : '';

    $addressHtml = '<strong>' . e(get_option('companyname') ?: 'OT Main') . '</strong><br />'
        . e($address) . '<br />'
        . e($postal) . '<br />'
        . e($city) . '<br />'
        . e($country) . '<br />';

    $html = '<table cellpadding="1" cellspacing="0" width="' . $tableW . '"' . $tableAlign . ' style="' . $style . '">'
        . '<tr><td colspan="2" valign="top"' . ($alignRight ? ' align="right" style="' . $style . 'text-align:right;"' : ' style="' . $style . '"') . '>'
        . $addressHtml
        . '</td></tr>';

    $rows = [
        ['Phone:', e($phone)],
        ['Email:', e($email)],
        ['Website:', e($website)],
        null,
        ['VAT N.:', e($vat)],
        ['COC N.:', e($coc)],
    ];

    if ($includeBankDetails) {
        $details = otmain_get_bank_details($currencyName);
        if (!empty($details)) {
            $accountNr = $details['account_number'] ?? ($details['iban'] ?? '-');
            $rows[]    = null;
            $rows[]    = ['Account Nr.:', e($accountNr)];
            $rows[]    = ['Account Holder:', e($details['account_holder'] ?? 'OT-MAIN')];

            if (!empty($details['bank'])) {
                $rows[] = ['Name Bank:', e($details['bank'])];
            }

            $rows[] = null;

            if (!empty($details['swift'])) {
                $rows[] = ['BIC / SWIFT:', e($details['swift'])];
            }
            if (!empty($details['routing_number'])) {
                $rows[] = ['Routing Nr.:', e($details['routing_number'])];
            }
            if (!empty($details['bank']) || !empty($details['address'])) {
                $bankAddress = e($details['bank'] ?? '');
                if (!empty($details['address'])) {
                    $bankAddress .= ($bankAddress !== '' ? '<br />' : '') . e($details['address']);
                }
                $rows[] = ['Bank Address:', $bankAddress];
            }
        }
    }

    foreach ($rows as $row) {
        if ($row === null) {
            $html .= '<tr><td colspan="2"><br /></td></tr>';
            continue;
        }

        $html .= otmain_pdf_meta_kv_table_row_html($row[0], $row[1], $labelW, $alignRight);
    }

    $html .= '</table>';

    return $html;
}

function otmain_pdf_company_meta_block($alignRight = false)
{
    return otmain_pdf_company_contact_block_html($alignRight, false);
}

function otmain_pdf_invoice_right_column_html($currencyName)
{
    return otmain_pdf_company_contact_block_html(true, true, $currencyName);
}

function otmain_pdf_right_column_wrap_html($content)
{
    return $content;
}

function otmain_pdf_quotation_header_html($estimate, $estimateNumber)
{
    $logo    = otmain_pdf_logo_url(130);
    $company = otmain_pdf_company_meta_block(true);
    $contact = otmain_get_primary_contact($estimate->clientid);

    $contactName  = $contact ? trim(($contact['firstname'] ?? '') . ' ' . ($contact['lastname'] ?? '')) : '-';
    $contactEmail = $contact['email'] ?? '-';
    $contactPhone = $contact['phonenumber'] ?? '-';

    $leftMeta = '<div style="font-size:10px;color:#424242;line-height:1.6;">'
        . '<strong>Quotation to:</strong><br />'
        . format_customer_info($estimate, 'estimate', 'billing')
        . '<br />'
        . '<strong>Quotation Date:</strong> ' . _d($estimate->date) . '<br />'
        . '<strong>Quote Number:</strong> ' . e($estimateNumber) . '<br />'
        . '<strong>Expiration Quote Date:</strong> ' . (!empty($estimate->expirydate) ? _d($estimate->expirydate) : '-') . '<br />'
        . '<strong>Client Ref.:</strong> ' . e($estimate->client_ref ?? '-') . '<br />'
        . '<strong>Contact Person:</strong> ' . e($contactName) . '<br />'
        . '<strong>Email Address:</strong> ' . e($contactEmail) . '<br />'
        . '<strong>Phone Number:</strong> ' . e($contactPhone)
        . '</div>';

    return '<table cellpadding="2" cellspacing="0" width="100%">'
        . '<tr>'
        . '<td width="52%" valign="top">'
        . '<span style="font-weight:bold;font-size:18px;color:#00205B;">Draft Quotation</span>'
        . '</td>'
        . '<td width="48%" valign="top" align="right">' . $logo . '</td>'
        . '</tr>'
        . '<tr>'
        . '<td valign="top">' . $leftMeta . '</td>'
        . '<td valign="top" align="right">' . otmain_pdf_right_column_wrap_html($company) . '</td>'
        . '</tr>'
        . '</table>';
}

function otmain_pdf_quotation_footer_html($estimate, $items, $currencyName)
{
    return otmain_pdf_footer_layout_html(
        otmain_pdf_footer_terms_block_html([
            ['Payment Terms:', $estimate->payment_terms_text ?? 'To be agreed.'],
            ['Delivery Time:', $estimate->delivery_time ?? '-'],
            ['Availability:', $estimate->availability ?? '-'],
            ['Shipment Terms:', $estimate->shipment_terms ?? 'EXW (Ex Works)'],
            ['Notes:', $estimate->clientnote ?? '-'],
        ]),
        $estimate,
        $items,
        $currencyName
    );
}

function otmain_pdf_format_tax_rate($rate)
{
    $rate = (float) $rate;

    return (fmod($rate, 1.0) === 0.0 ? (string) (int) $rate : app_format_number($rate)) . '%';
}

function otmain_pdf_items_table_html($items, $relType, $relId, $currencyName = '')
{
    $html = '<table cellpadding="6" border="1" cellspacing="0" width="100%" style="border-collapse:collapse;font-size:10px;">';
    $html .= '<tr style="background-color:#00205B;color:#ffffff;">'
        . '<th width="8%" align="center"><strong>QTY</strong></th>'
        . '<th width="52%"><strong>Description</strong></th>'
        . '<th width="15%" align="right"><strong>Unit Price</strong></th>'
        . '<th width="10%" align="center"><strong>VAT %</strong></th>'
        . '<th width="15%" align="right"><strong>Total</strong></th>'
        . '</tr>';

    foreach ($items as $item) {
        if ($relType === 'invoice') {
            $taxes = get_invoice_item_taxes($item['id']);
        } elseif ($relType === 'proposal') {
            $taxes = get_proposal_item_taxes($item['id']);
        } else {
            $taxes = get_estimate_item_taxes($item['id']);
        }
        $taxLabel = '-';
        if (!empty($taxes)) {
            $parts = [];
            foreach ($taxes as $tax) {
                $parts[] = otmain_pdf_format_tax_rate($tax['taxrate']);
            }
            $taxLabel = implode(', ', $parts);
        }

        $description = $item['description'];
        if (!empty($item['long_description'])) {
            $description .= '<br /><span style="font-size:9px;">' . process_text_content_for_display($item['long_description']) . '</span>';
        }

        $lineTotal  = (float) $item['qty'] * (float) $item['rate'];
        $unitPrice  = $currencyName !== '' ? app_format_money($item['rate'], $currencyName) : app_format_number($item['rate']);
        $lineAmount = $currencyName !== '' ? app_format_money($lineTotal, $currencyName) : app_format_number($lineTotal);

        $html .= '<tr>'
            . '<td align="center">' . app_format_number($item['qty']) . '</td>'
            . '<td>' . $description . '</td>'
            . '<td align="right">' . $unitPrice . '</td>'
            . '<td align="center">' . e($taxLabel) . '</td>'
            . '<td align="right">' . $lineAmount . '</td>'
            . '</tr>';
    }

    $html .= '</table>';

    return $html;
}

function otmain_pdf_format_total_amount($amount, $currencyName)
{
    if (!isset($amount) || $amount === '' || (float) $amount == 0.0) {
        return '-';
    }

    return app_format_money($amount, $currencyName);
}

function otmain_pdf_invoice_two_table_html($items, $packingItems, $currencyName)
{
    $headerBg   = '#00205B';
    $headerClr  = '#ffffff';
    $cellStyle  = 'font-size:10px;color:#424242;border:1px solid #cccccc;padding:4px;';
    $headerSty  = 'font-size:10px;font-weight:bold;background-color:' . $headerBg . ';color:' . $headerClr . ';border:1px solid #00205B;padding:4px;';
    $rightSty   = 'font-size:10px;color:#424242;border:1px solid #cccccc;padding:4px;text-align:right;white-space:nowrap;';
    $rightHdr   = 'font-size:10px;font-weight:bold;background-color:' . $headerBg . ';color:' . $headerClr . ';border:1px solid #00205B;padding:4px;text-align:right;';

    $html = '<table cellpadding="0" cellspacing="0" width="100%" style="border-collapse:collapse;">';

    // === HEADER ROW ===
    $html .= '<tr>'
        . '<th width="8%" style="' . $headerSty . 'text-align:center;">QTY</th>'
        . '<th width="57%" style="' . $headerSty . '">Description</th>'
        . '<th width="12%" style="' . $headerSty . 'text-align:right;">Unit Price</th>'
        . '<th width="8%" style="' . $headerSty . 'text-align:center;">VAT %</th>'
        . '<th width="15%" style="' . $rightHdr . '">Total</th>'
        . '</tr>';

    // === ITEMS ROWS ===
    $lineTotals = [];
    foreach ($items as $item) {
        $qty  = (float) $item['qty'];
        $rate = (float) $item['rate'];
        $lineTotal = $qty * $rate;
        $lineTotals[] = $lineTotal;

        // QTY: show integer if round
        $qtyDisplay = (fmod($qty, 1.0) === 0.0) ? (string) (int) $qty : app_format_number($qty);

        // Description with HS Code from long_description
        $desc = e($item['description']);
        if (!empty($item['long_description'])) {
            $desc .= '<br /><span style="font-size:9px;">' . process_text_content_for_display($item['long_description']) . '</span>';
        }

        $unitPrice = app_format_money($rate, $currencyName);
        $lineAmt   = app_format_money($lineTotal, $currencyName);

        // Tax label
        $taxLabel = '-';
        $taxes = [];
        if (isset($item['id'])) {
            $taxes = get_invoice_item_taxes($item['id']);
        } elseif (!empty($item['taxname'])) {
            foreach ($item['taxname'] as $tn) {
                if (is_string($tn)) {
                    $parts = explode('|', $tn);
                    if (count($parts) >= 2) {
                        $taxes[] = ['taxrate' => $parts[1]];
                    }
                } elseif (is_array($tn) && isset($tn['taxrate'])) {
                    $taxes[] = $tn;
                }
            }
        }
        if (!empty($taxes)) {
            $parts = [];
            foreach ($taxes as $tax) {
                $parts[] = otmain_pdf_format_tax_rate($tax['taxrate']);
            }
            $taxLabel = implode(', ', $parts);
        }

        $html .= '<tr>'
            . '<td style="' . $cellStyle . 'text-align:center;">' . $qtyDisplay . '</td>'
            . '<td style="' . $cellStyle . '">' . $desc . '</td>'
            . '<td style="' . $cellStyle . 'text-align:right;">' . $unitPrice . '</td>'
            . '<td style="' . $cellStyle . 'text-align:center;">' . $taxLabel . '</td>'
            . '<td style="' . $rightSty . '">' . $lineAmt . '</td>'
            . '</tr>';
    }

    // === PACKING SECTION HEADER ===
    $html .= '<tr>'
        . '<th width="8%" style="' . $headerSty . 'text-align:center;">QTY</th>'
        . '<th width="57%" style="' . $headerSty . '">Dimensions</th>'
        . '<th width="12%" style="' . $headerSty . 'text-align:right;">G.W (KGS)</th>'
        . '<th width="8%" style="' . $headerSty . 'text-align:right;">N.W (KGS)</th>'
        . '<th width="15%" style="' . $rightHdr . '">&nbsp;</th>'
        . '</tr>';

    // === PACKING ROWS ===
    $totalGw = 0;
    $totalNw = 0;
    $totalCbm = 0;

    if (!empty($packingItems)) {
        foreach ($packingItems as $pItem) {
            $qtyPack = (float) ($pItem['qty'] ?? 1);
            $gw      = (float) ($pItem['gw'] ?? 0);
            $nw      = (float) ($pItem['nw'] ?? 0);
            $dims    = $pItem['dimensions'] ?? '';
            $cbm     = (float) ($pItem['cbm'] ?? 0);

            $totalGw  += $gw;
            $totalNw  += $nw;
            $totalCbm += $cbm;

            $qtyPackDisplay = (fmod($qtyPack, 1.0) === 0.0) ? (string) (int) $qtyPack : app_format_number($qtyPack);

            $dimHtml = e($dims);
            if ($cbm > 0) {
                $dimHtml .= '<br /><span style="font-size:9px;">Volume: ' . app_format_number($cbm) . ' CBM</span>';
            }

            $html .= '<tr>'
                . '<td style="' . $cellStyle . 'text-align:center;">' . $qtyPackDisplay . '</td>'
                . '<td style="' . $cellStyle . '">' . $dimHtml . '</td>'
                . '<td style="' . $cellStyle . 'text-align:right;">' . app_format_number($gw) . '</td>'
                . '<td style="' . $cellStyle . 'text-align:right;">' . app_format_number($nw) . '</td>'
                . '<td style="' . $rightSty . '">&nbsp;</td>'
                . '</tr>';
        }
    }

    // === SUMMARY ROWS ===
    $subtotalEur = array_sum($lineTotals);

    // USD auto-calc
    $rate = (float) str_replace(',', '.', (string) get_option('otmain_eur_to_usd_rate'));
    $usdDisplay = '';
    if ($rate > 0) {
        $usdDisplay = '$ ' . app_format_number($subtotalEur * $rate);
    }

    // Total Weight + subtotal row
    $html .= '<tr>'
        . '<td style="' . $cellStyle . 'text-align:center;font-weight:bold;">&nbsp;</td>'
        . '<td style="' . $cellStyle . 'font-weight:bold;">Total Weight</td>'
        . '<td style="' . $cellStyle . 'text-align:right;font-weight:bold;">' . app_format_number($totalGw) . '</td>'
        . '<td style="' . $cellStyle . 'text-align:right;font-weight:bold;">' . app_format_number($totalNw) . '</td>'
        . '<td style="' . $rightSty . 'font-weight:bold;background-color:#f5f5f5;">Subtotal<br />' . app_format_money($subtotalEur, $currencyName) . '</td>'
        . '</tr>';

    // CBM row + USD
    $html .= '<tr>'
        . '<td style="' . $cellStyle . 'text-align:center;font-weight:bold;">&nbsp;</td>'
        . '<td style="' . $cellStyle . 'font-weight:bold;">Total CBM: ' . app_format_number($totalCbm) . '</td>'
        . '<td style="' . $cellStyle . 'text-align:right;">&nbsp;</td>'
        . '<td style="' . $cellStyle . 'text-align:right;">&nbsp;</td>'
        . '<td style="' . $rightSty . 'font-weight:bold;background-color:#f5f5f5;">Subtotal USD<br />' . ($usdDisplay ?: '-') . '</td>'
        . '</tr>';

    $html .= '</table>';

    return $html;
}

function otmain_pdf_footer_terms_block_html(array $sections, $prefixHtml = '')
{
    $html = '<div style="font-size:10px;color:#424242;line-height:1.6;">';

    if ($prefixHtml !== '') {
        $html .= '<strong>' . $prefixHtml . '</strong>';
    }

    foreach ($sections as $index => $section) {
        if ($prefixHtml !== '' || $index > 0) {
            $html .= '<br /><br />';
        }
        $html .= '<strong>' . e($section[0]) . '</strong><br />' . process_text_content_for_display($section[1]);
    }

    $html .= '</div>';

    return $html;
}

function otmain_pdf_footer_layout_html($termsCol, $document, $items, $currencyName)
{
    return '<table cellpadding="4" cellspacing="0" width="100%">'
        . '<tr>'
        . '<td width="52%" valign="top">' . $termsCol . '</td>'
        . '<td width="48%" valign="top" align="right">' . otmain_pdf_totals_column_html($document, $items, $currencyName) . '</td>'
        . '</tr>'
        . '</table>';
}

function otmain_pdf_calculate_vat_totals($items)
{
    $vat21 = 0;
    $vat0  = 0;

    foreach ($items->taxes() as $tax) {
        if ((float) $tax['taxrate'] == 0) {
            $vat0 += $tax['total_tax'];
        } else {
            $vat21 += $tax['total_tax'];
        }
    }

    return [$vat21, $vat0];
}

function otmain_pdf_totals_column_html($document, $items, $currencyName)
{
    [$vat21, $vat0] = otmain_pdf_calculate_vat_totals($items);

    $usdDisplay  = isset($document->total_usd_display) ? trim((string) $document->total_usd_display) : '';
    $goldDisplay = isset($document->total_gold_display) ? trim((string) $document->total_gold_display) : '';

    // Auto-calc when display fields are empty (requires options).
    if ($usdDisplay === '') {
        $rate = (float) str_replace(',', '.', (string) get_option('otmain_eur_to_usd_rate'));
        if ($rate > 0) {
            $usdDisplay = '$ ' . app_format_number(((float) $document->total) * $rate);
        }
    }
    if ($goldDisplay === '') {
        $pricePerGram = (float) str_replace(',', '.', (string) get_option('otmain_gold_price_eur_per_gram'));
        if ($pricePerGram > 0) {
            $grams = ((float) $document->total) / $pricePerGram;
            $goldDisplay = app_format_number($grams) . ' in Gram';
        }
    }

    $html = '<table cellpadding="3" cellspacing="0" width="100%" style="font-size:10px;color:#424242;">';
    $html .= '<tr><td align="right" width="70%"><strong>Subtotal</strong></td><td align="right" width="30%">' . otmain_pdf_format_total_amount($document->subtotal, $currencyName) . '</td></tr>';
    $html .= '<tr><td align="right" width="70%"><strong>VAT 21%</strong></td><td align="right" width="30%">' . otmain_pdf_format_total_amount($vat21, $currencyName) . '</td></tr>';
    $html .= '<tr><td align="right" width="70%"><strong>VAT 0%</strong></td><td align="right" width="30%">' . otmain_pdf_format_total_amount($vat0, $currencyName) . '</td></tr>';
    $html .= '<tr><td align="right" width="70%"><strong>TOTAL &euro; (EURO)</strong></td><td align="right" width="30%"><strong>' . otmain_pdf_format_total_amount($document->total, $currencyName) . '</strong></td></tr>';
    $html .= '<tr><td align="right" width="70%"><strong>TOTAL USD</strong></td><td align="right" width="30%">' . ($usdDisplay !== '' ? e($usdDisplay) : '-') . '</td></tr>';
    $html .= '<tr><td align="right" width="70%"><strong>TOTAL GOLD</strong></td><td align="right" width="30%">' . ($goldDisplay !== '' ? e($goldDisplay) : '-') . '</td></tr>';
    $html .= '</table>';

    return $html;
}

function otmain_pdf_totals_table_html($document, $items, $currencyName)
{
    $html = '<table cellpadding="5" width="100%" style="font-size:10px;">';
    $html .= '<tr><td width="70%"></td><td width="48%" align="right">' . otmain_pdf_totals_column_html($document, $items, $currencyName) . '</td></tr>';
    $html .= '</table>';

    return $html;
}

function otmain_pdf_contact_block($clientid)
{
    $contact = otmain_get_primary_contact($clientid);
    if (!$contact) {
        return '';
    }

    $name  = trim(($contact['firstname'] ?? '') . ' ' . ($contact['lastname'] ?? ''));
    $email = $contact['email'] ?? '';
    $phone = $contact['phonenumber'] ?? '';

    return '<table cellpadding="2" width="100%" style="font-size:10px;">'
        . '<tr><td><strong>Contact Person:</strong> ' . e($name) . '</td></tr>'
        . '<tr><td><strong>Email Address:</strong> ' . e($email) . '</td></tr>'
        . '<tr><td><strong>Phone Number:</strong> ' . e($phone) . '</td></tr>'
        . '</table>';
}

function otmain_pdf_invoice_bank_details_html($currencyName)
{
    return '';
}

function otmain_pdf_invoice_bank_block($currencyName)
{
    return otmain_pdf_invoice_right_column_html($currencyName);
}

function otmain_pdf_invoice_header_html($invoice, $invoiceNumber)
{
    $logo     = otmain_pdf_logo_url(130);
    $title    = !empty($invoice->document_title) ? $invoice->document_title : 'Commercial Invoice';
    $leftMeta = otmain_pdf_invoice_left_block_html($invoice, $invoiceNumber);
    $rightMeta = otmain_pdf_right_column_wrap_html(otmain_pdf_invoice_right_column_html($invoice->currency_name));

    return '<table cellpadding="2" cellspacing="0" width="100%">'
        . '<tr>'
        . '<td width="52%" valign="top">'
        . '<span style="font-weight:bold;font-size:18px;color:#00205B;">' . e($title) . '</span>'
        . '</td>'
        . '<td width="48%" valign="top" align="right">' . $logo . '</td>'
        . '</tr>'
        . '<tr>'
        . '<td valign="top">' . $leftMeta . '</td>'
        . '<td valign="top" align="right">' . $rightMeta . '</td>'
        . '</tr>'
        . '</table>';
}

function otmain_pdf_invoice_footer_html($invoice, $items, $currencyName)
{
    $notes = $invoice->notes ?? ($invoice->delivery_address ?? '-');

    return otmain_pdf_footer_layout_html(
        otmain_pdf_footer_terms_block_html([
            ['Payment Terms:', $invoice->payment_terms_text ?? 'To be agreed.'],
            ['Delivery Time:', $invoice->lead_time ?? '-'],
            ['Availability:', $invoice->availability ?? '-'],
            ['Shipment Terms:', $invoice->delivery_terms ?? 'EXW (Ex Works)'],
            ['Notes:', $notes],
        ], 'Please make sure to include our invoice number with your payment.'),
        $invoice,
        $items,
        $currencyName
    );
}

function otmain_pdf_proposal_header_html($proposal, $proposalNumber)
{
    $logo    = otmain_pdf_logo_url(130);
    $company = otmain_pdf_company_meta_block(true);
    $title   = !empty($proposal->document_title) ? $proposal->document_title : 'Draft Quotation';

    $quotationTo = otmain_pdf_recipient_address_html(
        $proposal->proposal_to ?? '',
        $proposal->address ?? '',
        $proposal->zip ?? '',
        $proposal->city ?? ''
    );

    // Quotation Date: auto-fill today when empty/invalid.
    $dateValue = !empty($proposal->date) && $proposal->date !== '0000-00-00' ? $proposal->date : date('Y-m-d');
    $quotationDate = otmain_pdf_format_document_date($dateValue);
    $expiryDate = otmain_pdf_format_expiry_date($proposal->open_till ?? '');

    $leftMeta = '<div style="font-size:10px;color:#424242;line-height:1.6;">'
        . '<strong>Quotation to:</strong><br />'
        . $quotationTo
        . '<br />'
        . '<strong>Quotation Date:</strong> <span style="font-weight:bold;">' . e($quotationDate) . '</span><br />'
        . '<strong>Quote Number:</strong> <span style="font-weight:bold;">' . e($proposalNumber) . '</span><br />'
        . '<strong>Expiration Quote Date:</strong> <span style="font-weight:bold;">' . e($expiryDate) . '</span><br />'
        . '<strong>Client Ref.:</strong> ' . e($proposal->client_ref ?? '-') . '<br />'
        . '<br />'
        . '<strong>Contact Person:</strong> ' . e($proposal->contact_person_name ?? '-') . '<br />'
        . '<strong>Email Address:</strong> ' . e($proposal->contact_person_email ?? '-') . '<br />'
        . '<strong>Phone Number:</strong> ' . e($proposal->contact_person_phone ?? '-')
        . '</div>';

    return '<table cellpadding="2" cellspacing="0" width="100%">'
        . '<tr>'
        . '<td width="52%" valign="top">'
        . '<span style="font-weight:bold;font-size:18px;color:#00205B;">' . e($title) . '</span>'
        . '</td>'
        . '<td width="48%" valign="top" align="right">' . $logo . '</td>'
        . '</tr>'
        . '<tr>'
        . '<td valign="top">' . $leftMeta . '</td>'
        . '<td valign="top" align="right">' . otmain_pdf_right_column_wrap_html($company) . '</td>'
        . '</tr>'
        . '</table>';
}

function otmain_pdf_proposal_footer_html($proposal, $items, $currencyName)
{
    return otmain_pdf_footer_layout_html(
        otmain_pdf_footer_terms_block_html([
            ['Payment Terms:', $proposal->payment_terms_text ?? 'To be agreed.'],
            ['Delivery Time:', $proposal->delivery_time ?? '-'],
            ['Availability:', $proposal->availability ?? '-'],
            ['Shipment Terms:', $proposal->shipment_terms ?? 'EXW (Ex Works)'],
            ['Notes:', $proposal->notes ?? '-'],
        ]),
        $proposal,
        $items,
        $currencyName
    );
}

function otmain_pdf_shipper_block_html()
{
    $phone   = get_option('invoice_company_phonenumber') ?: '+31618228651';
    $email   = get_option('smtp_email') ?: 'sales@otmain.com';
    $website = get_option('companywebsite') ?: 'www.otmain.com';
    $address = get_option('invoice_company_address') ?: 'Bajonetstraat 52';
    $city    = get_option('invoice_company_city') ?: 'Rotterdam';
    $postal  = get_option('invoice_company_postal_code') ?: '3014ZK';
    $country = get_option('invoice_company_country_code') ?: 'The Netherlands';
    $style   = otmain_pdf_meta_text_style();

    return '<div style="' . $style . '">'
        . '<strong>' . e(get_option('companyname') ?: 'OT-Main') . '</strong><br />'
        . e($address) . '<br />'
        . e($postal) . '<br />'
        . e($city) . '<br />'
        . e($country)
        . '<br /><br />'
        . e($phone) . '<br />'
        . e($email) . '<br />'
        . e($website)
        . '</div>';
}

function otmain_pdf_packing_party_block_html($title, $name, $address, $phone, $email)
{
    return '<strong>' . e($title) . '</strong><br />'
        . e($name ?: '-') . '<br />'
        . process_text_content_for_display($address ?: '-')
        . '<br />Phone: ' . e($phone ?: '-') . '<br />'
        . 'E: ' . e($email ?: '-');
}

function otmain_pdf_packing_item_description_html($item)
{
    $html = e($item['description'] ?? '');
    if (!empty($item['hs_code'])) {
        $html .= '<br /><span style="font-size:9px;">HS Code: ' . e($item['hs_code']) . '</span>';
    }

    return $html;
}

function otmain_pdf_packing_quote_ref_html($quoteRef)
{
    $quoteRef = trim((string) $quoteRef);
    if ($quoteRef === '') {
        return '-';
    }

    $lines = preg_split("/\r\n|\n|\r/", $quoteRef);
    $html  = e(array_shift($lines));
    foreach ($lines as $line) {
        $line = trim($line);
        if ($line !== '') {
            $html .= '<br />' . e($line);
        }
    }

    return $html;
}

function otmain_pdf_packing_left_block_html($packing)
{
    $consigneeAddress = process_text_content_for_display($packing->consignee_address ?? '-');
    $purchaserAddress = process_text_content_for_display($packing->purchaser_address ?? '-');

    return '<div style="font-size:10px;color:#424242;line-height:1.6;">'
        . '<strong>Consignee Details:</strong><br />'
        . e($packing->consignee_name ?? '-') . '<br />'
        . $consigneeAddress
        . '<br />Phone: ' . e($packing->consignee_phone ?? '-') . '<br />'
        . 'E: ' . e($packing->consignee_email ?? '-')
        . '<br /><br />'
        . '<strong>Purchaser\'s Details:</strong><br />'
        . e($packing->purchaser_name ?? '-') . '<br />'
        . $purchaserAddress
        . '<br />Phone: ' . e($packing->purchaser_phone ?? '-') . '<br />'
        . 'E: ' . e($packing->purchaser_email ?? '-')
        . '<br /><br />'
        . '<strong>Vessel/System:</strong> ' . e($packing->vessel ?? '-')
        . '<br /><br />'
        . '<strong>Our Quote ref.:</strong> ' . otmain_pdf_packing_quote_ref_html($packing->quote_ref ?? '')
        . '</div>';
}

function otmain_pdf_packing_header_html($packing)
{
    $logo     = otmain_pdf_logo_url(130);
    $title    = !empty($packing->document_title) ? $packing->document_title : 'Packing List & Invoice';
    $leftMeta = otmain_pdf_packing_left_block_html($packing);
    $rightMeta = otmain_pdf_right_column_wrap_html(otmain_pdf_company_meta_block(true));

    return '<table cellpadding="2" cellspacing="0" width="100%">'
        . '<tr>'
        . '<td width="52%" valign="top">'
        . '<span style="font-weight:bold;font-size:18px;color:#00205B;">' . e($title) . '</span>'
        . '</td>'
        . '<td width="48%" valign="top" align="right">' . $logo . '</td>'
        . '</tr>'
        . '<tr>'
        . '<td width="52%" valign="top">' . $leftMeta . '</td>'
        . '<td width="48%" valign="top">' . $rightMeta . '</td>'
        . '</tr>'
        . '</table>';
}

function otmain_pdf_packing_list_html($packing)
{
    $style = otmain_pdf_meta_text_style();

    $html = otmain_pdf_packing_header_html($packing) . '<br />';

    $html .= '<table border="1" cellpadding="4" cellspacing="0" width="100%" style="' . $style . 'border-collapse:collapse;">'
        . '<tr style="background-color:#00205B;color:#ffffff;">'
        . '<th width="8%" align="center"><strong>QTY</strong></th>'
        . '<th width="47%"><strong>Description</strong></th>'
        . '<th width="20%" align="right"><strong>Unit Price</strong></th>'
        . '<th width="25%" align="right"><strong>Total</strong></th>'
        . '</tr>';

    foreach ($packing->items as $item) {
        $html .= '<tr>'
            . '<td align="center">' . app_format_number($item['qty']) . '</td>'
            . '<td>' . otmain_pdf_packing_item_description_html($item) . '</td>'
            . '<td align="right">' . app_format_number($item['unit_price']) . '</td>'
            . '<td align="right">' . app_format_number($item['total']) . '</td>'
            . '</tr>';
    }

    $html .= '</table><br />';

    $html .= '<table border="1" cellpadding="4" cellspacing="0" width="100%" style="' . $style . 'border-collapse:collapse;">'
        . '<tr style="background-color:#00205B;color:#ffffff;">'
        . '<th width="8%" align="center"><strong>QTY</strong></th>'
        . '<th width="42%"><strong>Dimensions</strong></th>'
        . '<th width="12.5%" align="right"><strong>G.W (KGS)</strong></th>'
        . '<th width="12.5%" align="right"><strong>N.W (KGS)</strong></th>'
        . '<th width="25%">&nbsp;</th>'
        . '</tr>';

    foreach ($packing->items as $item) {
        $html .= '<tr>'
            . '<td align="center">' . app_format_number($item['qty']) . '</td>'
            . '<td>' . e($item['packing_detail'] ?? '') . '</td>'
            . '<td align="right">' . ($item['gross_weight'] !== null && $item['gross_weight'] !== '' ? app_format_number($item['gross_weight']) : '-') . '</td>'
            . '<td align="right">' . ($item['net_weight'] !== null && $item['net_weight'] !== '' ? app_format_number($item['net_weight']) : '-') . '</td>'
            . '<td align="right">&nbsp;</td>'
            . '</tr>';
    }

    // Add Total Weight row at bottom of packing table
    $totalGwFooter = 0;
    $totalNwFooter = 0;
    foreach ($packing->items as $item) {
        $totalGwFooter += (float) ($item['gross_weight'] ?? 0);
        $totalNwFooter += (float) ($item['net_weight'] ?? 0);
    }
    $html .= '<tr style="font-weight:bold;background-color:#f5f5f5;">'
        . '<td align="center">&nbsp;</td>'
        . '<td>Total Weight</td>'
        . '<td align="right">' . app_format_number($totalGwFooter) . '</td>'
        . '<td align="right">' . app_format_number($totalNwFooter) . '</td>'
        . '<td align="right">&nbsp;</td>'
        . '</tr>';

    $html .= '</table><br />';

    $totalWeight = isset($packing->total_weight) ? (float) $packing->total_weight : 0;
    if ($totalWeight <= 0) {
        foreach ($packing->items as $item) {
            $totalWeight += (float) ($item['gross_weight'] ?? 0);
        }
    }

    $subtotalUsd = (float) ($packing->subtotal_usd ?? 0);
    if ($subtotalUsd <= 0) {
        $rate = (float) str_replace(',', '.', (string) get_option('otmain_eur_to_usd_rate'));
        if ($rate > 0) {
            $subtotalUsd = ((float) $packing->subtotal) * $rate;
        }
    }

    // Calculate total G.W and N.W from items
    $totalGw = 0;
    $totalNw = 0;
    foreach ($packing->items as $item) {
        $totalGw += (float) ($item['gross_weight'] ?? 0);
        $totalNw += (float) ($item['net_weight'] ?? 0);
    }

    $html .= '<br /><br /><table cellpadding="3" cellspacing="0" width="100%" style="font-size:10px;color:#424242;">'
        . '<tr><td align="right" width="70%"><strong>Subtotal in EUR</strong></td><td align="right" width="30%">&euro; ' . otmain_pdf_format_total_amount($packing->subtotal, 'EUR') . '</td></tr>'
        . '<tr><td align="right" width="70%"><strong>Subtotal in USD</strong></td><td align="right" width="30%">' . ($subtotalUsd > 0 ? '$ ' . app_format_number($subtotalUsd) : '-') . '</td></tr>'
        . '</table>';

    return $html;
}
