<?php

defined('BASEPATH') or exit('No direct script access allowed');

$aColumns = [
    'formatted_number',
    'date',
    'total',
    'supplier_quote_ref',
];

$sIndexColumn = 'id';
$sTable       = db_prefix() . 'otmain_purchase_orders';

$result = data_tables_init($aColumns, $sIndexColumn, $sTable, [], [], [
    db_prefix() . 'otmain_purchase_orders.id as id',
    db_prefix() . 'otmain_purchase_orders.supplierid as supplierid',
    db_prefix() . 'otmain_purchase_orders.currency as currency',
]);

$output  = $result['output'];
$rResult = $result['rResult'];

foreach ($rResult as $aRow) {
    $row = [];

    $link   = admin_url('otmain/purchase_order/purchase_order/' . $aRow['id']);
    $number = $aRow['formatted_number'] ?: ('PO-' . $aRow['id']);

    $numberOutput = '<a href="' . $link . '" class="tw-font-medium">' . e($number) . '</a>';
    $numberOutput .= '<div class="row-options">';
    $numberOutput .= '<a href="' . $link . '">' . _l('view') . '</a>';
    if (staff_can('edit', 'otmain_purchase_order')) {
        $numberOutput .= ' | <a href="' . $link . '#tab_edit">' . _l('edit') . '</a>';
    }
    if (staff_can('delete', 'otmain_purchase_order')) {
        $numberOutput .= ' | <a href="' . admin_url('otmain/purchase_order/delete/' . $aRow['id']) . '" class="text-danger _delete">' . _l('delete') . '</a>';
    }
    $numberOutput .= '</div>';
    $row[] = $numberOutput;

    $row[] = e(_d($aRow['date']));
    $row[] = e(get_company_name($aRow['supplierid']));
    $row[] = e(app_format_money($aRow['total'], get_currency($aRow['currency'])));
    $row[] = e($aRow['supplier_quote_ref']);

    $row['DT_RowClass'] = 'has-row-options';
    $output['aaData'][] = $row;
}
