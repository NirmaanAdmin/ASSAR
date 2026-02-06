<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head();
$module_name = 'module_activity_log'; ?>
<style>
   .show_hide_columns {
      position: absolute;
      z-index: 5000;
      left: 200px
   }

   .n_width {
      width: 20% !important;
   }

   .dashboard_stat_title {
      font-size: 19px;
      font-weight: bold;
   }

   .dashboard_stat_value {
      font-size: 19px;
   }

   .bulk-title {
      font-weight: bold;
   }

   b {
      font-weight: 700;
   }

   .n_width {
      width: 25% !important;
   }

   .close-tab {
      color: red;
      margin-left: 8px;
      cursor: pointer;
      font-weight: bold;
   }

   .close-tab:hover {
      color: darkred;
   }
</style>
<?php $module_name = 'assar'; ?>
<div id="wrapper">
   <div class="content">
      <div class="row">

         <div class="row">
            <div class="col-md-12" id="small-table">
               <div class="panel_s">
                  <div class="panel-body">
                     <div class="row">
                        <div class="col-md-12">
                           <h4 class="no-margin font-bold"><i class="fa fa-clipboard" aria-hidden="true"></i> <?php echo _l('ASSAR'); ?></h4>
                           <hr />
                        </div>
                        <div class="_buttons col-md-3">
                           <a href="<?php echo admin_url('purchase/add_assar'); ?>" class="btn btn-info pull-left mright10 display-block">
                              <?php echo _l('New'); ?>
                           </a>
                        </div>
                        <div class="_buttons col-md-9">
                           <div class="_buttons col-md-3 pull-right">
                              <?php
                              $month_filter = get_module_filter($module_name, 'month');
                              $month_filter_val = !empty($month_filter) ? $month_filter->filter_value : '';
                              ?>

                              <div class="form-group">
                                 <select name="month_filter" id="month_filter" class="form-control">
                                    <option value="">Select Month</option>

                                    <?php
                                    $start   = new DateTime('2025-09-01');
                                    $end     = new DateTime('2028-09-01');
                                    $current = date('Y-m');

                                    // priority: saved filter → current month
                                    $selected_month = !empty($month_filter_val) ? $month_filter_val : $current;

                                    while ($start <= $end) {
                                       $value = $start->format('Y-m');
                                       $label = $start->format('F Y');
                                       $selected = ($value === $selected_month) ? 'selected' : '';
                                    ?>
                                       <option value="<?php echo $value; ?>" <?php echo $selected; ?>>
                                          <?php echo $label; ?>
                                       </option>
                                    <?php
                                       $start->modify('+1 month');
                                    }
                                    ?>
                                 </select>
                              </div>


                           </div>
                        </div>
                     </div>
                     <br>
                     <div class="row">
                        <div class="col-md-12">
                           <div class="horizontal-tabs">
                              <ul class="nav nav-tabs nav-tabs-horizontal mbot15" role="tablist">
                                 <li role="presentation" class="active">
                                    <a href="#master" aria-controls="master" role="tab" id="tab_master" data-toggle="tab">
                                       Master
                                    </a>
                                 </li>
                                 <li role="presentation">
                                    <a href="#main_sheet" aria-controls="main_sheet" role="tab" id="tab_main_sheet" data-toggle="tab">
                                       Main Sheet
                                    </a>
                                 </li>
                                 <li role="presentation">
                                    <a href="#daily_return_net" aria-controls="daily_return_net" role="tab" id="tab_daily_return_net" data-toggle="tab">
                                       Daily Return Net
                                    </a>
                                 </li>
                                 <li role="presentation">
                                    <a href="#daily_return_log" aria-controls="daily_return_log" role="tab" id="tab_daily_return_log" data-toggle="tab">
                                       Daily Return Log
                                    </a>
                                 </li>
                                 <li role="presentation">
                                    <a href="#monthly_summary" aria-controls="monthly_summary" role="tab" id="tab_monthly_summary" data-toggle="tab">
                                       Monthly Summary
                                    </a>
                                 </li>
                              </ul>
                           </div>
                        </div>
                        <div class="tab-content">
                           <div role="tabpanel" class="col-md-12 tab-pane tracker-pane active" id="master">
                              <div class="btn-group show_hide_columns" id="show_hide_columns">
                                 <!-- Settings Icon -->
                                 <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="padding: 4px 7px;">
                                    <i class="fa fa-cog"></i> <?php  ?> <span class="caret"></span>
                                 </button>
                                 <!-- Dropdown Menu with Checkboxes -->
                                 <div class="dropdown-menu" style="padding: 10px; min-width: 250px;">
                                    <!-- Select All / Deselect All -->
                                    <div>
                                       <input type="checkbox" id="select-all-columns"> <strong><?php echo _l('select_all'); ?></strong>
                                    </div>
                                    <hr>
                                    <!-- Column Checkboxes -->
                                    <?php
                                    $columns = [
                                       'Client ID',
                                       'Name',
                                       'Phone',
                                       'Start Date',
                                       'Investment Amount',
                                       'Status (Active/Inactive)',
                                       'Referred by',
                                       'Remarks',
                                    ];
                                    ?>
                                    <div>
                                       <?php foreach ($columns as $key => $label): ?>
                                          <input type="checkbox" class="toggle-column" data-id="<?php echo $label; ?>" value="<?php echo $key; ?>" checked>
                                          <?php echo _l($label); ?><br>
                                       <?php endforeach; ?>
                                    </div>

                                 </div>
                              </div>

                              <table class="dt-table-loading table table-table_assar">
                                 <thead>
                                    <tr>
                                       <th><?php echo _l('Client ID'); ?></th>
                                       <th><?php echo _l('Name'); ?></th>
                                       <th><?php echo _l('Phone'); ?></th>
                                       <th><?php echo _l('Start Date'); ?></th>
                                       <th><?php echo _l('Investment Amount'); ?></th>
                                       <th><?php echo _l('Status (Active/Inactive)'); ?></th>
                                       <th><?php echo _l('Referred by'); ?></th>
                                       <th><?php echo _l('Remarks'); ?></th>
                                    </tr>
                                 </thead>
                                 <tbody></tbody>
                                 <tfoot>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td class="investment"></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                 </tfoot>
                              </table>
                           </div>
                           <div role="tabpanel" class="col-md-12 tab-pane tracker-pane" id="main_sheet">
                              <div id="ac-charts-section" class="">
                                 <div class="row">
                                    <div class="col-md-12 mtop20">
                                       <div class="row">
                                          <div class="quick-stats-invoices col-md-3 tw-mb-2 sm:tw-mb-0 n_width">
                                             <div class="top_stats_wrapper">
                                                <div class="tw-text-neutral-800 mtop5 tw-flex tw-items-center tw-justify-between">
                                                   <div class="tw-font-medium tw-inline-flex text-neutral-600 tw-items-center tw-truncate">
                                                      <span class="tw-truncate dashboard_stat_title">Total Pool of Money</span>
                                                   </div>
                                                   <span class="tw-font-semibold tw-text-neutral-600 tw-shrink-0"></span>
                                                </div>
                                                <div class="tw-text-neutral-800 mtop15 tw-flex tw-items-center tw-justify-between">
                                                   <div class="tw-font-medium tw-inline-flex text-neutral-600 tw-items-center tw-truncate">
                                                      <span class="tw-truncate dashboard_stat_value total_pool_of_money"></span>
                                                   </div>
                                                   <span class="tw-font-semibold tw-text-neutral-600 tw-shrink-0"></span>
                                                </div>
                                             </div>
                                          </div>
                                          <div class="quick-stats-invoices col-md-3 tw-mb-2 sm:tw-mb-0 n_width">
                                             <div class="top_stats_wrapper">
                                                <div class="tw-text-neutral-800 mtop5 tw-flex tw-items-center tw-justify-between">
                                                   <div class="tw-font-medium tw-inline-flex text-neutral-600 tw-items-center tw-truncate">
                                                      <span class="tw-truncate dashboard_stat_title">Client Payouts Forecast</span>
                                                   </div>
                                                   <span class="tw-font-semibold tw-text-neutral-600 tw-shrink-0"></span>
                                                </div>
                                                <div class="tw-text-neutral-800 mtop15 tw-flex tw-items-center tw-justify-between">
                                                   <div class="tw-font-medium tw-inline-flex text-neutral-600 tw-items-center tw-truncate">
                                                      <span class="tw-truncate dashboard_stat_value client_payouts_forecast"></span>
                                                   </div>
                                                   <span class="tw-font-semibold tw-text-neutral-600 tw-shrink-0"></span>
                                                </div>
                                             </div>
                                          </div>
                                          <div class="quick-stats-invoices col-md-3 tw-mb-2 sm:tw-mb-0 n_width">
                                             <div class="top_stats_wrapper">
                                                <div class="tw-text-neutral-800 mtop5 tw-flex tw-items-center tw-justify-between">
                                                   <div class="tw-font-medium tw-inline-flex text-neutral-600 tw-items-center tw-truncate">
                                                      <span class="tw-truncate dashboard_stat_title">Minimum Profit required everyday</span>
                                                   </div>
                                                   <span class="tw-font-semibold tw-text-neutral-600 tw-shrink-0"></span>
                                                </div>
                                                <div class="tw-text-neutral-800 mtop15 tw-flex tw-items-center tw-justify-between">
                                                   <div class="tw-font-medium tw-inline-flex text-neutral-600 tw-items-center tw-truncate">
                                                      <span class="tw-truncate dashboard_stat_value minimum_profit_required_everyday"></span>
                                                   </div>
                                                   <span class="tw-font-semibold tw-text-neutral-600 tw-shrink-0"></span>
                                                </div>
                                             </div>
                                          </div>

                                       </div>
                                    </div>
                                 </div>

                              </div>
                              <br>
                              <table class="dt-table-loading table table-table_main_sheet">
                                 <thead>
                                    <tr>
                                       <th><?php echo _l('Client ID'); ?></th>
                                       <th><?php echo _l('Name'); ?></th>
                                       <th><?php echo _l('Assar Holds'); ?></th>
                                       <button id="apply_to_all" class="btn btn-sm btn-primary" style="position: absolute; left: 57.5%;top: 9%;z-index: 9999;">
                                          Apply To All
                                       </button>
                                       <th style="position: relative;"><?php echo _l('Earnings Forecast %'); ?></th>
                                       <th><?php echo _l('Client Earnings Forecast ₹'); ?></th>
                                    </tr>
                                 </thead>
                                 <tbody></tbody>
                                 <tfoot>
                                    <td></td>
                                    <td></td>
                                    <td class="investment"></td>
                                    <td></td>
                                    <td class="client_earnings_forecast"></td>
                                 </tfoot>
                              </table>
                           </div>
                           <div role="tabpanel" class="col-md-12 tab-pane tracker-pane" id="daily_return_net">
                              <div class="mbot10 pull-right">
                                 <button id="sync_daily_return"
                                    class="btn btn-info">
                                    <i class="fa fa-refresh"></i> Sync Daily Return
                                 </button>
                              </div>

                              <table class="dt-table-loading table table-table_daily_return_net">
                                 <thead>
                                    <tr>
                                       <th><?php echo _l('Date'); ?></th>
                                       <th><?php echo _l('% return'); ?></th>
                                       <th><?php echo _l('Actual P&L'); ?></th>
                                       <th><?php echo _l('Notes'); ?></th>
                                    </tr>
                                 </thead>
                                 <tbody></tbody>
                                 <tfoot>

                                 </tfoot>
                              </table>
                           </div>
                           <div role="tabpanel" class="col-md-12 tab-pane tracker-pane" id="daily_return_log">

                              <div class="horizontal-tabs">
                                 <ul class="nav nav-tabs nav-tabs-horizontal mbot15" id="rangeTabs" role="tablist">

                                    <!-- PLUS TAB -->
                                    <li role="presentation" class="active">
                                       <a href="#plus" id="tab_plus" role="tab" data-toggle="tab">
                                          <i class="fa fa-plus"></i>
                                       </a>
                                    </li>

                                 </ul>
                              </div>

                              <div class="tab-content" id="rangeTabContent">

                                 <!-- PLUS CONTENT -->
                                 <div role="tabpanel" class="tab-pane active" id="plus"></div>

                              </div>

                           </div>
                           <div role="tabpanel" class="col-md-12 tab-pane tracker-pane" id="monthly_summary">
                              <table class="dt-table-loading table table-table_monthly_summary">
                                 <thead>
                                    <tr>
                                       <th><?php echo _l('Client ID'); ?></th>
                                       <th><?php echo _l('Name'); ?></th>
                                       <th><?php echo _l('Investment'); ?></th>
                                       <th><?php echo _l('Principal'); ?></th>
                                       <th><?php echo _l('Total Days'); ?></th>
                                       <th><?php echo _l('Total P&L'); ?></th>
                                       <th><?php echo _l('Rolled Over? (Y/N)'); ?></th>
                                       <th><?php echo _l('Commission'); ?></th>
                                       <th><?php echo _l('Payout GROSS'); ?></th>
                                       <th><?php echo _l('TDS'); ?></th>
                                       <th><?php echo _l('Payout Net'); ?></th>
                                       <th><?php echo _l('Payout Date'); ?></th>
                                       <th><?php echo _l('Notes'); ?></th>
                                       <th><?php echo _l('Net Rollover'); ?></th>
                                    </tr>
                                 </thead>
                                 <tbody></tbody>
                                 <tfoot>
                                    <td></td>
                                    <td></td>
                                    <td class="investment"></td>
                                    <td class="principal"></td>
                                    <td></td>
                                    <td class="totalpl"></td>
                                    <td></td>
                                    <td class="commission"></td>
                                    <td class="payoutgross"></td>
                                    <td class="tds"></td>
                                    <td class="payoutnet"></td>
                                    <td></td>
                                    <td></td>
                                    <td class="netrollover"></td>
                                 </tfoot>
                              </table>
                           </div>

                        </div>
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>
</div>
<div class="modal fade" id="dateRangeModal">
   <div class="modal-dialog">
      <div class="modal-content">

         <div class="modal-header">
            <h4>Select Date Range</h4>
         </div>

         <div class="modal-body">
            <input type="date" id="from_date" class="form-control">
            <br>
            <input type="date" id="to_date" class="form-control">
         </div>

         <div class="modal-footer">
            <button id="createTab" class="btn btn-primary">Create</button>
         </div>

      </div>
   </div>
</div>

<?php init_tail(); ?>
<script>
   $(document).ready(function() {
      var table_assar = $('.table-table_assar');
      var Params = {};
      initDataTable(table_assar, admin_url + 'purchase/table_assar', [], [], Params, [3, 'asc']);
      $('.table-table_assar').on('draw.dt', function() {
         var reportsTable = $(this).DataTable();
         var sums = reportsTable.ajax.json().sums;
         $(this).find('tfoot').addClass('bold');
         $(this).find('tfoot td').eq(0).html("Total (Per Page)");
         $(this).find('tfoot td.investment').html(sums.investment);
      });

      // Handle "Select All" checkbox
      $('#select-all-columns').on('change', function() {
         var isChecked = $(this).is(':checked');
         $('.toggle-column').prop('checked', isChecked).trigger('change');
      });

      // Handle individual column visibility toggling
      $('.toggle-column').on('change', function() {
         var column = table_assar.DataTable().column($(this).val());
         column.visible($(this).is(':checked'));

         // Sync "Select All" checkbox state
         var allChecked = $('.toggle-column').length === $('.toggle-column:checked').length;
         $('#select-all-columns').prop('checked', allChecked);
      });

      // Sync checkboxes with column visibility on page load
      table_assar.DataTable().columns().every(function(index) {
         var column = this;
         $('.toggle-column[value="' + index + '"]').prop('checked', column.visible());
      });

      // Prevent dropdown from closing when clicking inside
      $('.dropdown-menu').on('click', function(e) {
         e.stopPropagation();
      });

      table_assar.on('draw.dt', function() {
         $('.toggle-column[data-id="group_pur"]').prop('checked', false).trigger('change');
         $('.selectpicker').selectpicker('refresh');
      });

      var table_main_sheet = $('.table-table_main_sheet');
      var Params_main_sheet = {
         "month": "[name='month_filter']",
      };
      initDataTable(table_main_sheet, admin_url + 'purchase/table_main_sheet', [], [], Params_main_sheet, [0, 'asc']);
      $('#month_filter').on('change', function() {
         table_main_sheet.DataTable().ajax.reload();
      });


      $('.table-table_main_sheet').on('draw.dt', function() {
         var reportsTable = $(this).DataTable();
         var sums = reportsTable.ajax.json().sums;
         $(this).find('tfoot').addClass('bold');
         $(this).find('tfoot td').eq(0).html("Total (Per Page)");
         $(this).find('tfoot td.investment').html(sums.investment);
         $(this).find('tfoot td.client_earnings_forecast').html(sums.client_earnings_forecast);
         $('.total_pool_of_money').text(sums.investment);
         $('.client_payouts_forecast').text(sums.client_earnings_forecast);
         let forecast = parseFloat(
            sums.client_earnings_forecast
            .replace(/₹/g, '')
            .replace(/,/g, '')
            .trim()
         );

         let minimum_profit_required_everyday = isNaN(forecast) ?
            0 :
            forecast / sums.client_count;

         $('.minimum_profit_required_everyday')
            .text('₹' + minimum_profit_required_everyday.toFixed(2));

      });
   });


   $(document).on('blur', '.assar-input', function() {

      let client_id = $(this).data('client');
      let holds = $(this).val();
      let month = $('#month_filter').val();
      var table_main_sheet = $('.table-table_main_sheet');
      if (month == '') {
         alert('Please select month first');
         return;
      }

      $.post(admin_url + 'purchase/save_main_sheet', {
         client_id: client_id,
         assar_holds: holds,
         month: month
      }, function(response) {

         // ✅ reload main sheet table after save
         table_main_sheet.DataTable().ajax.reload(null, false);

      });

   });
   $('#apply_to_all').on('click', function() {

      let firstValue = $('.assar-input').first().val();

      if (firstValue === '') {
         alert('Enter value in first row first');
         return;
      }

      $('.assar-input').each(function() {

         $(this).val(firstValue).trigger('blur');

      });

   });
   var table_daily_return_net = $('.table-table_daily_return_net');
   var Params_daily_return_net = {
      "month": "[name='month_filter']",
   };
   initDataTable(table_daily_return_net, admin_url + 'purchase/table_daily_return_net', [], [], Params_daily_return_net, [0, 'asc']);
   $('#month_filter').on('change', function() {
      table_daily_return_net.DataTable().ajax.reload();
   });

   $('#sync_daily_return').on('click', function() {

      if (!confirm('Sync daily return for selected month?')) {
         return;
      }

      $.post(admin_url + 'purchase/sync_daily_return_net', {
         month: $('#month_filter').val()
      }).done(function() {

         alert_float('success', 'Daily return synced');
         $('.table-table_daily_return_net').DataTable().ajax.reload();

      });
   });

   $('body').on('blur', '.actual-pl, .notes', function() {

      let row_id = $(this).data('id');
      let field = $(this).hasClass('actual-pl') ? 'actual_pl' : 'notes';
      let value = $(this).val();

      $.post(admin_url + 'purchase/update_daily_return_field', {
         id: row_id,
         field: field,
         value: value,
         month: $('#month_filter').val()
      }).done(function() {
         alert_float('success', 'Updated successfully');
         $('.table-table_daily_return_net').DataTable().ajax.reload();
      });
   });

   // Add this at the top with other global variables
   var usedRanges = [];
   var tabCounter = 1; // Counter for tab numbering

   // Function to format date
   function formatDate(d) {
      return new Date(d).toLocaleDateString('en-IN', {
         day: '2-digit',
         month: 'short',
         year: 'numeric'
      });
   }

   /**
    * Function to load saved daily return ranges
    * @param {string} monthValue - The month value from the filter
    */
   function loadSavedDailyReturnRanges(monthValue) {
      $.ajax({
         url: "<?php echo admin_url('purchase/get_saved_daily_return_ranges'); ?>",
         dataType: "json",
         data: {
            month: monthValue
         },
         success: function(ranges) {
            // Clear existing tabs (except the plus tab)
            $('#rangeTabs li:not(:last)').remove();
            $('#rangeTabContent .tab-pane:not(#plus)').remove();
            usedRanges = []; // Reset used ranges
            tabCounter = 1; // Reset tab counter

            if (ranges.length === 0) {
               // Show plus tab as active
               $('#rangeTabs li:last').addClass('active');
               $('#plus').addClass('active');
               return;
            }

            $.each(ranges, function(i, row) {
               usedRanges.push({
                  from: row.date_from,
                  to: row.date_to,
                  tabNumber: tabCounter // Store tab number
               });

               let rangeText = 'Sheet ' + tabCounter + ' - ' + formatDate(row.date_from) + ' to ' + formatDate(row.date_to);
               let tabId = 'tab_' + row.date_from + '_' + row.date_to;

               // Add tab
               $('#rangeTabs li:last').before(`
                <li role="presentation">
                    <a href="#${tabId}"
                       data-from="${row.date_from}"
                       data-to="${row.date_to}"
                       data-tab-number="${tabCounter}"
                       role="tab"
                       data-toggle="tab">
                       Sheet ${tabCounter}
                       <span class="close-tab"
                             data-from="${row.date_from}"
                             data-to="${row.date_to}">
                          &times;
                       </span>
                    </a>
                </li>
                `);

               // Add tab content
               $('#rangeTabContent').append(`
                <div role="tabpanel"
                     class="tab-pane"
                     id="${tabId}">
                </div>
                `);

               tabCounter++; // Increment counter for next tab
            });

            // Remove active class from all tabs and panes
            $('#rangeTabs li').removeClass('active');
            $('#rangeTabContent .tab-pane').removeClass('active');

            // Activate first range tab
            let firstTab = $('#rangeTabs li:not(:last) a').first();
            if (firstTab.length > 0) {
               firstTab.parent().addClass('active');
               $(firstTab.attr('href')).addClass('active');

               // Load data for the active tab
               loadTabData(firstTab);
            } else {
               // If no saved tabs, activate plus tab
               $('#rangeTabs li:last').addClass('active');
               $('#plus').addClass('active');
            }
         },
         error: function(xhr, status, error) {
            console.error('Error loading saved ranges:', error);
            alert_float('error', 'Failed to load saved ranges');
         }
      });
   }

   /**
    * Load data for a specific tab
    * @param {jQuery} tabElement - The tab link element
    */
   function loadTabData(tabElement) {
      let from = tabElement.data('from');
      let to = tabElement.data('to');
      let tabId = tabElement.attr('href').replace('#', '');
      let tabNumber = tabElement.data('tab-number') || getTabNumberForRange(from, to);

      // If content already loaded, don't reload
      if ($('#' + tabId).html().trim() !== '') return;

      $.ajax({
         url: "<?php echo admin_url('purchase/get_clients_for_daily_return'); ?>",
         type: "POST",
         dataType: "json",
         data: {
            from_date: from,
            to_date: to,
            month: $('#month_filter').val()
         },
         success: function(res) {
            renderTable(tabId, res, from, to, tabNumber);
         },
         error: function() {
            alert_float('error', 'Failed to load tab data');
         }
      });
   }

   /**
    * Get tab number for a given date range
    */
   function getTabNumberForRange(from, to) {
      for (let i = 0; i < usedRanges.length; i++) {
         if (usedRanges[i].from === from && usedRanges[i].to === to) {
            return usedRanges[i].tabNumber;
         }
      }
      return tabCounter;
   }

   /**
    * Check if date range overlaps with existing ranges
    * @param {string} from - Start date
    * @param {string} to - End date
    * @returns {boolean} - True if overlaps
    */
   function isOverlap(from, to) {
      for (let i = 0; i < usedRanges.length; i++) {
         if (from <= usedRanges[i].to && to >= usedRanges[i].from) {
            return true;
         }
      }
      return false;
   }

   /**
    * Render table in a tab
    * @param {string} tabId - Tab ID
    * @param {Array} res - Data rows
    * @param {string} from - Start date
    * @param {string} to - End date
    * @param {number} tabNumber - Tab number (Sheet 1, Sheet 2, etc.)
    */
   function renderTable(tabId, res, from, to, tabNumber) {
      let range = formatDate(from) + ' - ' + formatDate(to);
      let sheetTitle = 'Sheet ' + tabNumber + ': ' + range;

      let totalInvestment = 0;
      let totalAssar = 0;
      let totalPL = 0;
      let totalCumMonthPL = 0;
      let totalAccumPL = 0;
      let totalCapital = 0;

      let html = `
        <h4>${sheetTitle}</h4>
        <table class="table table-bordered">
        <thead>
        <tr>
            <th>Date Range</th>
            <th>Client ID</th>
            <th>Client Name</th>
            <th>Investment</th>
            <th>Assar Holds</th>
            <th>Client P&L %</th>
            <th>Client P&L</th>
            <th>Cummulative P&L this month</th>
            <th>Accumulated P&L Till date</th>
            <th>Cummulative Capital</th>
            <th>Notes</th>
        </tr>
        </thead>
        <tbody>
    `;

      $.each(res, function(i, row) {
         let investment = parseFloat(row.investment) || 0;
         let assar = parseFloat(row.assar_holds) || 0;
         let pl = parseFloat(row.client_pl) || 0;
         let cumMonth = parseFloat(row.cumulative_month_pl) || 0;
         let accum = parseFloat(row.accumulated_pl) || 0;
         let capital = parseFloat(row.cumulative_capital) || 0;

         totalInvestment += investment;
         totalAssar += assar;
         totalPL += pl;
         totalCumMonthPL += cumMonth;
         totalAccumPL += accum;
         totalCapital += capital;

         html += `
            <tr>
                <td>${range}</td>
                <td>${row.client_id}</td>
                <td>${row.client_name}</td>
                <td>₹${investment.toFixed(2)}</td>
                <td>₹${assar.toFixed(2)}</td>
                <td>${row.client_pl_percent}</td>
                <td>₹${pl.toFixed(2)}</td>
                <td>₹${cumMonth.toFixed(2)}</td>
                <td>₹${accum.toFixed(2)}</td>
                <td>₹${capital.toFixed(2)}</td>
                <td>
                    <input class="form-control notes-new"
                          data-id="${row.id}"
                          value="${row.notes ?? ''}">
                </td>
            </tr>
        `;
      });

      // TOTAL ROW
      html += `
        </tbody>
        <tfoot>
        <tr style="font-weight:bold;background:#f5f5f5;">
            <td colspan="3">TOTAL</td>
            <td>₹${totalInvestment.toFixed(2)}</td>
            <td>₹${totalAssar.toFixed(2)}</td>
            <td></td>
            <td>₹${totalPL.toFixed(2)}</td>
            <td>₹${totalCumMonthPL.toFixed(2)}</td>
            <td>₹${totalAccumPL.toFixed(2)}</td>
            <td>₹${totalCapital.toFixed(2)}</td>
            <td></td>
        </tr>
        </tfoot>
        </table>
    `;

      $('#' + tabId).html(html);
   }

   // Update the document ready function
   $(document).ready(function() {
      // ... existing code for other tabs ...

      /* ---------- DAILY RETURN LOG TAB ---------- */

      // Load saved ranges on page load
      var initialMonthVal = $('#month_filter').val();
      loadSavedDailyReturnRanges(initialMonthVal);

      // Reload when month filter changes
      $('#month_filter').on('change', function() {
         loadSavedDailyReturnRanges($(this).val());
      });

      /* ---------- PLUS CLICK ---------- */
      $(document).on('click', '#tab_plus', function() {
         $('#dateRangeModal').modal('show');
      });

      /* ---------- CREATE TAB ---------- */
      $('#createTab').click(function() {
         let from = $('#from_date').val();
         let to = $('#to_date').val();

         if (!from || !to) {
            alert('Select dates');
            return;
         }

         if (isOverlap(from, to)) {
            alert('Range already exists');
            return;
         }

         // Get next tab number
         let nextTabNumber = tabCounter;

         // Add to usedRanges with tab number
         usedRanges.push({
            from: from,
            to: to,
            tabNumber: nextTabNumber
         });

         let tabName = 'Sheet ' + nextTabNumber;
         let tabId = 'tab_' + from + '_' + to;

         // Create tab with Sheet number
         $('#rangeTabs li:last').before(`
            <li role="presentation">
                <a href="#${tabId}"
                   data-from="${from}"
                   data-to="${to}"
                   data-tab-number="${nextTabNumber}"
                   role="tab"
                   data-toggle="tab">
                   ${tabName}
                   <span class="close-tab"
                         data-from="${from}"
                         data-to="${to}">
                      &times;
                   </span>
                </a>
            </li>
        `);

         // Create tab content
         $('#rangeTabContent').append(`
            <div role="tabpanel"
                 class="tab-pane"
                 id="${tabId}">
            </div>
        `);

         // Remove active from plus, activate new tab
         $('#rangeTabs li').removeClass('active');
         $('#rangeTabContent .tab-pane').removeClass('active');

         $('a[href="#' + tabId + '"]').parent().addClass('active');
         $('#' + tabId).addClass('active');

         // Load data for the new tab
         $.ajax({
            url: "<?php echo admin_url('purchase/get_clients_for_daily_return'); ?>",
            type: "POST",
            dataType: "json",
            data: {
               from_date: from,
               to_date: to,
               month: $('#month_filter').val()
            },
            success: function(res) {
               renderTable(tabId, res, from, to, nextTabNumber);
               $('#dateRangeModal').modal('hide');
               $('#from_date').val('');
               $('#to_date').val('');

               // Increment tab counter for next tab
               tabCounter++;
            },
            error: function() {
               alert_float('error', 'Failed to load tab data');
            }
         });
      });

      /* ---------- CLICK EXISTING TAB ---------- */
      $(document).on('click', '#rangeTabs a[data-from]', function(e) {
         e.preventDefault();
         let tabElement = $(this);

         // Update active tab
         $('#rangeTabs li').removeClass('active');
         $('#rangeTabContent .tab-pane').removeClass('active');

         tabElement.parent().addClass('active');
         $(tabElement.attr('href')).addClass('active');

         // Load data if needed
         loadTabData(tabElement);
      });

      /* ---------- CLOSE TAB ---------- */
      $(document).on('click', '.close-tab', function(e) {
         e.stopPropagation();

         if (!confirm('Delete this date range data?')) return;

         let from = $(this).data('from');
         let to = $(this).data('to');

         $.post(
            "<?php echo admin_url('purchase/delete_daily_return_range'); ?>", {
               from_date: from,
               to_date: to,
               month: $('#month_filter').val()
            },
            function() {
               // Remove from usedRanges
               usedRanges = usedRanges.filter(function(range) {
                  return !(range.from === from && range.to === to);
               });

               // Remove tab and its content
               let tabId = 'tab_' + from + '_' + to;
               $('a[href="#' + tabId + '"]').parent().remove();
               $('#' + tabId).remove();

               alert_float('success', 'Deleted successfully');

               // Re-number tabs after deletion
               renumberTabs();

               // Activate another tab if available
               let nextTab = $('#rangeTabs li:not(:last) a').first();
               if (nextTab.length) {
                  nextTab.parent().addClass('active');
                  $(nextTab.attr('href')).addClass('active');
                  loadTabData(nextTab);
               } else {
                  // Activate plus tab
                  $('#rangeTabs li:last').addClass('active');
                  $('#plus').addClass('active');
               }
            }
         ).fail(function() {
            alert_float('error', 'Failed to delete range');
         });
      });

      /* ---------- RENUMBER TABS ---------- */
      function renumberTabs() {
         tabCounter = 1;
         $('#rangeTabs li:not(:last)').each(function(index) {
            let tabLink = $(this).find('a');
            let from = tabLink.data('from');
            let to = tabLink.data('to');

            // Update tab number in DOM
            tabLink.data('tab-number', tabCounter);
            tabLink.html('Sheet ' + tabCounter +
               '<span class="close-tab" data-from="' + from + '" data-to="' + to + '">&times;</span>');

            // Update tab number in usedRanges
            for (let i = 0; i < usedRanges.length; i++) {
               if (usedRanges[i].from === from && usedRanges[i].to === to) {
                  usedRanges[i].tabNumber = tabCounter;
                  break;
               }
            }

            tabCounter++;
         });
      }

      /* ---------- SAVE NOTES ---------- */
      $('body').on('blur', '.notes-new', function() {
         let row_id = $(this).data('id');
         let value = $(this).val();

         $.post(admin_url + 'purchase/update_daily_return_notes', {
            id: row_id,
            value: value
         }).done(function() {
            alert_float('success', 'Updated successfully');
         }).fail(function() {
            alert_float('error', 'Failed to update notes');
         });
      });

      // ... rest of your existing code for other tabs ...
   });
   

   var table_monthly_summary = $('.table-table_monthly_summary');
   var Params_monthly_summary = {
      "month": "[name='month_filter']",
   };
   initDataTable(table_monthly_summary, admin_url + 'purchase/table_monthly_summary', [], [], Params_monthly_summary, [0, 'asc']);
   $('#month_filter').on('change', function() {
      table_monthly_summary.DataTable().ajax.reload();
   });
   ('.table-table_monthly_summary').on('draw.dt', function() {
      var reportsTable1 = $(this).DataTable();
      var sums = reportsTable1.ajax.json().sums;
      $(this).find('tfoot').addClass('bold');
      $(this).find('tfoot td').eq(0).html("Total (Per Page)");
      $(this).find('tfoot td.investment').html(sums.investment);
      $(this).find('tfoot td.principal').html(sums.principal);
      $(this).find('tfoot td.totalpl').html(sums.totalpl);
      $(this).find('tfoot td.commission').html(sums.commission);
      $(this).find('tfoot td.payoutgross').html(sums.payoutgross);
      $(this).find('tfoot td.tds').html(sums.tds);
      $(this).find('tfoot td.payoutnet').html(sums.payoutnet);
      $(this).find('tfoot td.netrollover').html(sums.netrollover);
   });


   $(document).on('blur', '.assar-notes-rollover', function() {

      let id = $(this).data('id');
      let notes = $(this).val();

      $.post(admin_url + 'purchase/update_rollover_notes', {
         id: id,
         notes: notes
      }).done(function() {
         alert_float('success', 'Updated successfully');
         table_monthly_summary.DataTable().ajax.reload();
      });

   });

   $(document).on('change', '.rolled_over_select', function() {

      let id = $(this).data('id');
      let val = $(this).val();

      $.post(admin_url + 'purchase/update_rollover', {
         id: id,
         rolled_over: val
      }).done(function() {
         alert_float('success', 'Updated successfully');
         table_monthly_summary.DataTable().ajax.reload();
      });

   });
</script>



</body>

</html>