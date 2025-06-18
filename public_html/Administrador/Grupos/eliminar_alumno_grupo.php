<?php

include '../../src/conexion_escritura.php';

// Verificar si se ha enviado el formulario para eliminar un alumno
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Obtener el ID del alumno y el ID del grupo
    $id_alumno = $_POST['id_alumno'];
    $id_grupo = $_POST['id_grupo'];

    // Eliminar al alumno del grupo
    $sql = "DELETE FROM GRUPOS_ALUMNOS WHERE IDGrupo = ? AND IDAlumno = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $id_grupo, $id_alumno);
    $stmt->execute();

    // Redirigir de vuelta a la página de gestión de alumnos
    header("Location: gestionar_alumnos_grupo.php?id_grupo=" . $id_grupo); 
    exit;
}

// Cerrar conexión
$conn->close();
?>

