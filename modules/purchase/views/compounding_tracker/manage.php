<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<style>
   .compounding_btn {
      background-color: #fff !important;
      color: #0284C7 !important;
      border: 1px solid #0284C7 !important;
   }
   .compounding_btn:hover {
      background-color: #E0F2FE !important;
      color: #0284C7 !important;
      border-color: #0284C7 !important;
   }
   .compounding_btn.active {
      background-color: #0284C7 !important;
      color: #fff !important;
      border-color: #0284C7 !important;
   }
</style>
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
                  <div class="col-md-12 mbot5">
                     <?php foreach($tab as $groups){ ?>
                       <a href="<?php echo admin_url('purchase/compounding_tracker?group='.$groups); ?>" class="btn btn-info pull-left mright10 compounding_btn <?php echo ($group == $groups ? 'active' : ''); ?>">
                          <?php echo _l($groups); ?>
                       </a>
                     <?php } ?>
                  </div>
                  <div class="col-md-12">
                     <hr />
                  </div>
                  <div class="col-md-12">
                     <?php $this->load->view($tabs['view']); ?>
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