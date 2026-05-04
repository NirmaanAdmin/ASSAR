<style>
	.phase_btn {
		background-color: #fff !important;
		color: #0284C7 !important;
		border: 1px solid #0284C7 !important;
		transition: all 0.2s ease-in-out;
	}
	.phase_btn:hover {
		background-color: #E0F2FE !important;
		color: #0284C7 !important;
		border-color: #0284C7 !important;
	}
	.phase_btn.active {
		background-color: #0284C7 !important;
		color: #fff !important;
		border-color: #0284C7 !important;
	}
	.phase-wrapper {
		position: relative;
		display: inline-block;
		margin-right: 10px;
		margin-bottom: 10px;
	}
	.phase-wrapper .phase_btn {
		min-width: 165px;
		padding-right: 65px;
		text-align: left;
	}
	.edit_phase {
		position: absolute;
		top: 50%;
		right: 32px;
		transform: translateY(-50%);
		font-size: 13px;
		color: #0284C7;
		z-index: 10;
		text-decoration: none;
	}
	.edit_phase:hover {
		color: #0369A1;
	}
	.delete_phase {
		position: absolute;
		top: 50%;
		right: 10px;
		transform: translateY(-50%);
		font-size: 12px;
		text-decoration: none;
		line-height: 1;
		color: #0284C7;
		z-index: 10;
	}
	.delete_phase:hover {
		color: #dc2626;
	}
	.phase-wrapper .phase_btn.active ~ .edit_phase,
	.phase-wrapper .phase_btn.active ~ .delete_phase {
		color: #fff;
	}
	.phase-wrapper .phase_btn.active ~ .delete_phase:hover {
		color: #ffd6d6;
	}
	.show_hide_columns {
		position: absolute;
		z-index: 5000;
		left: 200px;
	}
</style>
<?php
$current_phase = $this->input->get('phase');
$current_phase = !empty($current_phase) ? $current_phase : 1;
?>
<div class="row">
	<div class="col-md-12 mtop10">
		<?php foreach($phases as $key => $phase){ ?>
			<div class="phase-wrapper">
				<a href="<?php echo admin_url('purchase/compounding_tracker?group='.$group.'&phase='.$phase['id']); ?>"
				   class="btn btn-info phase_btn <?php echo ($phase['id'] == $current_phase ? 'active' : ''); ?>">
					<?php echo $phase['name']; ?>
				</a>
				<a href="#"
				   class="edit_phase"
				   onclick="edit_compounding_phase(this, <?php echo $phase['id']; ?>); return false;"
				   data-name="<?php echo htmlspecialchars($phase['name'], ENT_QUOTES); ?>"
				   title="Edit Phase">
					<i class="fa fa-pencil-square"></i>
				</a>
				<?php if($key > 0){ ?>
					<a href="<?php echo admin_url('purchase/delete_compounding_phase/'.$phase['id']); ?>"
					   class="delete_phase"
					   title="Delete Phase">
						<i class="fa fa-times"></i>
					</a>
				<?php } ?>
			</div>
		<?php } ?>
		<a href="#" onclick="new_compounding_phase(); return false;"
		   class="btn btn-info pull-right display-block">
			<i class="fa fa-plus"></i> Add phase
		</a>
	</div>
</div>
<div class="row">
	<div class="col-md-12 mtop20">
		<input type="hidden" name="current_phase" id="current_phase" value="1">
		<div class="btn-group show_hide_columns" id="show_hide_columns">
			<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="padding: 4px 7px;">
				<i class="fa fa-cog"></i> <?php  ?> <span class="caret"></span>
			</button>
			<div class="dropdown-menu" style="padding: 10px; min-width: 250px;">
				<div>
					<input type="checkbox" id="select-all-columns"> <strong><?php echo _l('Select all'); ?></strong>
				</div>
				<hr>
				<?php
				$columns = [
					'Day',
					'Cycle',
					'Day in Cycle',
					'Plan Opening',
					'Plan Closing',
					'Actual Opening',
					'Actual P&L',
					'Actual Closing',
					'vs Plan %',
					'FIXED_MARGIN_INR',
					'Daily Return %',
					'Cum. P&L',
					'Cum. Return %',
					'W/D Target',
					'W/D Amount',
					'Notes',
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
		<table class="table tracker_table">
			<thead>
				<tr>
					<th><?php echo _l('Day'); ?></th>
					<th><?php echo _l('Cycle'); ?></th>
					<th><?php echo _l('Day in Cycle'); ?></th>
					<th><?php echo _l('Plan Opening'); ?></th>
					<th><?php echo _l('Plan Closing'); ?></th>
					<th><?php echo _l('Actual Opening'); ?></th>
					<th><?php echo _l('Actual P&L'); ?></th>
					<th><?php echo _l('Actual Closing'); ?></th>
					<th><?php echo _l('vs Plan %'); ?></th>
					<th><?php echo _l('FIXED_MARGIN_INR'); ?></th>
					<th><?php echo _l('Daily Return %'); ?></th>
					<th><?php echo _l('Cum. P&L'); ?></th>
					<th><?php echo _l('Cum. Return %'); ?></th>
					<th><?php echo _l('W/D Target'); ?></th>
					<th><?php echo _l('W/D Amount'); ?></th>
					<th><?php echo _l('Notes'); ?></th>
				</tr>
			</thead>
		</table>
	</div>
</div>

<div class="modal fade" id="compounding_phase_model" tabindex="-1" role="dialog">
	<div class="modal-dialog">
		<?php echo form_open_multipart(admin_url('purchase/compounding_phase'), array('id'=>'add_compounding_phase')); ?>
		<?php echo form_hidden('phase_id'); ?>
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title">
					<span class="add-title"><?php echo _l('Add phase'); ?></span>
					<span class="edit-title"><?php echo _l('Edit phase'); ?></span>
				</h4>
			</div>
			<div class="modal-body">
				<div class="row">
					<div class="col-md-12">
						<div class="form">
							<?php echo render_input('name', 'Name', ''); ?>
						</div>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
				<button type="submit" class="btn btn-info"><?php echo _l('submit'); ?></button>
			</div>
		</div>
		<?php echo form_close(); ?>
	</div>
</div>