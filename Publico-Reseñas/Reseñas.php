<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

$serverName = "Laptop_Villa\\SQLEXPRESS,1433";
$connectionInfo = [
    "Database" => "Fase3",
    "TrustServerCertificate" => true
];
$conn = sqlsrv_connect($serverName, $connectionInfo);

if (!$conn) {
    die("Conexión fallida: " . print_r(sqlsrv_errors(), true));
}

$sql = "SELECT Calificacion, Comentario, FechaComentario, NombrePersona FROM Calificaciones ORDER BY FechaComentario DESC";
$stmt = sqlsrv_query($conn, $sql);

if ($stmt === false) {
    die("Error en la consulta: " . print_r(sqlsrv_errors(), true));
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Reseñas</title>
  <link rel="stylesheet" href="../Libs/General CSS/General.css">
  <link rel="stylesheet" href="Reseñas.css">
  <link rel="stylesheet" href="../Libs/fontawesome-free-6.7.2-web/css/all.min.css">
  <link rel="stylesheet" href="../Libs/Bootstrap/bootstrap.min.css">
</head>
<body>
  <nav class="navbar navbar-expand-lg navbar-light bg-light">
  <div class="container-fluid">
    <a class="navbar-brand" href="#"></a>
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
              <li class="nav-item"><a class="nav-link active" href="../Publico-Servicios/Servicios.html">Servicios</a></li>
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

<main>
<?php while ($fila = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)): ?>
  <article>
    <div class="R1">
      <h2>Reseña</h2>
      <span>
        <?php
        for ($i = 1; $i <= 5; $i++) {
            echo $i <= $fila['Calificacion'] ? '★' : '☆';
        }
        ?>
      </span>
      <p><strong><?= htmlspecialchars($fila['NombrePersona']) ?></strong></p>
      <p><?= $fila['FechaComentario']->format('Y-m-d') ?></p>
    </div>
    <div class="R2">
      <h4>Comentarios Adicionales</h4>
      <p><?= nl2br(htmlspecialchars($fila['Comentario'])) ?></p>
    </div>
  </article>
<?php endwhile; ?>

  <div id="btnAqui">
    <a href="../Publica-CrearReseña/CrearReseña.html" id="btnCrear" class="btn btn-primary btn-lg">
      ¿Ya has sido atendido?<br />¡Déjanos tus comentarios!
    </a>
  </div>
</main>

<?php
sqlsrv_free_stmt($stmt);
sqlsrv_close($conn);
?>

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
