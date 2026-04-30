<script>
	calculate_day_target();

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

	$("body").on('change', '.config_table input', function() {
		save_compounding_config();
	});
</script>