<?php
$CI = &get_instance();
$CI->load->model('purchase_model');
$base_currency = get_base_currency_pur();
$draw = intval($CI->input->post('draw'));
$start = intval($CI->input->post('start'));
$length = intval($CI->input->post('length'));
$search_value = $CI->input->post('search')['value'] ?? '';
$order = $CI->input->post('order');
$compounding_tracker = $CI->purchase_model->get_compounding_tracker_data();
$cycle_summary = [];
if (!empty($compounding_tracker)) {
    $grouped = [];
    foreach ($compounding_tracker as $item) {
        $grouped[$item['cycle']][] = $item;
    }
    foreach ($grouped as $cycle => $rows) {
        $start_day = $rows[0]['day'] ?? 0;
        $end_day = end($rows)['day'] ?? 0;
        $start_bal = $rows[0]['actual_opening'] ?? 0;
        $end_bal = end($rows)['actual_closing'] ?? 0;
        $cycle_wd_amount = end($rows)['wd_amount'] ?? 0;
        $cycle_summary[] = [
            'cycle' => $cycle,
            'start_day' => $start_day,
            'end_day' => $end_day,
            'start_bal' => $start_bal,
            'end_bal' => $end_bal,
            'cycle_wd_amount' => $cycle_wd_amount,
        ];
    }
}
$total_records = count($cycle_summary);
if ($search_value != '') {
    $cycle_summary = array_filter($cycle_summary, function ($row) use ($search_value) {
        return (
            stripos($row['cycle'], $search_value) !== false ||
            stripos($row['start_day'], $search_value) !== false
        );
    });
}
$filtered_records = count($cycle_summary);
if (!empty($order)) {
    $col_index = $order[0]['column'];
    $col_dir = $order[0]['dir'];
    $column_map = [
        0 => 'cycle',
        1 => 'start_day',
        2 => 'end_day',
        3 => 'start_bal',
        4 => 'end_bal',
        5 => 'cycle_wd_amount',
    ];
    if (isset($column_map[$col_index])) {
        $field = $column_map[$col_index];
        usort($cycle_summary, function ($a, $b) use ($field, $col_dir) {
            if ($col_dir == 'asc') {
                return $a[$field] <=> $b[$field];
            } else {
                return $b[$field] <=> $a[$field];
            }
        });
    }
}
$paged_data = array_slice($cycle_summary, $start, $length);
$aaData = [];
foreach ($paged_data as $row) {
    $aaData[] = [
        $row['cycle'],
        $row['start_day'],
        $row['end_day'],
        app_format_money($row['start_bal'], $base_currency->symbol),
        app_format_money($row['end_bal'], $base_currency->symbol),
        app_format_money($row['cycle_wd_amount'], $base_currency->symbol),
    ];
}
$output = [
    "draw" => $draw,
    "iTotalRecords" => $total_records,
    "iTotalDisplayRecords" => $filtered_records,
    "aaData" => $aaData
];
?>