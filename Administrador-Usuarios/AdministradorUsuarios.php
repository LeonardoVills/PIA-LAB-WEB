<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$serverName = "Laptop_Villa\\SQLEXPRESS,1433";
$connectionInfo = ["Database" => "Fase3", "TrustServerCertificate" => true];
$conn = sqlsrv_connect($serverName, $connectionInfo);

if (!$conn) {
    die("Error de conexión con la base de datos.");
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (!empty($_POST['eliminar'])) {
        foreach ($_POST['eliminar'] as $idEliminar) {
            $sqlDelete = "DELETE FROM Usuarios WHERE IdUsuario = ?";
            sqlsrv_query($conn, $sqlDelete, [$idEliminar]);
        }
    }

    if (!empty($_POST['guardar']) && !empty($_POST['idUsuario'])) {
        foreach ($_POST['idUsuario'] as $index => $idUsuario) {
            $nombre = $_POST['nombreUsuario'][$index] ?? '';
            $clave = $_POST['claveUsuario'][$index] ?? '';
            $idRol = $_POST['idRol'][$index] ?? '';

            $sqlUpdate = "UPDATE Usuarios SET NombreUsuario = ?, ClaveUsuario = ?, IdRol = ? WHERE IdUsuario = ?";
            sqlsrv_query($conn, $sqlUpdate, [$nombre, $clave, $idRol, $idUsuario]);
        }
    }
    sqlsrv_close($conn);
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

$sqlUsuarios = "SELECT * FROM Usuarios";
$stmtUsuarios = sqlsrv_query($conn, $sqlUsuarios);
$usuarios = [];
while ($row = sqlsrv_fetch_array($stmtUsuarios, SQLSRV_FETCH_ASSOC)) {
    $usuarios[] = $row;
}

$sqlRoles = "SELECT * FROM Roles";
$stmtRoles = sqlsrv_query($conn, $sqlRoles);
$roles = [];
while ($row = sqlsrv_fetch_array($stmtRoles, SQLSRV_FETCH_ASSOC)) {
    $roles[] = $row;
}

sqlsrv_free_stmt($stmtUsuarios);
sqlsrv_free_stmt($stmtRoles);
sqlsrv_close($conn);
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <title>Administrador Usuarios</title>
  <link rel="stylesheet" href="../Libs/Bootstrap/bootstrap.min.css" />
  <link rel="stylesheet" href="../Libs/fontawesome-free-6.7.2-web/css/all.css" />
  <link rel="stylesheet" href="AdministradorUsuarios.css" />
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
    <h1>Administrador de Usuarios</h1>

    <form method="post" action="<?= htmlspecialchars($_SERVER['PHP_SELF']); ?>">

      <?php foreach ($usuarios as $index => $usuario): ?>
      <article>
        <input type="hidden" name="idUsuario[]" value="<?= $usuario['IdUsuario'] ?>" />

        <label>Usuario:
          <input type="text" name="nombreUsuario[]" value="<?= htmlspecialchars($usuario['NombreUsuario']) ?>" required />
        </label>

        <label>Contraseña:
          <input type="text" name="claveUsuario[]" value="<?= htmlspecialchars($usuario['ClaveUsuario']) ?>" required />
        </label>

        <label>Rol:
          <select name="idRol[]">
            <?php foreach ($roles as $rol): ?>
              <option value="<?= $rol['IdRol'] ?>" <?= ($rol['IdRol'] == $usuario['IdRol']) ? 'selected' : '' ?>>
                <?= htmlspecialchars($rol['NombreRol']) ?>
              </option>
            <?php endforeach; ?>
          </select>
        </label>

        <button type="submit" name="eliminar[]" value="<?= $usuario['IdUsuario'] ?>" onclick="return confirm('¿Seguro que deseas eliminar este usuario?');">
          Eliminar
        </button>
      </article>
      <?php endforeach; ?>

      <button type="submit" name="guardar" value="1">
        Guardar Cambios
      </button>

    </form>
  </main>
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
