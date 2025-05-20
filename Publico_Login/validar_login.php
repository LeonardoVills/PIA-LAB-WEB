<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    die("Método no permitido.");
}

$usuario = trim($_POST['usuario'] ?? '');
$contrasena = trim($_POST['contrasena'] ?? '');

if (empty($usuario) || empty($contrasena)) {
    echo "<script>alert('Faltan el usuario o la contraseña.'); window.location.href = 'Login.html';</script>";
    exit();
}

$serverName = "Laptop_Villa\\SQLEXPRESS,1433";
$connectionInfo = [
    "Database" => "Fase3",
    "TrustServerCertificate" => true
];
$conn = sqlsrv_connect($serverName, $connectionInfo);

if (!$conn) {
    echo "<script>alert('Error de conexión a la base de datos.'); window.location.href = 'Login.html';</script>";
    exit();
}

$sql = "SELECT RTRIM(ClaveUsuario) AS ClaveUsuario, idrol FROM Usuarios WHERE NombreUsuario = ?";
$params = [$usuario];
$stmt = sqlsrv_query($conn, $sql, $params);

if ($stmt === false) {
    echo "<script>alert('Error en la consulta.'); window.location.href = 'Login.html';</script>";
    exit();
}

$usuarioEncontrado = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);

if ($usuarioEncontrado && $contrasena === trim($usuarioEncontrado['ClaveUsuario'])) {
    $idrol = (int) $usuarioEncontrado['idrol'];

    switch ($idrol) {
        case 10:
            header("Location: http://localhost/PIA-LAB-WEB/Doctor-ModuloPantalla/Pantalla%20Modo%20Doctor.html");
            exit();
        case 12:
            header("Location: http://localhost/PIA-LAB-WEB/Secretario-ModuloPantalla/Pantalla%20Modo%20Secretario.html");
            exit();
        case 13:
            header("Location: http://localhost/PIA-LAB-WEB/Administrador_ModulosPantalla/Pantalla%20de%20Modulo%20Administrador.html");
            exit();
        default:
            echo "<script>alert('Bienvenido, $usuario. Rol no asignado a una redirección específica.'); window.location.href = 'Login.html';</script>";
            exit();
    }
} else {
    echo "<script>alert('❌ Usuario o contraseña incorrectos.'); window.location.href = 'Login.html';</script>";
    exit();
}

sqlsrv_free_stmt($stmt);
sqlsrv_close($conn);
?>
