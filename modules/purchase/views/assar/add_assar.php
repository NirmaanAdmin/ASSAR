<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>

<div id="wrapper">
  <div class="content">
    <div class="row">

      <?php echo form_open($this->uri->uri_string(), ['id' => 'client-form']); ?>

      <div class="col-md-12">
        <div class="panel_s">
          <div class="panel-body">

            <h4 class="mbot20">Add New Client</h4>

            <div class="row">

              <!-- Client ID -->
              <div class="col-md-6">
                <?php
                $client_id = (isset($assar) && $assar['client_id'] != '') ? $assar['client_id'] : '';
                echo render_input('client_id', 'Client ID', $client_id); ?>
              </div>

              <!-- Name -->
              <div class="col-md-6">
                <?php
                $name = (isset($assar) && $assar['name'] != '') ? $assar['name'] : '';
                echo render_input('name', 'Name', $name); ?>
              </div>

              <!-- Phone -->
              <div class="col-md-6">
                <?php
                $phone = (isset($assar) && $assar['phone'] != '') ? $assar['phone'] : '';
                echo render_input('phone', 'Phone', $phone, 'text'); ?>
              </div>

              <!-- Start Date -->
              <div class="col-md-6">
                <?php
                $start_date = (isset($assar) && $assar['start_date'] != '') ? $assar['start_date'] : '';
                echo render_date_input('start_date', 'Start Date', $start_date); ?>
              </div>

              <!-- Investment -->
              <div class="col-md-6">
                <?php
                $investment = (isset($assar) && $assar['investment'] != '') ? $assar['investment'] : '';
                echo render_input('investment', 'Investment', $investment, 'number'); ?>
              </div>

              <div class="col-md-6">
                <div class="row">
                  <!-- Month -->
                  <div class="col-md-6">
                    <label for="month">Month</label>
                    <select name="month" id="month" class="form-control selectpicker">
                      <option value="">Select Month</option>
                      <?php
                      $start    = new DateTime('2025-09-01');
                      $end      = new DateTime('2028-09-01');
                      $current  = date('Y-m'); // current month

                      // If editing and we have monthly investments, get the latest month
                      $selected_month = $current;
                      $monthly_investment_value = '';

                      if (isset($assar['id']) && !empty($monthly_investments)) {
                        $latest_investment = $monthly_investments[0];
                        $selected_month = $latest_investment['month'];
                        $monthly_investment_value = $latest_investment['monthly_investment'];
                      }

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

                  <!-- Monthly Investment -->
                  <div class="col-md-6">
                    <?php
                    $monthly_investment = (isset($monthly_investment_value) && $monthly_investment_value != '')
                      ? $monthly_investment_value
                      : '';
                    echo render_input('monthly_investment', 'Monthly Investment', $monthly_investment, 'text'); ?>
                  </div>

                </div>
              </div>

              <!-- Status -->
              <div class="col-md-3">
                <label>Status</label>
                <?php
                $status = (isset($assar) && $assar['status'] != '') ? $assar['status'] : '';
                ?>
                <select name="status" class="selectpicker" data-width="100%">
                  <option value="1" <?php echo ($status == 1) ? 'selected' : ''; ?>>Active</option>
                  <option value="0" <?php echo ($status == 0) ? 'selected' : ''; ?>>Inactive</option>
                </select>
              </div>

              <!-- commission -->
              <div class="col-md-3">
                <label>Commission</label>
                <div class="row">
                  <!-- Commission checkbox -->
                  <div class="col-md-4">
                    <div style="margin-top:6px;">
                      <input type="hidden" name="commission" value="0">
                      <label>
                        <input type="checkbox" name="commission" value="1"
                          <?php echo (!empty($assar) && (int)$assar['commission'] === 1) ? 'checked' : ''; ?>>
                        Commission
                      </label>
                    </div>
                  </div>

                  <!-- Staff dropdown -->
                  <div class="col-md-8">
                    <select
                      name="commission_staff[]"
                      class="selectpicker"
                      multiple
                      data-width="100%"
                      data-live-search="true"
                      data-max-options="3"
                      title="Staff (max 3)">
                      <?php
                      $selected_staff = !empty($assar['commission_staff'])
                        ? json_decode($assar['commission_staff'], true)
                        : [];

                      foreach ($client_list as $client) { ?>
                        <option value="<?php echo $client['id']; ?>"
                          <?php echo in_array($client['id'], $selected_staff ?? []) ? 'selected' : ''; ?>>
                          <?php echo $client['name']; ?>
                        </option>
                      <?php } ?>
                    </select>
                  </div>
                </div>
              </div>

              <!-- Referred By -->
              <div class="col-md-6">
                <?php
                $referred_by = (isset($assar) && $assar['refferred_by'] != '') ? $assar['refferred_by'] : '';
                echo render_input('refferred_by', 'Referred By', $referred_by); ?>
              </div>

              <!-- Remarks -->
              <div class="col-md-6">
                <?php
                $remarks = (isset($assar) && $assar['remarks'] != '') ? $assar['remarks'] : '';
                echo render_input('remarks', 'Remarks', $remarks); ?>
              </div>

              <div class="col-md-6">
                <div class="row">
                  <!-- Month -->
                  <div class="col-md-6">
                    <label for="month">Select Month To Increase or Decrease</label>
                    <select name="month_increase" id="month" class="form-control selectpicker">
                      <option value="">Select Month</option>
                      <?php
                      $start    = new DateTime('2025-09-01');
                      $end      = new DateTime('2028-09-01');
                      $current  = date('Y-m'); // current month

                      // If editing and we have monthly investments, get the latest month
                      $selected_month = $current;
                      $monthly_increase_desc_amount = '';

                      if (isset($assar['id']) && !empty($monthly_increase)) {
                        $latest_investment = $monthly_increase[0];
                        $selected_month = $latest_investment['month'];
                        $monthly_increase_desc_amount = $latest_investment['increase_desc_amount'];
                      }

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

                  <!-- Monthly Investment -->
                  <div class="col-md-6">
                    <?php
                    $monthly_increase_amount = (isset($monthly_increase_desc_amount) && $monthly_increase_desc_amount != '')
                      ? $monthly_increase_desc_amount
                      : '';
                    echo render_input('increase_desc_amount', 'Increase Descrease Amount', $monthly_increase_amount, 'text'); ?>
                  </div>

                </div>
              </div>

            </div>

            <!-- Show Monthly Investments History if editing -->
            <?php if (isset($assar['id']) && !empty($monthly_investments)): ?>
              <div class="row mtop20">
                <div class="col-md-12">
                  <h4>Monthly Investment Pattern</h4>
                  <div class="table-responsive">
                    <table class="table">
                      <thead>
                        <tr>
                          <th>Month</th>
                          <th>Investment</th>
                          <th>Created At</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php foreach ($monthly_investments as $investment): ?>
                          <tr>
                            <td><?php echo date('F Y', strtotime($investment['month'] . '-01')); ?></td>
                            <td><?php echo app_format_money($investment['monthly_investment'], ''); ?></td>
                            <td><?php echo date('d M, Y', strtotime($investment['created_at'])); ?></td>
                          </tr>
                        <?php endforeach; ?>
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>
            <?php endif; ?>

            <!-- Submit Button -->
            <div class="text-right mtop20">
              <button type="submit" class="btn btn-info">
                Save
              </button>
            </div>

          </div>
        </div>
      </div>

      <?php echo form_close(); ?>

    </div>
  </div>
</div>

<?php init_tail(); ?>
</body>

</html>