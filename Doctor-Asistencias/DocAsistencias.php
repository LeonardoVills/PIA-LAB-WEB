<?php
// Conexión con autenticación de Windows
$serverName = "LAPT-ACTII\\SQLEXPRESS,1433";
$connectionOptions = [
    "Database" => "Fase3",
    "UID" => "",
    "PWD" => "",
    "CharacterSet" => "UTF-8"
];
$conn = sqlsrv_connect($serverName, $connectionOptions);
if (!$conn) {
    die(print_r(sqlsrv_errors(), true));
}

// Procesar actualizaciones de asistencias
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['asistencias'])) {
    foreach ($_POST['asistencias'] as $idCita => $valor) {
        $asistio = ($valor == "1") ? 1 : 0;
        $sqlUpdate = "UPDATE Citas SET Asistió = ? WHERE IdCitas = ?";
        $params = [$asistio, $idCita];
        sqlsrv_query($conn, $sqlUpdate, $params);
    }
}

// Obtener las citas de hoy (después del POST también)
$hoy = date("Y-m-d");
$sql = "
    SELECT c.IdCitas, h.HorarioDisponible, cl.NombreCliente, c.Asistió
    FROM Citas c
    INNER JOIN Clientes cl ON c.CURPCliente = cl.CURPCliente
    INNER JOIN Horarios h ON c.HoraSolicitada = h.IdHora
    WHERE c.Fecha = ? AND c.EstaAprobada = 1
    ORDER BY h.HorarioDisponible
";
$stmt = sqlsrv_query($conn, $sql, [$hoy]);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Asistencias del Día</title>
    <link rel="stylesheet" href="../Libs/Bootstrap/bootstrap.min.css">
    <link rel="stylesheet" href="../Libs/fontawesome-free-6.7.2-web/css/all.css">
    <link rel="stylesheet" href="DocAsistencias.css">
</head>
<body>
  <nav class="navbar navbar-expand-lg navbar-light bg-light shadow-sm">
    <div class="container">
      <a class="navbar-brand" href="../Doctor-ModuloPantalla/Pantalla Modo Doctor.html"><i class="fa-solid fa-arrow-left"></i></a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent"
        aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <div class="d-flex w-100">
          <div class="d-flex justify-content-center flex-grow-1">
            <ul class="navbar-nav mb-2 mb-lg-0">
              <li class="nav-item"><a class="nav-link" aria-current="page" href="../Publico-Inicio/Index.html">Principal</a></li>
              <li class="nav-item"><a class="nav-link" href="../Publico-Nosotros/Pantalla Nosotros.html">Nosotros</a></li>
              <li class="nav-item"><a class="nav-link" href="../Publico-Cuidado/Pantalla Cuidados.html">Cuidado y Consejos</a></li>
              <li class="nav-item"><a class="nav-link" href="../Publico-Servicios/Servicios.php">Servicios</a></li>
              <li class="nav-item"><a class="nav-link" href="../Publico-Citas/Pantalla citas.html">Citas</a></li>
              <li class="nav-item"><a class="nav-link" href="../Publico-Reseñas/Reseñas.php">Reseñas</a></li>
            </ul>
          </div>
          <div class="d-flex justify-content-end">
            <ul class="navbar-nav mb-2 mb-lg-0">
              <li class="nav-item"><a class="nav-link" href="../Publico-Contacto/Pantalla Contacto.html">Contacto</a></li>
              <li class="nav-item"><a class="nav-link active" href="../Publico_Login/Login.html">Empleados</a></li>
            </ul>
          </div>
        </div>
      </div>
    </div>
  </nav>
    <main class="container my-4">
        <h1 class="text-center mb-4">Modo Doctor: Asistencias del Día</h1>
        <form method="POST">
            <?php while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)): ?>
              <article class="d-flex align-items-center justify-content-between border p-3 mb-2 rounded">
                  <p class="mb-0 fw-bold">
                      <?= htmlspecialchars($row['NombreCliente']) ?> - <?= $row['HorarioDisponible']->format('H:i') ?> hrs
                  </p>
                  <div class="form-check">
                      <!-- input oculto para garantizar envío de "0" si no se marca -->
                      <input type="hidden" name="asistencias[<?= $row['IdCitas'] ?>]" value="0">
                      <input class="form-check-input" type="checkbox"
                          name="asistencias[<?= $row['IdCitas'] ?>]"
                          id="asistencia<?= $row['IdCitas'] ?>"
                          value="1" <?= $row['Asistió'] ? 'checked' : '' ?>>
                      <label class="form-check-label" for="asistencia<?= $row['IdCitas'] ?>">
                          Asistió
                      </label>
                  </div>
              </article>
            <?php endwhile; ?>
            <div class="d-flex justify-content-end">
                <button type="submit" class="btn btn-success mt-3">Guardar cambios</button>
            </div>
        </form>
    </main>

  </div>
    <footer>
      <div class="container d-flex flex-column flex-md-row justify-content-between align-items-center">
        <div class="social mb-2 mb-md-0">
          <a href="#"><i class="fab fa-x"></i></a>
          <a href="#"><i class="fab fa-instagram"></i></a>
          <a href="#"><i class="fab fa-youtube"></i></a>
          <a href="#"><i class="fab fa-linkedin"></i></a>
        </div>
        <div class="text-center text-md-end">
          <small>&copy; 2025 Todos los derechos reservados</small>
          <div class="footer-links">
            <a href="#">Privacidad</a> /
            <a href="#">Condiciones de Uso</a> /
            <a href="#">Cookies</a>
          </div>
        </div>
      </div>
    </footer>
  </div>
</body>
</html>