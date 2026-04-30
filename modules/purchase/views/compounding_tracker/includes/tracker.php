<style>
	.show_hide_columns {
		position: absolute;
		z-index: 5000;
		left: 200px
	}
</style>
<div class="row">
	<div class="col-md-12">
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
					'Date',
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