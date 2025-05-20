<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    echo json_encode(['success' => false, 'message' => 'Método no permitido.']);
    exit();
}

$nombre = trim($_POST['nuevoNombre'] ?? '');
$clave = trim($_POST['nuevaContraseña'] ?? '');
$idRol = intval($_POST['nuevoRolUsuario'] ?? 0);

if (empty($nombre) || empty($clave) || $idRol <= 0) {
    echo json_encode(['success' => false, 'message' => 'Faltan datos obligatorios o valores inválidos.']);
    exit();
}

$serverName = "Laptop_Villa\\SQLEXPRESS,1433";
$connectionInfo = [
    "Database" => "Fase3",
    "TrustServerCertificate" => true
];
$conn = sqlsrv_connect($serverName, $connectionInfo);

if (!$conn) {
    echo json_encode(['success' => false, 'message' => 'Error de conexión con la base de datos.']);
    exit();
}
$sqlRol = "SELECT COUNT(*) AS Total FROM Roles WHERE IdRol = ?";
$stmtRol = sqlsrv_query($conn, $sqlRol, [$idRol]);
$rowRol = sqlsrv_fetch_array($stmtRol, SQLSRV_FETCH_ASSOC);
if ($rowRol['Total'] == 0) {
    echo json_encode(['success' => false, 'message' => 'El IdRol proporcionado no existe.']);
    exit();
}
$sqlCheckUser = "SELECT COUNT(*) AS Total FROM Usuarios WHERE NombreUsuario = ?";
$stmtCheckUser = sqlsrv_query($conn, $sqlCheckUser, [$nombre]);
$rowCheckUser = sqlsrv_fetch_array($stmtCheckUser, SQLSRV_FETCH_ASSOC);

if ($rowCheckUser['Total'] > 0) {
    echo json_encode(['success' => false, 'message' => "El nombre de usuario \"$nombre\" ya existe. Por favor elige otro."]);
    exit();
}
$sqlInsert = "INSERT INTO Usuarios (NombreUsuario, ClaveUsuario, IdRol) VALUES (?, ?, ?)";
$params = [$nombre, $clave, $idRol];  
$stmtInsert = sqlsrv_query($conn, $sqlInsert, $params);

if ($stmtInsert === false) {
    echo json_encode(['success' => false, 'message' => 'Error al insertar en la base de datos.']);
    exit();
}

sqlsrv_free_stmt($stmtRol);
sqlsrv_free_stmt($stmtCheckUser);
sqlsrv_free_stmt($stmtInsert);
sqlsrv_close($conn);

echo json_encode(['success' => true, 'message' => 'Usuario creado exitosamente.']);
exit();

?>
