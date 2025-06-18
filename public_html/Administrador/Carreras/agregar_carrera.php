<?php
session_start();
if (!isset($_SESSION['IDUsuario']) || $_SESSION['Rol'] != 3) {
    header("Location: ../../login.php");
    exit();
}

include '../../src/conexion_escritura.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre_carrera = $_POST['nombre_carrera'];

    // Validar que el campo no esté vacío
    if (!empty($nombre_carrera)) {
        // Verificar si la carrera ya existe
        $sql = "SELECT * FROM CARRERAS WHERE NombreCarrera = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('s', $nombre_carrera);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            echo "La carrera ya existe.";
        } else {
            // Insertar la nueva carrera
            $sql = "INSERT INTO CARRERAS (NombreCarrera) VALUES (?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param('s', $nombre_carrera);
            if ($stmt->execute()) {
                header("Location: Carreras.php");
                exit();
            } else {
                echo "Error al agregar la carrera: " . $stmt->error;
            }
        }

        $stmt->close();
    } else {
        echo "El nombre de la carrera no puede estar vacío.";
    }
}

$conn->close();
?>

