<?php

defined('BASEPATH') or exit('No direct script access allowed');

$CI = &get_instance();
$CI->load->helper('otmain/otmain');

$p = db_prefix() . 'proposals';
$t = db_prefix() . 'otmain_item_tracker';

// Use "as alias" so result keys are predictable (tblproposals.id alone becomes key "tblproposals.id").
$aColumns = [
    $p . '.id as id',
    'CASE WHEN ' . $p . '.rel_type = "customer" THEN (SELECT company FROM ' . db_prefix() . 'clients WHERE userid = ' . $p . '.rel_id) ELSE (SELECT name FROM ' . db_prefix() . 'leads WHERE id = ' . $p . '.rel_id) END as client_name',
    $p . '.date as date',
    $p . '.quotation_status as quotation_status',
    '(SELECT COUNT(*) FROM ' . $t . ' ti WHERE ti.rel_type = \'proposal\' AND ti.rel_id = ' . $p . '.id AND ti.deleted_at IS NULL) as item_total',
    $p . '.invoice_id as invoice_id',
];

$sIndexColumn = 'id';
$sTable       = $p;

$join = [];

$where = [
    'AND ' . $p . '.status = 3',
    'AND EXISTS (SELECT 1 FROM ' . $t . ' tx WHERE tx.rel_type = \'proposal\' AND tx.rel_id = ' . $p . '.id)',
];

$statusFilter = $CI->input->post('quotation_status');
if ($statusFilter !== null && $statusFilter !== '') {
    $where[] = 'AND ' . $p . '.quotation_status = "' . $CI->db->escape_str($statusFilter) . '"';
}

$additionalSelect = [
    $p . '.rel_type as rel_type',
    $p . '.rel_id as rel_id',
    $p . '.subject as subject',
    '(SELECT COUNT(*) FROM ' . $t . ' ti WHERE ti.rel_type = \'proposal\' AND ti.rel_id = ' . $p . '.id AND ti.deleted_at IS NULL AND ti.item_status = \'received\') as item_received',
    '(SELECT ti.invoice_id FROM ' . $t . ' ti WHERE ti.rel_type = \'proposal\' AND ti.rel_id = ' . $p . '.id AND ti.invoice_id IS NOT NULL AND ti.deleted_at IS NULL LIMIT 1) as tracker_invoice_id',
];

$result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, $additionalSelect);

$output  = $result['output'];
$rResult = $result['rResult'];

foreach ($rResult as $aRow) {
    $row = [];

    // Support both aliased "id" and Perfex wildcard key "tblproposals.id"
    $proposalId = (int) ($aRow['id'] ?? $aRow[$p . '.id'] ?? 0);
    $link       = admin_url('otmain/item_tracker/detail/' . $proposalId);
    $number     = $proposalId > 0 ? format_proposal_number($proposalId) : '-';

    $numberOutput = '<a href="' . $link . '" class="tw-font-medium">' . e($number) . '</a>';
    if (!empty($aRow['subject'])) {
        $numberOutput .= '<br><span class="text-muted">' . e($aRow['subject']) . '</span>';
    }
    $numberOutput .= '<div class="row-options">';
    $numberOutput .= '<a href="' . $link . '">' . _l('otmain_view_details') . '</a>';
    $numberOutput .= '</div>';
    $row[] = $numberOutput;

    $clientName = $aRow['client_name'] ?? '';
    if (($aRow['rel_type'] ?? '') === 'lead' && $clientName !== '') {
        $clientName .= ' (Lead)';
    }
    $row[] = e($clientName);

    $row[] = e(_d($aRow['date'] ?? ''));

    $row[] = otmain_format_quotation_status($aRow['quotation_status'] ?? 'pending');

    $total    = (int) ($aRow['item_total'] ?? 0);
    $received = (int) ($aRow['item_received'] ?? 0);
    $row[]    = e($received . '/' . $total . ' ' . _l('otmain_status_received'));

    $invoiceId = !empty($aRow['tracker_invoice_id'])
        ? (int) $aRow['tracker_invoice_id']
        : (!empty($aRow['invoice_id']) ? (int) $aRow['invoice_id'] : 0);

    if ($invoiceId > 0) {
        $row[] = '<a href="' . admin_url('invoices/list_invoices/' . $invoiceId) . '">' . e(format_invoice_number($invoiceId)) . '</a>';
    } else {
        $row[] = '-';
    }

    $row['DT_RowClass'] = 'has-row-options';
    $output['aaData'][] = $row;
}
