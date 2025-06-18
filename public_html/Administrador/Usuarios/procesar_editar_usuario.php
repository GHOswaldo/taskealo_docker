<?php
session_start();
if (!isset($_SESSION['IDUsuario']) || $_SESSION['Rol'] != 3) {
    header("Location: login.html");
    exit();
}

include '../../src/conexion_escritura.php';

$id = $_POST['id'];
$nombre = $_POST['nombre'];
$usuario = $_POST['usuario'];
$password = $_POST['password'];
$curp = $_POST['curp'];
$rol = $_POST['rol'];
$carrera = isset($_POST['carrera']) ? $_POST['carrera'] : null; // Carrera solo si es alumno

// Verificar si se ingresó una nueva contraseña
if (empty($password)) {
    // Si no se ingresó una nueva contraseña, mantener la actual
    $sql_password = "SELECT Password FROM USUARIOS WHERE IDUsuario = ?";
    $stmt_password = $conn->prepare($sql_password);
    $stmt_password->bind_param("i", $id);
    $stmt_password->execute();
    $stmt_password->bind_result($password_actual);
    $stmt_password->fetch();
    $stmt_password->close();

    $password_hashed = $password_actual; // Mantener la contraseña actual
} else {
    // Si se ingresó una nueva contraseña, almacenarla sin cifrar
    $password_hashed = $password; // No cifrar la nueva contraseña
}

// Actualizar usuario
$sql_usuario = "UPDATE USUARIOS SET Nombre=?, Usuario=?, Password=?, CURP=?, IDRol=? WHERE IDUsuario=?";
$stmt_usuario = $conn->prepare($sql_usuario);
$stmt_usuario->bind_param("ssssii", $nombre, $usuario, $password_hashed, $curp, $rol, $id);
if (!$stmt_usuario->execute()) {
    die("Error al actualizar usuario: " . $stmt_usuario->error);
}


// Solo actualizar detalles del alumno si el rol es Alumno
if ($rol == 1 && $carrera) {
    $sql_detalle = "UPDATE DETALLES_ALUMNOS SET IDCarrera=? WHERE IDUsuario=?";
    $stmt_detalle = $conn->prepare($sql_detalle);
    $stmt_detalle->bind_param("ii", $carrera, $id);
    if (!$stmt_detalle->execute()) {
        die("Error al actualizar detalles del alumno: " . $stmt_detalle->error);
    }
}

$conn->close();
header("Location: Usuarios.php");
exit();
?>
