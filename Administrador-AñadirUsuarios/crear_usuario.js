document.getElementById('tarjetaDatos').addEventListener('submit', function(event) {
  event.preventDefault(); 

  const form = this;
  const formData = new FormData(form);

  fetch(form.action, {
    method: form.method,
    body: formData,
  })
  .then(response => response.text())
  .then(data => {
    alert("✅ Usuario creado exitosamente.");
    form.reset(); 
  })
  .catch(error => {
    alert("❌ Error al crear usuario. Intenta nuevamente.");
    console.error(error);
  });
});