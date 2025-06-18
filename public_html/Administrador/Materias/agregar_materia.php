<?php
session_start();
if (!isset($_SESSION['IDUsuario']) || $_SESSION['Rol'] != 3) {
    header("Location: ../../login.php"); 
    exit();
}

include '../../src/conexion_escritura.php'; 

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombreMateria = $_POST['materia'];

    // Verificar si la materia ya existe
    $sql = "SELECT * FROM MATERIAS WHERE Nombre = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('s', $nombreMateria);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo "La materia ya existe. Por favor, elige otro nombre.";
    } else {
        // Insertar nueva materia
        $sql = "INSERT INTO MATERIAS (Nombre) VALUES (?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('s', $nombreMateria);

        if ($stmt->execute()) {
            header("Location: Materias.php");
            exit();
        } else {
            echo "Error al registrar materia: " . $stmt->error;
        }
    }

    $stmt->close();
}

$conn->close();
?>
