<?php

defined('BASEPATH') or exit('No direct script access allowed');

$this->ci->load->model('purchase/purchase_model');
$module_name = 'per_client';
$pre_client_name_filter = 'per_client';
$month_filter_name = 'months';
$frequency_filter_name = 'frequency';

// Get selected months from filter - default to current + previous 4 months
$default_months = [];
$current_date = new DateTime(date('Y-m') . '-01');
for ($i = 4; $i >= 0; $i--) {
    $month_date = clone $current_date;
    $month_date->modify("-$i months");
    $default_months[] = $month_date->format('Y-m');
}

// Check for POST data first, then saved filter, then default
$posted_months = $this->ci->input->post('months');

if (is_array($posted_months) && count($posted_months) > 0) {
    $selected_months = $posted_months;
} else {
    // ONLY fallback for first page load (not AJAX reload)
    $selected_months = $default_months;
}


// Limit to 5 months max and sort chronologically
$selected_months = array_slice($selected_months, 0, 5);
usort($selected_months, function ($a, $b) {
    return strtotime($a) - strtotime($b);
});

// Generate months_to_display array
$months_to_display = [];
foreach ($selected_months as $selected_month) {
    $selected_date = new DateTime($selected_month . '-01');
    $month_key = strtolower($selected_date->format('F_Y')); // Format: january_2026
    $month_display = $selected_date->format('F Y'); // Format: January 2026
    $db_month = $selected_date->format('Y-m'); // Format: 2026-01

    $months_to_display[$month_key] = [
        'display' => $month_display,
        'db_format' => $db_month,
        'class' => strtolower($selected_date->format('M')) // jan, feb, etc.
    ];
}

// Build aColumns array
$aColumns = [
    db_prefix() . 'assar_clients.client_id',
    db_prefix() . 'assar_clients.name',
    db_prefix() . 'assar_clients.phone',
    db_prefix() . 'assar_clients.start_date',
    db_prefix() . 'assar_clients.investment as investment',
    db_prefix() . 'assar_clients.frequency',
];

// Add dynamic month columns for selected months
foreach ($months_to_display as $month_key => $month_info) {
    $aColumns[] = "COALESCE(MAX(CASE WHEN ams.month = '" . $month_info['db_format'] . "' THEN ams.total_pl END), 0) as " . $month_key;
}

// Add earned_to_date and percent_profits
$aColumns[] = "COALESCE(SUM(ams.total_pl), 0) as earned_to_date";
$aColumns[] = "CASE 
    WHEN " . db_prefix() . "assar_clients.investment > 0 
    THEN (COALESCE(SUM(ams.total_pl), 0) / " . db_prefix() . "assar_clients.investment) * 100 
    ELSE 0 
    END as percent_profits";

$sIndexColumn = 'id';
$sTable = db_prefix() . 'assar_clients';
$join = [
    'LEFT JOIN ' . db_prefix() . 'assar_monthly_summary ams ON ams.client_pk_id = ' . db_prefix() . 'assar_clients.id',
];

$where = [];

// Apply filters
if ($this->ci->input->post('per_client') && count($this->ci->input->post('per_client')) > 0) {
    $clients = $this->ci->input->post('per_client');
    $conditions = [];
    foreach ($clients as $client_code) {
        $clean_client_code = $this->ci->db->escape($client_code);
        $conditions[] = db_prefix() . "assar_clients.id = " . $clean_client_code;
    }
    $where[] = "AND (" . implode(' OR ', $conditions) . ")";
}

// Month filter - apply to monthly summary data
if (!empty($selected_months)) {
    $month_conditions = [];
    foreach ($selected_months as $month) {
        $month_conditions[] = "ams.month = '" . $month . "'";
    }
    $where[] = " AND (" . implode(' OR ', $month_conditions) . ")";
}

if ($this->ci->input->post('frequency') && $this->ci->input->post('frequency') != '') {
    $frequency = $this->ci->input->post('frequency');
    if ($frequency !== 'all') {
        $where[] = " AND " . db_prefix() . "assar_clients.frequency = '" . $this->ci->db->escape_str($frequency) . "'";
    }
}

// Update module filters
$per_client_filter_name_value = !empty($this->ci->input->post('per_client')) ?
    implode(',', $this->ci->input->post('per_client')) : NULL;
update_module_filter($module_name, $pre_client_name_filter, $per_client_filter_name_value);

$month_filter_name_value = !empty($selected_months) ?
    implode(',', $selected_months) :
    implode(',', $default_months);
update_module_filter($module_name, $month_filter_name, $month_filter_name_value);

$frequency_filter_name_value = !empty($this->ci->input->post('frequency')) ?
    $this->ci->input->post('frequency') : NULL;
update_module_filter($module_name, $frequency_filter_name, $frequency_filter_name_value);

// Use data_tables_init function with GROUP BY
$result = data_tables_init(
    $aColumns,
    $sIndexColumn,
    $sTable,
    $join,
    $where,
    [db_prefix() . 'assar_clients.id', db_prefix() . 'assar_clients.name'],
    'GROUP BY ' . db_prefix() . 'assar_clients.id'
);

$output = $result['output'];
$rResult = $result['rResult'];

// Process the data for display
$footer_data = [
    'investment' => 0,
    'earned_to_date' => 0,
];

// Initialize month totals
foreach ($months_to_display as $month_key => $month_info) {
    $footer_data[$month_key] = 0;
}

// Store month info for JavaScript
$output['month_info'] = $months_to_display;
$output['selected_months'] = $selected_months;
$output['aaData'] = [];
// Process each row
foreach ($rResult as $aRow) {
    $row = [];

    // Process each column
    for ($i = 0; $i < count($aColumns); $i++) {
        // Get the column alias
        $column_parts = explode(' as ', $aColumns[$i]);
        $column_alias = trim(end($column_parts));

        $_data = $aRow[$column_alias];

        // Format data based on column type
        if ($column_alias == 'client_id') {
            $row[] = $_data;
        } elseif ($column_alias == 'name') {
            $numberOutput = $_data;
            $numberOutput .= '<div class="row-options">';
            $numberOutput .= '<a href="' . admin_url('purchase/add_assar/' . $aRow['id']) . '">' . _l('edit') . '</a>';
            $numberOutput .= ' | <a href="' . admin_url('purchase/delete_assar/' . $aRow['id']) . '" class="text-danger _delete">' . _l('delete') . '</a>';
            $numberOutput .= '</div>';
            $row[] = $numberOutput;
        } elseif ($column_alias == 'phone') {
            $row[] = $_data;
        } elseif ($column_alias == 'start_date') {
            $row[] = date('d M, Y', strtotime($_data));
        } elseif ($column_alias == 'investment') {

            $base_investment   = (float) $_data; // existing investment
            $increase_amount   = (float) get_increase_amount($aRow['id']); 
            // final value = investment + increase/decrease
            $total_investment = $base_investment + $increase_amount;

            $formatted = app_format_money($total_investment, '₹');
            $row[] = $formatted;

            // footer total
            $footer_data['investment'] += $total_investment;
        } elseif ($column_alias == 'frequency') {
            $row[] = $_data;
        } elseif (array_key_exists($column_alias, $months_to_display)) {
            // Monthly columns
            $formatted = app_format_money($_data, '₹');
            $row[] = $formatted;
            $footer_data[$column_alias] += $_data;
        } elseif ($column_alias == 'earned_to_date') {
            $formatted = app_format_money($_data, '₹');
            $row[] = $formatted;
            $footer_data['earned_to_date'] += $_data;
        } elseif ($column_alias == 'percent_profits') {
            $row[] = round($_data, 2) . '%';
        } else {
            $row[] = $_data;
        }
    }

    $output['aaData'][] = $row;
}

// Format footer data
$output['sums'] = [
    'investment' => app_format_money($footer_data['investment'], '₹'),
    'earned_to_date' => app_format_money($footer_data['earned_to_date'], '₹'),
];

// Add month totals with class names
foreach ($months_to_display as $month_key => $month_info) {
    $output['sums'][$month_info['class']] = app_format_money($footer_data[$month_key], '₹');
}
