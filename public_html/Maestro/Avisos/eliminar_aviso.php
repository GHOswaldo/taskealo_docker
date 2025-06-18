<?php
// Iniciar sesión para verificar que el usuario esté logueado
session_start();

// Verificar que el usuario haya iniciado sesión
if (!isset($_SESSION['IDUsuario'])) {
    die("Acceso denegado. Por favor, inicia sesión.");
}

// Incluir el archivo de conexión a la base de datos
include '../../src/conexion_escritura.php';

// Verificar si se ha enviado un ID a través del formulario
if (isset($_POST['id'])) {
    $id = $_POST['id'];

    // Preparar la consulta para eliminar el aviso de forma segura
    $sql = "DELETE FROM AVISOS WHERE IDAviso = ?";
    $stmt = $conn->prepare($sql);

    // Verificar si la preparación del statement fue exitosa
    if ($stmt === false) {
        die("Error al preparar la consulta: " . $conn->error);
    }

    // Vincular el parámetro a la consulta
    $stmt->bind_param("i", $id);

    // Ejecutar la consulta y verificar si fue exitosa
    if ($stmt->execute()) {
        // Redirigir de vuelta a la página de listar avisos
        header("Location: listar_avisos.php");
        exit();
    } else {
        echo "Error al eliminar el aviso: " . htmlspecialchars($stmt->error);
    }

    // Cerrar el statement
    $stmt->close();
} else {
    echo "ID no especificado.";
}

// Cerrar la conexión a la base de datos
$conn->close();
?>

