<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$serverName = "Laptop_Villa\\SQLEXPRESS,1433";
$connectionInfo = ["Database" => "Fase3", "TrustServerCertificate" => true];
$conn = sqlsrv_connect($serverName, $connectionInfo);

if (!$conn) {
    die("Error de conexión con la base de datos.");
}

// Eliminar usuario(s) si se presionó botón eliminar
if (!empty($_POST['eliminar'])) {
    foreach ($_POST['eliminar'] as $idEliminar) {
        $sqlDelete = "DELETE FROM Usuarios WHERE IdUsuario = ?";
        $stmt = sqlsrv_query($conn, $sqlDelete, [$idEliminar]);
    }
}

// Actualizar usuario(s) si se presionó botón guardar
if (!empty($_POST['guardar']) && !empty($_POST['idUsuario'])) {
    foreach ($_POST['idUsuario'] as $index => $idUsuario) {
        $nombre = $_POST['nombreUsuario'][$index] ?? '';
        $clave = $_POST['claveUsuario'][$index] ?? '';
        $idRol = $_POST['idRol'][$index] ?? '';

        $sqlUpdate = "UPDATE Usuarios SET NombreUsuario = ?, ClaveUsuario = ?, IdRol = ? WHERE IdUsuario = ?";
        $params = [$nombre, $clave, $idRol, $idUsuario];
        $stmt = sqlsrv_query($conn, $sqlUpdate, $params);
    }
}

sqlsrv_close($conn);

// Redireccionar a la página de administración para evitar reenvío de formulario
header("Location: AdministradorUsuarios.php");
exit;