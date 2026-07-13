<?php

defined('BASEPATH') or exit('No direct script access allowed');

$aColumns = [
    'formatted_number',
    'date',
    'vessel',
    'quote_ref',
];

$sIndexColumn = 'id';
$sTable       = db_prefix() . 'otmain_packing_lists';

$result = data_tables_init($aColumns, $sIndexColumn, $sTable, [], [], [
    db_prefix() . 'otmain_packing_lists.id as id',
    db_prefix() . 'otmain_packing_lists.clientid as clientid',
]);

$output  = $result['output'];
$rResult = $result['rResult'];

foreach ($rResult as $aRow) {
    $row = [];

    $link   = admin_url('otmain/packing_list/packing_list/' . $aRow['id']);
    $number = $aRow['formatted_number'] ?: otmain_format_packing_list_number($aRow['id']);

    $numberOutput = '<a href="' . $link . '" class="tw-font-medium">' . e($number) . '</a>';
    $numberOutput .= '<div class="row-options">';
    $numberOutput .= '<a href="' . $link . '">' . _l('view') . '</a>';
    if (staff_can('edit', 'otmain_packing_list')) {
        $numberOutput .= ' | <a href="' . $link . '#tab_edit">' . _l('edit') . '</a>';
    }
    if (staff_can('delete', 'otmain_packing_list')) {
        $numberOutput .= ' | <a href="' . admin_url('otmain/packing_list/delete/' . $aRow['id']) . '" class="text-danger _delete">' . _l('delete') . '</a>';
    }
    $numberOutput .= '</div>';
    $row[] = $numberOutput;

    $row[] = e(_d($aRow['date']));
    $row[] = e(get_company_name($aRow['clientid']));
    $row[] = e($aRow['vessel']);
    $row[] = e(strip_tags($aRow['quote_ref']));

    $row['DT_RowClass'] = 'has-row-options';
    $output['aaData'][] = $row;
}
