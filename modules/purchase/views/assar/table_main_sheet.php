<?php
defined('BASEPATH') or exit('No direct script access allowed');

$month = $this->ci->input->post('month');
// Get previous month for rollover lookup
$date = DateTime::createFromFormat('Y-m', $month);
$date->modify('-1 month');
$prev_month = $date->format('Y-m');

$aColumns = [
    'tblassar_clients.client_id',
    'tblassar_clients.name',
    'nrr.net_rollver_amount as net_rollver_amount',
    'm.assar_holds',
    'm.client_earnings'
];

$sIndexColumn = 'tblassar_clients.id';
$sTable = db_prefix() . 'assar_clients';

$join = [
    'LEFT JOIN tbl_assar_main_sheet m
     ON m.client_id = tblassar_clients.id
     AND m.month_year = "' . $month . '"',
    'LEFT JOIN tblassar_net_rollver nrr
     ON nrr.client_id = tblassar_clients.id
     AND nrr.month = "' . $prev_month . '"',
     'LEFT JOIN tblassar_monthly_increase mi
     ON mi.client_id = tblassar_clients.id
     AND mi.month = "' . $month . '"'
];

$where = [];
// Get last date of the month for WHERE condition
$date = DateTime::createFromFormat('Y-m', $month);
$lastDayOfMonth = $date->format('Y-m-t'); // 't' gives last day of month

array_push($where, ' AND tblassar_clients.start_date <= "' . $lastDayOfMonth . '"');
$having = '';

$result = data_tables_init(
    $aColumns,
    $sIndexColumn,
    $sTable,
    $join,
    $where,
    ['tblassar_clients.id','mi.increase_desc_amount as increase_desc_amount'],
    '',
    [],
    $having
);

$output  = $result['output'];
$rResult = $result['rResult'];
$footer_data = [
    'investment' => 0,
    'client_earnings_forecast' => 0
];

$aColumns = array_map(function ($col) {
    if (stripos($col, ' as ') !== false) {
        $parts = preg_split('/\s+as\s+/i', $col);
        return trim($parts[1], '"` ');
    }
    return trim($col, '"` ');
}, $aColumns);
$client_count = 0;
$client_count = count($rResult);
foreach ($rResult as $aRow) {

    $row = [];

    foreach ($aColumns as $col) {

        $_data = $aRow[$col] ?? '';

        if ($col == 'client_id') {

            $_data = $aRow['client_id'];
        } elseif ($col == 'name') {

            $_data = $aRow['name'];
        } elseif ($col == 'net_rollver_amount') {
            $final_rollver_amount = $aRow['net_rollver_amount'] + ($aRow['increase_desc_amount'] ?? 0);
            $_data = app_format_money($final_rollver_amount, '₹');
        } elseif ($col == 'm.assar_holds') {

            $_data = '<input type="number"
                class="form-control assar-input"
                data-client="' . $aRow['id'] . '"
                data-first="1"
                value="' . ($aRow['assar_holds'] ?? 0) . '">';
        } elseif ($col == 'm.client_earnings') {

            $_data = '<span class="earning-text">'
                . app_format_money($aRow['client_earnings'] ?? 0, '₹') .
                '</span>';
        }


        $row[] = $_data;
    }
    $footer_data['investment'] += $aRow['net_rollver_amount'];
    $footer_data['client_earnings_forecast'] += $aRow['client_earnings'];
    $output['aaData'][] = $row;
}
foreach ($footer_data as $key => $total) {
    $footer_data[$key] = app_format_money($total, '₹');
}
$footer_data['client_count'] = $client_count;
$output['sums'] = $footer_data;
