<?php
session_start();
if (!isset($_SESSION['IDUsuario']) || $_SESSION['Rol'] != 3) {
    header("Location: ../../login.php");
    exit();
}

include '../../src/conexion_escritura.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre_rol = $_POST['nombre_rol'];

    // Validación simple de entrada
    if (!empty($nombre_rol)) {
        // Insertar el rol en la base de datos
        $sql = "INSERT INTO ROLES (NombreRol) VALUES (?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('s', $nombre_rol);

        if ($stmt->execute()) {
            // Redireccionar después de agregar el rol
            header("Location: Roles.php");
            exit();
        } else {
            echo "Error al agregar el rol: " . $stmt->error;
        }
        $stmt->close();
    } else {
        echo "El nombre del rol no puede estar vacío.";
    }
}

$conn->close();
?>
