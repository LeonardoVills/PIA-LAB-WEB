document.getElementById('tarjetaDatos').addEventListener('submit', function(event) {
  event.preventDefault();

  const form = this;
  const formData = new FormData(form);

  fetch(form.action, {
    method: form.method,
    body: formData
  })
  .then(response => response.json())
  .then(data => {
    alert(data.message);
    if (data.success) {
      form.reset();
    }
  })
  .catch(error => {
    alert("❌ Error al crear usuario. Intenta nuevamente.");
    console.error(error);
  });
});
