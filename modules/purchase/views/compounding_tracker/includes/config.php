<?php $module_name = 'compounding_tracker'; ?>
<div class="row">
   <div class="col-md-12">
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