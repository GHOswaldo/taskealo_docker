<?php
session_start();
if (!isset($_SESSION['IDUsuario']) || $_SESSION['Rol'] != 3) {
    header("Location: ../../login.php");
    exit();
}

include '../../src/conexion_escritura.php'; 

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'];
    $sql = "DELETE FROM CARRERAS WHERE IDCarrera = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $id);
    if ($stmt->execute()) {
        header("Location: Carreras.php");
        exit();
    } else {
        echo "Error al eliminar la carrera.";
    }
    $stmt->close();
}

$conn->close();
?>
