<?php

defined('BASEPATH') or exit('No direct script access allowed');

/*
Module Name: OT-Main Customization
Description: Custom quotation, invoice, packing list, and purchase order for OT-Main
Version: 1.0.0
Requires at least: 3.0.*
*/

define('OTMAIN_MODULE_NAME', 'otmain');

$CI = &get_instance();
$CI->load->helper(OTMAIN_MODULE_NAME . '/otmain');

register_activation_hook(OTMAIN_MODULE_NAME, 'otmain_module_activation_hook');
register_language_files(OTMAIN_MODULE_NAME, [OTMAIN_MODULE_NAME]);

hooks()->add_action('admin_init', 'otmain_permissions');
hooks()->add_action('admin_init', 'otmain_init_menu');
hooks()->add_action('admin_init', 'otmain_ensure_schema');
hooks()->add_action('app_admin_head', 'otmain_admin_head_css');
hooks()->add_action('app_admin_footer', 'otmain_admin_footer_assets');
hooks()->add_action('otmain_invoice_form_fields', 'otmain_render_invoice_fields');
hooks()->add_action('otmain_estimate_form_fields', 'otmain_render_estimate_fields');
hooks()->add_action('otmain_proposal_form_fields', 'otmain_render_proposal_fields');

hooks()->add_filter('sales_number_format', 'otmain_sales_number_format', 10, 2);
hooks()->add_filter('format_estimate_number', 'otmain_format_estimate_number', 10, 2);
hooks()->add_filter('format_invoice_number', 'otmain_format_invoice_number', 10, 2);
hooks()->add_filter('proposal_number_format', 'otmain_format_proposal_number', 10, 2);

hooks()->add_filter('before_estimate_added', 'otmain_before_estimate_save');
hooks()->add_filter('before_estimate_updated', 'otmain_before_estimate_save');
hooks()->add_filter('before_invoice_added', 'otmain_before_invoice_save');
hooks()->add_filter('before_update_invoice', 'otmain_before_invoice_save');
hooks()->add_filter('before_create_proposal', 'otmain_before_proposal_save');
hooks()->add_filter('before_proposal_updated', 'otmain_before_proposal_update', 10, 2);
hooks()->add_filter('pdf_logo_url', 'otmain_filter_pdf_logo_url');
hooks()->add_filter('process_pdf_signature_on_close', 'otmain_disable_sales_pdf_signature');

// Allow free currency selection on sales documents (EUR/USD/IDR/etc from Settings → Currencies).
hooks()->add_filter('invoice_currency_attributes', 'otmain_enable_currency_select');
hooks()->add_filter('estimate_currency_attributes', 'otmain_enable_currency_select');
hooks()->add_filter('proposal_currency_attributes', 'otmain_enable_currency_select');
hooks()->add_filter('credit_note_currency_attributes', 'otmain_enable_currency_select');

// Free-form VAT % on sales line items (invoice / estimate / proposal / credit note)
// and OT-Main custom docs (packing list / purchase order via their own forms).
hooks()->add_filter('taxes_dropdown_template', 'otmain_taxes_dropdown_template', 10, 7);

/**
 * Replace tax dropdown with a numeric VAT % input for document line items.
 *
 * @param mixed  $custom
 * @param string $name
 * @param mixed  $taxname
 * @param string $type
 * @param mixed  $item_id
 * @param bool   $is_edit
 * @param bool   $manual
 * @return mixed
 */
function otmain_taxes_dropdown_template($custom, $name, $taxname, $type = '', $item_id = '', $is_edit = false, $manual = false)
{
    // Keep Setup → Finance → default tax as a normal multi-select.
    if (strpos((string) $name, '[taxname]') === false && (string) $name !== 'taxname') {
        return $custom;
    }

    return otmain_get_taxes_input_template($name, $taxname);
}

/**
 * Remove the core "disabled" lock so staff can pick any configured currency.
 *
 * @param array $attrs
 * @return array
 */
function otmain_enable_currency_select($attrs)
{
    if (!is_array($attrs)) {
        $attrs = [];
    }

    unset($attrs['disabled']);
    $attrs['data-show-subtext'] = true;

    return $attrs;
}

function otmain_disable_sales_pdf_signature($process)
{
    // Hide core "Authorized Signature" block on OT-Main sales PDFs
    // (custom document layouts do not use this default footer signature).
    if (
        isset($GLOBALS['invoice_pdf'])
        || isset($GLOBALS['proposal_pdf'])
        || isset($GLOBALS['estimate_pdf'])
        || isset($GLOBALS['credit_note_pdf'])
    ) {
        return false;
    }

    return $process;
}

hooks()->add_filter('module_' . OTMAIN_MODULE_NAME . '_action_links', 'otmain_module_action_links');
hooks()->add_filter('before_settings_updated', 'otmain_filter_settings_updated');

function otmain_module_action_links($actions)
{
    $actions[] = '<a href="' . admin_url('settings?group=otmain') . '">' . _l('settings') . '</a>';

    return $actions;
}

function otmain_filter_settings_updated($data)
{
    if (!isset($data['settings']) || !is_array($data['settings'])) {
        return $data;
    }

    foreach (['eur', 'usd'] as $currency) {
        $bank     = [];
        $hasField = false;

        foreach (otmain_bank_detail_fields() as $field) {
            $inputKey = 'otmain_bank_' . $currency . '_' . $field;
            if (!array_key_exists($inputKey, $data['settings'])) {
                continue;
            }

            $bank[$field] = $data['settings'][$inputKey];
            unset($data['settings'][$inputKey]);
            $hasField = true;
        }

        if ($hasField) {
            $data['settings']['otmain_bank_details_' . $currency] = json_encode($bank);
        }
    }

    return $data;
}

function otmain_module_activation_hook()
{
    $CI = &get_instance();
    require_once __DIR__ . '/install.php';
}

/**
 * Apply additive schema upgrades without requiring module re-activation.
 */
function otmain_ensure_schema()
{
    static $done = false;
    if ($done) {
        return;
    }
    $done = true;

    require_once __DIR__ . '/install.php';
}

function otmain_permissions()
{
    $capabilities = [
        'capabilities' => [
            'view'   => _l('permission_view') . '(' . _l('permission_global') . ')',
            'create' => _l('permission_create'),
            'edit'   => _l('permission_edit'),
            'delete' => _l('permission_delete'),
        ],
    ];

    register_staff_capabilities('otmain_packing_list', $capabilities, _l('otmain_packing_lists'));
    register_staff_capabilities('otmain_purchase_order', $capabilities, _l('otmain_purchase_orders'));
}

function otmain_init_menu()
{
    $CI = &get_instance();

    if (staff_can('view', 'otmain_packing_list')) {
        $CI->app_menu->add_sidebar_children_item('sales', [
            'slug'     => 'otmain-packing-list',
            'name'     => _l('otmain_packing_lists'),
            'href'     => admin_url('otmain/packing_list'),
            'position' => 26,
        ]);
    }

    if (staff_can('view', 'otmain_purchase_order')) {
        $CI->app_menu->add_sidebar_children_item('sales', [
            'slug'     => 'otmain-purchase-order',
            'name'     => _l('otmain_purchase_orders'),
            'href'     => admin_url('otmain/purchase_order'),
            'position' => 27,
        ]);
    }

    $CI->app->add_settings_section_child('finance', 'otmain', [
        'name'     => _l('otmain_settings'),
        'view'     => 'otmain/settings',
        'position' => 36,
        'icon'     => 'fa fa-university',
    ]);
}

function otmain_admin_footer_assets()
{
    $CI = &get_instance();
    $uri = $CI->uri->uri_string();

    if (
        strpos($uri, 'estimates/estimate') !== false
        || strpos($uri, 'invoices/invoice') !== false
        || strpos($uri, 'proposals/proposal') !== false
        || strpos($uri, 'credit_notes/credit_note') !== false
        || strpos($uri, 'otmain/packing_list') !== false
        || strpos($uri, 'otmain/purchase_order') !== false
    ) {
        echo '<link rel="stylesheet" href="' . module_dir_url(OTMAIN_MODULE_NAME, 'assets/css/otmain-forms.css') . '?v=1.0.2" />';
        echo '<script src="' . module_dir_url(OTMAIN_MODULE_NAME, 'assets/js/otmain.js') . '?v=1.1.1"></script>';
    }
}

/**
 * Always show action buttons (view/edit/delete) in listing tables
 * instead of hiding them until hover.
 */
function otmain_admin_head_css()
{
    echo '<style>
.table-proposals .row-options,
.table-invoices .row-options,
.table-otmain-packing-lists .row-options,
.table-otmain-purchase-orders .row-options {
    position: static !important;
    left: auto !important;
}
</style>';
}

function otmain_render_estimate_fields($estimate = null)
{
    $CI = &get_instance();
    $CI->load->view('otmain/estimate_fields', ['estimate' => $estimate]);
}

function otmain_render_invoice_fields($invoice = null)
{
    otmain_invoice_section('document', $invoice);
    otmain_invoice_section('contact', $invoice);
    otmain_invoice_section('terms', $invoice);
    otmain_invoice_section('addresses', $invoice);
    otmain_invoice_section('extras', $invoice);
    otmain_invoice_section('notes', $invoice);
}

function otmain_render_proposal_fields($proposal = null)
{
    otmain_proposal_section('document', $proposal);
    otmain_proposal_section('contact', $proposal);
    otmain_proposal_section('terms', $proposal);
    otmain_proposal_section('extras', $proposal);
    otmain_proposal_section('notes', $proposal);
}

/**
 * Render a single OT-Main proposal field section into the edit form.
 *
 * @param string     $section  document|contact|terms|extras|notes
 * @param object|null $proposal
 */
function otmain_proposal_section($section, $proposal = null)
{
    $CI = &get_instance();
    $CI->load->view('otmain/proposal_fields', [
        'proposal' => $proposal,
        'section'  => $section,
    ]);
}

/**
 * Render a single OT-Main invoice field section into the edit form.
 *
 * @param string      $section document|contact|terms|addresses|extras|notes
 * @param object|null $invoice
 */
function otmain_invoice_section($section, $invoice = null)
{
    $CI = &get_instance();
    $CI->load->view('otmain/invoice_fields', [
        'invoice' => $invoice,
        'section' => $section,
    ]);
}

function otmain_before_estimate_save($hookData)
{
    $data = $hookData['data'];

    if (isset($data['expiry_days']) && is_numeric($data['expiry_days']) && !empty($data['date'])) {
        $date = to_sql_date($data['date']);
        if ($date) {
            $data['expirydate'] = date('Y-m-d', strtotime('+' . (int) $data['expiry_days'] . ' days', strtotime($date)));
        }
    }

    foreach (['shipment_terms', 'delivery_time', 'availability', 'payment_terms_text'] as $field) {
        if (isset($data[$field])) {
            $data[$field] = nl2br_save_html($data[$field]);
        }
    }

    if (empty($data['terms'])) {
        $data['terms'] = nl2br_save_html(otmain_get_quotation_terms());
    }

    if (empty($data['otmain_contact_id'])) {
        $data['otmain_contact_id'] = null;
    }

    $hookData['data'] = $data;

    return $hookData;
}

function otmain_before_invoice_save($hookData)
{
    $data = $hookData['data'];

    if (isset($data['expiry_days']) && is_numeric($data['expiry_days']) && !empty($data['date'])) {
        $date = to_sql_date($data['date']);
        if ($date) {
            $data['duedate'] = date('Y-m-d', strtotime('+' . (int) $data['expiry_days'] . ' days', strtotime($date)));
        }
    }

    if (isset($data['quote_ref']) && $data['quote_ref'] === '') {
        $data['quote_ref'] = null;
    }

    if (!empty($data['invoice_title'])) {
        $data['invoice_title'] = trim($data['invoice_title']);
    }

    foreach (['payment_terms_text', 'delivery_terms', 'lead_time', 'delivery_address', 'availability', 'notes'] as $field) {
        if (isset($data[$field])) {
            $data[$field] = nl2br_save_html($data[$field]);
        }
    }

    if (empty($data['document_title'])) {
        $data['document_title'] = 'Commercial Invoice';
    }

    if (empty($data['terms'])) {
        $data['terms'] = nl2br_save_html(otmain_get_invoice_terms());
    }

    // Serialize packing items to JSON
    if (isset($data['packing_items']) && is_array($data['packing_items'])) {
        $totalGw  = 0;
        $totalNw  = 0;
        $totalCbm = 0;
        $packing  = [];

        foreach ($data['packing_items'] as $i => $pItem) {
            $qty  = (float) ($pItem['qty'] ?? 1);
            $gw   = (float) ($pItem['gw'] ?? 0);
            $nw   = (float) ($pItem['nw'] ?? 0);
            $dims = trim($pItem['dimensions'] ?? '');

            // Calculate CBM from dimensions (format: LxWxH or L x W x H)
            $cbm = 0;
            if (preg_match('/([\d.]+)\s*[xX*]\s*([\d.]+)\s*[xX*]\s*([\d.]+)/', $dims, $m)) {
                $cbm = ((float) $m[1] * (float) $m[2] * (float) $m[3]) / 1000000; // cm to CBM
            } elseif (!empty($pItem['cbm'])) {
                $cbm = (float) $pItem['cbm'];
            }

            $totalGw  += $gw;
            $totalNw  += $nw;
            $totalCbm += $cbm;

            $packing[] = [
                'qty'        => $qty,
                'dimensions' => $dims,
                'gw'         => $gw,
                'nw'         => $nw,
                'cbm'        => $cbm,
            ];
        }

        $data['packing_items'] = json_encode($packing);
        $data['total_gw']      = $totalGw;
        $data['total_nw']      = $totalNw;
        $data['total_cbm']     = $totalCbm;
    } elseif (isset($data['packing_items'])) {
        unset($data['packing_items']);
    }

    $hookData['data'] = $data;

    return $hookData;
}

function otmain_filter_pdf_logo_url($logoImage)
{
    $path = otmain_get_pdf_logo_path();
    if ($path !== '') {
        $width = get_option('pdf_logo_width');
        if ($width == '') {
            $width = 120;
        }

        return '<img width="' . $width . 'px" src="' . $path . '">';
    }

    if (!empty($logoImage)) {
        return $logoImage;
    }

    return '';
}

function otmain_format_proposal_number($format, $id)
{
    $CI = &get_instance();
    $CI->db->select('date, quote_title')->where('id', $id);
    $row = $CI->db->get(db_prefix() . 'proposals')->row();

    $year = $row && !empty($row->date) ? date('Y', strtotime($row->date)) : date('Y');
    $prefix = trim((string) (get_option('proposal_number_prefix') ?: 'OTPSQ'));
    $title = $row && !empty($row->quote_title) ? (' - ' . trim((string) $row->quote_title)) : '';

    // Example target: "20 - 2026 - OTPSQ - 120 - Kovako M120"
    $offerCount = (int) $id;
    $counter1xx = (int) $id + 100;

    return $offerCount . ' - ' . $year . ' - ' . $prefix . ' - ' . $counter1xx . $title;
}

function otmain_before_proposal_save($hookData)
{
    $data = $hookData['data'];

    if (isset($data['expiry_days']) && is_numeric($data['expiry_days']) && !empty($data['date'])) {
        $date = to_sql_date($data['date']);
        if ($date) {
            $data['open_till'] = date('Y-m-d', strtotime('+' . (int) $data['expiry_days'] . ' days', strtotime($date)));
        }
    }

    foreach (['shipment_terms', 'delivery_time', 'availability', 'notes', 'payment_terms_text'] as $field) {
        if (isset($data[$field])) {
            $data[$field] = nl2br_save_html($data[$field]);
        }
    }

    if (empty($data['terms'])) {
        $data['terms'] = nl2br_save_html(otmain_get_quotation_terms());
    }

    if (empty($data['document_title'])) {
        $data['document_title'] = 'Quotation';
    }

    $hookData['data'] = $data;

    return $hookData;
}

function otmain_before_proposal_update($hookData, $id)
{
    // Same normalization as create, keep array shape for Perfex core.
    $result = otmain_before_proposal_save([
        'data'  => $hookData['data'],
        'items' => $hookData['items'] ?? [],
    ]);

    $hookData['data'] = $result['data'];

    return $hookData;
}
