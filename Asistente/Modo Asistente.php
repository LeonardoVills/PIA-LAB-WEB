<?php
$serverName = "Laptop_Villa\\SQLEXPRESS,1433";
$connectionOptions = [
    "Database" => "Fase3",
    "TrustServerCertificate" => true,
    "CharacterSet" => "UTF-8"
];

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['añadir'])) {
    $nombre = $_POST['nombre'];
    $nuevoDato = $_POST['nuevo_dato'];

    $conn = sqlsrv_connect($serverName, $connectionOptions);
    if (!$conn) {
        die(print_r(sqlsrv_errors(), true));
    }

    $query = "SELECT InformacionPaciente FROM Expediente WHERE NombrePaciente = ?";
    $stmt = sqlsrv_query($conn, $query, [$nombre]);
    $infoActual = "";
    if ($stmt && $row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
        $infoActual = $row['InformacionPaciente'];
    }

    $infoActual .= "\n" . $nuevoDato;

    $update = "UPDATE Expediente SET InformacionPaciente = ? WHERE NombrePaciente = ?";
    $stmtUpdate = sqlsrv_query($conn, $update, [$infoActual, $nombre]);

    sqlsrv_close($conn);

    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

$conn = sqlsrv_connect($serverName, $connectionOptions);
if (!$conn) {
    die(print_r(sqlsrv_errors(), true));
}

$expedientes = [];
$sql = "SELECT NombrePaciente, InformacionPaciente FROM Expediente";
$stmt = sqlsrv_query($conn, $sql);
if ($stmt !== false) {
    while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
        $expedientes[] = $row;
    }
}
sqlsrv_close($conn);
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Administrador-Asistente</title>
  <link rel="stylesheet" href="estiloAdministradorExpedientes.css">
  <link rel="stylesheet" href="../Libs/fontawesome-free-6.7.2-web/css/all.min.css"> 
  <link rel="stylesheet" href="../Libs/Bootstrap/bootstrap.min.css">
</head>
<body>
  <nav class="navbar navbar-expand-lg navbar-light bg-light">
    <div class="container-fluid">
      <a class="navbar-brand" href="../Administrador_ModulosPantalla/Pantalla de Modulo Administrador.html"><i class="fa-solid fa-arrow-left"></i></a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <div class="d-flex w-100">
          <div class="d-flex justify-content-center flex-grow-1">
            <ul class="navbar-nav mb-2 mb-lg-0">
              <li class="nav-item"><a class="nav-link" href="../Publico-Inicio/Index.html">Principal</a></li>
              <li class="nav-item"><a class="nav-link" href="../Publico-Nosotros/Pantalla Nosotros.html">Nosotros</a></li>
              <li class="nav-item"><a class="nav-link" href="../Publico-Cuidado/Pantalla Cuidados.html">Cuidado y Consejos</a></li>
              <li class="nav-item"><a class="nav-link" href="../Publico-Servicios/Servicios.html">Servicios</a></li>
              <li class="nav-item"><a class="nav-link" href="../Publico-Citas/Pantalla citas.html">Citas</a></li>
              <li class="nav-item"><a class="nav-link" href="../Publico-Reseñas/Reseñas.html">Reseñas</a></li>
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
  <main class="container mt-4">
    <div class="row">
      <aside class="col-md-3">
        <h5>Filtros Búsqueda:</h5>
        <div class="form-check">
          <input class="form-check-input" type="checkbox" id="chkCitaHoy" checked>
          <label class="form-check-label" for="chkCitaHoy">Cita hoy</label>
        </div>
        <div class="form-check">
          <input class="form-check-input" type="checkbox" id="chkLimpieza" checked>
          <label class="form-check-label" for="chkLimpieza">Limpieza</label>
        </div>
        <div class="form-check">
          <input class="form-check-input" type="checkbox" id="chkConsulta" checked>
          <label class="form-check-label" for="chkConsulta">Consulta</label>
        </div>
        <div class="form-check">
          <input class="form-check-input" type="checkbox" id="chkCaries" checked>
          <label class="form-check-label" for="chkCaries">Caries</label>
        </div>
      </aside>
      <section class="col-md-9">
        <h2>Modo Administrador: Expedientes</h2>
        <?php foreach ($expedientes as $exp): ?>
          <div class="mt-4">
            <h5>Expediente <?= htmlspecialchars($exp['NombrePaciente']) ?></h5>
            <textarea class="form-control" rows="4" readonly><?= htmlspecialchars($exp['InformacionPaciente']) ?></textarea>
            <form method="POST" action="">
              <input type="hidden" name="nombre" value="<?= htmlspecialchars($exp['NombrePaciente']) ?>">
              <div class="input-group mt-2">
                <input type="text" class="form-control" name="nuevo_dato" placeholder="Añadir nuevos datos al expediente" required>
                <button class="btn btn-primary" type="submit" name="añadir">Añadir</button>
              </div>
            </form>
            <button class="btn btn-dark mt-2">Expediente Completo</button>
          </div>
        <?php endforeach; ?>
      </section>
    </div>
  </main>
  <footer>
    <div class="container d-flex flex-column flex-md-row justify-content-between align-items-center">
      <div class="social mb-3 mb-md-0">
        <a href="#" aria-label="Twitter"><i class="fab fa-x"></i></a>
        <a href="#" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
        <a href="#" aria-label="YouTube"><i class="fab fa-youtube"></i></a>
        <a href="#" aria-label="LinkedIn"><i class="fab fa-linkedin"></i></a>
      </div>
      <div class="text-center text-md-end">
        <small class="d-block">&copy; 2025 Todos los derechos reservados</small>
        <div class="footer-links">
          <a href="#">Privacidad</a> /
          <a href="#">Condiciones de Uso</a> /
          <a href="#">Cookies</a>
        </div>
      </div>
    </div>
  </footer>
  <script src="../Libs/Bootstrap/bootstrap.bundle.min.js"></script>
</body>
</html>
