<?php
// Mostrar errores para desarrollo
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Responder solo con JSON
header('Content-Type: application/json');

// Validar método POST
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    echo json_encode(['success' => false, 'error' => 'Método no permitido']);
    exit;
}

// Capturar y limpiar datos requeridos
$nombreCliente = trim($_POST['nombreCliente'] ?? '');
$edadCliente = trim($_POST['edadCliente'] ?? '');
$celular = trim($_POST['celular'] ?? '');
$curpCliente = trim($_POST['curpCliente'] ?? '');

// Validar campos básicos
if (empty($nombreCliente) || empty($edadCliente) || empty($celular) || empty($curpCliente)) {
    echo json_encode(['success' => false, 'error' => 'Faltan datos']);
    exit;
}

// Conectar a SQL Server
$serverName = "Laptop_Villa\\SQLEXPRESS,1433";
$connectionInfo = [
    "Database" => "Fase3",
    "TrustServerCertificate" => true
];
$conn = sqlsrv_connect($serverName, $connectionInfo);

if (!$conn) {
    echo json_encode(['success' => false, 'error' => 'Error de conexión']);
    exit;
}

// Insertar datos (solo los campos requeridos por ahora)
$sql = "INSERT INTO Clientes (NombreCliente, EdadCliente, Celular, CURPCliente) VALUES (?, ?, ?, ?)";
$params = [$nombreCliente, $edadCliente, $celular, $curpCliente];
$stmt = sqlsrv_query($conn, $sql, $params);

if ($stmt === false) {
    echo json_encode(['success' => false, 'error' => 'Error al guardar']);
    exit;
}

// Éxito
echo json_encode(['success' => true]);

// Cerrar recursos
sqlsrv_free_stmt($stmt);
sqlsrv_close($conn);
