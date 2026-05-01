<script>
  var tracker_table;
  tracker_table = $('.tracker_table');
  // On page load, fetch and apply saved preferences for the logged-in user
  $.ajax({
     url: admin_url + 'purchase/getPreferences',
     type: 'GET',
     data: {
        module: 'compounding_tracker'
     },
     dataType: 'json',
     success: function(data) {
        let tracker_table = $('.tracker_table').DataTable();
        $('.toggle-column').each(function() {
           let colIndex = parseInt($(this).val(), 10);
           let prefValue = data.preferences && data.preferences[colIndex] !== undefined ?
              data.preferences[colIndex] :
              "true";
           let isVisible = (typeof prefValue === "string") ?
              (prefValue.toLowerCase() === "true") :
              prefValue;
           tracker_table.column(colIndex).visible(isVisible, false);
           $(this).prop('checked', isVisible);
        });
        tracker_table.columns.adjust().draw();
        let allChecked = $('.toggle-column').length === $('.toggle-column:checked').length;
        $('#select-all-columns').prop('checked', allChecked);
     },
     error: function() {
        console.error('Could not retrieve column preferences.');
     }
  });
  var urlParams = new URLSearchParams(window.location.search);
  var current_phase = parseInt(urlParams.get('phase')) || 1;
  $('#current_phase').val(current_phase);
  var Params = {
    phase: '[name="current_phase"]'
  };
  initDataTable('.tracker_table', admin_url + 'purchase/get_compounding_tracker_table', [], [], Params, [0, 'asc'], false);

  $("body").on('change', '.actual_closing_input', function() {
    var actual_closing = $(this).val();
    var phase = $(this).data('phase');
    var day = $(this).data('day');
    $.post(admin_url + 'purchase/save_compounding_actual_closing', {
      actual_closing: actual_closing,
      phase: phase,
      day: day
    }, function(response){
      var data = JSON.parse(response);
      if(data.status) {
       alert_float('success', data.message);
       tracker_table.DataTable().ajax.reload();
     }
   });
  });
  $("body").on('change', '.notes_input', function() {
    var notes = $(this).val();
    var phase = $(this).data('phase');
    var day = $(this).data('day');
    $.post(admin_url + 'purchase/save_compounding_notes', {
      notes: notes,
      phase: phase,
      day: day
    }, function(response){
      var data = JSON.parse(response);
      if(data.status) {
       alert_float('success', data.message);
       tracker_table.DataTable().ajax.reload();
     }
   });
  });

  $('#select-all-columns').on('change', function() {
   var isChecked = $(this).is(':checked');
   $('.toggle-column').prop('checked', isChecked).trigger('change');
  });

  $('.toggle-column').on('change', function() {
   var column = tracker_table.DataTable().column($(this).val());
   column.visible($(this).is(':checked'));
   var allChecked = $('.toggle-column').length === $('.toggle-column:checked').length;
   $('#select-all-columns').prop('checked', allChecked);
   saveColumnPreferences();
  });

  tracker_table.DataTable().columns().every(function(index) {
   var column = this;
   $('.toggle-column[value="' + index + '"]').prop('checked', column.visible());
  });

  $('.dropdown-menu').on('click', function(e) {
   e.stopPropagation();
  });

  tracker_table.on('draw.dt', function () {
   $('.selectpicker').selectpicker('refresh');
  });

  function saveColumnPreferences() {
   var preferences = {};
   $('.toggle-column').each(function() {
    preferences[$(this).val()] = $(this).is(':checked');
   });
   $.ajax({
    url: admin_url + 'purchase/savePreferences',
    type: 'POST',
    data: {
     preferences: preferences,
     module: 'compounding_tracker'

   },
   success: function(response) {
     console.log('Preferences saved successfully.');
   },
   error: function() {
     console.error('Failed to save preferences.');
   }
  });
 }

 $(document).on('click', '.delete_phase', function(e) {
   e.preventDefault();
   var url = $(this).attr('href');
   if(confirm('Are you sure you want to delete this compounding phase?')){
      window.location.href = url;
   }
 });
</script>