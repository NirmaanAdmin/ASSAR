<?php

defined('BASEPATH') or exit('No direct script access allowed');

$month = $this->ci->input->post('month');

$aColumns = [
    'client_id',
    'name',
    'investment',
    1,
    2,
    3,
    4,
    5,
    6,
    7,
    8,
    9,
    10,
    11
];

$sIndexColumn = 'id';
$sTable = db_prefix() . 'assar_clients';
$join = [];

$where = [];


$having = '';

$result = data_tables_init(
    $aColumns,
    $sIndexColumn,
    $sTable,
    $join,
    $where,
    ['id'],
    '',
    [],
    $having
);

$output  = $result['output'];
$rResult = $result['rResult'];
$footer_data = [
    'investment' => 0,
];

$aColumns = array_map(function ($col) {
    $col = trim($col);
    if (stripos($col, ' as ') !== false) {
        $parts = preg_split('/\s+as\s+/i', $col);
        return trim($parts[1], '"` ');
    }
    return trim($col, '"` ');
}, $aColumns);

// Calculate total days of selected month
if (!empty($month)) {

    // If month is like 2026-01
    if (strpos($month, '-') !== false) {
        $total_days = date('t', strtotime($month . '-01'));
    }
    // If only month number (01-12)
    else {
        $year = date('Y');
        $total_days = cal_days_in_month(CAL_GREGORIAN, (int)$month, $year);
    }
} else {
    $total_days = date('t'); // fallback current month
}
// ================================
// Prepare Total P&L Per Client
// ================================

$client_pl_map = [];

if (!empty($month)) {

    // Build month start & end
    if (strpos($month, '-') !== false) {
        $month_start = date('Y-m-01', strtotime($month . '-01'));
        $month_end   = date('Y-m-t', strtotime($month . '-01'));
    } else {
        $year = date('Y');
        $month_start = $year . '-' . str_pad($month, 2, '0', STR_PAD_LEFT) . '-01';
        $month_end   = date('Y-m-t', strtotime($month_start));
    }

    // Fetch summed P&L
    $pl_results = $this->ci->db
        ->select('client_id, SUM(client_pl) as total_pl')
        ->from(db_prefix() . '_daily_return_snapshot')
        ->where('date_from >=', $month_start)
        ->where('date_to <=', $month_end)
        ->group_by('client_id')
        ->get()
        ->result_array();
    foreach ($pl_results as $row) {
        $client_pl_map[$row['client_id']] = $row['total_pl'];
    }
}
// ================================
// Build ID => CLIENT_ID MAP
// ================================

$id_to_client = [];

$id_rows = $this->ci->db
    ->select('id, client_id')
    ->from(db_prefix() . 'assar_clients')
    ->get()
    ->result_array();

foreach ($id_rows as $row) {
    $id_to_client[$row['id']] = $row['client_id'];
}

// ===================================================
// BUILD ID => CLIENT_ID MAP
// ===================================================

$id_to_client = [];

$id_rows = $this->ci->db
    ->select('id, client_id')
    ->from(db_prefix() . 'assar_clients')
    ->get()
    ->result_array();

foreach ($id_rows as $row) {
    $id_to_client[$row['id']] = $row['client_id'];
}


// ===================================================
// COMMISSION SETTINGS (receiver => staff PK IDs)
// ===================================================

$commission_settings = [];

$commission_rows = $this->ci->db
    ->select('client_id, commission, commission_staff')
    ->from(db_prefix() . 'assar_clients')
    ->where('commission', 1)
    ->get()
    ->result_array();

foreach ($commission_rows as $row) {

    $staff_ids = json_decode($row['commission_staff'], true);

    if (is_array($staff_ids)) {
        $commission_settings[$row['client_id']] = $staff_ids;
    }
}


// ===================================================
// COMMISSION RECEIVED MAP
// ===================================================

$commission_received = [];

foreach ($commission_settings as $receiver_client_id => $staff_pk_ids) {

    $total = 0;

    foreach ($staff_pk_ids as $pk_id) {

        if (isset($id_to_client[$pk_id])) {

            $staff_client_id = $id_to_client[$pk_id];

            if (isset($client_pl_map[$staff_client_id])) {
                $total += ($client_pl_map[$staff_client_id] * 0.10);
            }
        }
    }

    $commission_received[$receiver_client_id] = $total;
}


// ===================================================
// COMMISSION PAID MAP
// ===================================================

$commission_paid = [];

foreach ($commission_settings as $receiver_client_id => $staff_pk_ids) {

    foreach ($staff_pk_ids as $pk_id) {

        if (isset($id_to_client[$pk_id])) {

            $staff_client_id = $id_to_client[$pk_id];

            if (!isset($commission_paid[$staff_client_id])) {
                $commission_paid[$staff_client_id] = 0;
            }

            if (isset($client_pl_map[$staff_client_id])) {
                $commission_paid[$staff_client_id] += ($client_pl_map[$staff_client_id] * 0.10);
            }
        }
    }
}

// ===================================================
// PAYOUT DATE (7th OF MONTH)
// ===================================================

if (!empty($month)) {

    if (strpos($month, '-') !== false) {
        // format: YYYY-MM
        $payout_date = date('Y-m-07', strtotime($month . '-01'));
    } else {
        // format: MM
        $year = date('Y');
        $payout_date = $year . '-' . str_pad($month, 2, '0', STR_PAD_LEFT) . '-07';
    }
} else {
    $payout_date = date('Y-m-07');
}


foreach ($rResult as $aRow) {
    $row = [];

    for ($i = 0; $i < count($aColumns); $i++) {
        $_data = $aRow[$aColumns[$i]];

        if ($aColumns[$i] == 'client_id') {
            $_data = $aRow['client_id'];
        } elseif ($aColumns[$i] == 'name') {
            $_data = $aRow['name'];
        } elseif ($aColumns[$i] == 'investment') {
            $_data = app_format_money($aRow['investment'], '₹');
        } elseif ($aColumns[$i] == 1) {
            $_data = app_format_money($aRow['investment'], '₹');
        } elseif ($aColumns[$i] == 2) {
            $_data = $total_days;
        } elseif ($aColumns[$i] == 3) {
            $client_id = $aRow['client_id'];

            $pl = isset($client_pl_map[$client_id])
                ? $client_pl_map[$client_id]
                : 0;

            $_data = app_format_money($pl, '₹');
        } elseif ($aColumns[$i] == 4) {
            $client_id = $aRow['client_id'];

            $_data = '
                    <select class="form-control rolled_over_select"
                            data-client_id="' . $client_id . '">

                        <option value="">Select</option>
                        <option value="1">Yes</option>
                        <option value="2">No</option>

                    </select>
                ';
        } elseif ($aColumns[$i] == 5) {
            $cid = $aRow['client_id'];

            $received = isset($commission_received[$cid])
                ? $commission_received[$cid]
                : 0;

            $paid = isset($commission_paid[$cid])
                ? $commission_paid[$cid]
                : 0;

            $final = $received - $paid;

            $_data = app_format_money($final, '₹');
        } elseif ($aColumns[$i] == 6) {
            $gross_payout = 0;
            $gross_payout = $pl + $final;
            $_data = app_format_money($gross_payout, '₹');
        } elseif ($aColumns[$i] == 7) {
            $tds = 0;
            $tds = $gross_payout * 0.10;
            $_data = app_format_money($tds, '₹');
        } elseif ($aColumns[$i] == 8) {
            $net_payout = 0;
            $net_payout = $gross_payout - $tds;
            $_data = app_format_money($net_payout, '₹');
        } elseif ($aColumns[$i] == 9) {
            $_data = date('d M, Y', strtotime($payout_date));
        } elseif ($aColumns[$i] == 10) {
            $_data = '<textarea
                    class="form-control assar-notes-rollover"
                    data-client="' . $aRow['id'] . '"
                    rows="3"
                ></textarea>';
        } elseif ($aColumns[$i] == 11) {
            $_data = app_format_money($aRow['investment'], '₹');
        }

        $row[] = $_data;
    }
    $output['aaData'][] = $row;
    $sr++;
}
