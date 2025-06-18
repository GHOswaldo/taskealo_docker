<?php

include '../../src/conexion_escritura.php';

// Recoger el ID del grupo a eliminar
$id_grupo = $_POST['id_grupo'];

// Eliminar el grupo de la tabla GRUPOS
$sqlEliminar = "DELETE FROM GRUPOS WHERE IDGrupo = $id_grupo";
if ($conn->query($sqlEliminar) === TRUE) {
    echo "Grupo eliminado exitosamente.";
} else {
    echo "Error al eliminar el grupo: " . $conn->error;
}

// Cerrar la conexión
$conn->close();

// Redirigir a la página de grupos después de eliminar
header("Location: Grupos.php");
exit();

?>
