<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    echo json_encode(['success' => false, 'error' => 'Método no permitido']);
    exit;
}

// Recolectar datos
$nombreCliente = trim($_POST['nombreCliente'] ?? '');
$edadCliente = trim($_POST['edadCliente'] ?? '');
$celular = trim($_POST['celular'] ?? '');
$curpCliente = trim($_POST['curpCliente'] ?? '');
$fecha = trim($_POST['fecha'] ?? '');
$idHora = intval($_POST['hora'] ?? 0);
$idServicio = intval($_POST['servicio'] ?? 0);

// Validar datos
if (
    empty($nombreCliente) || empty($edadCliente) || empty($celular) ||
    empty($curpCliente) || empty($fecha) || $idHora <= 0 || $idServicio <= 0
) {
    echo json_encode(['success' => false, 'error' => 'Faltan datos']);
    exit;
}

// Convertir fecha de DD/MM/YYYY a YYYY-MM-DD
$fechaPartes = explode('/', $fecha);
if (count($fechaPartes) !== 3) {
    echo json_encode(['success' => false, 'error' => 'Formato de fecha incorrecto']);
    exit;
}
$fechaSQL = "{$fechaPartes[2]}-{$fechaPartes[1]}-{$fechaPartes[0]}";

// Conexión a SQL Server
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

// Verificar si el cliente ya existe
$checkSql = "SELECT COUNT(*) AS total FROM Clientes WHERE CURPCliente = ?";
$checkStmt = sqlsrv_query($conn, $checkSql, [$curpCliente]);
if ($checkStmt === false) {
    echo json_encode(['success' => false, 'error' => 'Error al verificar existencia']);
    exit;
}

$row = sqlsrv_fetch_array($checkStmt, SQLSRV_FETCH_ASSOC);
$clienteExiste = $row['total'] > 0;
sqlsrv_free_stmt($checkStmt);

// Insertar o actualizar cliente
if (!$clienteExiste) {
    $insertClienteSql = "INSERT INTO Clientes (NombreCliente, EdadCliente, Celular, CURPCliente)
                         VALUES (?, ?, ?, ?)";
    $params = [$nombreCliente, $edadCliente, $celular, $curpCliente];
    $stmt = sqlsrv_query($conn, $insertClienteSql, $params);
    if ($stmt === false) {
        echo json_encode(['success' => false, 'error' => 'Error al insertar cliente']);
        exit;
    }
    sqlsrv_free_stmt($stmt);
} else {
    $updateClienteSql = "UPDATE Clientes SET NombreCliente = ?, EdadCliente = ?, Celular = ?
                         WHERE CURPCliente = ?";
    $params = [$nombreCliente, $edadCliente, $celular, $curpCliente];
    $stmt = sqlsrv_query($conn, $updateClienteSql, $params);
    if ($stmt === false) {
        echo json_encode(['success' => false, 'error' => 'Error al actualizar cliente']);
        exit;
    }
    sqlsrv_free_stmt($stmt);
}

// Registrar la cita
$insertCitaSql = "INSERT INTO Citas (CURPCliente, EstaAprobada, Horasolicitada, IdServicio, Usuario, Fecha, EstaActivo)
                  VALUES (?, 0, ?, ?, NULL, ?, 0)";
$citaParams = [$curpCliente, $idHora, $idServicio, $fechaSQL];
$citaStmt = sqlsrv_query($conn, $insertCitaSql, $citaParams);

if ($citaStmt === false) {
    echo json_encode(['success' => false, 'error' => 'Error al registrar la cita']);
    exit;
}

sqlsrv_free_stmt($citaStmt);
sqlsrv_close($conn);

echo json_encode(['success' => true, 'cliente_nuevo' => !$clienteExiste]);
?>