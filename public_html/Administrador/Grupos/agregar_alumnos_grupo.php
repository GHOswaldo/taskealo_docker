<?php
// Iniciar sesión para poder usar variables de sesión
session_start();

include '../../src/conexion_escritura.php'; 

// Verificar si se ha enviado el formulario
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Obtener el ID del grupo y los alumnos seleccionados
    $id_grupo = $_POST['id_grupo'];
    $alumnos = isset($_POST['alumnos']) ? $_POST['alumnos'] : [];

    // Si hay alumnos seleccionados, añadirlos al grupo
    if (!empty($alumnos)) {
        foreach ($alumnos as $id_alumno) {
            // Evitar duplicados, insertando solo si no existe
            $sql = "INSERT INTO GRUPOS_ALUMNOS (IDGrupo, IDAlumno) 
                    SELECT ?, ? 
                    WHERE NOT EXISTS (
                        SELECT 1 FROM GRUPOS_ALUMNOS WHERE IDGrupo = ? AND IDAlumno = ?
                    )";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("iiii", $id_grupo, $id_alumno, $id_grupo, $id_alumno);
            $stmt->execute();
        }
        // Guardar el mensaje en la sesión
        $_SESSION['mensaje'] = "Alumnos agregados correctamente al grupo.";
    } else {
        $_SESSION['mensaje'] = "No se seleccionó ningún alumno.";
    }

    // Redirigir de vuelta a la página de gestión de alumnos
    header("Location: gestionar_alumnos_grupo.php?id_grupo=" . $id_grupo);
    exit;
}

// Cerrar conexión
$conn->close();
?>
