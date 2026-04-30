<?php
$CI = &get_instance();
$base_currency = get_base_currency_pur();
$CI->load->model('purchase_model');
$data = $CI->purchase_model->get_compounding_tracker_data();
$output  = [];
$rResult = [];
foreach ($data as $row) {
    $r = [];
    $r[] = $row['day'];
    $r[] = $row['date'];
    $r[] = $row['cycle'];
    $r[] = $row['day_in_cycle'];
    $r[] = app_format_money($row['plan_opening'], $base_currency->symbol);
    $r[] = app_format_money($row['plan_closing'], $base_currency->symbol);
    $r[] = app_format_money($row['actual_opening'], $base_currency->symbol);
    $r[] = app_format_money($row['actual_pnl'], $base_currency->symbol);
    $r[] = $row['actual_closing_html'];
    $r[] = $row['vs_plan'].'%';
    $r[] = app_format_money($row['fixed_margin'], $base_currency->symbol);
    $r[] = app_format_money($row['wd_target'], $base_currency->symbol);
    $r[] = app_format_money($row['wd_amount'], $base_currency->symbol);
    $r[] = $row['notes_html'];
    $r[] = $row['daily_return_percent'].'%';
    $r[] = app_format_money($row['cumulative_pnl'], $base_currency->symbol);
    $r[] = $row['cum_return_percent'].'%';
    $output['aaData'][] = $r;
}
?>