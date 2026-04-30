<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
   <div class="content">
      <div class="row">
         <div class="panel_s mbot10">
            <div class="panel-body">
               <div class="row">
                  <div class="col-md-12">
                     <h4 class="no-margin font-bold">
                        <i class="fa fa-line-chart menu-icon"></i>
                        <?php echo _l('compounding_tracker'); ?>
                     </h4>
                     <hr />
                  </div>
                  <div class="col-md-12">
                     <ul class="nav nav-tabs nav-tabs-horizontal mbot15">
                        <?php foreach($tab as $groups){ ?>
                           <li class="<?php echo ($group == $groups ? 'active' : ''); ?>">
                              <a href="<?php echo admin_url('purchase/compounding_tracker?group='.$groups); ?>">
                                 <?php echo _l($groups); ?>
                              </a>
                           </li>
                        <?php } ?>
                     </ul>
                  </div>
                  <div class="col-md-12">
                     <div class="tab-content">
                        <?php $this->load->view($tabs['view']); ?>
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>
</div>

<?php init_tail(); ?>
</body>
</html>
<?php if($group == 'dashboard') { ?>
<?php require 'modules/purchase/assets/js/compounding_tracker/dashboard_js.php'; ?>
<?php } elseif ($group == 'tracker') {
  require 'modules/purchase/assets/js/compounding_tracker/tracker_js.php';
} elseif ($group == 'config') {
  require 'modules/purchase/assets/js/compounding_tracker/config_js.php';
} else {
} ?>