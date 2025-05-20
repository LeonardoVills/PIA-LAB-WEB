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
    die("Error de conexión con la base de datos.");
}

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$servicio = null;

if ($id > 0) {
    $sql = "SELECT TOP 1 IdServicio, NombreServicio, DescripcionServicio, TiempoAprox, PrecioServicio, EstaActivo
            FROM dbo.Servicio WHERE IdServicio = ?";
    $stmt = sqlsrv_query($conn, $sql, [$id]);

    if ($stmt === false) {
        die(print_r(sqlsrv_errors(), true));
    }

    $servicio = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
}

sqlsrv_free_stmt($stmt);
sqlsrv_close($conn);
?>