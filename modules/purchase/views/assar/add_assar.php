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
                    <div  style="margin-top:6px;">
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
              <div class="col-md-6" style="clear: both;">
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

            </div>

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