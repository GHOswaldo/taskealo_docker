<?php
// Iniciar sesión si aún no se ha iniciado
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Datos de conexión
$host = 'nginx';
$user = 'root';
$password = '12345';
$database = 'taskealo';

// Crear conexión
$conn = new mysqli($host, $user, $password, $database);

// Verificar la conexión
if ($conn->connect_error) {
    die("Error de conexión a la base de datos: " . $conn->connect_error);
}
?>