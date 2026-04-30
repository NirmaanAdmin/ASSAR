<?php
$CI = &get_instance();
$CI->load->model('purchase_model');
$base_currency = get_base_currency_pur();
$draw = intval($CI->input->post('draw'));
$start = intval($CI->input->post('start'));
$length = intval($CI->input->post('length'));
$search_value = $CI->input->post('search')['value'] ?? '';
$order = $CI->input->post('order');
$data = $CI->purchase_model->get_compounding_tracker_data();
$total_records = count($data);
if ($search_value != '') {
    $data = array_filter($data, function ($row) use ($search_value) {
        return (
            stripos((string)$row['day'], $search_value) !== false ||
            stripos((string)$row['cycle'], $search_value) !== false
        );
    });
}
$filtered_records = count($data);
if (!empty($order)) {
    $col_index = $order[0]['column'];
    $col_dir = $order[0]['dir'];
    $column_map = [
        0 => 'day',
        1 => 'cycle',
        2 => 'day_in_cycle',
        3 => 'plan_opening',
        4 => 'plan_closing',
        5 => 'actual_opening',
        6 => 'actual_pnl',
        7 => 'actual_closing',
        8 => 'vs_plan',
        9 => 'fixed_margin',
        10 => 'daily_return_percent',
        11 => 'cumulative_pnl',
        12 => 'cum_return_percent',
        13 => 'wd_target',
        14 => 'wd_amount',
        15 => 'notes',
    ];
    if (isset($column_map[$col_index])) {
        $field = $column_map[$col_index];
        usort($data, function ($a, $b) use ($field, $col_dir) {
            if ($col_dir == 'asc') {
                return $a[$field] <=> $b[$field];
            } else {
                return $b[$field] <=> $a[$field];
            }
        });
    }
}
$paged_data = array_slice($data, $start, $length);
$aaData = [];
foreach ($paged_data as $index => $row) {
    $aaData[] = [
        $row['day'],
        $row['cycle'],
        $row['day_in_cycle'],
        app_format_money($row['plan_opening'], $base_currency->symbol),
        app_format_money($row['plan_closing'], $base_currency->symbol),
        app_format_money($row['actual_opening'], $base_currency->symbol),
        app_format_money($row['actual_pnl'], $base_currency->symbol),
        $row['actual_closing_html'],
        $row['vs_plan'].'%',
        app_format_money($row['fixed_margin'], $base_currency->symbol),
        $row['daily_return_percent'].'%',
        app_format_money($row['cumulative_pnl'], $base_currency->symbol),
        $row['cum_return_percent'].'%',
        app_format_money($row['wd_target'], $base_currency->symbol),
        app_format_money($row['wd_amount'], $base_currency->symbol),
        $row['notes_html'],
    ];
}
$output = [
    "draw" => $draw,
    "iTotalRecords" => $total_records,
    "iTotalDisplayRecords" => $filtered_records,
    "aaData" => $aaData
];
?>