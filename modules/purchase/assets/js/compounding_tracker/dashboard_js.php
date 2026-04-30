<script>
	get_compounding_dashboard();

	function get_compounding_dashboard() {
      $.post(admin_url + 'purchase/get_compounding_dashboard', {
        }, function(response){
            var data = JSON.parse(response);

            $('.current_balance_dashboard').html(format_money(data.current_balance_dashboard));
            $('.days_elapsed_dashboard').html(data.days_elapsed_dashboard);
            $('.current_cycle_dashboard').html(data.current_cycle_dashboard);
            $('.total_withdrawn_dashboard').html(format_money(data.total_withdrawn_dashboard));
            $('.plan_balance_dashboard').html(format_money(data.plan_balance_dashboard));
            $('.vs_plan_dashboard').html(data.vs_plan_dashboard+'%');
            $('.target_dashboard').html(format_money(data.target_dashboard));
            $('.distance_target_dashboard').html(format_money(data.distance_target_dashboard));
            $('.railway_tomorrow_morning_dashboard').html(format_money(data.railway_tomorrow_morning_dashboard));
            $('.today_pnl_dashboard').html(format_money(data.today_pnl_dashboard));
            $('.today_return_dashboard').html(data.today_return_dashboard+'%');
            $('.yesterday_pnl_dashboard').html(format_money(data.yesterday_pnl_dashboard));
            $('.yesterday_return_dashboard').html(data.yesterday_return_dashboard+'%');
            $('.total_profit_dashboard').html(format_money(data.total_profit_dashboard));
            $('.overall_return_dashboard').html(data.overall_return_dashboard+'%');

            var cycle_summary_tbody = '';
            if (Array.isArray(data.cycle_summary) && data.cycle_summary.length > 0) {
               $.each(data.cycle_summary, function(i, row){
                  cycle_summary_tbody += '<tr>';
                  cycle_summary_tbody += '<td>'+row.cycle+'</td>';
                  cycle_summary_tbody += '<td>'+row.start_day+'</td>';
                  cycle_summary_tbody += '<td>'+row.end_day+'</td>';
                  cycle_summary_tbody += '<td>'+format_money(row.start_bal)+'</td>';
                  cycle_summary_tbody += '<td>'+format_money(row.end_bal)+'</td>';
                  cycle_summary_tbody += '<td>'+format_money(row.cycle_wd_amount)+'</td>';
                  cycle_summary_tbody += '</tr>';
               });
            }
            $('.cycle_summary_table tbody').html(cycle_summary_tbody);
      });
   }
</script>