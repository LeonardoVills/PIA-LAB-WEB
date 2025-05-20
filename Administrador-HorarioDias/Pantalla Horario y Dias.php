<?php
// Conexión con autenticación de Windows
$serverName = "LAPT-ACTII\\SQLEXPRESS,1433";
$connectionOptions = [
    "Database" => "Fase3",
    "CharacterSet" => "UTF-8"
];

$conn = sqlsrv_connect($serverName, $connectionOptions);
if (!$conn) {
    die("Error de conexión:<br><pre>" . print_r(sqlsrv_errors(), true) . "</pre>");
}

// INSERTAR NUEVO HORARIO
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nueva_hora'])) {
    $hora = $_POST['nueva_hora'];
    $horaTime = date("H:i:s", strtotime($hora));
    $stmt = sqlsrv_query($conn, "INSERT INTO Horarios (HorarioDisponible) VALUES (?)", [$horaTime]);
    if ($stmt === false) {
        die("Error al insertar horario:<br><pre>" . print_r(sqlsrv_errors(), true) . "</pre>");
    }
}

// ELIMINAR HORARIO
if (isset($_POST['eliminar_hora_id'])) {
    $id = intval($_POST['eliminar_hora_id']);
    $stmt = sqlsrv_query($conn, "DELETE FROM Horarios WHERE IdHora = ?", [$id]);
    if ($stmt === false) {
        die("Error al eliminar horario:<br><pre>" . print_r(sqlsrv_errors(), true) . "</pre>");
    }
}

// INSERTAR NUEVO DÍA NO DISPONIBLE
if (isset($_POST['dia_no_disponible'])) {
    $fecha = DateTime::createFromFormat('d/m/Y', $_POST['dia_no_disponible'])->format('Y-m-d');
    $stmt = sqlsrv_query($conn, "INSERT INTO Dias (DiaNoDisponible) VALUES (?)", [$fecha]);
    if ($stmt === false) {
        die("Error al insertar día no disponible:<br><pre>" . print_r(sqlsrv_errors(), true) . "</pre>");
    }
}

// ELIMINAR DÍA NO DISPONIBLE
if (isset($_POST['eliminar_dia_id'])) {
    $id = intval($_POST['eliminar_dia_id']);
    $stmt = sqlsrv_query($conn, "DELETE FROM Dias WHERE IdDias = ?", [$id]);
    if ($stmt === false) {
        die("Error al eliminar día:<br><pre>" . print_r(sqlsrv_errors(), true) . "</pre>");
    }
}

// CARGAR HORARIOS
$horarios = [];
$result = sqlsrv_query($conn, "
    SELECT 
        IdHora, 
        RIGHT(CONVERT(VARCHAR(20), HorarioDisponible, 100), 7) AS Hora 
    FROM Horarios 
    ORDER BY HorarioDisponible
");
if ($result === false) {
    die("Error al cargar horarios:<br><pre>" . print_r(sqlsrv_errors(), true) . "</pre>");
}
while ($row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC)) {
    $horarios[] = $row;
}

// CARGAR DÍAS NO DISPONIBLES
$dias = [];
$result = sqlsrv_query($conn, "
    SELECT 
        IdDias, 
        CONVERT(VARCHAR, DiaNoDisponible, 103) AS Fecha 
    FROM Dias 
    ORDER BY DiaNoDisponible
");
if ($result === false) {
    die("Error al cargar días:<br><pre>" . print_r(sqlsrv_errors(), true) . "</pre>");
}
while ($row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC)) {
    $dias[] = $row;
}
?>


<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Modo Administrador: Horarios y Días</title>
 
  <link rel="stylesheet" href="../Libs/fontawesome-free-6.7.2-web/css/all.min.css">
  <link rel="stylesheet" href="../Libs/Bootstrap/bootstrap.min.css">
 
  <link rel="stylesheet" href="estilosAdministardorHorarioyDia.css">
</head>
 
<body>
  <nav class="navbar navbar-expand-lg navbar-light bg-light">
    <div class="container-fluid">
      <a class="navbar-brand" href="../Administrador_ModulosPantalla/Pantalla de Modulo Administrador.html"><i class="fa-solid fa-arrow-left"></i></a>
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
 
  <main>
  <div class="main-content">
    <div class="container">
      <h3 class="text-center mb-4">Modo Administrador: Horarios y Días</h3>

      <div class="container configuracion-admin">
        <div class="row">

          <!-- Horarios disponibles -->
          <h5 class="mb-3">Horarios Disponibles</h5>
          <div class="horarios-lista">
            <?php if (empty($horarios)): ?>
              <p class="text-muted">No hay horarios disponibles. Añade uno nuevo.</p>
            <?php else: ?>
              <?php foreach ($horarios as $horario): ?>
                <div class="horario-item">
                   <span><?= htmlspecialchars($horario['Hora']) ?></span>
                  <form method="POST" class="mb-0">
                    <input type="hidden" name="eliminar_hora_id" value="<?= $horario['IdHora'] ?>">
                    <button type="submit" class="btn btn-sm btn-danger">Eliminar</button>
                  </form>
                </div>
              <?php endforeach; ?>
            <?php endif; ?>
          </div>

          <!-- Formulario para añadir nueva hora -->
          <form method="POST" class="d-flex mt-3">
            <input type="text" name="nueva_hora" class="form-control" placeholder="Ej: 12:00 PM" required>
            <button type="submit" class="btn btn-dark ms-2">Añadir</button>
          </form>

          <!-- Botón para modificar horarios -->
          <button class="btn btn-dark mt-4 mx-auto">Modificar Horarios Existentes</button>

          <!-- Días no disponibles -->
          <h5 class="mt-5 mb-3">Días No Disponibles</h5>
          <div class="w-100">
            <?php foreach ($dias as $dia): ?>
              <div class="dia-item">
                <span><?= htmlspecialchars($dia['Fecha']) ?></span>
                <form method="POST" class="mb-0">
                  <input type="hidden" name="eliminar_dia_id" value="<?= $dia['IdDias'] ?>">
                  <button type="submit" class="btn btn-sm btn-danger">Eliminar</button>
                </form>
              </div>
            <?php endforeach; ?>
          </div>

          <!-- Formulario oculto para añadir día no disponible -->
          <form method="POST" id="form-dia-no-disponible" class="d-none mt-4">
            <input type="hidden" name="dia_no_disponible" id="input-dia-seleccionado">
          </form>

          <!-- Datepicker -->
          <div class="mt-4 w-100 text-center">
            <label for="datepicker">Selecciona un día no disponible:</label>
            <input type="text" id="datepicker" class="form-control d-inline-block w-auto" placeholder="dd/mm/aaaa" readonly>
          </div>

          <!-- Botón guardar cambios -->
          <button class="btn btn-dark mt-4 mx-auto">Guardar Cambios</button>
        </div>
      </div>
    </div>
  </div>
</main>

 
  <footer class="mt-5 border-top pt-4 pb-3 bg-light">  
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
  <script src="../Libs/Jquery/jquery-3.7.1.min.js"></script>
  <script src="../Libs/Bootstrap/bootstrap-datepicker.min.js"></script>
  <script src="../Libs/Bootstrap/bootstrap-datepicker.es.min.js"></script>
  <script src="HorariosDias.js"></script>
</body>
</html>
 