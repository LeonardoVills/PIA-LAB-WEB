  const form = document.querySelector('form');

  form.addEventListener('submit', function(event) {
    event.preventDefault();

    const formData = new FormData(form);

    fetch(form.action, {
      method: form.method,
      body: formData
    })
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        alert("✅ Cita registrada exitosamente");
        form.reset();
      } else {
        alert("❌ Error al registrar la cita");
      }
    })
    .catch(() => {
      alert("❌ Error de comunicación con el servidor");
    });
  });