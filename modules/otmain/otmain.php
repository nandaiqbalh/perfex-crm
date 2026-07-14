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

// Item Tracker hooks
hooks()->add_action('proposal_accepted', 'otmain_item_tracker_on_accepted');
hooks()->add_action('after_proposal_staff_status_changed', 'otmain_item_tracker_on_staff_status');
hooks()->add_action('after_proposal_converted_to_invoice', 'otmain_item_tracker_link_invoice');
hooks()->add_action('clients_init', 'otmain_item_tracker_client_menu');

hooks()->add_filter('sales_number_format', 'otmain_sales_number_format', 10, 2);
hooks()->add_filter('format_estimate_number', 'otmain_format_estimate_number', 10, 2);
hooks()->add_filter('format_invoice_number', 'otmain_format_invoice_number', 10, 2);
hooks()->add_filter('proposal_number_format', 'otmain_format_proposal_number', 10, 2);
hooks()->add_filter('datatables_query_order_column', 'otmain_proposals_datatables_order_column', 10, 2);

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

    $CI = &get_instance();
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

    $trackerCapabilities = [
        'capabilities' => [
            'view'   => _l('permission_view') . '(' . _l('permission_global') . ')',
            'edit'   => _l('permission_edit'),
            'delete' => _l('permission_delete'),
        ],
    ];
    register_staff_capabilities('otmain_item_tracker', $trackerCapabilities, _l('otmain_item_tracker'));
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

    if (staff_can('view', 'otmain_item_tracker')) {
        $CI->app_menu->add_sidebar_children_item('sales', [
            'slug'     => 'otmain-item-tracker',
            'name'     => _l('otmain_item_tracker'),
            'href'     => admin_url('otmain/item_tracker'),
            'position' => 28,
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
        || strpos($uri, 'otmain/item_tracker') !== false
    ) {
        echo '<link rel="stylesheet" href="' . module_dir_url(OTMAIN_MODULE_NAME, 'assets/css/otmain-forms.css') . '?v=1.0.2" />';
        echo '<script src="' . module_dir_url(OTMAIN_MODULE_NAME, 'assets/js/otmain.js') . '?v=1.3.0"></script>';
    }

    if (strpos($uri, 'otmain/item_tracker') !== false) {
        echo '<style>
.item-status-pending,.quote-status-pending{background:#fbbf24;color:#000;}
.item-status-ordered,.quote-status-in_progress{background:#3b82f6;color:#fff;}
.item-status-eta{background:#a855f7;color:#fff;}
.item-status-quality_check{background:#9ca3af;color:#fff;}
.item-status-received,.quote-status-ready_for_shipment{background:#22c55e;color:#fff;}
.quote-status-shipped{background:#6b7280;color:#fff;}
.otmain-status-badge{display:inline-block;padding:3px 10px;border-radius:12px;font-size:12px;font-weight:600;line-height:1.4;}
.table-otmain-item-trackers .row-options{position:static!important;left:auto!important;}
</style>';
    }

    // Keep customer default currency freely editable (any currency from Settings → Currencies).
    if (strpos($uri, 'clients/client') !== false) {
        echo '<script>
(function($){
  $(function(){
    var $currency = $(\'select[name="default_currency"]\');
    if (!$currency.length) { return; }
    $currency.prop("disabled", false);
    if ($currency.hasClass("selectpicker") || $currency.parent().hasClass("bootstrap-select")) {
      $currency.selectpicker("refresh");
    }
  });
})(jQuery);
</script>';
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
.table-otmain-purchase-orders .row-options,
.table-otmain-item-trackers .row-options {
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

    // Preview-row fields must not be written to tblestimates.
    foreach (['profit_percent', 'purchase_amount', 'quantity', 'rate', 'unit', 'description', 'long_description', 'taxname', 'isedit', 'taskid', 'expense_id', 'save_and_send'] as $previewField) {
        if (array_key_exists($previewField, $data)) {
            unset($data[$previewField]);
        }
    }

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

    $data = otmain_normalize_conversion_fields($data);

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

    if (array_key_exists('conversion_rate', $data) || array_key_exists('conversion_currency', $data)) {
        $data = otmain_normalize_conversion_fields($data);
    }

    // Strip accidental POST keys from Perfex render_select id-as-name quirk.
    foreach (array_keys($data) as $key) {
        if (is_string($key) && strpos($key, 'otmain-') === 0) {
            unset($data[$key]);
        }
    }

    // Packing details belong on Packing List — ignore if posted from invoice form.
    if (isset($data['packing_items'])) {
        unset($data['packing_items']);
    }

    if (array_key_exists('bank_account', $data)) {
        $bank = strtoupper(trim((string) $data['bank_account']));
        $data['bank_account'] = ($bank === 'USD' || $bank === 'EUR') ? $bank : null;
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
    $select = 'date, quote_title';
    if ($CI->db->field_exists('source_quote_number', db_prefix() . 'proposals')) {
        $select .= ', source_quote_number';
    }
    $CI->db->select($select)->where('id', $id);
    $row = $CI->db->get(db_prefix() . 'proposals')->row();

    // Prefer original PDF / seed number when present (e.g. "3 - 2026 - OTMSQ - 103 - Suction Hose").
    if ($row && !empty($row->source_quote_number)) {
        return trim((string) $row->source_quote_number);
    }

    $year = $row && !empty($row->date) ? date('Y', strtotime($row->date)) : date('Y');
    $prefix = trim((string) (get_option('proposal_number_prefix') ?: 'OTMSQ'));
    $title = $row && !empty($row->quote_title) ? (' - ' . trim((string) $row->quote_title)) : '';

    // Fallback for manually created proposals (no source number):
    // Example: "20 - 2026 - OTPSQ - 120 - Kovako M120"
    $offerCount = (int) $id;
    $counter1xx = (int) $id + 100;

    return $offerCount . ' - ' . $year . ' - ' . $prefix . ' - ' . $counter1xx . $title;
}

/**
 * Sort Proposal # by PDF number (year DESC, sequence ASC), not DB id.
 * Example: 1…21 for 2026 first, then any 2025 rows below.
 *
 * DataTables appends ASC/DESC after this expression, so year stays fixed DESC
 * and the direction applies to the sequence number.
 *
 * @param string $columnName
 * @param string $sTable
 * @return string
 */
function otmain_proposals_datatables_order_column($columnName, $sTable)
{
    $proposalsTable = db_prefix() . 'proposals';
    if ($sTable !== $proposalsTable) {
        return $columnName;
    }

    $idColumns = [$proposalsTable . '.id', 'id'];
    if (!in_array($columnName, $idColumns, true)) {
        return $columnName;
    }

    $CI = &get_instance();
    if (!$CI->db->field_exists('source_quote_number', $proposalsTable)) {
        return $columnName;
    }

    $src = $proposalsTable . '.source_quote_number';
    // "3 - 2026 - OTMSQ - 103 - Title" → seq=3, year=2026
    $yearExpr = 'COALESCE('
        . 'NULLIF(CAST(SUBSTRING_INDEX(SUBSTRING_INDEX(TRIM(' . $src . '), \' - \', 2), \' - \', -1) AS UNSIGNED), 0),'
        . 'YEAR(' . $proposalsTable . '.date),'
        . 'YEAR(' . $proposalsTable . '.datecreated)'
        . ')';
    $seqExpr = 'COALESCE('
        . 'NULLIF(CAST(SUBSTRING_INDEX(TRIM(' . $src . '), \' - \', 1) AS UNSIGNED), 0),'
        . $proposalsTable . '.id'
        . ')';

    return $yearExpr . ' DESC, ' . $seqExpr;
}

function otmain_before_proposal_save($hookData)
{
    $data = $hookData['data'];

    // Preview-row fields must not be written to tblproposals.
    foreach (['profit_percent', 'purchase_amount', 'quantity', 'rate', 'unit', 'description', 'long_description', 'taxname', 'isedit', 'taskid', 'expense_id', 'save_and_send'] as $previewField) {
        if (array_key_exists($previewField, $data)) {
            unset($data[$previewField]);
        }
    }

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

    $data = otmain_normalize_conversion_fields($data);

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

/**
 * Populate item tracker when customer accepts a proposal.
 *
 * @param int $proposal_id
 */
function otmain_item_tracker_on_accepted($proposal_id)
{
    $CI = &get_instance();
    $CI->load->model('otmain/item_tracker_model');
    $CI->item_tracker_model->populate_from_proposal((int) $proposal_id);
}

/**
 * Populate item tracker when staff marks proposal as Accepted (status 3).
 * Note: proposal_accepted only fires on client accept path.
 *
 * @param array $data
 */
function otmain_item_tracker_on_staff_status($data)
{
    if ((int) ($data['new_status'] ?? 0) !== 3) {
        return;
    }

    otmain_item_tracker_on_accepted((int) ($data['proposal_id'] ?? 0));
}

/**
 * Link invoice_id on tracker rows after proposal → invoice conversion.
 *
 * @param array $data
 */
function otmain_item_tracker_link_invoice($data)
{
    $proposal_id = (int) ($data['proposal_id'] ?? 0);
    $invoice_id  = (int) ($data['invoice_id'] ?? 0);
    if ($proposal_id < 1 || $invoice_id < 1) {
        return;
    }

    $CI = &get_instance();
    $CI->load->model('otmain/item_tracker_model');
    $CI->item_tracker_model->link_invoice($proposal_id, $invoice_id);
}

/**
 * Add Item Tracker menu item in client area.
 */
function otmain_item_tracker_client_menu()
{
    if (!is_client_logged_in()) {
        return;
    }

    add_theme_menu_item('item-tracker', [
        'name'     => _l('otmain_item_tracker'),
        'href'     => site_url('otmain/item_tracker_client'),
        'position' => 32,
    ]);
}
