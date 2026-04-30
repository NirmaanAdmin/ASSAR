<script>
  var tracker_table;
  tracker_table = $('.tracker_table');
  initDataTable('.tracker_table', admin_url + 'purchase/get_compounding_tracker_table', [], [], {}, [0, 'asc'], false);

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
       tracker_table.DataTable().ajax.reload();
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
       tracker_table.DataTable().ajax.reload();
     }
   });
  });
</script>