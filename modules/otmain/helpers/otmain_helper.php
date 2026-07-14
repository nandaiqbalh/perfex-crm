<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Extract a single numeric tax rate from Perfex taxname payload(s).
 *
 * @param mixed $taxname string "VAT|21", array of those, or tax row arrays
 * @return float
 */
/**
 * Packing unit type options (box / pallet / other).
 *
 * @return array<string,string> value => label
 */
function otmain_packing_unit_types()
{
    return [
        'box'    => _l('otmain_unit_box'),
        'pallet' => _l('otmain_unit_pallet'),
        'other'  => _l('otmain_unit_other'),
    ];
}

/**
 * Human-readable packing unit label.
 *
 * @param string $unitType
 * @param string $unitLabel custom label when type is other
 * @return string
 */
function otmain_packing_unit_display($unitType, $unitLabel = '')
{
    $unitType = strtolower(trim((string) $unitType));
    if ($unitType === 'other') {
        $custom = trim((string) $unitLabel);

        return $custom !== '' ? $custom : _l('otmain_unit_other');
    }

    $types = otmain_packing_unit_types();

    return $types[$unitType] ?? _l('otmain_unit_box');
}

/**
 * CBM from dimensions in millimetres.
 * Formula: (L × W × H × qty) / 1_000_000_000
 *
 * @param float $length
 * @param float $width
 * @param float $height
 * @param float $qty
 * @return float
 */
function otmain_calc_cbm_mm($length, $width, $height, $qty = 1)
{
    $length = (float) $length;
    $width  = (float) $width;
    $height = (float) $height;
    $qty    = (float) $qty;

    if ($length <= 0 || $width <= 0 || $height <= 0 || $qty <= 0) {
        return 0.0;
    }

    return ($length * $width * $height * $qty) / 1000000000;
}

/**
 * Whether a packing-list item has physical packing data (dims / volume / weight).
 * Commercial-only lines should not appear in the Dimensions PDF table.
 *
 * @param array|object $item
 * @return bool
 */
function otmain_packing_item_has_packaging($item)
{
    $item = (array) $item;
    $length = isset($item['length']) && $item['length'] !== '' && $item['length'] !== null ? (float) $item['length'] : 0;
    $width  = isset($item['width']) && $item['width'] !== '' && $item['width'] !== null ? (float) $item['width'] : 0;
    $height = isset($item['height']) && $item['height'] !== '' && $item['height'] !== null ? (float) $item['height'] : 0;
    if ($length > 0 && $width > 0 && $height > 0) {
        return true;
    }
    if (trim((string) ($item['packing_detail'] ?? '')) !== '') {
        return true;
    }
    if (trim((string) ($item['volume'] ?? '')) !== '') {
        return true;
    }
    $gw = $item['gross_weight'] ?? null;
    $nw = $item['net_weight'] ?? null;
    if ($gw !== null && $gw !== '' && (float) $gw > 0) {
        return true;
    }
    if ($nw !== null && $nw !== '' && (float) $nw > 0) {
        return true;
    }

    return false;
}

/**
 * Packing-only line: physical package with no commercial qty/price.
 * Used so Invoice Items and Packing Details can be independent
 * (one box/pallet may cover multiple commercial lines).
 *
 * @param array|object $item
 * @return bool
 */
function otmain_is_packing_only_line($item)
{
    $item = (array) $item;
    if (!otmain_packing_item_has_packaging($item)) {
        return false;
    }

    return (float) ($item['qty'] ?? 0) == 0.0 && (float) ($item['unit_price'] ?? 0) == 0.0;
}

/**
 * Commercial invoice line (exclude packing-only package rows).
 *
 * @param array|object $item
 * @return bool
 */
function otmain_is_commercial_line($item)
{
    return !otmain_is_packing_only_line($item);
}

/**
 * Format quantity for PDF/UI: whole numbers without trailing decimals (1 not 1.00).
 *
 * @param mixed $qty
 * @return string
 */
function otmain_format_qty($qty)
{
    $qty = (float) $qty;
    if (fmod($qty, 1.0) === 0.0) {
        return (string) (int) $qty;
    }

    return app_format_number($qty);
}

/**
 * Build packing dimensions display string, e.g. "2 Box: L2630 x W860 x H1000mm".
 *
 * @param float  $qty
 * @param string $unitType
 * @param string $unitLabel
 * @param float  $length
 * @param float  $width
 * @param float  $height
 * @return string
 */
function otmain_format_packing_dimensions_string($qty, $unitType, $unitLabel, $length, $width, $height)
{
    $qtyDisplay = otmain_format_qty($qty);
    $unit       = otmain_packing_unit_display($unitType, $unitLabel);
    $length     = (float) $length;
    $width      = (float) $width;
    $height     = (float) $height;

    if ($length > 0 && $width > 0 && $height > 0) {
        $l = (fmod($length, 1.0) === 0.0) ? (string) (int) $length : rtrim(rtrim(number_format($length, 2, '.', ''), '0'), '.');
        $w = (fmod($width, 1.0) === 0.0) ? (string) (int) $width : rtrim(rtrim(number_format($width, 2, '.', ''), '0'), '.');
        $h = (fmod($height, 1.0) === 0.0) ? (string) (int) $height : rtrim(rtrim(number_format($height, 2, '.', ''), '0'), '.');

        return $qtyDisplay . ' ' . $unit . ': L' . $l . ' x W' . $w . ' x H' . $h . 'mm';
    }

    return $qtyDisplay . ' ' . $unit;
}

/**
 * Parse legacy free-text dimensions (LxWxH) into CBM.
 * Treats values as mm when "mm" is present or any side >= 100; otherwise cm.
 *
 * @param string $dims
 * @param float  $qty
 * @return float
 */
function otmain_cbm_from_dimensions_text($dims, $qty = 1)
{
    $dims = trim((string) $dims);
    if ($dims === '' || !preg_match('/([\d.]+)\s*[xX*]\s*([\d.]+)\s*[xX*]\s*([\d.]+)/', $dims, $m)) {
        return 0.0;
    }

    $l = (float) $m[1];
    $w = (float) $m[2];
    $h = (float) $m[3];
    $qty = (float) $qty;

    $isMm = (stripos($dims, 'mm') !== false) || ($l >= 100 || $w >= 100 || $h >= 100);
    if ($isMm) {
        return otmain_calc_cbm_mm($l, $w, $h, $qty);
    }

    // centimetres → m³
    return ($l * $w * $h * max($qty, 1)) / 1000000;
}

/**
 * Normalize a packing_items[] row from invoice form POST into stored JSON shape.
 *
 * @param array $pItem
 * @return array{qty:float,unit_type:string,unit_label:string,length:?float,width:?float,height:?float,dimensions:string,gw:float,nw:float,cbm:float}
 */
function otmain_normalize_invoice_packing_item(array $pItem)
{
    $qty       = (float) ($pItem['qty'] ?? 1);
    $gw        = (float) ($pItem['gw'] ?? 0);
    $nw        = (float) ($pItem['nw'] ?? 0);
    $unitType  = strtolower(trim((string) ($pItem['unit_type'] ?? 'box')));
    if (!in_array($unitType, ['box', 'pallet', 'other'], true)) {
        $unitType = 'box';
    }
    $unitLabel = trim((string) ($pItem['unit_label'] ?? ''));

    $length = isset($pItem['length']) && $pItem['length'] !== '' ? (float) $pItem['length'] : null;
    $width  = isset($pItem['width']) && $pItem['width'] !== '' ? (float) $pItem['width'] : null;
    $height = isset($pItem['height']) && $pItem['height'] !== '' ? (float) $pItem['height'] : null;

    $legacyDims = trim((string) ($pItem['dimensions'] ?? ''));
    $cbm        = 0.0;

    if ($length !== null && $width !== null && $height !== null && $length > 0 && $width > 0 && $height > 0) {
        $cbm  = otmain_calc_cbm_mm($length, $width, $height, $qty);
        $dims = otmain_format_packing_dimensions_string($qty, $unitType, $unitLabel, $length, $width, $height);
    } else {
        $dims = $legacyDims;
        $cbm  = otmain_cbm_from_dimensions_text($dims, $qty);
        if ($cbm <= 0 && !empty($pItem['cbm'])) {
            $cbm = (float) $pItem['cbm'];
        }
        if ($dims === '' && $qty > 0) {
            $dims = otmain_format_packing_dimensions_string($qty, $unitType, $unitLabel, 0, 0, 0);
        }
    }

    return [
        'qty'        => $qty,
        'unit_type'  => $unitType,
        'unit_label' => $unitType === 'other' ? $unitLabel : '',
        'length'     => $length,
        'width'      => $width,
        'height'     => $height,
        'dimensions' => $dims,
        'gw'         => $gw,
        'nw'         => $nw,
        'cbm'        => $cbm,
    ];
}

/**
 * HTML select options for packing unit type.
 *
 * @param string $selected
 * @param string $name
 * @param string $extraClass
 * @return string
 */
function otmain_packing_unit_select_html($selected, $name, $extraClass = '')
{
    $selected = strtolower(trim((string) $selected));
    if ($selected === '') {
        $selected = 'box';
    }
    $html = '<select name="' . e($name) . '" class="form-control otmain-packing-unit-type' . ($extraClass ? ' ' . e($extraClass) : '') . '">';
    foreach (otmain_packing_unit_types() as $value => $label) {
        $html .= '<option value="' . e($value) . '"' . ($selected === $value ? ' selected' : '') . '>' . e($label) . '</option>';
    }
    $html .= '</select>';

    return $html;
}

function otmain_extract_tax_rate($taxname)
{
    if ($taxname === null || $taxname === '') {
        return 0.0;
    }

    if (is_array($taxname)) {
        foreach ($taxname as $tax) {
            if (is_array($tax)) {
                if (isset($tax['taxrate'])) {
                    return (float) $tax['taxrate'];
                }
                if (!empty($tax['taxname'])) {
                    return otmain_extract_tax_rate($tax['taxname']);
                }
                continue;
            }

            return otmain_extract_tax_rate($tax);
        }

        return 0.0;
    }

    $parts = explode('|', (string) $taxname);
    if (count($parts) >= 2 && is_numeric($parts[count($parts) - 1])) {
        return (float) $parts[count($parts) - 1];
    }

    return is_numeric($taxname) ? (float) $taxname : 0.0;
}

/**
 * Free-form VAT % input that still posts Perfex-compatible taxname "VAT|{rate}".
 *
 * @param string $name    input name, e.g. items[1][taxname][]
 * @param mixed  $taxname current tax value(s)
 * @return string
 */
function otmain_get_taxes_input_template($name, $taxname = '')
{
    $rate     = otmain_extract_tax_rate($taxname);
    $rateAttr = rtrim(rtrim(number_format($rate, 4, '.', ''), '0'), '.');
    if ($rateAttr === '') {
        $rateAttr = '0';
    }
    $taxValue = 'VAT|' . $rateAttr;
    $nameAttr = htmlspecialchars((string) $name, ENT_QUOTES, 'UTF-8');
    $rateHtml = htmlspecialchars($rateAttr, ENT_QUOTES, 'UTF-8');
    $taxHtml  = htmlspecialchars($taxValue, ENT_QUOTES, 'UTF-8');

    return '<div class="otmain-tax-input-wrap">'
        . '<div class="input-group">'
        . '<input type="number" step="any" min="0" class="form-control otmain-tax-rate" value="' . $rateHtml . '" title="VAT %">'
        . '<span class="input-group-addon">%</span>'
        . '</div>'
        . '<select class="tax otmain-tax-select' . ((string) $name === 'taxname' ? ' main-tax' : '') . '" name="' . $nameAttr . '" multiple tabindex="-1" aria-hidden="true">'
        . '<option value="' . $taxHtml . '" data-taxrate="' . $rateHtml . '" selected>' . $rateHtml . '%</option>'
        . '</select>'
        . '</div>';
}

/**
 * Main preview-row tax input (invoice / estimate / proposal / credit note).
 *
 * @return string
 */
function otmain_get_main_tax_input_html()
{
    $default_tax = get_option('default_tax');
    $default_tax = $default_tax ? @unserialize($default_tax) : [];
    if (!is_array($default_tax)) {
        $default_tax = [];
    }

    return otmain_get_taxes_input_template('taxname', $default_tax);
}

/**
 * Open a labeled form section used across OT-Main edit forms.
 *
 * @param string $title Section heading (already translated)
 * @param string $id    Optional element id
 */
function otmain_form_section_open($title, $id = '')
{
    $CI = &get_instance();
    $CI->load->view('otmain/partials/form_section_open', [
        'title' => $title,
        'id'    => $id,
    ]);
}

/**
 * Close a form section opened with otmain_form_section_open().
 */
function otmain_form_section_close()
{
    $CI = &get_instance();
    $CI->load->view('otmain/partials/form_section_close');
}

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

function otmain_pdf_append_invoice_terms($pdf, $font_name, $font_size, $currencyName = '', $invoice = null)
{
    otmain_pdf_append_invoice_tc_page($pdf);
    $bankAccount = '';
    if (is_object($invoice)) {
        $bankAccount = otmain_invoice_bank_account_key($invoice);
    } elseif (is_string($currencyName) && $currencyName !== '') {
        $bankAccount = otmain_invoice_bank_account_key($currencyName);
    }
    otmain_pdf_append_account_detail_proof($pdf, $bankAccount);
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

function otmain_pdf_append_account_detail_proof($pdf, $bankAccount = '')
{
    $bankAccount = strtoupper(trim((string) $bankAccount));
    if ($bankAccount === 'USD') {
        otmain_pdf_append_customize_image_sequence($pdf, 'generated/account_details_proof_usd', false);

        return;
    }
    if ($bankAccount === 'EUR') {
        otmain_pdf_append_customize_image_sequence($pdf, 'generated/account_details_proof_eur', false);

        return;
    }

    otmain_pdf_append_customize_image_sequence($pdf, 'generated/account_details_proof_eur', false);
    otmain_pdf_append_customize_image_sequence($pdf, 'generated/account_details_proof_usd', false);
}

function otmain_invoice_bank_account_key($invoice)
{
    if (is_object($invoice) && !empty($invoice->bank_account)) {
        $bank = strtoupper(trim((string) $invoice->bank_account));
        if ($bank === 'EUR' || $bank === 'USD') {
            return $bank;
        }
    }

    $currencyName = '';
    if (is_object($invoice) && !empty($invoice->currency_name)) {
        $currencyName = $invoice->currency_name;
    } elseif (is_string($invoice)) {
        $currencyName = $invoice;
    }

    $currencyName = strtoupper(trim($currencyName));
    if ($currencyName === 'USD' || $currencyName === 'US DOLLAR' || strpos($currencyName, 'USD') !== false) {
        return 'USD';
    }

    return 'EUR';
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

    $street = trim(clear_textarea_breaks((string) ($client->billing_street ?? '')));
    $zip    = trim((string) ($client->billing_zip ?? ''));
    $city   = trim((string) ($client->billing_city ?? ''));
    $state  = trim((string) ($client->billing_state ?? ''));
    $countryId = (int) ($client->billing_country ?? 0);

    // Customer profile "Address / City / Zip / Country" live on address/city/... —
    // billing_* is often left empty. Fall back so PO/packing get the real address.
    if ($street === '' && $zip === '' && $city === '' && $state === '') {
        $street = trim(clear_textarea_breaks((string) ($client->address ?? '')));
        $zip    = trim((string) ($client->zip ?? ''));
        $city   = trim((string) ($client->city ?? ''));
        $state  = trim((string) ($client->state ?? ''));
        $countryId = (int) ($client->country ?? 0);
    }

    $lines = array_filter([
        $street,
        trim($zip . ' ' . $city),
        $state,
    ], static function ($line) {
        return $line !== '';
    });

    if ($countryId > 0) {
        $countryName = get_country_short_name($countryId);
        if ($countryName) {
            $lines[] = $countryName;
        }
    }

    return implode("\n", $lines);
}

/**
 * Collapse address text for comparison (ignore breaks / spacing).
 */
function otmain_normalize_address_compare($text)
{
    $text = clear_textarea_breaks((string) $text);
    $text = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    $text = strtolower(preg_replace('/\s+/u', '', $text));

    return $text !== null ? $text : '';
}

/**
 * True when $address is empty or looks like the PO issuer (OT-MAIN) address.
 */
function otmain_supplier_address_needs_client_refresh($address, $companyAddress = '')
{
    $current = trim(clear_textarea_breaks((string) $address));
    if ($current === '') {
        return true;
    }

    $companyAddress = trim((string) $companyAddress);
    if ($companyAddress === '') {
        $companyAddress = (string) (get_option('invoice_company_address') ?: 'Bajonetstraat 52');
    }

    $a = otmain_normalize_address_compare($current);
    $b = otmain_normalize_address_compare($companyAddress);
    if ($a !== '' && $b !== '' && ($a === $b || strpos($a, $b) !== false || strpos($b, $a) !== false)) {
        return true;
    }

    return false;
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

    if (!empty($po->supplierid)) {
        $fromClient = otmain_format_client_address_lines($po->supplierid);
        if ($fromClient !== '' && otmain_supplier_address_needs_client_refresh(
            $po->supplier_address ?? '',
            $po->company_address ?? ''
        )) {
            $po->supplier_address = nl2br_save_html($fromClient);
        }
    }

    return $po;
}

function otmain_tax_rate_key($rate)
{
    $rate = (float) $rate;
    if (fmod($rate, 1.0) === 0.0) {
        return (string) (int) $rate;
    }

    return rtrim(rtrim(number_format($rate, 4, '.', ''), '0'), '.');
}

function otmain_pdf_po_calculate_vat_summary($items)
{
    $subtotal = 0;
    $byRate   = [];

    foreach ($items as $item) {
        $line = (float) ($item['qty'] ?? 0) * (float) ($item['unit_price'] ?? 0);
        $rate = (float) ($item['taxrate'] ?? 0);
        $subtotal += $line;

        $key = otmain_tax_rate_key($rate);
        if (!isset($byRate[$key])) {
            $byRate[$key] = 0.0;
        }
        $byRate[$key] += $line * ($rate / 100);
    }

    ksort($byRate, SORT_NUMERIC);

    $totalTax = array_sum($byRate);

    return [
        'subtotal' => $subtotal,
        'by_rate'  => $byRate,
        'vat21'    => $totalTax,
        'vat0'     => 0.0,
        'total'    => $subtotal + $totalTax,
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

function otmain_packing_currency_name($packing)
{
    if (empty($packing->currency)) {
        $base = get_base_currency();

        return $base ? $base->name : 'EUR';
    }

    $CI = &get_instance();
    $CI->load->model('currencies_model');
    $currency = $CI->currencies_model->get($packing->currency);

    return $currency ? $currency->name : 'EUR';
}

/**
 * Normalize conversion_rate / conversion_currency from POST into DB-ready values.
 *
 * @param array $data
 * @return array
 */
function otmain_normalize_conversion_fields(array $data)
{
    if (array_key_exists('conversion_rate', $data)) {
        $rate = trim((string) $data['conversion_rate']);
        if ($rate === '') {
            $data['conversion_rate'] = null;
        } else {
            $data['conversion_rate'] = (float) str_replace(',', '.', $rate);
        }
    }

    if (array_key_exists('conversion_currency', $data)) {
        $cid = (int) $data['conversion_currency'];
        $data['conversion_currency'] = $cid > 0 ? $cid : null;
    }

    return $data;
}

/**
 * Render Convert to + Conversion rate fields for admin forms.
 *
 * @param object|null $document
 * @param array       $options id_prefix, col_class
 * @return string
 */
function otmain_render_conversion_fields_html($document = null, array $options = [])
{
    $CI = &get_instance();
    $CI->load->model('currencies_model');
    $currencies = $CI->currencies_model->get();

    $idPrefix = $options['id_prefix'] ?? 'otmain';
    $colClass = $options['col_class'] ?? 'col-md-6';
    $defaultConvRate = get_option('otmain_eur_to_usd_rate');

    $convRate = '';
    if (is_object($document) && isset($document->conversion_rate) && $document->conversion_rate !== null && $document->conversion_rate !== '') {
        $convRate = $document->conversion_rate;
    } else {
        $convRate = $defaultConvRate;
    }

    $convCurrency = otmain_get_conversion_currency_id($document);
    $nativeSelect = !empty($options['native_select']);

    ob_start();
    ?>
    <div class="<?php echo e($colClass); ?>">
        <?php if ($nativeSelect) { ?>
        <div class="form-group" app-field-wrapper="conversion_currency">
            <label for="<?php echo e($idPrefix); ?>-conversion-currency" class="control-label"><?php echo _l('otmain_conversion_currency'); ?></label>
            <select name="conversion_currency" id="<?php echo e($idPrefix); ?>-conversion-currency" class="form-control otmain-native-currency-select">
                <?php foreach ($currencies as $currencyOption) {
                    $cid = (int) ($currencyOption['id'] ?? 0);
                    if ($cid < 1) {
                        continue;
                    }
                    $cname   = (string) ($currencyOption['name'] ?? '');
                    $csymbol = trim((string) ($currencyOption['symbol'] ?? ''));
                    $label   = $cname;
                    if ($csymbol !== '' && strcasecmp($csymbol, $cname) !== 0) {
                        $label .= ' (' . $csymbol . ')';
                    }
                    ?>
                    <option value="<?php echo $cid; ?>"
                        data-subtext="<?php echo e($csymbol); ?>"
                        <?php echo ((int) $convCurrency === $cid) ? 'selected' : ''; ?>>
                        <?php echo e($label); ?>
                    </option>
                <?php } ?>
            </select>
        </div>
        <?php } else {
            echo render_select(
                'conversion_currency',
                $currencies,
                ['id', 'name', 'symbol'],
                _l('otmain_conversion_currency'),
                $convCurrency,
                // Do NOT pass "id" here: Perfex render_select uses select_attrs["id"] as the input NAME.
                ['data-show-subtext' => true],
                [],
                '',
                '',
                false
            );
        } ?>
    </div>
    <div class="<?php echo e($colClass); ?>">
        <?php
        echo render_input(
            'conversion_rate',
            _l('otmain_conversion_rate'),
            $convRate,
            'number',
            [
                'step'        => 'any',
                'min'         => '0',
                // id is safe on render_input (does not override name)
                'id'          => $idPrefix . '-conversion-rate',
                'placeholder' => $defaultConvRate !== '' ? $defaultConvRate : 'e.g. 1.09',
            ]
        );
        ?>
        <p class="text-muted"><?php echo _l('otmain_conversion_rate_help'); ?></p>
    </div>
    <?php

    return ob_get_clean();
}

/**
 * Resolve conversion rate: document override, else settings default.
 *
 * @param mixed $document object/array with conversion_rate, or null
 * @return float
 */
function otmain_get_conversion_rate($document = null)
{
    $fromDoc = null;
    if (is_object($document) && isset($document->conversion_rate) && $document->conversion_rate !== '' && $document->conversion_rate !== null) {
        $fromDoc = $document->conversion_rate;
    } elseif (is_array($document) && array_key_exists('conversion_rate', $document) && $document['conversion_rate'] !== '' && $document['conversion_rate'] !== null) {
        $fromDoc = $document['conversion_rate'];
    }

    if ($fromDoc !== null) {
        $rate = (float) str_replace(',', '.', (string) $fromDoc);
        if ($rate > 0) {
            return $rate;
        }
    }

    return (float) str_replace(',', '.', (string) get_option('otmain_eur_to_usd_rate'));
}

/**
 * Target conversion currency id (document override, else settings, else USD, else base).
 *
 * @param mixed $document
 * @return int
 */
function otmain_get_conversion_currency_id($document = null)
{
    $fromDoc = null;
    if (is_object($document) && isset($document->conversion_currency) && $document->conversion_currency !== '' && $document->conversion_currency !== null) {
        $fromDoc = (int) $document->conversion_currency;
    } elseif (is_array($document) && !empty($document['conversion_currency'])) {
        $fromDoc = (int) $document['conversion_currency'];
    }

    if ($fromDoc > 0) {
        return $fromDoc;
    }

    $opt = (int) get_option('otmain_default_conversion_currency');
    if ($opt > 0) {
        return $opt;
    }

    $usd = get_currency('USD');
    if ($usd) {
        return (int) $usd->id;
    }

    $base = get_base_currency();

    return $base ? (int) $base->id : 0;
}

/**
 * @param mixed $document
 * @return object|null currency row
 */
function otmain_get_conversion_currency($document = null)
{
    $id = otmain_get_conversion_currency_id($document);

    return $id > 0 ? get_currency($id) : null;
}

/**
 * @deprecated use otmain_get_conversion_rate()
 */
function otmain_get_eur_usd_rate($document = null)
{
    return otmain_get_conversion_rate($document);
}

/**
 * Normalize a currency reference to a short display code for use in PDFs.
 * EUR → EURO; Indonesian Rupiah → IDR; US Dollar → USD.
 *
 * @param mixed $currency string name/code, currency object, or id
 * @return string
 */
function otmain_currency_display_code($currency)
{
    $name   = '';
    $symbol = '';

    if (is_object($currency)) {
        $name   = (string) ($currency->name ?? '');
        $symbol = (string) ($currency->symbol ?? '');
    } elseif (is_numeric($currency)) {
        $row = get_currency((int) $currency);
        if ($row) {
            $name   = (string) ($row->name ?? '');
            $symbol = (string) ($row->symbol ?? '');
        }
    } else {
        $name = trim((string) $currency);
        if ($name !== '') {
            $row = get_currency($name);
            if ($row) {
                $name   = (string) ($row->name ?? $name);
                $symbol = (string) ($row->symbol ?? '');
            }
        }
    }

    $name   = strtoupper(trim(html_entity_decode($name, ENT_QUOTES | ENT_HTML5, 'UTF-8')));
    $name   = preg_replace('/\x{00A0}/u', ' ', $name);
    $name   = preg_replace('/[^A-Z0-9]+/', ' ', $name);
    $name   = trim(preg_replace('/\s+/', ' ', $name));
    $symbol = trim(html_entity_decode((string) $symbol, ENT_QUOTES | ENT_HTML5, 'UTF-8'));

    // Use symbol when available
    if ($symbol === '€') {
        return '€';
    }
    if ($symbol === '£') {
        return '£';
    }
    if ($symbol === '$') {
        return '$';
    }
    if ($symbol !== '' && $symbol !== $name) {
        return $symbol;
    }

    // Fallback: short text codes
    if ($name === 'EUR' || $name === 'EURO' || $name === 'EUROPEAN EURO') {
        return 'EURO';
    }
    if ($name === 'GBP' || strpos($name, 'POUND') !== false || strpos($name, 'STERLING') !== false) {
        return 'GBP';
    }
    if ($name === 'USD' || strpos($name, 'DOLLAR') !== false || strpos($name, 'UNITED STATES') !== false) {
        return 'USD';
    }
    if ($name === 'IDR' || strpos($name, 'RUPIAH') !== false || strpos($name, 'INDONESIA') !== false) {
        return 'IDR';
    }
    if (preg_match('/^[A-Z]{3}$/', $name)) {
        return $name;
    }

    // Never print long full names on PDFs (causes wrapping like INDONESIAN / RUPIAH)
    if (strlen($name) > 4) {
        return substr(str_replace(' ', '', $name), 0, 3);
    }

    return $name !== '' ? $name : 'EURO';
}

/**
 * Label-safe currency code for totals rows (USD / GBP / EURO).
 * Avoids "Subtotal $ … $ 1.00" doubling when amounts already use a symbol.
 *
 * @param mixed $currency
 * @return string
 */
function otmain_currency_label_code($currency)
{
    $code = otmain_currency_display_code($currency);
    if ($code === '$') {
        return 'USD';
    }
    if ($code === '£') {
        return 'GBP';
    }
    if ($code === '€') {
        return 'EURO';
    }

    return $code;
}

/**
 * Get the currency symbol (€, $, etc.) for display in PDFs.
 * Falls back to the display code if no symbol is found.
 *
 * @param object|string|int $currency Currency object, name string, or ID
 * @return string
 */
function otmain_currency_display_symbol($currency)
{
    $symbol = '';

    if (is_object($currency) && !empty($currency->symbol)) {
        $symbol = trim((string) $currency->symbol);
    } elseif (is_numeric($currency)) {
        $row = get_currency((int) $currency);
        if ($row && !empty($row->symbol)) {
            $symbol = trim((string) $row->symbol);
        }
    } elseif (is_string($currency) && $currency !== '') {
        $row = get_currency($currency);
        if ($row && !empty($row->symbol)) {
            $symbol = trim((string) $row->symbol);
        }
    }

    if ($symbol === '€') {
        return '€';
    }

    return $symbol !== '' ? $symbol : otmain_currency_display_code($currency);
}

function otmain_format_money_text($amount, $currency = 'EUR')
{
    if (!isset($amount) || $amount === '' || (float) $amount == 0.0) {
        return '-';
    }

    $code = otmain_currency_display_code($currency);

    // Symbol (€, $) goes before, text code (EURO, USD) goes after
    if (in_array($code, ['€', '$', '£', '¥', 'Rp', '₩', '₽', '₹'])) {
        return $code . ' ' . app_format_number($amount);
    }

    return app_format_number($amount) . ' ' . $code;
}

function otmain_pdf_po_left_block_html($po)
{
    $poNumber = $po->formatted_number ?: otmain_format_purchase_order_number($po->id);

    $quoteRefDisplay = trim((string) ($po->supplier_quote_ref ?? ''));
    if ($quoteRefDisplay === '' && !empty($po->proposal_id)) {
        $quoteRefDisplay = format_proposal_number((int) $po->proposal_id);
    }
    if ($quoteRefDisplay === '') {
        $quoteRefDisplay = '-';
    }

    $ourQuoteHtml = '';
    if (!empty($po->proposal_id)) {
        $ourQuoteHtml = '<strong>Our Quote Ref.:</strong> ' . e(format_proposal_number((int) $po->proposal_id)) . '<br />';
    }

    return '<div style="' . otmain_pdf_meta_text_style() . '">'
        . '<strong>P.O. to:</strong><br />'
        . e(get_company_name($po->supplierid)) . '<br />'
        . process_text_content_for_display($po->supplier_address ?? '')
        . '<br />'
        . '<strong>Order Date:</strong> <span style="font-weight:bold;">' . e(otmain_pdf_format_document_date($po->date ?? '')) . '</span><br />'
        . '<strong>P.O. Number:</strong> <span style="font-weight:bold;">' . e($poNumber) . '</span><br />'
        . '<strong>Supplier Quote Ref.:</strong> ' . e($quoteRefDisplay) . '<br />'
        . $ourQuoteHtml
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
        $unitPrice  = otmain_format_money_text($item['unit_price'], $currencyName);
        $lineAmount = otmain_format_money_text($lineTotal, $currencyName);
        $taxLabel   = otmain_pdf_format_tax_rate($item['taxrate'] ?? 0);

        $html .= '<tr>'
            . '<td align="center">' . otmain_format_qty($item['qty']) . '</td>'
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
    // Prefer ISO codes in labels so amounts that already carry $, £, € don't look doubled.
    $currencyLabel = otmain_currency_label_code($currencyName);

    $origUsdDisplay  = isset($po->total_usd_display) ? trim((string) $po->total_usd_display) : '';
    $origGoldDisplay = isset($po->total_gold_display) ? trim((string) $po->total_gold_display) : '';
    $usdDisplay      = $origUsdDisplay;
    $goldDisplay     = $origGoldDisplay;
    if ($goldDisplay === '0' || $goldDisplay === '0.00' || strtolower($goldDisplay) === '0') {
        $goldDisplay = '';
    }

    $rate   = otmain_get_conversion_rate($po);
    $target = otmain_get_conversion_currency($po);
    $docCurrencyId = isset($po->currency) ? (int) $po->currency : 0;
    $targetId = $target ? (int) $target->id : 0;
    $canConvert = ($rate > 0 && $target && $targetId > 0 && $targetId !== $docCurrencyId);

    // Auto-calc converted total from Convert to + rate when display override is empty.
    if ($usdDisplay === '' && $canConvert) {
        $usdDisplay = otmain_format_money_text(((float) $summary['total']) * $rate, $target);
    }
    if ($goldDisplay === '') {
        $pricePerGram = (float) str_replace(',', '.', (string) get_option('otmain_gold_price_eur_per_gram'));
        if ($pricePerGram > 0) {
            $grams = ((float) $summary['total']) / $pricePerGram;
            $goldDisplay = app_format_number($grams) . ' in Gram';
        }
    }

    $html = '<table cellpadding="3" cellspacing="0" width="100%" style="font-size:10px;color:#424242;">';
    $html .= '<tr><td align="right" width="70%"><strong>Subtotal ' . e($currencyLabel) . '</strong></td><td align="right" width="30%">' . otmain_pdf_format_total_amount($summary['subtotal'], $currencyName) . '</td></tr>';
    foreach ($summary['by_rate'] as $vatRate => $amount) {
        $html .= '<tr><td align="right"><strong>VAT ' . e((string) $vatRate) . '%</strong></td><td align="right">' . otmain_pdf_format_total_amount($amount, $currencyName) . '</td></tr>';
    }
    $html .= '<tr><td align="right"><strong>TOTAL ' . e($currencyLabel) . '</strong></td><td align="right"><strong>' . otmain_pdf_format_total_amount($summary['total'], $currencyName) . '</strong></td></tr>';

    // Show conversion when Convert to + rate are set (or explicit TOTAL USD display override).
    if ($usdDisplay !== '' && ($canConvert || $origUsdDisplay !== '')) {
        $convertedLabel = $target
            ? ('TOTAL ' . otmain_currency_label_code($target))
            : 'TOTAL CONVERTED';
        $html .= '<tr><td align="right"><strong>' . e($convertedLabel) . '</strong></td><td align="right">' . e($usdDisplay) . '</td></tr>';
    }
    if ($origGoldDisplay !== '') {
        $html .= '<tr><td align="right"><strong>TOTAL GOLD</strong></td><td align="right">' . e($goldDisplay) . '</td></tr>';
    }
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

function otmain_get_pdf_logo_path()
{
    $paths = array_filter([
        FCPATH . 'media/removed-bg-logo.png',
        FCPATH . 'uploads/company/otmain_logo.png',
        FCPATH . 'uploads/company/otmain_logo.jpeg',
        otmain_customize_file_path('logo_otmain.png'),
        otmain_customize_file_path('logo_otmain.jpeg'),
    ]);

    foreach ($paths as $path) {
        if (file_exists($path)) {
            return $path;
        }
    }

    return '';
}

function otmain_pdf_logo_url($width = 160)
{
    $path = otmain_get_pdf_logo_path();
    if ($path !== '') {
        return '<img width="' . (int) $width . 'px" src="' . $path . '">';
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

    $quoteRef = '-';
    if (!empty($invoice->proposal_id)) {
        $quoteRef = format_proposal_number((int) $invoice->proposal_id);
    } elseif (!empty($invoice->quote_ref)) {
        // Legacy estimate link (pre–proposal Quote Ref)
        $quoteRef = format_estimate_number($invoice->quote_ref);
    }

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

    $contactName  = trim((string) ($estimate->contact_person_name ?? ''));
    $contactEmail = trim((string) ($estimate->contact_person_email ?? ''));
    $contactPhone = trim((string) ($estimate->contact_person_phone ?? ''));

    if ($contactName === '') {
        $contact = otmain_get_primary_contact($estimate->clientid);
        $contactName  = $contact ? trim(($contact['firstname'] ?? '') . ' ' . ($contact['lastname'] ?? '')) : '-';
        $contactEmail = $contact['email'] ?? '-';
        $contactPhone = $contact['phonenumber'] ?? '-';
    }

    if ($contactEmail === '') {
        $contactEmail = '-';
    }
    if ($contactPhone === '') {
        $contactPhone = '-';
    }

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
        . '<span style="font-weight:bold;font-size:18px;color:#00205B;">Quotation</span>'
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
        $unitPrice  = $currencyName !== '' ? otmain_format_money_text($item['rate'], $currencyName) : app_format_number($item['rate']);
        $lineAmount = $currencyName !== '' ? otmain_format_money_text($lineTotal, $currencyName) : app_format_number($lineTotal);

        $html .= '<tr>'
            . '<td align="center">' . otmain_format_qty($item['qty']) . '</td>'
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
    return otmain_format_money_text($amount, $currencyName);
}

function otmain_pdf_invoice_two_table_html($items, $packingItems, $currencyName, $document = null)
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
        $qtyDisplay = otmain_format_qty($qty);

        // Description with HS Code from long_description
        $desc = e($item['description']);
        if (!empty($item['long_description'])) {
            $desc .= '<br /><span style="font-size:9px;">' . process_text_content_for_display($item['long_description']) . '</span>';
        }

        $unitPrice = otmain_format_money_text($rate, $currencyName);
        $lineAmt   = otmain_format_money_text($lineTotal, $currencyName);

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
            if (!otmain_packing_item_has_packaging($pItem)) {
                continue;
            }

            $qtyPack = isset($pItem['packing_qty']) && $pItem['packing_qty'] !== '' && $pItem['packing_qty'] !== null
                ? (float) $pItem['packing_qty']
                : (float) ($pItem['qty'] ?? 1);
            $gw      = (float) ($pItem['gw'] ?? $pItem['gross_weight'] ?? 0);
            $nw      = (float) ($pItem['nw'] ?? $pItem['net_weight'] ?? 0);
            $cbm     = (float) ($pItem['cbm'] ?? 0);
            $unitDisp = otmain_packing_unit_display($pItem['unit_type'] ?? 'box', $pItem['unit_label'] ?? '');

            $length = $pItem['length'] ?? null;
            $width  = $pItem['width'] ?? null;
            $height = $pItem['height'] ?? null;
            if ($length && $width && $height && $cbm <= 0) {
                $cbm = otmain_calc_cbm_mm($length, $width, $height, $qtyPack);
            }

            $dims = $pItem['dimensions'] ?? ($pItem['packing_detail'] ?? '');
            if ($length && $width && $height) {
                $dims = otmain_format_packing_dimensions_string($qtyPack, $pItem['unit_type'] ?? 'box', $pItem['unit_label'] ?? '', $length, $width, $height);
            }

            $totalGw  += $gw;
            $totalNw  += $nw;
            $totalCbm += $cbm;

            $qtyPackDisplay = otmain_format_qty($qtyPack);
            $qtyPackDisplay .= ' ' . e($unitDisp);

            $dimHtml = e($dims);
            if ($cbm > 0) {
                $dimHtml .= '<br /><span style="font-size:9px;">Volume: ' . number_format($cbm, 2, '.', '') . ' CBM</span>';
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

    // USD auto-calc (any target currency via document conversion settings)
    $rate = otmain_get_conversion_rate($document);
    $usdDisplay = '';
    if ($rate > 0) {
        $target = otmain_get_conversion_currency($document);
        $usdDisplay = otmain_format_money_text($subtotalEur * $rate, $target ?: 'USD');
    }

    // Total Weight + subtotal row
    $html .= '<tr>'
        . '<td style="' . $cellStyle . 'text-align:center;font-weight:bold;">&nbsp;</td>'
        . '<td style="' . $cellStyle . 'font-weight:bold;">Total Weight</td>'
        . '<td style="' . $cellStyle . 'text-align:right;font-weight:bold;">' . app_format_number($totalGw) . '</td>'
        . '<td style="' . $cellStyle . 'text-align:right;font-weight:bold;">' . app_format_number($totalNw) . '</td>'
        . '<td style="' . $rightSty . 'font-weight:bold;background-color:#f5f5f5;">Subtotal<br />' . otmain_format_money_text($subtotalEur, $currencyName) . '</td>'
        . '</tr>';

    // CBM row + USD
    $html .= '<tr>'
        . '<td style="' . $cellStyle . 'text-align:center;font-weight:bold;">&nbsp;</td>'
        . '<td style="' . $cellStyle . 'font-weight:bold;">Total CBM: ' . app_format_number($totalCbm) . '</td>'
        . '<td style="' . $cellStyle . 'text-align:right;">&nbsp;</td>'
        . '<td style="' . $cellStyle . 'text-align:right;">&nbsp;</td>'
        . '<td style="' . $rightSty . 'font-weight:bold;background-color:#f5f5f5;">Subtotal ' . e(otmain_currency_display_code(otmain_get_conversion_currency($document) ?: 'USD')) . '<br />' . ($usdDisplay ?: '-') . '</td>'
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
    $byRate = [];

    foreach ($items->taxes() as $tax) {
        $rate = otmain_tax_rate_key((float) $tax['taxrate']);
        if (!isset($byRate[$rate])) {
            $byRate[$rate] = 0.0;
        }
        $byRate[$rate] += (float) $tax['total_tax'];
    }

    ksort($byRate, SORT_NUMERIC);

    return $byRate;
}

function otmain_pdf_totals_column_html($document, $items, $currencyName)
{
    $byRate = otmain_pdf_calculate_vat_totals($items);

    $origUsdDisplay  = isset($document->total_usd_display) ? trim((string) $document->total_usd_display) : '';
    $origGoldDisplay = isset($document->total_gold_display) ? trim((string) $document->total_gold_display) : '';
    $usdDisplay      = $origUsdDisplay;
    $goldDisplay     = $origGoldDisplay;
    if ($goldDisplay === '0' || $goldDisplay === '0.00' || strtolower($goldDisplay) === '0') {
        $goldDisplay = '';
    }

    $rate   = otmain_get_conversion_rate($document);
    $target = otmain_get_conversion_currency($document);
    $docCurrencyId = isset($document->currency) ? (int) $document->currency : 0;
    $targetId = $target ? (int) $target->id : 0;
    $canConvert = ($rate > 0 && $target && $targetId > 0 && $targetId !== $docCurrencyId);

    // Auto-calc when display fields are empty (requires options / document rate).
    if ($usdDisplay === '' && $canConvert) {
        $usdDisplay = otmain_format_money_text(((float) $document->total) * $rate, $target);
    } elseif (!$canConvert && $usdDisplay === '') {
        $usdDisplay = '';
    }

    if ($goldDisplay === '') {
        $pricePerGram = (float) str_replace(',', '.', (string) get_option('otmain_gold_price_eur_per_gram'));
        if ($pricePerGram > 0) {
            $grams = ((float) $document->total) / $pricePerGram;
            $goldDisplay = app_format_number($grams) . ' in Gram';
        }
    }

    $cellAmt = 'align="right" width="30%" style="white-space:nowrap;"';
    $html = '<table cellpadding="3" cellspacing="0" width="100%" style="font-size:10px;color:#424242;">';
    $html .= '<tr><td align="right" width="70%"><strong>Subtotal</strong></td><td ' . $cellAmt . '>' . otmain_pdf_format_total_amount($document->subtotal, $currencyName) . '</td></tr>';
    if (is_sale_discount_applied($document)) {
        $discountLabel = _l('estimate_discount');
        if (isset($document->discount_percent) && (float) $document->discount_percent > 0) {
            $discountLabel .= ' (' . e(app_format_number($document->discount_percent, true)) . '%)';
        }
        $html .= '<tr><td align="right" width="70%"><strong>' . e($discountLabel) . '</strong></td><td ' . $cellAmt . '>-' . otmain_pdf_format_total_amount($document->discount_total, $currencyName) . '</td></tr>';
    }
    // Always show VAT rows — even when amount is 0 (displayed as € -).
    // Standard rates: 21%, then 0%.
    $vat21 = isset($byRate[21]) ? $byRate[21] : 0.0;
    $vat0  = isset($byRate[0]) ? $byRate[0] : 0.0;
    $html .= '<tr><td align="right" width="70%"><strong>VAT 21%</strong></td><td ' . $cellAmt . '>' . otmain_pdf_format_total_amount($vat21, $currencyName) . '</td></tr>';
    $html .= '<tr><td align="right" width="70%"><strong>VAT 0%</strong></td><td ' . $cellAmt . '>' . otmain_pdf_format_total_amount($vat0, $currencyName) . '</td></tr>';
    $currencyLabel = otmain_currency_label_code($currencyName);
    $html .= '<tr><td align="right" width="70%"><strong>TOTAL ' . e($currencyLabel) . '</strong></td><td ' . $cellAmt . '><strong>' . otmain_pdf_format_total_amount($document->total, $currencyName) . '</strong></td></tr>';

    // Show conversion when Convert to + rate are set, or when TOTAL USD display override is filled.
    if ($usdDisplay !== '' && ($canConvert || $origUsdDisplay !== '')) {
        $convertedLabel = 'TOTAL CONVERTED';
        if ($target) {
            $convertedLabel = 'TOTAL ' . otmain_currency_label_code($target);
        }
        $html .= '<tr><td align="right" width="70%"><strong>' . e($convertedLabel) . '</strong></td><td ' . $cellAmt . '>' . e($usdDisplay) . '</td></tr>';
    }

    // Gold row — only shown when total_gold_display was EXPLICITLY set on the document
    if ($origGoldDisplay !== '') {
        $html .= '<tr><td align="right" width="70%"><strong>TOTAL GOLD</strong></td><td ' . $cellAmt . '>' . e($goldDisplay) . '</td></tr>';
    }
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
    $bankKey  = otmain_invoice_bank_account_key($invoice);
    $rightMeta = otmain_pdf_right_column_wrap_html(otmain_pdf_invoice_right_column_html($bankKey));

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
    $title   = !empty($proposal->document_title) ? $proposal->document_title : 'Quotation';

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

    // Data stored already has <br /> from nl2br_save_html in packing_list_model.
    // Strip them back before we split, so we don't double-encode.
    $quoteRef = str_ireplace(['<br />', '<br>', '<br/>'], "\n", $quoteRef);

    $lines = preg_split("/\\r\\n|\\n|\\r/", $quoteRef);
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

    $contactName  = trim((string) ($packing->contact_person_name ?? ''));
    $contactEmail = trim((string) ($packing->contact_person_email ?? ''));
    $contactPhone = trim((string) ($packing->contact_person_phone ?? ''));

    if ($contactName === '' && !empty($packing->clientid)) {
        $contact = otmain_get_primary_contact($packing->clientid);
        $contactName  = $contact ? trim(($contact['firstname'] ?? '') . ' ' . ($contact['lastname'] ?? '')) : '';
        $contactEmail = $contact['email'] ?? '';
        $contactPhone = $contact['phonenumber'] ?? '';
    }

    $contactBlock = '';
    if ($contactName !== '' || $contactEmail !== '' || $contactPhone !== '') {
        $contactBlock = '<br /><br />'
            . '<strong>Contact Person:</strong> ' . e($contactName ?: '-') . '<br />'
            . '<strong>Email Address:</strong> ' . e($contactEmail ?: '-') . '<br />'
            . '<strong>Phone Number:</strong> ' . e($contactPhone ?: '-');
    }

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
        . $contactBlock
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
        . '<th width="42%"><strong>Description</strong></th>'
        . '<th width="15%" align="right"><strong>Unit Price</strong></th>'
        . '<th width="10%" align="center"><strong>VAT %</strong></th>'
        . '<th width="25%" align="right"><strong>Total</strong></th>'
        . '</tr>';

    foreach ($packing->items as $item) {
        if (otmain_is_packing_only_line($item)) {
            continue;
        }
        $html .= '<tr>'
            . '<td align="center">' . otmain_format_qty($item['qty']) . '</td>'
            . '<td>' . otmain_pdf_packing_item_description_html($item) . '</td>'
            . '<td align="right">' . app_format_number($item['unit_price']) . '</td>'
            . '<td align="center">' . e(otmain_pdf_format_tax_rate($item['taxrate'] ?? 0)) . '</td>'
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

    $totalCbmFooter = 0;
    $hasPackingRows = false;
    foreach ($packing->items as $item) {
        if (!otmain_packing_item_has_packaging($item)) {
            continue;
        }
        $hasPackingRows = true;

        // Packing QTY = physical units (box/pallet), not commercial line qty.
        $packQty = isset($item['packing_qty']) && $item['packing_qty'] !== '' && $item['packing_qty'] !== null
            ? (float) $item['packing_qty']
            : 1.0;
        $unitDisp = otmain_packing_unit_display($item['unit_type'] ?? 'box', $item['unit_label'] ?? '');
        $qtyDisplay = otmain_format_qty($packQty);
        $qtyDisplay .= ' ' . e($unitDisp);

        $length = $item['length'] ?? null;
        $width  = $item['width'] ?? null;
        $height = $item['height'] ?? null;
        $dims   = $item['packing_detail'] ?? '';
        $cbm    = 0.0;

        if ($length && $width && $height) {
            $cbm  = otmain_calc_cbm_mm($length, $width, $height, $packQty);
            $dims = otmain_format_packing_dimensions_string(
                $packQty,
                $item['unit_type'] ?? 'box',
                $item['unit_label'] ?? '',
                $length,
                $width,
                $height
            );
        } elseif (!empty($item['volume']) && preg_match('/([\d.]+)/', $item['volume'], $vm)) {
            $cbm = (float) $vm[1];
        } elseif ($dims !== '') {
            $cbm = otmain_cbm_from_dimensions_text($dims, $packQty);
        }

        $totalCbmFooter += $cbm;

        $dimHtml = e($dims);
        if ($cbm > 0) {
            $dimHtml .= '<br /><span style="font-size:9px;">Volume: ' . number_format($cbm, 2, '.', '') . ' CBM</span>';
        }

        $html .= '<tr>'
            . '<td align="center">' . $qtyDisplay . '</td>'
            . '<td>' . $dimHtml . '</td>'
            . '<td align="right">' . ($item['gross_weight'] !== null && $item['gross_weight'] !== '' ? app_format_number($item['gross_weight']) : '-') . '</td>'
            . '<td align="right">' . ($item['net_weight'] !== null && $item['net_weight'] !== '' ? app_format_number($item['net_weight']) : '-') . '</td>'
            . '<td align="right">&nbsp;</td>'
            . '</tr>';
    }

    if (!$hasPackingRows) {
        $html .= '<tr>'
            . '<td align="center">-</td>'
            . '<td>-</td>'
            . '<td align="right">-</td>'
            . '<td align="right">-</td>'
            . '<td align="right">&nbsp;</td>'
            . '</tr>';
    }

    // Add Total Weight row at bottom of packing table
    $totalGwFooter = 0;
    $totalNwFooter = 0;
    foreach ($packing->items as $item) {
        if (!otmain_packing_item_has_packaging($item)) {
            continue;
        }
        $totalGwFooter += (float) ($item['gross_weight'] ?? 0);
        $totalNwFooter += (float) ($item['net_weight'] ?? 0);
    }
    if (isset($packing->total_cbm) && (float) $packing->total_cbm > 0) {
        $totalCbmFooter = (float) $packing->total_cbm;
    }
    $html .= '<tr style="font-weight:bold;background-color:#f5f5f5;">'
        . '<td align="center">&nbsp;</td>'
        . '<td>Total Weight</td>'
        . '<td align="right">' . app_format_number($totalGwFooter) . '</td>'
        . '<td align="right">' . app_format_number($totalNwFooter) . '</td>'
        . '<td align="right">&nbsp;</td>'
        . '</tr>';
    $html .= '<tr style="font-weight:bold;background-color:#f5f5f5;">'
        . '<td align="center">&nbsp;</td>'
        . '<td>Total CBM: ' . app_format_number($totalCbmFooter) . '</td>'
        . '<td align="right">&nbsp;</td>'
        . '<td align="right">&nbsp;</td>'
        . '<td align="right">&nbsp;</td>'
        . '</tr>';

    $html .= '</table><br />';

    $totalWeight = isset($packing->total_weight) ? (float) $packing->total_weight : 0;
    if ($totalWeight <= 0) {
        foreach ($packing->items as $item) {
            $totalWeight += (float) ($item['gross_weight'] ?? 0);
        }
    }

    $subtotalConverted = (float) ($packing->subtotal_usd ?? 0);
    $currencyName = otmain_packing_currency_name($packing);
    $currencyCode = otmain_currency_display_code($currencyName);
    $targetCurrency = otmain_get_conversion_currency($packing);
    $targetCode = $targetCurrency ? otmain_currency_display_code($targetCurrency) : 'USD';
    $docCurrencyId = !empty($packing->currency) ? (int) $packing->currency : 0;
    $targetId = $targetCurrency ? (int) $targetCurrency->id : 0;
    if ($subtotalConverted <= 0) {
        $rate = otmain_get_conversion_rate($packing);
        if ($rate > 0 && $targetId > 0 && $targetId !== $docCurrencyId) {
            $subtotalConverted = ((float) $packing->subtotal) * $rate;
        }
    }

    // Calculate total G.W and N.W from items
    $totalGw = 0;
    $totalNw = 0;
    foreach ($packing->items as $item) {
        $totalGw += (float) ($item['gross_weight'] ?? 0);
        $totalNw += (float) ($item['net_weight'] ?? 0);
    }

    $vatSummary = otmain_pdf_po_calculate_vat_summary($packing->items);

    $html .= '<br /><br /><table cellpadding="3" cellspacing="0" width="100%" style="font-size:10px;color:#424242;">'
        . '<tr><td align="right" width="70%"><strong>Subtotal in ' . e($currencyCode) . '</strong></td><td align="right" width="30%">' . otmain_pdf_format_total_amount($packing->subtotal, $currencyName) . '</td></tr>';
    if ($subtotalConverted > 0 && $targetId !== $docCurrencyId) {
        $html .= '<tr><td align="right" width="70%"><strong>Subtotal in ' . e($targetCode) . '</strong></td><td align="right" width="30%">' . otmain_format_money_text($subtotalConverted, $targetCurrency ?: $targetCode) . '</td></tr>';
    }
    foreach ($vatSummary['by_rate'] as $rate => $amount) {
        $html .= '<tr><td align="right" width="70%"><strong>VAT ' . e((string) $rate) . '%</strong></td><td align="right" width="30%">' . otmain_pdf_format_total_amount($amount, $currencyName) . '</td></tr>';
    }
    $html .= '<tr><td align="right" width="70%"><strong>TOTAL ' . e($currencyCode) . '</strong></td><td align="right" width="30%"><strong>' . otmain_pdf_format_total_amount($vatSummary['total'], $currencyName) . '</strong></td></tr>';
    $html .= '</table>';

    return $html;
}

/**
 * Format item tracker status as a colored badge.
 *
 * @param string $status
 * @return string
 */
function otmain_format_item_status($status)
{
    $status = $status ?: 'pending';
    $label  = _l('otmain_status_' . $status);
    if ($label === 'otmain_status_' . $status) {
        $label = ucwords(str_replace('_', ' ', $status));
    }

    return '<span class="otmain-status-badge item-status-' . e($status) . '">' . e($label) . '</span>';
}

/**
 * Format quotation tracker status as a colored badge.
 *
 * @param string $status
 * @return string
 */
function otmain_format_quotation_status($status)
{
    $status = $status ?: 'pending';
    $label  = _l('otmain_qstatus_' . $status);
    if ($label === 'otmain_qstatus_' . $status) {
        $label = ucwords(str_replace('_', ' ', $status));
    }

    return '<span class="otmain-status-badge quote-status-' . e($status) . '">' . e($label) . '</span>';
}

/**
 * Item status options for dropdowns.
 *
 * @return array
 */
function otmain_item_tracker_status_options()
{
    return [
        'pending'       => _l('otmain_status_pending'),
        'ordered'       => _l('otmain_status_ordered'),
        'eta'           => _l('otmain_status_eta'),
        'quality_check' => _l('otmain_status_quality_check'),
        'received'      => _l('otmain_status_received'),
    ];
}

/**
 * Quotation status options for dropdowns.
 *
 * @return array
 */
function otmain_quotation_status_options()
{
    return [
        'pending'            => _l('otmain_qstatus_pending'),
        'in_progress'        => _l('otmain_qstatus_in_progress'),
        'ready_for_shipment' => _l('otmain_qstatus_ready_for_shipment'),
        'shipped'            => _l('otmain_qstatus_shipped'),
    ];
}
