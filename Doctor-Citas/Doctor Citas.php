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

$mensaje = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['curpOriginal'])) {
    $curpOriginal = trim($_POST['curpOriginal']);
    $curp = trim($_POST['CURPCliente']);
    $nombre = $_POST['NombrePaciente'] ?? '';
    $info = $_POST['InformacionPaciente'] ?? '';
    $comentario = $_POST['ExpComentario'] ?? '';
    $usuario = $_POST['Usuario'] ?? '';

    if ($curp && $nombre && $info && $comentario && $usuario) {
        // Verificar que el nuevo CURPCliente existe en Clientes
        $checkClienteSql = "SELECT COUNT(*) AS count FROM Clientes WHERE CURPCliente = ?";
        $checkStmt = sqlsrv_query($conn, $checkClienteSql, [$curp]);
        if ($checkStmt === false) {
            $mensaje = "❌ Error al verificar CURPCliente: " . print_r(sqlsrv_errors(), true);
        } else {
            $checkRow = sqlsrv_fetch_array($checkStmt, SQLSRV_FETCH_ASSOC);
            if ($checkRow['count'] == 0) {
                $mensaje = "⚠️ El CURPCliente '$curp' no existe en la tabla Clientes. No se puede actualizar.";
            } else {
                // Si existe, actualiza
                $sqlUpdate = "UPDATE Expediente SET 
                    CURPCliente = ?, 
                    NombrePaciente = ?, 
                    InformacionPaciente = ?, 
                    ExpComentario = ?, 
                    Usuario = ? 
                    WHERE CURPCliente = ?";
                $params = [$curp, $nombre, $info, $comentario, $usuario, $curpOriginal];
                $stmt = sqlsrv_query($conn, $sqlUpdate, $params);
                if ($stmt === false) {
                    $mensaje = "❌ Error al actualizar: " . print_r(sqlsrv_errors(), true);
                } else {
                    $mensaje = "✅ Expediente actualizado correctamente.";
                }
            }
        }
    } else {
        $mensaje = "⚠️ Por favor llena todos los campos para actualizar.";
    }
}

$expedientes = [];
$sql = "SELECT CURPCliente, NombrePaciente, InformacionPaciente, ExpComentario, Usuario FROM Expediente";
$stmt = sqlsrv_query($conn, $sql);
if ($stmt === false) {
    die(print_r(sqlsrv_errors(), true));
}
while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
    $expedientes[] = $row;
}

$usuarios = [];
$sqlUsuarios = "SELECT IdUsuario, NombreUsuario FROM Usuarios";
$stmtUsuarios = sqlsrv_query($conn, $sqlUsuarios);
if ($stmtUsuarios === false) {
    die(print_r(sqlsrv_errors(), true));
}
while ($rowU = sqlsrv_fetch_array($stmtUsuarios, SQLSRV_FETCH_ASSOC)) {
    $usuarios[] = $rowU;
}

sqlsrv_close($conn);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <title>Administrar Expedientes</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="Doctorcitas.css" />
    <link rel="stylesheet" href="../Libs/fontawesome-free-6.7.2-web/css/all.min.css" />
    <link rel="stylesheet" href="../Libs/Bootstrap/bootstrap.min.css" />
</head>

<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <div class="container-fluid">
     <a class="navbar-brand" href="../Doctor-ModuloPantalla/Pantalla Modo Doctor.html"><i class="fa-solid fa-arrow-left"></i></a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent">
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

<body class="container mt-4">
    <h2>Administrar Expedientes</h2>

    <?php if ($mensaje): ?>
        <div class="alert alert-info"><?= htmlspecialchars($mensaje) ?></div>
    <?php endif; ?>

    <h4>Editar Expedientes Existentes</h4>

    <?php foreach ($expedientes as $exp): ?>
    <form method="POST" class="border p-3 rounded mb-4">
        <input type="hidden" name="curpOriginal" value="<?= htmlspecialchars($exp['CURPCliente']) ?>" />
        <div class="mb-3">
            <label class="form-label">CURP del Paciente</label>
            <input type="text" name="CURPCliente" class="form-control" value="<?= htmlspecialchars($exp['CURPCliente']) ?>" required />
        </div>
        <div class="mb-3">
            <label class="form-label">Nombre del Paciente</label>
            <input type="text" name="NombrePaciente" class="form-control" value="<?= htmlspecialchars($exp['NombrePaciente']) ?>" required />
        </div>
        <div class="mb-3">
            <label class="form-label">Información del Paciente</label>
            <textarea name="InformacionPaciente" class="form-control" rows="3" required><?= htmlspecialchars($exp['InformacionPaciente']) ?></textarea>
        </div>
        <div class="mb-3">
            <label class="form-label">Comentario</label>
            <textarea name="ExpComentario" class="form-control" rows="2" required><?= htmlspecialchars($exp['ExpComentario']) ?></textarea>
        </div>
        <div class="mb-3">
            <label class="form-label">Usuario</label>
            <select name="Usuario" class="form-control" required>
                <?php foreach ($usuarios as $usuario): ?>
                    <option value="<?= htmlspecialchars($usuario['IdUsuario']) ?>" <?= ($usuario['IdUsuario'] == $exp['Usuario']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($usuario['NombreUsuario']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <button type="submit" class="btn btn-success">Guardar Cambios</button>
    </form>
    <?php endforeach; ?>

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
    </body>
</html>

