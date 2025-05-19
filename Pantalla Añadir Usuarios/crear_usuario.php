<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    die("Método no permitido.");
}

$nombre = trim($_POST['nuevoNombre'] ?? '');
$clave = trim($_POST['nuevaContraseña'] ?? '');
$idRol = intval($_POST['nuevoRolUsuario'] ?? 0);
$horario = intval($_POST['nuevoHorario'] ?? 0); 


if (empty($nombre) || empty($clave) || $idRol <= 0 || $horario <= 0) {
    die("Faltan datos obligatorios o valores inválidos.");
}

$claveHash = password_hash($clave, PASSWORD_DEFAULT);

$serverName = "Laptop_Villa\\SQLEXPRESS,1433";
$connectionInfo = [
    "Database" => "Fase3",
    "TrustServerCertificate" => true
];
$conn = sqlsrv_connect($serverName, $connectionInfo);

if (!$conn) {
    die("Error de conexión con la base de datos: " . print_r(sqlsrv_errors(), true));
}

$sqlRol = "SELECT COUNT(*) AS Total FROM Roles WHERE IdRol = ?";
$stmtRol = sqlsrv_query($conn, $sqlRol, [$idRol]);
$rowRol = sqlsrv_fetch_array($stmtRol);
if ($rowRol['Total'] == 0) {
    die("El IdRol proporcionado no existe.");
}

$sqlHorario = "SELECT COUNT(*) AS Total FROM Horarios WHERE IdHora = ?";
$stmtHorario = sqlsrv_query($conn, $sqlHorario, [$horario]);
$rowHorario = sqlsrv_fetch_array($stmtHorario);
if ($rowHorario['Total'] == 0) {
    die("El IdHora proporcionado no existe.");
}

$sqlInsert = "INSERT INTO Usuarios (NombreUsuario, ClaveUsuario, IdRol, Horario) VALUES (?, ?, ?, ?)";
$params = [$nombre, $claveHash, $idRol, $horario];
$stmtInsert = sqlsrv_query($conn, $sqlInsert, $params);

if ($stmtInsert === false) {
    die("Error al insertar en la base de datos: " . print_r(sqlsrv_errors(), true));
}

sqlsrv_free_stmt($stmtRol);
sqlsrv_free_stmt($stmtHorario);
sqlsrv_free_stmt($stmtInsert);
sqlsrv_close($conn);

echo "<h2>✅ Usuario creado exitosamente.</h2>";
echo "<a href='añadir_usuario.html'>Volver</a>";
?>
