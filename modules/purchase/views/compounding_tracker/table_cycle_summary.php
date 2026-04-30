<?php
$CI = &get_instance();
$base_currency = get_base_currency_pur();
$CI->load->model('purchase_model');
$compounding_tracker = $CI->purchase_model->get_compounding_tracker_data();
$cycle_summary = [];
if(!empty($compounding_tracker)) {
    $total_cycles = array_values(
        array_map(
            function ($group) {
                return ['cycle' => $group[0]['cycle']];
            },
            array_reduce($compounding_tracker, function ($carry, $item) {
                $carry[$item['cycle']][] = $item;
                return $carry;
            }, [])
        )
    );
    foreach ($total_cycles as $key => $value) {
        $current_cycle = $value['cycle'];
        $current_cycle_data = array_values(
            array_filter($compounding_tracker, function ($item) use ($current_cycle) {
                return $item['cycle'] == $current_cycle;
            })
        );
        $start_day = $current_cycle_data[0]['day'] ?? 0;
        $end_day = 0;
        $start_bal = $current_cycle_data[0]['actual_opening'] ?? 0;
        $end_bal = end($current_cycle_data)['actual_closing'] ?? 0;
        $cycle_wd_amount = end($current_cycle_data)['wd_amount'] ?? 0;

        $cycle_summary[] = [
            'cycle' => $current_cycle,
            'start_day' => $start_day,
            'end_day' => $end_day,
            'start_bal' => $start_bal,
            'end_bal' => $end_bal,
            'cycle_wd_amount' => $cycle_wd_amount,
        ];
    }
}
$output  = [];
$rResult = [];
foreach ($cycle_summary as $row) {
    $r = [];
    $r[] = $row['cycle'];
    $r[] = $row['start_day'];
    $r[] = $row['end_day'];
    $r[] = app_format_money($row['start_bal'], $base_currency->symbol);
    $r[] = app_format_money($row['end_bal'], $base_currency->symbol);
    $r[] = app_format_money($row['cycle_wd_amount'], $base_currency->symbol);
    $output['aaData'][] = $r;
}
?>