<?php
$serverName = "Laptop_Villa\\SQLEXPRESS,1433";
$connectionOptions = [
    "Database" => "Fase3",
    "TrustServerCertificate" => true,
    "CharacterSet" => "UTF-8"
];

$conn = sqlsrv_connect($serverName, $connectionOptions);

if (!$conn) {
    die(print_r(sqlsrv_errors(), true));
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['idServicio'])) {
    $idServicio = intval($_POST['idServicio']);
    $nombreServicio = $_POST['nombreServicio'] ?? '';
    $descripcionServicio = $_POST['descripcionServicio'] ?? '';
    $tiempoAprox = $_POST['tiempoAprox'] ?? '';
    $precioServicio = floatval($_POST['precioServicio'] ?? 0);
    $estaActivo = isset($_POST['estaActivo']) ? 1 : 0;
    $updateSql = "UPDATE Servicio SET 
        NombreServicio = ?, 
        DescripcionServicio = ?, 
        TiempoAprox = ?, 
        PrecioServicio = ?, 
        EstaActivo = ? 
        WHERE IdServicio = ?";
    $params = [
        $nombreServicio,
        $descripcionServicio,
        $tiempoAprox,
        $precioServicio,
        $estaActivo,
        $idServicio
    ];

    $stmt = sqlsrv_query($conn, $updateSql, $params);
    if ($stmt === false) {
        die(print_r(sqlsrv_errors(), true));
    } else {
        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    }
}
$servicios = [];
$sql = "SELECT IdServicio, NombreServicio, DescripcionServicio, TiempoAprox, PrecioServicio, EstaActivo FROM Servicio";
$stmt = sqlsrv_query($conn, $sql);
if ($stmt === false) {
    die(print_r(sqlsrv_errors(), true));
}
while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
    if ($row['TiempoAprox'] instanceof DateTime) {
        $row['TiempoAprox'] = $row['TiempoAprox']->format('H:i:s');
    }
    $row['EstaActivo'] = intval($row['EstaActivo']);
    $servicios[] = $row;
}
sqlsrv_close($conn);
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Modo Administrador: Servicios</title>
  <link rel="stylesheet" href="estiloAdministradorServicios.css" />
  <link rel="stylesheet" href="../Libs/fontawesome-free-6.7.2-web/css/all.min.css" />
  <link rel="stylesheet" href="../Libs/Bootstrap/bootstrap.min.css" />
</head>
<body><nav class="navbar navbar-expand-lg navbar-light bg-light">
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
              <li class="nav-item"><a class="nav-link" href="../Publico_Login/Login.html">Empleados</a></li>
            </ul>
          </div>
        </div>
      </div>
    </div>
  </nav>

<div class="mb-3 text-start">
  <button class="btn btn-dark" style="transform: translate(230px, 80px);">Añadir Servicio</button>
</div>
<?php foreach ($servicios as $servicio): ?>
  <form method="post" class="admin-container p-4 border rounded mt-4 mx-auto" style="max-width: 600px;">
    <div class="title fw-bold fs-5 mb-3">Modo Administrador : Servicios</div>

    <input type="hidden" name="idServicio" value="<?= $servicio['IdServicio'] ?>" />

    <div class="mb-3">
      <label class="form-label" for="nombreServicio<?= $servicio['IdServicio'] ?>">Nombre del Servicio</label>
      <input type="text" id="nombreServicio<?= $servicio['IdServicio'] ?>" name="nombreServicio" class="form-control" value="<?= htmlspecialchars(trim($servicio['NombreServicio'])) ?>" required />
    </div>

    <div class="mb-3">
      <label class="form-label" for="precioServicio<?= $servicio['IdServicio'] ?>">Precio</label>
      <input type="number" step="0.01" min="0" id="precioServicio<?= $servicio['IdServicio'] ?>" name="precioServicio" class="form-control" value="<?= htmlspecialchars(trim($servicio['PrecioServicio'])) ?>" required />
    </div>

    <div class="mb-3">
      <label class="form-label" for="tiempoServicio<?= $servicio['IdServicio'] ?>">Tiempo (HH:MM:SS)</label>
      <input type="time" step="1" id="tiempoServicio<?= $servicio['IdServicio'] ?>" name="tiempoAprox" class="form-control" value="<?= htmlspecialchars($servicio['TiempoAprox']) ?>" required />
    </div>

    <div class="mb-3">
      <label class="form-label" for="descripcionServicio<?= $servicio['IdServicio'] ?>">Descripción</label>
      <textarea id="descripcionServicio<?= $servicio['IdServicio'] ?>" name="descripcionServicio" class="form-control" rows="3" required><?= htmlspecialchars(trim($servicio['DescripcionServicio'])) ?></textarea>
    </div>

    <div class="form-check form-switch mb-3">
      <input class="form-check-input" type="checkbox" id="estaActivo<?= $servicio['IdServicio'] ?>" name="estaActivo" <?= ($servicio['EstaActivo'] == 1) ? 'checked' : '' ?> />
      <label class="form-check-label" for="estaActivo<?= $servicio['IdServicio'] ?>">Activo</label>
    </div>

    <div class="mb-3">
      <button type="submit" class="btn btn-dark w-100">Guardar Cambios</button>
    </div>
  </form>
<?php endforeach; ?>
 <footer class=" border-top pt-4 pb-3">  
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
