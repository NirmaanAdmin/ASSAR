<script>
	get_compounding_tracker();

	function get_compounding_tracker() {
      $.post(admin_url + 'purchase/get_compounding_tracker', {
        }, function(response){
            var data = JSON.parse(response);
            var tracker_tbody = '';
            if (data.length > 0) {
               $.each(data, function(i, row){
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
                  tracker_tbody += '<td>'+row.daily_return_percent+'%</td>';
                  tracker_tbody += '<td>'+format_money(row.cumulative_pnl)+'</td>';
                  tracker_tbody += '<td>'+row.cum_return_percent+'%</td>';
                  tracker_tbody += '</tr>';
               });
            }
            $('.tracker_table tbody').html(tracker_tbody);
      });
   }

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
           get_compounding_tracker();
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
           get_compounding_tracker();
        }
      });
  });
</script>