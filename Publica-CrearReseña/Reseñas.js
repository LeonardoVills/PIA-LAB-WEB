$(document).ready(function () {
  $('#formResena').on('submit', function (e) {
    let errores = [];

    const calificacion = $('input[name="calificacion"]:checked').val();
    const nombre = $('input[name="nombre"]').val().trim();
    // Comentario puede ser vacío, no se valida
    // const comentario = $('textarea[name="comentario"]').val().trim();

    if (!calificacion) {
      errores.push("Selecciona una calificación.");
    }

    if (nombre.length < 2) {
      errores.push("El nombre debe tener al menos 2 caracteres.");
    }

    if (errores.length > 0) {
      e.preventDefault(); // Detiene el envío
      $('#mensajes').html(errores.map(err => `<p>${err}</p>`).join(""));
    } else {
      $('#mensajes').html(""); // Limpia mensajes si todo está bien
    }
  });
});
