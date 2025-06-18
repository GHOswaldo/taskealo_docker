<?php
session_start();
if (!isset($_SESSION['IDUsuario']) || $_SESSION['Rol'] != 3) {
    header("Location: login.html");
    exit();
}

include '../../src/conexion_escritura.php';

// Procesar la solicitud de eliminación de usuario
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'];

    // Eliminar el usuario
    $sql = "DELETE FROM USUARIOS WHERE IDUsuario = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $id);
    $stmt->execute();

    header("Location: Usuarios.php");
    exit();
}

$conn->close();
?>
