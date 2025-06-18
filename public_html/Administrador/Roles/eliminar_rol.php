<?php
session_start();
if (!isset($_SESSION['IDUsuario']) || $_SESSION['Rol'] != 3) {
    header("Location: ../../login.php");
    exit();
}

include '../../conexion_escritura.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'];
    $sql = "DELETE FROM ROLES WHERE IDRol = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $id);
    if ($stmt->execute()) {
        header("Location: Roles.php");
        exit();
    } else {
        echo "Error al eliminar el rol.";
    }
    $stmt->close();
}

$conn->close();
?>
