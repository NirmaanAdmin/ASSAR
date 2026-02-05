<?php
defined('BASEPATH') or exit('No direct script access allowed');

$month = $this->ci->input->post('month');
$date = DateTime::createFromFormat('Y-m', $month);
$lastDayOfMonth = $date->format('Y-m-t'); // 't' gives last day of month
/*
|--------------------------------------------------------------------------
| MONTH + TOTAL DAYS
|--------------------------------------------------------------------------
*/
if (!empty($month)) {

    if (strpos($month, '-') !== false) {
        $total_days = date('t', strtotime($month . '-01'));
        $summary_month = $month;
    } else {
        $year = date('Y');
        $total_days = cal_days_in_month(CAL_GREGORIAN, (int)$month, $year);
        $summary_month = $year . '-' . str_pad($month, 2, '0', STR_PAD_LEFT);
    }
} else {
    $total_days = date('t');
    $summary_month = date('Y-m');
}

/*
|--------------------------------------------------------------------------
| P&L PER CLIENT
|--------------------------------------------------------------------------
*/
$client_pl_map = [];


$month_start = date('Y-m-08', strtotime($month . '-01'));
$month_end   = date('Y-m-06', strtotime($start . ' +1 month'));
// echo $month_start . ' - ' . $month_end; exit;
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
/*
|--------------------------------------------------------------------------
| ID => CLIENT MAP
|--------------------------------------------------------------------------
*/
$id_to_client = [];
$id_rows = $this->ci->db
    ->select('id, client_id')
    ->from(db_prefix() . 'assar_clients')
    ->get()->result_array();

foreach ($id_rows as $row) {
    $id_to_client[$row['id']] = $row['client_id'];
}

/*
|--------------------------------------------------------------------------
| COMMISSION SETTINGS
|--------------------------------------------------------------------------
*/
$commission_settings = [];

$commission_rows = $this->ci->db
    ->select('client_id, commission_staff')
    ->from(db_prefix() . 'assar_clients')
    ->where('commission', 1)
    ->get()->result_array();

foreach ($commission_rows as $row) {
    $staff_ids = json_decode($row['commission_staff'], true);
    if (is_array($staff_ids)) {
        $commission_settings[$row['client_id']] = $staff_ids;
    }
}

/*
|--------------------------------------------------------------------------
| COMMISSION RECEIVED
|--------------------------------------------------------------------------
*/
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

/*
|--------------------------------------------------------------------------
| COMMISSION PAID
|--------------------------------------------------------------------------
*/
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

/*
|--------------------------------------------------------------------------
| PAYOUT DATE
|--------------------------------------------------------------------------
*/
$payout_date = date('Y-m-07', strtotime($summary_month . '-01'));

/*
|--------------------------------------------------------------------------
| INSERT / UPDATE SUMMARY TABLE
|--------------------------------------------------------------------------
*/
$clients = $this->ci->db
    ->select('id, client_id, investment')
    ->from(db_prefix() . 'assar_clients')
    ->where('start_date <=', $lastDayOfMonth)
    ->get()->result_array();

foreach ($clients as $c) {

    $cid = $c['client_id'];

    $pl = $client_pl_map[$cid] ?? 0;

    $received = $commission_received[$cid] ?? 0;
    $paid     = $commission_paid[$cid] ?? 0;
    $final_commission = $received - $paid;

    $gross_payout = $pl + $final_commission;
    $tds = $gross_payout * 0.10;
    $net_payout = $gross_payout - $tds;

    // ----------------------------------
    // CHECK EXISTING ROLLED OVER VALUE
    // ----------------------------------
    $exists = $this->ci->db
        ->where('client_pk_id', $c['id'])
        ->where('month', $summary_month)
        ->get('tblassar_monthly_summary')
        ->row();

    $rolled_over = $exists->rolled_over ?? null;

    // ----------------------------------
    // APPLY ROLLOVER RULE
    // ----------------------------------
    if ($rolled_over == 1) {

        $tds = 0;
        $net_payout = 0;
        $payout_date_db = NULL;
        $net_rollover = $c['investment'] + $gross_payout;
    } else {

        $payout_date_db = $payout_date;
        $net_rollover = $c['investment'];
    }

    // ----------------------------------
    // SAVE ARRAY
    // ----------------------------------
    $save = [
        'client_pk_id'      => $c['id'],
        'client_id'         => $cid,
        'month'             => $summary_month,
        'investment'        => $c['investment'],
        'principal'         => $c['investment'],
        'total_days'        => $total_days,
        'total_pl'          => $pl,
        'commission_amount' => $final_commission,
        'gross_payout'      => $gross_payout,
        'tds'               => $tds,
        'net_payout'        => $net_payout,
        'net_rollover'      => $net_rollover,
        'payout_date'       => $payout_date_db,

    ];

    // ----------------------------------
    // INSERT OR UPDATE
    // ----------------------------------
    if ($exists) {

        $this->ci->db
            ->where('id', $exists->id)
            ->update('tblassar_monthly_summary', $save);
    } else {
        $save['rolled_over'] = 2;
        $this->ci->db->insert(
            'tblassar_monthly_summary',
            $save
        );
    }

    // ----------------------------------
    // INSERT OR UPDATE - tblassar_net_rollver
    // ----------------------------------
    // Check if record exists in tblassar_net_rollver
    $rollver_exists = $this->ci->db
        ->where('client_id', $c['id'])  // Assuming 'id' is the primary client ID
        ->where('month', $summary_month)
        ->get('tblassar_net_rollver')
        ->row();

    $rollver_data = [
        'client_id' => $c['id'],  // Using the primary client ID
        'month' => $summary_month,
        'net_rollver_amount' => $net_rollover
    ];

    if ($rollver_exists) {
        // Update existing record
        $this->ci->db
            ->where('id', $rollver_exists->id)
            ->update('tblassar_net_rollver', $rollver_data);
    } else {
        // Insert new record
        $this->ci->db->insert('tblassar_net_rollver', $rollver_data);
    }
}


/*
|--------------------------------------------------------------------------
| DATATABLE LOAD FROM SUMMARY
|--------------------------------------------------------------------------
*/

$aColumns = [
    'tblassar_monthly_summary.client_id',
    'tblassar_monthly_summary.id',
    'tblassar_monthly_summary.investment',
    'tblassar_monthly_summary.principal',
    'tblassar_monthly_summary.total_days',
    'tblassar_monthly_summary.total_pl',
    'tblassar_monthly_summary.rolled_over',
    'tblassar_monthly_summary.commission_amount',
    'tblassar_monthly_summary.gross_payout',
    'tblassar_monthly_summary.tds',
    'tblassar_monthly_summary.net_payout',
    'tblassar_monthly_summary.payout_date',
    'tblassar_monthly_summary.notes',
    'tblassar_monthly_summary.net_rollover'
];

$sIndexColumn = 'id';
$sTable = 'tblassar_monthly_summary';

$join = [
    'LEFT JOIN ' . db_prefix() . 'assar_clients 
     ON ' . db_prefix() . 'assar_clients.id = tblassar_monthly_summary.client_pk_id'
];

$where = [];
$where[] = "AND tblassar_monthly_summary.month = '" . $summary_month . "'";

$result = data_tables_init(
    $aColumns,
    $sIndexColumn,
    $sTable,
    $join,
    $where,
    ['tblassar_monthly_summary.id', db_prefix() . 'assar_clients.name']
);

$output = $result['output'];
$rResult = $result['rResult'];
/*
|--------------------------------------------------------------------------
| OUTPUT ROWS
|--------------------------------------------------------------------------
*/
foreach ($rResult as $aRow) {

    $row = [];

    $row[] = $aRow['tblassar_monthly_summary.client_id'];
    $row[] = $aRow['name'];
    $row[] = app_format_money($aRow['tblassar_monthly_summary.investment'], '₹');
    $row[] = app_format_money($aRow['tblassar_monthly_summary.principal'], '₹');
    $row[] = $aRow['tblassar_monthly_summary.total_days'];
    $row[] = app_format_money($aRow['tblassar_monthly_summary.total_pl'], '₹');

    $row[] = '
    <select class="form-control rolled_over_select"
        data-id="' . $aRow['tblassar_monthly_summary.id'] . '">
        <option value="">Select</option>
        <option value="1" ' . ($aRow['tblassar_monthly_summary.rolled_over'] == 1 ? 'selected' : '') . '>Yes</option>
        <option value="2" ' . ($aRow['tblassar_monthly_summary.rolled_over'] == 2 ? 'selected' : '') . '>No</option>
    </select>';

    $row[] = app_format_money($aRow['tblassar_monthly_summary.commission_amount'], '₹');
    $row[] = app_format_money($aRow['tblassar_monthly_summary.gross_payout'], '₹');
    $row[] = app_format_money($aRow['tblassar_monthly_summary.tds'], '₹');
    $row[] = app_format_money($aRow['tblassar_monthly_summary.net_payout'], '₹');
    $row[] = !empty($aRow['tblassar_monthly_summary.payout_date'])
        ? date('d M Y', strtotime($aRow['tblassar_monthly_summary.payout_date']))
        : '';

    $row[] = '
    <textarea class="form-control assar-notes-rollover"
        data-id="' . $aRow['tblassar_monthly_summary.id'] . '"
        rows="3">' . $aRow['tblassar_monthly_summary.notes'] . '</textarea>';

    $row[] = app_format_money($aRow['tblassar_monthly_summary.net_rollover'], '₹');

    $output['aaData'][] = $row;
}
