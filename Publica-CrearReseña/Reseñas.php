<?php
// Mostrar errores
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Conexión a la base de datos
$serverName = "Laptop_Villa\\SQLEXPRESS,1433";
$connectionInfo = [
    "Database" => "Fase3",
    "TrustServerCertificate" => true
];
$conn = sqlsrv_connect($serverName, $connectionInfo);

if (!$conn) {
    die("Conexión fallida: " . print_r(sqlsrv_errors(), true));
}

// Obtener datos del POST
$nombre = $_POST['nombre'] ?? '';
$comentario = $_POST['comentario'] ?? '';
$calificacion = intval($_POST['calificacion'] ?? 0);

// Validación básica
if (empty($nombre) || $calificacion < 1 || $calificacion > 5) {
    die("Datos inválidos.");
}

// Insertar en la base de datos
$sql = "INSERT INTO Calificaciones (Calificacion, Comentario, FechaComentario, NombrePersona)
        VALUES (?, ?, GETDATE(), ?)";
$params = [$calificacion, $comentario, $nombre];

$stmt = sqlsrv_query($conn, $sql, $params);

if ($stmt === false) {
    die("Error al insertar: " . print_r(sqlsrv_errors(), true));
}

sqlsrv_free_stmt($stmt);
sqlsrv_close($conn);

// Redirigir de vuelta
header("Location: ../Publico-Reseñas/Reseñas.php");
exit;
?>
