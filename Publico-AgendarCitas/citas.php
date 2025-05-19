<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);
header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    echo json_encode(['success' => false, 'error' => 'Método no permitido']);
    exit;
}

$nombreCliente = trim($_POST['nombreCliente'] ?? '');
$edadCliente = trim($_POST['edadCliente'] ?? '');
$celular = trim($_POST['celular'] ?? '');
$curpCliente = trim($_POST['curpCliente'] ?? '');


if (empty($nombreCliente) || empty($edadCliente) || empty($celular) || empty($curpCliente)) {
    echo json_encode(['success' => false, 'error' => 'Faltan datos']);
    exit;
}
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

$sqlCheck = "SELECT CURPCliente FROM Clientes WHERE CURPCliente = ?";
$paramsCheck = [$curpCliente];
$stmtCheck = sqlsrv_query($conn, $sqlCheck, $paramsCheck);

if ($stmtCheck === false) {
    echo json_encode(['success' => false, 'error' => 'Error al verificar CURP']);
    exit;
}

if (sqlsrv_has_rows($stmtCheck)) {

    $sqlUpdate = "UPDATE Clientes SET NombreCliente = ?, EdadCliente = ?, Celular = ? WHERE CURPCliente = ?";
    $paramsUpdate = [$nombreCliente, $edadCliente, $celular, $curpCliente];
    $stmtUpdate = sqlsrv_query($conn, $sqlUpdate, $paramsUpdate);

    if ($stmtUpdate === false) {
        echo json_encode(['success' => false, 'error' => 'Error al actualizar']);
    } else {
        echo json_encode(['success' => true, 'message' => 'Cliente actualizado']);
    }

    sqlsrv_free_stmt($stmtUpdate);
} else {

    $sqlInsert = "INSERT INTO Clientes (NombreCliente, EdadCliente, Celular, CURPCliente) VALUES (?, ?, ?, ?)";
    $paramsInsert = [$nombreCliente, $edadCliente, $celular, $curpCliente];
    $stmtInsert = sqlsrv_query($conn, $sqlInsert, $paramsInsert);

    if ($stmtInsert === false) {
        echo json_encode(['success' => false, 'error' => 'Error al insertar']);
    } else {
        echo json_encode(['success' => true, 'message' => 'Cliente registrado']);
    }

    sqlsrv_free_stmt($stmtInsert);
}

sqlsrv_free_stmt($stmtCheck);
sqlsrv_close($conn);
