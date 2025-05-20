$(document).ready(function () {
      $('#datepicker').datepicker({
        format: 'dd/mm/yyyy',
        todayHighlight: true,
        language: 'es',
        autoclose: true
}).on('changeDate', function(e) {
  const fecha = $('#datepicker').datepicker('getFormattedDate');
  $('#input-dia-seleccionado').val(fecha);
  $('#form-dia-no-disponible').submit();
})

});