<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<?php $module_name = 'compounding_tracker'; ?>
<style>
</style>
<div id="wrapper">
   <div class="content">
      <div class="row">
         <div class="panel_s mbot10">
            <div class="panel-body">
               <div class="row">
                  <div class="col-md-12">
                     <h4 class="no-margin font-bold"><i class="fa fa-line-chart menu-icon" aria-hidden="true"></i> <?php echo _l('compounding_tracker'); ?></h4>
                     <hr />
                  </div>
                  <div class="col-md-12">
                     <div class="horizontal-tabs">
                        <ul class="nav nav-tabs nav-tabs-horizontal mbot15" role="tablist">
                           <li role="presentation" class="active">
                            <a href="#dashboard" aria-controls="dashboard" role="tab" id="tab_dashboard" data-toggle="tab">
                              Dashboard
                            </a>
                           </li>
                           <li role="presentation">
                            <a href="#tracker" aria-controls="tracker" role="tab" id="tab_tracker" data-toggle="tab">
                              Tracker
                            </a>
                           </li>
                           <li role="presentation">
                            <a href="#config" aria-controls="config" role="tab" id="tab_config" data-toggle="tab">
                              Config
                            </a>
                           </li>
                        </ul>
                     </div>
                  </div>

                  <div class="tab-content">
                     <div role="tabpanel" class="col-md-12 tab-pane active" id="dashboard">
                     </div>
                     <div role="tabpanel" class="col-md-12 tab-pane" id="tracker">
                        <div class="row">
                           <div class="col-md-12">
                              <table class="table dt-table tracker_table border">
                                 <thead>
                                    <tr>
                                       <th><?php echo _l('Day'); ?></th>
                                       <th><?php echo _l('Date'); ?></th>
                                       <th><?php echo _l('Cycle'); ?></th>
                                       <th><?php echo _l('Day in Cycle'); ?></th>
                                       <th><?php echo _l('Plan Opening'); ?></th>
                                       <th><?php echo _l('Plan Closing'); ?></th>
                                       <th><?php echo _l('Actual Opening'); ?></th>
                                       <th><?php echo _l('Actual P&L'); ?></th>
                                       <th><?php echo _l('Actual Closing'); ?></th>
                                       <th><?php echo _l('vs Plan %'); ?></th>
                                       <th><?php echo _l('FIXED_MARGIN_INR'); ?></th>
                                       <th><?php echo _l('W/D Target'); ?></th>
                                       <th><?php echo _l('W/D Amount'); ?></th>
                                       <th><?php echo _l('Notes'); ?></th>
                                    </tr>
                                 </thead>
                                 <tbody>
                                 </tbody>
                              </table>
                           </div>
                        </div>
                     </div>
                     <div role="tabpanel" class="col-md-12 tab-pane" id="config">
                        <table class="table table-bordered table-striped config_table">
                           <thead>
                              <tr>
                                 <th><?php echo _l('Parameter'); ?></th>
                                 <th><?php echo _l('Value'); ?></th>
                                 <th><?php echo _l('Description'); ?></th>
                              </tr>
                           </thead>
                           <tbody>
                              <tr>
                                 <td>Starting Capital (<?php echo $base_currency->symbol; ?>)</td>
                                 <td>
                                    <?php
                                    $starting_capital_filter = get_module_filter($module_name, 'starting_capital');
                                    $starting_capital_filter_val = !empty($starting_capital_filter) ?  $starting_capital_filter->filter_value : 2500;
                                    echo render_input('starting_capital', '', $starting_capital_filter_val, 'number');
                                    ?>
                                 </td>
                                 <td>Fixed Day 1 opening balance</td>
                              </tr>
                              <tr>
                                 <td>Daily Return %</td>
                                 <td>
                                    <?php
                                    $daily_return_filter = get_module_filter($module_name, 'daily_return');
                                    $daily_return_filter_val = !empty($daily_return_filter) ?  $daily_return_filter->filter_value : 20;
                                    echo render_input('daily_return', '', $daily_return_filter_val, 'number');
                                    ?>
                                 </td>
                                 <td>Target compounding rate</td>
                              </tr>
                              <tr>
                                 <td>Days per Cycle</td>
                                 <td>
                                    <?php
                                    $days_per_cycle_filter = get_module_filter($module_name, 'days_per_cycle');
                                    $days_per_cycle_filter_val = !empty($days_per_cycle_filter) ?  $days_per_cycle_filter->filter_value : 7;
                                    echo render_input('days_per_cycle', '', $days_per_cycle_filter_val, 'number');
                                    ?>
                                 </td>
                                 <td>Withdrawal checkpoint interval</td>
                              </tr>
                              <tr>
                                 <td>Withdrawal %</td>
                                 <td>
                                    <?php
                                    $withdrawal_filter = get_module_filter($module_name, 'withdrawal');
                                    $withdrawal_filter_val = !empty($withdrawal_filter) ?  $withdrawal_filter->filter_value : 20;
                                    echo render_input('withdrawal', '', $withdrawal_filter_val, 'number');
                                    ?>
                                 </td>
                                 <td>% of closing balance to withdraw</td>
                              </tr>
                              <tr>
                                 <td>Positions at Once</td>
                                 <td>
                                    <?php
                                    $positions_once_filter = get_module_filter($module_name, 'positions_once');
                                    $positions_once_filter_val = !empty($positions_once_filter) ?  $positions_once_filter->filter_value : 5;
                                    echo render_input('positions_once', '', $positions_once_filter_val, 'number');
                                    ?>
                                 </td>
                                 <td>For FIXED_MARGIN_INR calc</td>
                              </tr>
                              <tr>
                                 <td>Wallet Usage %</td>
                                 <td>
                                    <?php
                                    $wallet_usage_filter = get_module_filter($module_name, 'wallet_usage');
                                    $wallet_usage_filter_val = !empty($wallet_usage_filter) ?  $wallet_usage_filter->filter_value : 85;
                                    echo render_input('wallet_usage', '', $wallet_usage_filter_val, 'number');
                                    ?>
                                 </td>
                                 <td>Safety buffer (85% of capital)</td>
                              </tr>
                              <tr>
                                 <td>Leverage</td>
                                 <td>
                                    <?php
                                    $leverage_filter = get_module_filter($module_name, 'leverage');
                                    $leverage_filter_val = !empty($leverage_filter) ?  $leverage_filter->filter_value : 5;
                                    echo render_input('leverage', '', $leverage_filter_val, 'number');
                                    ?>
                                 </td>
                                 <td>CoinDCX futures leverage</td>
                              </tr>
                              <tr>
                                 <td>Target (<?php echo $base_currency->symbol; ?>)</td>
                                 <td>
                                    <?php
                                    $target_filter = get_module_filter($module_name, 'target');
                                    $target_filter_val = !empty($target_filter) ?  $target_filter->filter_value : 10000000;
                                    echo render_input('target', '', $target_filter_val, 'number');
                                    ?>
                                 </td>
                                 <td>-</td>
                              </tr>
                              <tr>
                                 <td>Day 1 W/D Target</td>
                                 <td class="day_target"></td>
                                 <td>-</td>
                              </tr>
                           </tbody>
                        </table>
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>
</div>

<?php init_tail(); ?>
<script>
   $(document).ready(function() {
      "use strict";
      calculate_day_target();
      load_compounding_tracker_data();
      $("body").on('change', '.config_table input', function() {
         save_compounding_config();
      });
      $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
         load_compounding_tracker_data();
      });
      $("body").on('change', '#actual_closing_input', function() {
          var actual_closing = $(this).val();
          var compounding_date = $(this).data('compounding_date');
          $.post(admin_url + 'purchase/save_compounding_actual_closing', {
            actual_closing: actual_closing,
            compounding_date: compounding_date
          }, function(response){
            var data = JSON.parse(response);
            if(data.status) {
               alert_float('success', data.message);
               load_compounding_tracker_data();
            }
          });
      });
      $("body").on('change', '#notes_input', function() {
          var notes = $(this).val();
          var compounding_date = $(this).data('compounding_date');
          $.post(admin_url + 'purchase/save_compounding_notes', {
            notes: notes,
            compounding_date: compounding_date
          }, function(response){
            var data = JSON.parse(response);
            if(data.status) {
               alert_float('success', data.message);
               load_compounding_tracker_data();
            }
          });
      });
   });

   function calculate_day_target() {
    var starting_capital = parseFloat($('.config_table input[name="starting_capital"]').val()) || 0;
    var daily_return = parseFloat($('.config_table input[name="daily_return"]').val()) || 0;
    var days_per_cycle = parseInt($('.config_table input[name="days_per_cycle"]').val()) || 0;
    var day_target = starting_capital * Math.pow((1 + (daily_return / 100)), days_per_cycle);
    day_target = Math.round(day_target);
    $('.day_target').html(format_money(day_target));
   }

   function save_compounding_config() {
      var starting_capital = $('.config_table input[name="starting_capital"]').val();
      var daily_return = $('.config_table input[name="daily_return"]').val();
      var days_per_cycle = $('.config_table input[name="days_per_cycle"]').val();
      var withdrawal = $('.config_table input[name="withdrawal"]').val();
      var positions_once = $('.config_table input[name="positions_once"]').val();
      var wallet_usage = $('.config_table input[name="wallet_usage"]').val();
      var leverage = $('.config_table input[name="leverage"]').val();
      var target = $('.config_table input[name="target"]').val();
      $.post(admin_url + 'purchase/save_compounding_config', {
         starting_capital: starting_capital,
         daily_return: daily_return,
         days_per_cycle: days_per_cycle,
         withdrawal: withdrawal,
         positions_once: positions_once,
         wallet_usage: wallet_usage,
         leverage: leverage,
         target: target
        }, function(response){
            var data = JSON.parse(response);
            if(data.status) {
               alert_float('success', data.message);
               calculate_day_target();
            }
      });
   }

   function load_compounding_tracker_data() {
      $.post(admin_url + 'purchase/get_compounding_tracker_data', {
        }, function(response){
            var data = JSON.parse(response);
            var tracker_tbody = '';
            if (Array.isArray(data.compounding_tracker) && data.compounding_tracker.length > 0) {
               $.each(data.compounding_tracker, function(i, row){
                  tracker_tbody += '<tr>';
                  tracker_tbody += '<td>'+row.day+'</td>';
                  tracker_tbody += '<td>'+row.date+'</td>';
                  tracker_tbody += '<td>'+row.cycle+'</td>';
                  tracker_tbody += '<td>'+row.day_in_cycle+'</td>';
                  tracker_tbody += '<td>'+format_money(row.plan_opening)+'</td>';
                  tracker_tbody += '<td>'+format_money(row.plan_closing)+'</td>';
                  tracker_tbody += '<td>'+format_money(row.actual_opening)+'</td>';
                  tracker_tbody += '<td>'+format_money(row.actual_pnl)+'</td>';
                  tracker_tbody += '<td>'+row.actual_closing_html+'</td>';
                  tracker_tbody += '<td>'+row.vs_plan+'%</td>';
                  tracker_tbody += '<td>'+format_money(row.fixed_margin)+'</td>';
                  tracker_tbody += '<td>'+format_money(row.wd_target)+'</td>';
                  tracker_tbody += '<td>'+format_money(row.wd_amount)+'</td>';
                  tracker_tbody += '<td>'+row.notes_html+'</td>';
                  tracker_tbody += '</tr>';
               });
            }
            $('.tracker_table tbody').html(tracker_tbody);
      });
   }
</script>
</body>
</html>