<?php
// Iniciar sesión para verificar si el usuario está logueado
session_start();

// Verificar que el usuario esté logueado
if (!isset($_SESSION['IDUsuario'])) {
    die("Acceso denegado. Por favor, inicia sesión.");
}

// Incluir archivo de conexión a la base de datos
include '../../src/conexion_escritura.php';

// Obtener el ID de la tarea desde el formulario POST
$idTarea = isset($_POST['id']) ? intval($_POST['id']) : 0;

if ($idTarea == 0) {
    die("ID de tarea inválido.");
}

// Eliminar la tarea de la base de datos utilizando consulta preparada
$sql = "DELETE FROM TAREAS WHERE IDTarea = ?";
$stmt = $conn->prepare($sql);

if ($stmt === false) {
    die("Error al preparar la consulta: " . $conn->error);
}

// Vincular el parámetro a la consulta
$stmt->bind_param('i', $idTarea);

// Ejecutar la consulta
if ($stmt->execute()) {
    // Redirigir de vuelta a la lista de tareas
    header("Location: listar_tareas_maestro.php");
    exit();
} else {
    echo "Error al eliminar la tarea: " . $stmt->error;
}

// Cerrar la consulta y la conexión
$stmt->close();
$conn->close();
?>
